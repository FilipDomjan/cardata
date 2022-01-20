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
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <link rel="stylesheet" href="add_service.css">
    <title>CarData | Add services to the Service Book</title>
</head>
<body>
    <div class="navbar not-fixed">    
        <div class="links">
            <img src="img/cardata_logo_white.png" alt="logo">
            <?php
                error_reporting(0);

                // Navigation bar items change wether the user is logged in or not
                // If the user is logged in it will show users username and logout button
                if($_SESSION["loggedin"] === true){
                    $username = htmlspecialchars($_SESSION["username"]);
                    echo "<a href='index.php'>Home</a>";
                    echo "<a href='contact.php'>Contact</a>";
                    echo "<a href='about.php' class='about'>About</a>";
                    echo "<a href='#' class='active'>My Cardata</a>";
                    echo "<div class='dropdown login-dropdown'>";
                    echo "<button class='drop-btn login-btn'><i class='fa-solid fa-user'></i><i class='fa-solid fa-caret-down'></i></button>";
                    echo "<div class='dropdown-content login-dropdown-content'>";
                    echo "<div class='connection-status'>";
                    echo "<p class='cs-user'>$username</p>";
                    echo "<p class='cs-connected'><i class='fa-solid fa-circle'></i>Connected</p>";
                    echo "</div>";
                    echo "<hr>";
                    echo "<a href='users/logout.php' class='logout'>Logout <i class='fa-solid fa-right-from-bracket'></i></a>"; 
                    echo "</div>";
                    echo "</div>";
                }
                // If not show original items
                else{
                    echo "<a href='#' class='active'>Home</a>";
                    echo "<a href='contact.php'>Contact</a>";
                    echo "<a href='about.php' class='about'>About</a>";
                    echo "<a href='users/register.php' class='login-links reg-link'>Register</a>";
                    echo "<a href='users/login.php' class='login-links log-link'>Sign In</a>";
                }
            ?>
        </div>
    </div>
    
    <!-- Mini navbar, only present in My Cardata sections -->
    <div id="mini-navbar">
        <div class="mini-links">
            <div class="dropdown-mini">
                <button class="drop-btn" onclick="location.href='dashboard.php'">Dashboard</button>
            </div>
            <div class="dropdown-mini">
                <button class="drop-btn" onclick="location.href='my_cars.php'">My Cars</button>
                <div class="dropdown-mini-content">
                    <a href="add_car.php">Add a car</a>
                </div>
            </div>
            <div class="dropdown-mini">
                <button class="drop-btn" onclick="location.href='service_book.php?carid=0'">Service Book</button>
                <div class="dropdown-mini-content">
                    <a href="#">Add to Service Book</a>
                </div>
            </div>
            <div class="dropdown-mini">
                <button class="drop-btn" onclick="location.href='gas_data.php'">Gas Data</button>
                <div class="dropdown-mini-content">
                    <a href="add_gas.php">Add to Gas Data</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form below handles adding mods, repairs, tunes and other into the database -->
    <div id="main">
        <div class="clear"></div>
        <div class="form-wrapper">
            <form action="" method="POST" autocomplete="off">
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
                        <select name="unitselector" id="units" onchange=getUnit()>
                            <option value="" disabled selected hidden>Select your unit</option>
                            <option value="HRK">HRK</option>
                            <option value="EUR">EUR</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                    <div class="row-price">
                        <!-- Input the price, only a number. Should add string checking soon -->
                        <label for="">Price (only number)</label>
                        <label id="price-of-work">-</label>
                        <input type="text" name="price" id="price" placeholder="ex. 2000" oninput=checkForString() maxlength="7">
                    </div>
                </div>
                <div class="row">
                    <div class="row-date">
                        <label for="">Day</label>
                        <select name="day" id="days">
                            <option value="" disabled selected hidden>Select day of service</option>
                            <?php
                                for($i = 1; $i <= 31; $i++){
                                    echo "<option value='$i'>$i</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="row-date">
                        <label for="">Month</label>
                        <select name="month" id="months">
                            <option value="" disabled selected hidden>Select month of service</option>
                            <?php
                                for($i = 1; $i <= 12; $i++){
                                    echo "<option value='$i'>$i</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="row-date">
                        <label for="">Year</label>
                        <select name="year" id="years">
                            <option value="" disabled selected hidden>Select year of service</option>
                            <?php
                                for($i = date("Y"); $i >= 1900; $i--){
                                    echo "<option value='$i'>$i</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <p>*If the date is not selected, time and date of submission is used</p>
                </div>
                <div class="row">
                    <!-- Input field for milage, also only a number and add string checking -->
                    <label for="">Milage (only number)</label>
                    <label id="milage-in-km">KM</label>
                    <input type="text" name="milage" id="milage" placeholder="ex. 255000" oninput=checkForString() maxlength="7">
                </div>
                <div class="row">
                    <!-- Description area for describing what was done to the car -->
                    <label for="">Description</label>
                    <label id="chars-left">(800)</label>
                    <textarea name="description" id="desc" cols="30" rows="10" oninput=checkChars() placeholder="ex. Changed a timing belt" maxlength="800"></textarea>
                </div>
                <div class="row last-row">
                    <!-- Submit button -->
                    <input type="submit" name="submit" id="submit" value="Submit">
                    <p id="writeError"></p>
                    <div class="clear"></div>
                    <div class="errors">
                        <?php
                            // If submit is pressed the database data entry is initiated
                            if(isset($_POST["submit"])){
                                $error = 0;
                                $error_list = [];
                                
                                // Disable error reporting, we don't want users to see temporary errors thrown everywhere
                                error_reporting(0);

                                // Get the data from the input fields
                                $carid = $_POST["carselector"];
                                $repairtype = $_POST["repairselect"];
                                $unit = $_POST["unitselector"];
                                $price = $_POST["price"];
                                $day = $_POST["day"];
                                $month = $_POST["month"];
                                $year = $_POST["year"];
                                $milage = $_POST["milage"];
                                $description = addslashes($_POST["description"]);

                                $date_of_service = "";

                                // Check for empty fields and letter inputs in number fields
                                if($carid == ""){
                                    $error += 1;
                                    array_push($error_list, "You need to pick a car on which you are working on.");
                                }
                                if($repairtype == ""){
                                    $error += 1;
                                    array_push($error_list, "You need to choose what kind of work you are doing to the car.");
                                }
                                if($unit == ""){
                                    $error += 1;
                                    array_push($error_list, "Unit has to be selected.");
                                }
                                if($price == "" || is_numeric($price) == false){
                                    $error += 1;
                                    array_push($error_list, "You didn't enter the price or the price contains a letter. If there is no price type 0.");

                                }
                                if($milage == "" || is_numeric($milage) == false){
                                    $error += 1;
                                    array_push($error_list, "You didn't enter the milage or milage contains a letter.");
                                }

                                if($day == "" || $month == "" || $year == ""){
                                    null;
                                }
                                else{
                                    $date_of_service = "$year-$month-$day";
                                }

                                // If there are no errors insert data into the database table
                                if($error == 0){
                                    
                                    if($date_of_service == ""){
                                        $query = "INSERT INTO mods (id, carid, repair_type, description, milage, price, unit) VALUES (NULL, $carid, '$repairtype', '$description', '$milage', $price, '$unit')";
                                    }
                                    else{
                                        $query = "INSERT INTO mods (id, carid, repair_type, description, milage, price, unit, done_at) VALUES (NULL, $carid, '$repairtype', '$description', '$milage', $price, '$unit', '$date_of_service')";
                                    }
                                    $result = mysqli_query($userdb, $query);
                                    
                                    // Notify user of the upload status
                                    if($result){
                                        echo "<p>Status</p>";
                                        echo "<hr>";
                                        echo "<a class='mod-upload-status'><i class='fa-solid fa-circle-check'></i> Successfully added!</a>";
                                    }
                                    else{
                                        echo "<p>Status</p>";
                                        echo "<hr>";
                                        echo "<a class='mod-upload-status'><i class='fa-solid fa-circle-xmark'></i> Something went wrong on our part. Sorry about that!</a>";
                                    }
                                }
                                else{
                                    echo "<p>Errors</p>";
                                    echo "<hr>";
                                    foreach($error_list as $err){
                                        echo "<a><i class='fa-solid fa-circle-xmark'></i> $err</a>";
                                        echo "<br>";
                                    }
                                }
                            }
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        <div class="footer-content">
            <div class="column">
                <h3>Links</h3>
                <a href="">Login</a>
                <br>
                <a href="">About</a>
                <br>
                <a href="">Contact</a>
            </div>
            <div class="column">
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
    </div>

    <script>
        // Check for a string in price and milage fields
        function checkForString(){
            var x = document.getElementById("price").value;
            var y = document.getElementById("milage").value;

            if(isNaN(x) && isNaN(y)){
                document.getElementById("writeError").style.display = "block";
                document.getElementById("writeError").innerHTML = "Note: <b>Price</b> nor <b>Milage</b> <b>must not</b> contain letters!";
            }
            else if(isNaN(x)){
                document.getElementById("writeError").style.display = "block";
                document.getElementById("writeError").innerHTML = "Note: <b>Price</b> <b>must not</b> contain letters!";
            }
            else if(isNaN(y)){
                document.getElementById("writeError").style.display = "block";
                document.getElementById("writeError").innerHTML = "Note: <b>Milage</b> <b>must not</b> contain letters!";
            }
            else{
                document.getElementById("writeError").style.display = "none";
            }
        }
    </script>

    <script>
        if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
        }
    </script>

    <script>
        function getUnit(){
            unit = document.getElementById("units").value;

            document.getElementById("price-of-work").innerHTML = unit;
        }
    </script>

    <!-- Sticky mini navbar -->
    <script>
        // When the user scrolls the page, execute myFunction
        window.onscroll = function() {myFunction()};

        // Get the navbar
        var navbar = document.getElementById("mini-navbar");

        // Get the offset position of the navbar
        var sticky = navbar.offsetTop;

        // Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
        function myFunction() {
            if (window.pageYOffset >= sticky) {
                navbar.classList.add("mini-sticky")
            } else {
                navbar.classList.remove("mini-sticky");
            }
        }
    </script>

    <script>
        function checkChars(){
            x = document.getElementById("desc").value;
            y = 800 - x.length;

            document.getElementById("chars-left").innerHTML = "(" + y + ")";
        }
    </script>
</body>
</html>