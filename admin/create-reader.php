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
           
            $stmt = $db->prepare("SELECT * FROM Lib_Reader;");
            $r = $stmt->execute();
            if ($r) {
                $readers = $stmt->fetchALL(PDO::FETCH_ASSOC);
                
            } 
            
            if (isset($_POST["create"])) {
                $card = $_POST["card"];
                $type = $_POST["type"];
                $name = $_POST["name"];
                $phone = $_POST["phone"];
                $address = $_POST["address"];
               
                
               
                $stmt = $db->prepare("INSERT INTO Lib_Reader (CardNumber, Type, ReaderName, PhoneNo, Address) VALUES (:cn, :type, :rn, :phone, :addr);");
                try {
                    $r = $stmt->execute([":cn" => $card, ":type" => $type, ":rn" => $name, ":phone" => $phone, ":addr" => $address]);
                    if ($r) {
                        
                    }
                } catch (Exception $e) {
                    
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
        <h2>Add Reader</h2>
        <a href="home.php">Home</a>
        <a href="../login.php" style="float: right;">Quit</a>
        <form method="POST">
            <div class="mb-3">
                <input class="form-control" type="text" name="card" placeholder="Card Number"/> <br/>
                <input class="form-control" type="text" name="type" placeholder="Type of Reader"/> <br/>
                <input class="form-control" type="text" name="name" placeholder="Reader Name"/> <br/>
                <input class="form-control" type="text" name="phone" placeholder="Phone Number"/> <br/>
                <input class="form-control" type="text" name="address" placeholder="Address"/> <br/>
            </div>
            <input type="submit" class="mt-3 btn btn-dark" name="create" value="Create" />
        </form>
        
        <h3>Readers</h3>
        <hr/>
        <table width=100%>
            <thead align="left">
                <th>Reader ID</th>
                <th>Card Number</th>
                <th>Type</th>
                <th>Reader Name</th>
                <th>Phone</th>
                <th>Address</th>
            </thead>
            <tbody>
                <?php foreach ($readers as $reader): ?> 
                    <tr>
                        <td> <?php echo $reader['ReaderID']; ?> </td>
                        <td> <?php echo $reader['CardNumber']; ?> </td>
                        <td> <?php echo $reader['Type']; ?> </td>
                        <td> <?php echo $reader['ReaderName']; ?> </td>
                        <td> <?php echo $reader['PhoneNo']; ?> </td>
                        <td> <?php echo $reader['Address']; ?> </td>
                        
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
</html>