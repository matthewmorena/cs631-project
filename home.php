<?php session_start(); ?>
<html>
    <head>
        <?php
            require("db.php");
            if (isset($_POST["doc"])) {
                $doc = $_POST["doc"];
               
                $db = new PDO( 'mysql:host=sql1.njit.edu;dbname=mem63', $dbuser, $dbpass );
                $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
              	if(!$db){
              		  die("Fatal Error: Connection Failed!");
              	}
               
                $stmt = $db->prepare("SELECT d.DocID, Title, PubName, PDate, ISBN, NumberOfCopies from Lib_Document AS d INNER JOIN Lib_Publisher AS p ON d.PubID = p.PublisherID INNER JOIN Lib_Book AS b ON d.DocID = b.DocID where (d.DocID like CONCAT('%', :doc, '%') or Title like CONCAT('%', :doc, '%') or PubName like CONCAT('%', :doc, '%'))");
                try {
                    $r = $stmt->execute([":doc" => $doc]);
                    if ($r) {
                        $books = $stmt->fetchALL(PDO::FETCH_ASSOC);
                        if ($books) {
                            
                        }
                    } else {
                        echo "error";
                    }
                } catch (Exception $e) {
                    var_export($e, true);
                }
                
                $stmt = $db->prepare("SELECT d.DocID, Title, PubName, PDate, VolumeNo, IssueNo, Scope, NumberOfCopies from Lib_Document AS d INNER JOIN Lib_Publisher AS p ON d.PubID = p.PublisherID INNER JOIN Lib_Journal_Volume AS j ON d.DocID = j.DocID INNER JOIN Lib_Journal_Issue AS i ON JournalID = j.DocID where (d.DocID like CONCAT('%', :doc, '%') or Title like CONCAT('%', :doc, '%') or PubName like CONCAT('%', :doc, '%'))");
                try {
                    $r = $stmt->execute([":doc" => $doc]);
                    if ($r) {
                        $journals = $stmt->fetchALL(PDO::FETCH_ASSOC);
                        if ($journals) {
                            
                        }
                    } else {
                        echo "error";
                    }
                } catch (Exception $e) {
                    var_export($e, true);
                }
                
                $stmt = $db->prepare("SELECT d.DocID, Title, PubName, PDate, CDate, CLocation, PName, NumberOfCopies from Lib_Document AS d INNER JOIN Lib_Publisher AS p ON d.PubID = p.PublisherID INNER JOIN Lib_Proceeding AS pr ON d.DocID = pr.DocID INNER JOIN Lib_Chairs as c on pr.DocID = ProID INNER JOIN Lib_Person ON ChairID = PID where (d.DocID like CONCAT('%', :doc, '%') or Title like CONCAT('%', :doc, '%') or PubName like CONCAT('%', :doc, '%'))");
                try {
                    $r = $stmt->execute([":doc" => $doc]);
                    if ($r) {
                        $proceedings = $stmt->fetchALL(PDO::FETCH_ASSOC);
                        if ($proceedings) {
                            
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
        <h2>Welcome, <?php echo $_SESSION['readername']; ?></h2>
        <a href="./login.php" style="float: right;">Quit</a>
        <form method="POST">
            <div class="mb-3">
                <input class="form-control" type="text" name="doc" placeholder="Search for a document"/>
            </div>
            <input type="submit" class="mt-3 btn btn-dark" value="Search" />
        </form>
        <form method="post" action="account.php">
            <input type="submit" class="mt-3 btn btn-dark" value="My Account" />
        </form>
        <h3>Books</h3>
        <hr/>
        <table width=100%>
            <thead align="left">
                <th>DocID</th>
                <th>Title</th>
                <th>Publisher</th>
                <th>Published</th>
                <th>ISBN</th>
                <th width=10%>Number of Copies</th>
                <th width=5%></th>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td> <?php echo $book['DocID']; ?> </td>
                        <td> <?php echo $book['Title']; ?> </td>
                        <td> <?php echo $book['PubName']; ?> </td>
                        <td> <?php echo $book['PDate']; ?> </td>
                        <td> <?php echo $book['ISBN']; ?> </td>
                        <td> <?php echo $book['NumberOfCopies']; ?> </td>
                        <td> 
                            <form method="post" action="checkout.php">
                                <input type="hidden" name="DocID" value="<?php echo $book['DocID'] ?>">
                                <input type="hidden" name="title" value="<?php echo $book['Title'] ?>">
                                <input type="submit" value="Checkout">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h3>Journals</h3>
        <hr/>
        <table width=100%>
            <thead align="left">
                <th>DocID</th>
                <th>Title</th>
                <th>Publisher</th>
                <th>Published</th>
                <th>Volume</th>
                <th>Issue</th>
                <th>Scope</th>
                <th width=10%>Number of Copies</th>
                <th width=5%></th>
            </thead>
            <tbody>
                <?php foreach ($journals as $journal): ?>
                    <tr>
                        <td> <?php echo $journal['DocID']; ?> </td>
                        <td> <?php echo $journal['Title']; ?> </td>
                        <td> <?php echo $journal['PubName']; ?> </td>
                        <td> <?php echo $journal['PDate']; ?> </td>
                        <td> <?php echo $journal['VolumeNo']; ?> </td>
                        <td> <?php echo $journal['IssueNo']; ?> </td>
                        <td> <?php echo $journal['Scope']; ?> </td>
                        <td> <?php echo $journal['NumberOfCopies']; ?> </td>
                        <td> 
                            <form method="post" action="checkout.php">
                                <input type="hidden" name="DocID" value="<?php echo $journal['DocID'] ?>">
                                <input type="hidden" name="title" value="<?php echo $journal['Title'] ?>">
                                <input type="submit" value="Checkout">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h3>Proceedings</h3>
        <hr/>
        <table width=100%>
            <thead align="left">
                <th>DocID</th>
                <th>Title</th>
                <th>Publisher</th>
                <th>Published</th>
                <th>Date</th>
                <th>Location</th>
                <th>Chair</th>
                <th width=10%>Number of Copies</th>
                <th width=5%></th>
            </thead>
            <tbody>
                <?php foreach ($proceedings as $proceeding): ?>
                    <tr>
                        <td> <?php echo $proceeding['DocID']; ?> </td>
                        <td> <?php echo $proceeding['Title']; ?> </td>
                        <td> <?php echo $proceeding['PubName']; ?> </td>
                        <td> <?php echo $proceeding['PDate']; ?> </td>
                        <td> <?php echo $proceeding['CDate']; ?> </td>
                        <td> <?php echo $proceeding['CLocation']; ?> </td>
                        <td> <?php echo $proceeding['PName']; ?> </td>
                        <td> <?php echo $proceeding['NumberOfCopies']; ?> </td>
                        <td> 
                            <form method="post" action="checkout.php">
                                <input type="hidden" name="DocID" value="<?php echo $proceeding['DocID'] ?>">
                                <input type="hidden" name="title" value="<?php echo $proceeding['Title'] ?>">
                                <input type="submit" value="Checkout">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>