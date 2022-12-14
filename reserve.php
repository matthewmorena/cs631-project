<?php session_start(); ?>
<html>
    <head>
        <?php
            require("db.php");
            if (isset($_POST["copyno"])) {
                $copy = $_POST["copyno"];
                $doc = $_POST["doc"];
                $branch = $_POST["branch"];
                
                $db = new PDO( 'mysql:host=sql1.njit.edu;dbname=mem63', $dbuser, $dbpass );
                $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
              	if(!$db){
              		  die("Fatal Error: Connection Failed!");
              	}
               
                $stmt = $db->prepare("SELECT NumResBooks from Lib_Reader where ReaderID = :rid");
                try {
                    $r = $stmt->execute([":rid" => $_SESSION['readerid']]);
                    if ($r) {
                        $res = $stmt->fetch(PDO::FETCH_ASSOC);
                        $b = intval($res["NumResBooks"]);
                        
                        if ($b < 10) {
                            $stmt = $db->prepare("UPDATE Lib_Reader SET NumResBooks = :res where ReaderID = :rid");
                            $r = $stmt->execute([":res" => $b+1, "rid" => $_SESSION['readerid']]);
                            
                            $stmt = $db->prepare("UPDATE Lib_Copy SET Available = 0 where CopyNo = :cn");
                            $r = $stmt->execute([":cn" => $copy]);
                            
                            $stmt = $db->prepare("INSERT INTO Lib_Reserves (RID, CN, Doc, Branch) VALUES (:rid, :cn, :doc, :branch)");
                            $r = $stmt->execute([":rid" => $_SESSION['readerid'], ":cn" => $copy, ":doc" => $doc, ":branch" => $branch]);
                            
                            $stmt = $db->prepare("SELECT RNum from Lib_Reserves where RID = :rid AND CN = :cn");
                            $r = $stmt->execute([":rid" => $_SESSION['readerid'], ":cn" => $copy]);
                            if ($r) {
                                $rnum = $stmt->fetch(PDO::FETCH_ASSOC);
                                $n = intval($rnum['RNum']);
                                
                                $stmt = $db->prepare("INSERT INTO Lib_Reservation (ResNumber) VALUES (:rn)");
                                $r = $stmt->execute([":rn" => $n]);
                            }                          
                            
                        }
                    } else {
                        echo "error";
                    }
                } catch (Exception $e) {
                    var_export($e, true);
                }
            }
        ?>
    <style>
            body {
                font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
                font-size: large;
                padding: 2%;
            }
        </style>
    </head>
    <body>
        <h1>CS631 Library</h1>
        <p> Thank you! </p>
        <a href="./home.php"> Return Home </a>
    </body>
</html>