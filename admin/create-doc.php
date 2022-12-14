<?php session_start(); ?>
<html>
    <head>
        <?php
            require("../db.php");
            if (isset($_POST["create"])) {
                $Title = $_POST["Title"];
                $PDate = $_POST["PDate"];
                $PubID = $_POST["PubID"];
                $NumCopies = $_POST["NumCopies"];
                $DocType = $_POST["doctype"];
                $branch = $_POST["branch"];
                $position = $_POST["position"];
                $available = $_POST["available"];
               
                $db = new PDO( 'mysql:host=sql1.njit.edu;dbname=mem63', $dbuser, $dbpass );
                $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
              	if(!$db){
              		  die("Fatal Error: Connection Failed!");
              	}
               
                $stmt = $db->prepare("INSERT INTO Lib_Document (Title, PDate, PubID, NumberOfCopies) VALUES (:title, :pdate, :pid, :num);");
                try {
                    $r = $stmt->execute([":title" => $Title, ":pdate" => $PDate, ":pid" => $PubID, ":num" => $NumCopies]);
                    if ($r) {
                        $stmt = $db->prepare("SELECT max(DocID) AS DocID from Lib_Document");
                        $r = $stmt->execute();
                        if ($r) {
                            $DocID = $stmt->fetch(PDO::FETCH_ASSOC);
                            $d = intval($DocID["DocID"]);
                        }
                        if ($DocType == "Book") {
                            $stmt = $db->prepare("INSERT INTO Lib_Book (DocID, ISBN) VALUES (:doc, :isbn);");
                            $r = $stmt->execute([":doc" => $d, ":isbn" => $_POST["isbn"]]);
                        } else if ($DocType == "Journal") {
                            $stmt = $db->prepare("INSERT INTO Lib_Journal_Volume (DocID, VolumeNo, EditorID) VALUES (:doc, :vnum, :eid);");
                            $r = $stmt->execute([":doc" => $d, ":vnum" => $_POST["volume"], ":eid" => $_POST["editor"]]);
                            $stmt = $db->prepare("INSERT INTO Lib_Journal_Issue (JournalID, IssueNo, Scope) VALUES (:doc, :inum, :scope);");
                            $r = $stmt->execute([":doc" => $d, ":inum" => $_POST["issue"], ":scope" => $_POST["scope"]]);
                        } else {
                            $stmt = $db->prepare("INSERT INTO Lib_Proceeding (DocID, CDate, CLocation) VALUES (:doc, :date, :loc);");
                            $r = $stmt->execute([":doc" => $d, ":date" => $_POST["date"], ":loc" => $_POST["location"]]);
                            $stmt = $db->prepare("INSERT INTO Lib_Chairs (ProID, ChairID) VALUES (:doc, :chair);");
                            $r = $stmt->execute([":doc" => $d, ":chair" => $_POST["chair"]]);
                        }
                        
                        $stmt = $db->prepare("INSERT INTO Lib_Copy (CopyID, BranchID, Position, Available) VALUES (:cid, :bid, :pos, :av);");
                        for ($i = 0; $i < $NumCopies; $i++) {
                            $r = $stmt->execute([":cid" => $d, ":bid" => $branch, ":pos" => $position, ":av" => $available]);
                        }
                    }
                } catch (Exception $e) {
                    
                }
            }
        ?>
        <script>
            function showBook() {
                var book = document.getElementById("books");
                var jour = document.getElementById("journals");
                var proc = document.getElementById("proceedings");

                book.hidden = false;
                jour.hidden = true;
                proc.hidden = true;
            }
            function showJournal() {
                var book = document.getElementById("books");
                var jour = document.getElementById("journals");
                var proc = document.getElementById("proceedings");

                book.hidden = true;
                jour.hidden = false;
                proc.hidden = true;
            }
            function showProceeding() {
                var book = document.getElementById("books");
                var jour = document.getElementById("journals");
                var proc = document.getElementById("proceedings");

                book.hidden = true;
                jour.hidden = true;
                proc.hidden = false;
            }
        </script>
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
        <h2>Add Document</h2>
        <a href="home.php">Home</a>
        <a href="../login.php" style="float: right;">Quit</a>
        <form method="POST">
            <div class="mb-3">
                <input class="form-control" type="text" name="Title" placeholder="Document Title"/> <br/>
                <input class="form-control" type="text" name="PDate" placeholder="Date Published"/> <br/>
                <input class="form-control" type="text" name="PubID" placeholder="Publisher ID"/> <br/>
                <input class="form-control" type="text" name="NumCopies" placeholder="Number of Copies"/> <br/>
                
                <div id="radio">
                    <br/>
                    <input type="radio" id="book" name="doctype" value="Book" onChange="showBook()">
                    <label for="book">Book</label>
                    <br/>
                    <input type="radio" id="journal" name="doctype" value="Journal" onChange="showJournal()">
                    <label for="journal">Journal</label>
                    <br/>
                    <input type="radio" id="proceeding" name="doctype" value="Proceeding" onChange="showProceeding()">
                    <label for="proceeding">Proceeding</label>
                    <br/>
                </div>
                
                <div id="books" hidden>
                    <input class="form-control" type="text" name="isbn" placeholder="ISBN"/> <br/>
                </div>
                
                <div id="journals" hidden>
                    <input class="form-control" type="text" name="volume" placeholder="Volume"/> <br/>
                    <input class="form-control" type="text" name="editor" placeholder="Editor ID"/> <br/>
                    <input class="form-control" type="text" name="issue" placeholder="Issue"/> <br/>
                    <input class="form-control" type="text" name="scope" placeholder="Scope"/> <br/>
                </div>
                
                <div id="proceedings" hidden>
                    <input class="form-control" type="text" name="date" placeholder="Date"/> <br/>
                    <input class="form-control" type="text" name="location" placeholder="Location"/> <br/>
                    <input class="form-control" type="text" name="chair" placeholder="Chair ID"/> <br/>
                </div>
                
                <br/>
                
                <input class="form-control" type="text" name="branch" placeholder="Branch ID"/> <br/>
                <input class="form-control" type="text" name="position" placeholder="Position"/> <br/>
                <input class="form-control" type="text" name="available" placeholder="Available (1 or 0)"/> <br/>
                
            </div>
            <input type="submit" class="mt-3 btn btn-dark" name="create" value="Create" />
        </form>
    </body>
</html>