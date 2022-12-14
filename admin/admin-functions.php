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
           
            if (isset($_POST['borrowers'])) {
            
                if ($_POST['branch'] != "") {
                //var_dump($_POST);
                    $stmt = $db->prepare("SELECT ReaderID, ReaderName, COUNT(*) AS NumBorrows, Name FROM Lib_Reader AS r INNER JOIN Lib_Borrows ON ReaderID = RID INNER JOIN Lib_Branch ON Branch = BID WHERE Branch = :bid GROUP BY ReaderID ORDER BY NumBorrows LIMIT :lim;");
                    try {
                        $stmt->bindValue(':bid', $_POST['branch'], PDO::PARAM_STR);
                        $stmt->bindValue(':lim', intval($_POST['number']), PDO::PARAM_INT);
                        $r = $stmt->execute();
                        if ($r) {
                            $borrowers = $stmt->fetchALL(PDO::FETCH_ASSOC);
                        }
                    } catch (Exception $e) {
                        
                    }
                } else {
                
                    $stmt = $db->prepare("SELECT ReaderID, ReaderName, COUNT(*) AS NumBorrows FROM Lib_Reader AS r INNER JOIN Lib_Borrows ON ReaderID = RID GROUP BY ReaderID ORDER BY NumBorrows LIMIT :lim");
                    try {
                        $stmt->bindValue(':lim', intval($_POST['number']), PDO::PARAM_INT);
                        $r = $stmt->execute();
                        
                        if ($r) {
                        
                            $borrowers = $stmt->fetchALL(PDO::FETCH_ASSOC);
                        }
                    } catch (Exception $e) {
                        //echo $e;
                    }
                }
            }
            
            if (isset($_POST['borrowed'])) {
            
                if ($_POST['branch'] != "") {
                //var_dump($_POST);
                    $stmt = $db->prepare("SELECT DocID, Title, COUNT(*) AS NumBorrows, Name FROM Lib_Document AS d INNER JOIN Lib_Borrows ON DocID = Doc INNER JOIN Lib_Branch ON Branch = BID WHERE Branch = :bid GROUP BY DocID ORDER BY NumBorrows LIMIT :lim;");
                    try {
                        $stmt->bindValue(':bid', $_POST['branch'], PDO::PARAM_STR);
                        $stmt->bindValue(':lim', intval($_POST['number']), PDO::PARAM_INT);
                        $r = $stmt->execute();
                        if ($r) {
                            $borroweds = $stmt->fetchALL(PDO::FETCH_ASSOC);
                        }
                    } catch (Exception $e) {
                        
                    }
                } else {
                    $stmt = $db->prepare("SELECT DocID, Title, COUNT(*) AS NumBorrows FROM Lib_Document AS d INNER JOIN Lib_Borrows ON DocID = Doc GROUP BY DocID ORDER BY NumBorrows LIMIT :lim;");
                    try {
                        $stmt->bindValue(':lim', intval($_POST['number']), PDO::PARAM_INT);
                        $r = $stmt->execute();
                        //$statement->debugDumpParams();
                        if ($r) {
                        
                            $borroweds = $stmt->fetchALL(PDO::FETCH_ASSOC);
                        }
                    } catch (Exception $e) {
                        //echo $e;
                    }
                }
            }
            
            if (isset($_POST['popular'])) {
                $stmt = $db->prepare("SELECT DocID, Title, COUNT(*) AS NumBorrows FROM Lib_Document AS d INNER JOIN Lib_Borrows ON DocID = Doc WHERE PDate LIKE CONCAT('%', :date, '%') GROUP BY DocID ORDER BY NumBorrows LIMIT 10;");
                try {
                    $r = $stmt->execute([":date" => $_POST['year']]);
                    //$statement->debugDumpParams();
                    if ($r) {
                    
                        $books = $stmt->fetchALL(PDO::FETCH_ASSOC);
                    }
                } catch (Exception $e) {
                    //echo $e;
                }
            }
            
            if (isset($_POST['fees'])) {
                $stmt = $db->prepare("SELECT BID, Name, AVG(LateFee) AS AvgFee FROM Lib_Branch INNER JOIN Lib_Borrows ON BID = Branch INNER JOIN Lib_BorTransaction ON BNum = BorNumber WHERE RetDateTime BETWEEN :sdate AND :edate AND LateFee > 0 GROUP BY BID;");
                try {
                    $r = $stmt->execute([":sdate" => $_POST['start'], ":edate" => $_POST['end']]);
                    //$statement->debugDumpParams();
                    if ($r) {
                    
                        $latefees = $stmt->fetchALL(PDO::FETCH_ASSOC);
                    }
                } catch (Exception $e) {
                    //echo $e;
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
        <a href="home.php">Home</a>
        <a href="../login.php" style="float: right;">Quit</a>
        
        <h2>Most Frequent Borrowers</h2>
        
        <form method="POST">
            <div class="mb-3">
                <input class="form-control" type="text" name="number" placeholder="Number"/> <br/>
                <input class="form-control" type="text" name="branch" placeholder="Branch"/> <br/>
            </div>
            <input type="submit" class="mt-3 btn btn-dark" name="borrowers" value="Submit" />
        </form>
        <hr/>
        
        <table width=100%>
            <thead align="left">
                <th>Reader ID</th>
                <th>Name</th>
                <th>Number of Borrows</th>
                <th>Branch</th>
            </thead>
            <tbody>
                <?php foreach ($borrowers as $borrower): ?> 
                    <tr>
                        <td> <?php echo $borrower['ReaderID']; ?> </td>
                        <td> <?php echo $borrower['ReaderName']; ?> </td>
                        <td> <?php echo $borrower['NumBorrows']; ?> </td>
                        <td> <?php echo $borrower['Name']; ?> </td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>Most Frequently Borrowed</h2>
        
        <form method="POST">
            <div class="mb-3">
                <input class="form-control" type="text" name="number" placeholder="Number"/> <br/>
                <input class="form-control" type="text" name="branch" placeholder="Branch"/> <br/>
            </div>
            <input type="submit" class="mt-3 btn btn-dark" name="borrowed" value="Submit" />
        </form>
        <hr/>
        
        <table width=100%>
            <thead align="left">
                <th>Doc ID</th>
                <th>Title</th>
                <th>Number of Borrows</th>
                <th>Branch</th>
            </thead>
            <tbody>
                <?php foreach ($borroweds as $borrowed): ?> 
                    <tr>
                        <td> <?php echo $borrowed['DocID']; ?> </td>
                        <td> <?php echo $borrowed['Title']; ?> </td>
                        <td> <?php echo $borrowed['NumBorrows']; ?> </td>
                        <td> <?php echo $borrowed['Name']; ?> </td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>Most Popular From Year</h2>
        
        <form method="POST">
            <div class="mb-3">
                <input class="form-control" type="text" name="year" placeholder="Number"/> <br/>
            </div>
            <input type="submit" class="mt-3 btn btn-dark" name="popular" value="Submit" />
        </form>
        <hr/>
        
        <table width=100%>
            <thead align="left">
                <th>Doc ID</th>
                <th>Title</th>
                <th>Number of Borrows</th>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?> 
                    <tr>
                        <td> <?php echo $book['DocID']; ?> </td>
                        <td> <?php echo $book['Title']; ?> </td>
                        <td> <?php echo $book['NumBorrows']; ?> </td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <h2>Average Late Fees</h2>
        
        <form method="POST">
            <div class="mb-3">
                <input class="form-control" type="date" name="start" placeholder="Number"/> <br/>
                <input class="form-control" type="date" name="end" placeholder="Number"/> <br/>
            </div>
            <input type="submit" class="mt-3 btn btn-dark" name="fees" value="Submit" />
        </form>
        <hr/>
        
        <table width=100%>
            <thead align="left">
                <th>Branch ID</th>
                <th>Branch Name</th>
                <th>Average Late Fee</th>
            </thead>
            <tbody>
                <?php foreach ($latefees as $fee): ?> 
                    <tr>
                        <td> <?php echo $fee['BID']; ?> </td>
                        <td> <?php echo $fee['Name']; ?> </td>
                        <td> <?php echo $fee['AvgFee']; ?> </td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>