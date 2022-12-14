<?php session_start(); ?>
<html>
    <head>
        <?php
            require("db.php");
            if (isset($_POST["DocID"])) {
                $doc = $_POST["DocID"];
                $title = $_POST["title"];
                
                $db = new PDO( 'mysql:host=sql1.njit.edu;dbname=mem63', $dbuser, $dbpass );
                $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
              	if(!$db){
              		  die("Fatal Error: Connection Failed!");
              	}
               
                $stmt = $db->prepare("SELECT DocID, CopyNo, BranchID, b.Name, b.Location, Position from Lib_Document AS d INNER JOIN Lib_Copy AS c ON d.DocID = c.CopyID INNER JOIN Lib_Branch AS b ON BranchID = BID where d.DocID = :doc and available = 1");
                try {
                    $r = $stmt->execute([":doc" => $doc]);
                    if ($r) {
                        $copies = $stmt->fetchALL(PDO::FETCH_ASSOC);
                        if ($copies) {
                            
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
        <h3>Copies of <?php echo $title ?></h3>
        <hr/>
        <table width=100%>
            <thead align="left">
                <th>Copy Number</th>
                <th>Branch</th>
                <th>Location</th>
                <th>Position</th>
                <th width=5%></th>
                <th width=5%></th>
            </thead>
            <tbody>
                <?php foreach ($copies as $copy): ?>
                    <tr>
                        <td> <?php echo $copy['CopyNo']; ?> </td>
                        <td> <?php echo $copy['Name']; ?> </td>
                        <td> <?php echo $copy['Location']; ?> </td>
                        <td> <?php echo $copy['Position']; ?> </td>
                        <td> 
                            <form method="post" action="borrow.php">
                                <input type="hidden" name="doc" value="<?php echo $copy['DocID'] ?>">
                                <input type="hidden" name="branch" value="<?php echo $copy['BranchID'] ?>">
                                <input type="hidden" name="copyno" value="<?php echo $copy['CopyNo'] ?>">
                                <input type="submit" value="Borrow">
                            </form>
                        </td>
                        <td> 
                            <form method="post" action="reserve.php">
                                <input type="hidden" name="doc" value="<?php echo $copy['DocID'] ?>">
                                <input type="hidden" name="branch" value="<?php echo $copy['BranchID'] ?>">
                                <input type="hidden" name="copyno" value="<?php echo $copy['CopyNo'] ?>">
                                <input type="submit" value="Reserve">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>