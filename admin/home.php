<?php session_start(); ?>
<html>
    <head>
        <?php
            require("../db.php");
            
            $db = new PDO( 'mysql:host=sql1.njit.edu;dbname=mem63', $dbuser, $dbpass );
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
          	if(!$db){
          		  die("Fatal Error: Connection Failed!");
          	}
           
            $stmt = $db->prepare("SELECT * FROM Lib_Branch");
            try {
                $r = $stmt->execute([":doc" => $doc]);
                if ($r) {
                    $branches = $stmt->fetchALL(PDO::FETCH_ASSOC);
                    if ($branches) {
                    
                    }
                } else {
                
                }
            } catch (Exception $e) {
                var_export($e, true);
            }
            
            if (isset($_POST["doc"])) {
                $doc = $_POST["doc"];
               
                $stmt = $db->prepare("SELECT DocID, Title, CopyNo, BranchID, b.Name, b.Location, Position, Available from Lib_Document AS d INNER JOIN Lib_Copy AS c ON d.DocID = c.CopyID INNER JOIN Lib_Branch AS b ON BranchID = BID where (d.DocID like CONCAT('%', :doc, '%') or Title like CONCAT('%', :doc, '%'))");
                try {
                    $r = $stmt->execute([":doc" => $doc]);
                    if ($r) {
                        $docs = $stmt->fetchALL(PDO::FETCH_ASSOC);
                        if ($docs) {
                        
                        }
                    } else {
                        echo $doc;
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
        <h2>Welcome, <?php echo $_SESSION['username']; ?></h2>
        <a href="../login.php" style="float: right;">Quit</a>
        <a href="create-doc.php">Add a document</a> <br/>
        <a href="create-reader.php">Add a reader</a><br/>
        <a href="admin-functions.php">Admin Functions</a>
        <form method="POST">
            <div class="mb-3">
                <input class="form-control" type="text" name="doc" placeholder="Search for a document"/>
            </div>
            <input type="submit" class="mt-3 btn btn-dark" value="Search" />
        </form>
        <h3>Documents</h3>
        <hr/>
        <table width=100%>
            <thead align="left">
                <th>DocID</th>
                <th>Title</th>
                <th>Copy Num</th>
                <th>Branch</th>
                <th>Location</th>
                <th>Position</th>
                <th>Available</th>
            </thead>
            <tbody>
                <?php foreach ($docs as $doc): ?> 
                    <tr>
                        <td> <?php echo $doc['DocID']; ?> </td>
                        <td> <?php echo $doc['Title']; ?> </td>
                        <td> <?php echo $doc['CopyNo']; ?> </td>
                        <td> <?php echo $doc['Name']; ?> </td>
                        <td> <?php echo $doc['Location']; ?> </td>
                        <td> <?php echo $doc['Position']; ?> </td>
                        <td> <?php echo $doc['Available']; ?> </td>
                        <td> 
                            <form method="post" action="doc-details.php">
                                <input type="hidden" name="copy" value="<?php echo $doc['CopyNo'] ?>">
                                <input type="hidden" name="title" value="<?php echo $doc['Title'] ?>">
                                <input type="submit" value="Details" <?php if ($doc['Available'] == 1) { echo "disabled";}?> >
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h3>Branches</h3>
        <hr/>
        <table width=100%>
            <thead align="left">
                <th>Branch ID</th>
                <th>Name</th>
                <th>Location</th>
            </thead>
            <tbody>
                <?php foreach ($branches as $branch): ?> 
                    <tr>
                        <td> <?php echo $branch['BID']; ?> </td>
                        <td> <?php echo $branch['Name']; ?> </td>
                        <td> <?php echo $branch['Location']; ?> </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>