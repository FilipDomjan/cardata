<?php
    session_start();
    
    // Check if the user is not logged in, then redirect the user to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: index.php");
        header("location: users/logout.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <link rel="stylesheet" href="addmod.css">
    <title>CarData | Add mods or repairs</title>
</head>
<body>
    <div class="navbar">    
        <img src="img/cardata_logo_white.png" alt="logo">
        <div class="links">
            <?php
                error_reporting(0);

                // Navigation bar items change wether the user is logged in or not
                // If the user is logged in it will show users username and logout button
                if($_SESSION["loggedin"] === true){
                    $username = htmlspecialchars($_SESSION["username"]);
                    echo "<a href='index.php'>Home</a>";
                    echo "<a href='dashboard.php' class='active'>My CarData</a>";
                    echo "<a href='#'>Contact</a>";
                    echo "<a class='login'><i class='fa-solid fa-user'></i> $username</a>";
                    echo "<a href='users/logout.php' class='logout'><i class='fa-solid fa-right-from-bracket'></i><span>Logout</span></a>";
                }
                // If not show original items
                else{
                    echo "<a class='active'>Home</a>";
                    echo "<a href='#'>Contact</a>";
                    echo "<a href='users/login.php'>Login</a>";
                }
            ?>
        </div>
    </div>
    <!-- Form below handles adding mods, repairs, tunes and other into the database -->
    <div id="main">
        <div class="page-name">
            <h3 class="page-name-text">Add new work</h3>
            <p class="page-name-text"><a href="index.php"><i class="fa-solid fa-house"></i></a> <span>-</span> <a href="#">My CarData</a> <span>-</span> <a href="#">Car Mods</a> <span>-</span> <a href="#">Add new work</a></p>
        </div>
        <nav class="sidebar" id="mySidebar" onmouseover="toggleSidebar()" onmouseout="toggleSidebar()">
            <div class="sidebar-items">
                <a href="dashboard.php"><i class="fa-solid fa-gauge-simple"></i><span>Dashboard</span></a>
                <a href="mycars.php"><i class="fa-solid fa-car"></i><span>My Cars</span></a>
                <a href="carmods.php?carid=0"><i class="fa-solid fa-screwdriver-wrench"></i><span>Car Mods</span></a>
                <a href="addcar.php"><i class="fa-solid fa-circle-plus"></i><span>Add a car</span></a>
            </div>
        </nav>
        <div class="form-wrapper">
            <form action="" method="POST">
                <div class="row">
                    <div class="row-selectors">
                        <!-- Choose a car selector will get all users cars from the database and show them -->
                        <!-- In a selector for easier selection -->
                        <label for="">Choose a car</label>
                        <br>
                        <select name="carselector" id="top-selector">
                            <option value="" disabled selected hidden>Select a car</option>
                            <?php
                                // Get the username so that we can access users database
                                // Every user has their own database which is named after their username
                                $username = htmlspecialchars($_SESSION["username"]);

                                // Connect to MySQL database
                                $server = 'localhost';
                                $user = 'root';
                                $password = '';
                                $database = "$username";
                                
                                $userdb = mysqli_connect($server, $user, $password, $database);
                                
                                if($userdb -> connect_errno){
                                    echo "Failed to connect to MySQL: ".$userdb -> connect_error;
                                    exit();
                                }
                                
                                // Select all the cars from users database
                                $query = "SELECT id, manufacturer, model, model_year FROM cars";
                                $result = mysqli_query($userdb, $query);
    
                                while($row = mysqli_fetch_assoc($result)){
                                    $manuf = $row["manufacturer"];
                                    $model = $row["model"];
                                    $year = $row["model_year"];
                                    $id = $row["id"];
                                    
                                    // Add each car into an select option, every option has an value of the cars id
                                    // This is mandatory so that we can later see in the table which car was being modded
                                    // And so that we can sort the table much more easily
                                    echo "<option value='$id'>$year $manuf $model</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="row-selectors">
                        <!-- Selector for type of work being done -->
                        <label for="">Repair type</label>
                        <br>
                        <select name="repairselect" id="top-selector">
                            <option value="" disabled selected hidden>Select a repair type</option>
                            <option value="Repair">Repair</option>
                            <option value="Mod">Mod</option>
                            <option value="Tune">Tune</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="row-price">
                        <!-- Selector for units, we want to know wether the price is in euros, usd or other -->
                        <label for="">Unit</label>
                        <br>
                        <select name="unitselector" id="units">
                            <option value="" disabled selected hidden>Select a unit</option>
                            <option value="HRK">HRK</option>
                            <option value="EUR">EUR</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                    <div class="row-price">
                        <!-- Input the price, only a number. Should add string checking soon -->
                        <label for="">Price (only number)</label>
                        <br>
                        <input type="text" name="price" id="price" placeholder="ex. 2000">
                    </div>
                </div>
                <div class="row">
                    <!-- Input field for milage, also only a number and add string checking -->
                    <label for="">Milage (only number)</label>
                    <br>
                    <input type="text" name="milage" id="milage" placeholder="ex. 255000">
                </div>
                <div class="row">
                    <!-- Description area for describing what was done to the car -->
                    <label for="">Description</label>
                    <br>
                    <textarea name="description" id="desc" cols="30" rows="10" placeholder="ex. Changed a timing belt"></textarea>
                </div>
                <div class="row last-row">
                    <!-- Submit button -->
                    <input type="submit" name="submit" id="submit" value="Submit">
                    <div class="clear"></div>

                    <?php
                        // If submit is pressed the database data entry is initiated
                        if(isset($_POST["submit"])){
                            $error = 0;
                            
                            // Disable error reporting, we don't want users to see temporary errors thrown everywhere
                            error_reporting(0);

                            // Get the data from the input fields
                            $carid = $_POST["carselector"];
                            $repairtype = $_POST["repairselect"];
                            $unit = $_POST["unitselector"];
                            $price = $_POST["price"];
                            $milage = $_POST["milage"];
                            $description = addslashes($_POST["description"]);

                            // Check for empty fields (There shouldn't be any)
                            if($carid == ""){
                                $error += 1;
                            }
                            if($repairtype == ""){
                                $error += 1;
                            }

                            // If there are no errors insert data into the database table
                            if($error == 0){
                                $query = "INSERT INTO mods (id, carid, repair_type, description, milage, price, unit) VALUES (NULL, $carid, '$repairtype', '$description', '$milage', $price, '$unit')";
                                $result = mysqli_query($userdb, $query);
                                
                                // Notify user of the upload status
                                if($result){
                                    echo "<p class='mod-upload-status'>Successfully added!</p>";
                                }
                                else{
                                    echo "<p class='mod-upload-status'>Something went wrong</p>";
                                }
                            }
                            else{
                                echo "<p class='mod-upload-status'>Some information was wrong...</p>";
                            }
                        }
                    ?>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        <div class="footer-content">
            <div class="footer-column">
                <h3>Links</h3>
                <a href="">Login</a>
                <br>
                <a href="">About</a>
                <br>
                <a href="">Contact</a>
            </div>
            <div class="footer-column">
                <h3>Legal Documents</h3>
                <a href="">Terms of service</a>
                <br>
                <a href="">Privacy policy</a>
                <br>
                <a href="">Cookies policy</a>
            </div>
            <div class="back-to-top">
                <a href="#"><i class="fa-solid fa-arrow-up"></i>Back to top</a>
            </div>
            <hr>
            <p>&copy; CARDATA.COM · 2021 - 2022. All rights reserved.</p>

        </div>

    <script>
        if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
        }
    </script>

    <script>
        // Sidebar toggler
        var mini = true;
        function toggleSidebar() {
            if (mini) {
            document.getElementById("mySidebar").style.width = "200px";
            document.getElementById("mySidebar").style.left = "-210px";
            // document.getElementById("main").style.marginLeft = "210px";
            this.mini = false;
        } else {
            document.getElementById("mySidebar").style.width = "70px";
            document.getElementById("mySidebar").style.left = "-80px";
            // document.getElementById("main").style.marginLeft = "80px";
            this.mini = true;
        }
        }
    </script>
</body>
</html>