<?php session_start(); ?>
<html>
    <head>
        <?php
            require("db.php");
           
            $db = new PDO( 'mysql:host=sql1.njit.edu;dbname=mem63', $dbuser, $dbpass );
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
          	if(!$db){
          		  die("Fatal Error: Connection Failed!");
          	}
           
            $stmt = $db->prepare("SELECT BNum, Title, BorDateTime, RetDateTime, br.Name, LateFee, CopyNo from Lib_Borrows AS b INNER JOIN Lib_BorTransaction AS bt ON b.BNum = bt.BorNumber INNER JOIN Lib_Copy AS c ON b.CN = c.CopyNo INNER JOIN Lib_Document AS d ON d.DocID = c.CopyID INNER JOIN Lib_Branch AS br ON br.BID = BranchID where (b.RID = :rid)");
            try {
                $r = $stmt->execute([":rid" => $_SESSION["readerid"]]);
                if ($r) {
                    $borrows = $stmt->fetchALL(PDO::FETCH_ASSOC);
                    if ($borrows) {
                        //var_dump($borrows);
                    }
                } else {
                    echo "error";
                }
            } catch (Exception $e) {
                var_export($e, true);
            }
            
            $stmt = $db->prepare("SELECT RNum, Title, ResDateTime, PickedUp, br.Name, rt.Canceled, CopyNo from Lib_Reserves AS r INNER JOIN Lib_Reservation AS rt ON r.RNum = rt.ResNumber INNER JOIN Lib_Copy AS c ON r.CN = c.CopyNo INNER JOIN Lib_Document AS d ON d.DocID = c.CopyID INNER JOIN Lib_Branch AS br ON br.BID = BranchID where (r.RID = :rid)");
            try {
                $r = $stmt->execute([":rid" => $_SESSION["readerid"]]);
                if ($r) {
                    $reserves = $stmt->fetchALL(PDO::FETCH_ASSOC);
                    if ($reserves) {
                        //var_dump($reserves);
                    }
                } else {
                    echo "error";
                }
            } catch (Exception $e) {
                var_export($e, true);
            }
            
            if (isset($_POST['return'])) {
                
                $BNum = $_POST['returnBNum'];
                $CN = $_POST['returnCNum'];
                
                $stmt = $db->prepare("UPDATE Lib_BorTransaction SET RetDateTime = CURRENT_TIMESTAMP WHERE BorNumber = :bn");
                $r = $stmt->execute([":bn" => $BNum]);
                
                $stmt = $db->prepare("UPDATE Lib_Copy SET Available = 1 WHERE CopyNo = :cn");
                $r = $stmt->execute([":cn" => $CN]);
                
                $stmt = $db->prepare("UPDATE Lib_Reader SET NumBorBooks = NumBorBooks - 1 WHERE ReaderID = :rid AND NumBorBooks > 0");
                $r = $stmt->execute([":rid" => $_SESSION['readerid']]);
                
                $stmt = $db->prepare("SELECT DATEDIFF(RetDateTime, BorDateTime) AS num from Lib_BorTransaction WHERE BorNumber = :bn");
                $r = $stmt->execute([":bn" => $BNum]);
                if ($r) {
                    $date = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($date) {
                        echo "TRUE";
                        $num = $date['num'];
                        if ($num > 20) {
                            $latefee = ($num - 20) * 0.20;
                            $stmt = $db->prepare("UPDATE Lib_BorTransaction SET LateFee = :late WHERE BorNumber = :bn");
                            $r = $stmt->execute([":late" => $latefee, ":bn" => $BNum]);
                        }  
                    }
                }
            }
            
            if (isset($_POST['cancel'])) {
                echo "true";
                $RNum = $_POST['cancelRNum'];
                $CN = $_POST['cancelCNum'];
                echo $RNum.$CN;
                $stmt = $db->prepare("UPDATE Lib_Reservation SET Canceled = 1 WHERE ResNumber = :rn");
                $r = $stmt->execute([":rn" => $RNum]);
                
                $stmt = $db->prepare("UPDATE Lib_Copy SET Available = 1 WHERE CopyNo = :cn");
                $r = $stmt->execute([":cn" => $CN]);
                
                $stmt = $db->prepare("UPDATE Lib_Reader SET NumResBooks = NumResBooks - 1 WHERE ReaderID = :rid AND NumResBooks > 0");
                $r = $stmt->execute([":rid" => $_SESSION['readerid']]);
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
        <h2>Welcome, <?php echo $_SESSION['readername']; ?></h2>
        <a href="./login.php" style="float: right;">Quit</a>
        <form method="post" action="home.php">
            <input type="submit" class="mt-3 btn btn-dark" value="Home" />
        </form>
        <form method="post" action="account.php">
            <input type="submit" class="mt-3 btn btn-dark" value="My Account" />
        </form>
        <h3>My Borrows</h3>
        <hr/>
        <h3>borrows</h3>
        <hr/>
        <table width=100%>
            <thead align="left">
                <th width=10%>Borrow Num</th>
                <th width=20%>Document Title</th>
                <th width=10%>Borrowed Time</th>
                <th width=10%>Returned Time</th>
                <th width=10%>Branch</th>
                <th width=10%>Late Fee</th>
                <th width=5%></th>
            </thead>
            <tbody>
                <?php foreach ($borrows as $borrow): ?>
                    <tr>
                        <td> <?php echo $borrow['BNum']; ?> </td>
                        <td> <?php echo $borrow['Title']; ?> </td>
                        <td> <?php echo $borrow['BorDateTime']; ?> </td>
                        <td> <?php echo $borrow['RetDateTime']; ?> </td>
                        <td> <?php echo $borrow['Name']; ?> </td>
                        <td> <?php echo $borrow['LateFee']; ?> </td>
                        <td> 
                            <form method="post">
                                <input type="hidden" name="returnBNum" value="<?php echo $borrow['BNum'] ?>">
                                <input type="hidden" name="returnCNum" value="<?php echo $borrow['CopyNo'] ?>">
                                <input type="submit" name="return" value="return" <?php if($borrow['RetDateTime'] != NULL) { echo "disabled";} ?>>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h3>My Reservations</h3>
        <hr/>
        <table width=100%>
            <thead align="left">
                <th width=10%>Reserve Num</th>
                <th width=20%>Document Title</th>
                <th width=10%>Reserved Time</th>
                <th width=10%>Picked Up</th>
                <th width=10%>Branch</th>
                <th width=10%>Canceled</th>
                <th width=5%></th>
            </thead>
            <tbody>
                <?php foreach ($reserves as $reserve): ?>
                    <tr>
                        <td> <?php echo $reserve['RNum']; ?> </td>
                        <td> <?php echo $reserve['Title']; ?> </td>
                        <td> <?php echo $reserve['ResDateTime']; ?> </td>
                        <td> <?php if($reserve['PickedUp']) { echo "YES"; } else { echo "NO"; } ?> </td>
                        <td> <?php echo $reserve['Name']; ?> </td>
                        <td> <?php if($reserve['Canceled']) { echo "YES"; } else { echo "NO"; } ?> </td>
                        <td> 
                            <form method="post">
                                <input type="hidden" name="cancelRNum" value="<?php echo $reserve['RNum'] ?>">
                                <input type="hidden" name="cancelCNum" value="<?php echo $reserve['CopyNo'] ?>">
                                <input type="submit" name="cancel" value="cancel" <?php if($reserve['Canceled']) { echo "disabled";} ?>>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
    </body>
</html>