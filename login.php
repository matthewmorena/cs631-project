<?php session_start(); ?>
<html>
    <head>
        <?php
            require("db.php");
        ?>
        <style>
            body {
                font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
                font-size: large;
                padding: 2%;
            }
        </style>
        
        <script>
            function setReader() {
                var rb = document.getElementById("role-button");
                var rd = document.getElementById("reader-login");
                rb.hidden = true;
                rd.hidden = false;
            }
            function setAdmin() {
                var rb = document.getElementById("role-button");
                var ad = document.getElementById("admin-login");
                rb.hidden = true;
                ad.hidden = false;
            }
            function reset() {
                var rb = document.getElementById("role-button");
                var ad = document.getElementById("admin-login");
                var rd = document.getElementById("reader-login");
                rb.hidden = false;
                ad.hidden = true;
                rd.hidden = true;
            }
        </script>
    </head>
    <body>
        <h1>CS631 Library</h1>
        <div id="role-button">
            <h2>I am a...</h2>
            <button onClick="setReader()">Reader</button>
            <button onClick="setAdmin()">Admin</button>
        </div>

        <div id="admin-login" hidden>
            <h2>Admin Login</h2>
            <form method="POST">
                <div class="mb-3">
                    <input class="form-control" type="text" id="user" name="user" required placeholder="username"/>
                </div>
                <div class="mb-3">
                    <input class="form-control" type="password" id="pw" name="password" required minlength="8" placeholder="password"/>
                </div>
                <input type="submit" class="mt-3 btn btn-dark" value="Login" />
            </form>
            <br/>
            <button onClick="reset()">Back</button>
        </div>

        <div id="reader-login" hidden>
            <h2>Reader Login</h2>
            <form method="POST">
                <div class="mb-3">
                    <input class="form-control" type="text" id="card" name="card" required placeholder="card number"/>
                </div>
                <input type="submit" class="mt-3 btn btn-dark" value="Login" />
            </form>
            <br/>
            <button onClick="reset()">Back</button>
        </div>
    </body>
    <?php //echo password_hash(password, PASSWORD_BCRYPT); ?>
</html>

<?php 

    if (isset($_POST["user"]) && isset($_POST["password"])) {
        $username = $_POST["user"];
        $password = $_POST["password"];
       
        $db = new PDO( 'mysql:host=sql1.njit.edu;dbname=mem63', $dbuser, $dbpass );
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      	if(!$db){
      		  die("Fatal Error: Connection Failed!");
      	}
       
        $data = [];
        $stmt = $db->prepare("SELECT * from Lib_Users where username = :username");
        try {
            $r = $stmt->execute([":username" => $username]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        unset($_SESSION['readerid']);
                        unset($_SESSION['readername']);
                        $_SESSION['userid'] = $user["id"];
                        $_SESSION['username'] = $user["username"];
                        $_SESSION['role'] = $user["role"];
                        //var_dump($_SESSION);
                        header("Location: ./admin/home.php");
                    } else {
                        echo "Invalid Credentials";
                        unset($_SESSION);
                    }
                } else {
                    echo "Invalid Credentials";
                    unset($_SESSION);
                }
            }
        } catch (Exception $e) {
            var_export($e, true);
        }
    }
    if (isset($_POST["card"])) {
        $card = $_POST["card"];
        
        $db = new PDO( 'mysql:host=sql1.njit.edu;dbname=mem63', $dbuser, $dbpass );
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      	if(!$db){
      		  die("Fatal Error: Connection Failed!");
      	}
       
        $data = [];
        $stmt = $db->prepare("SELECT * from Lib_Reader where CardNumber = :card");
        try {
            $r = $stmt->execute([":card" => $card]);
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                      unset($_SESSION['userid']);
                      unset($_SESSION['username']);
                      $_SESSION['readerid'] = $user["ReaderID"];
                      $_SESSION['readername'] = $user["ReaderName"];
                      $_SESSION['role'] = "reader";
                      //var_dump($_SESSION);
                      die(header("Location: ./home.php"));
                } else {
                    echo "Invalid Credentials";
                    unset($_SESSION);
                }
            }
        } catch (Exception $e) {
            var_export($e, true);
        }
    }
?>