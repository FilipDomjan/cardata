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
    <link rel="stylesheet" href="add_gas.css">
    <title>CarData | Add the amount of gas you pumped</title>
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
                    <a href="add_service.php">Add to Service Book</a>
                </div>
            </div>
            <div class="dropdown-mini">
                <button class="drop-btn" onclick="location.href='gas_data.php'">Gas Data</button>
                <div class="dropdown-mini-content">
                    <a href="#">Add to Gas Data</a>
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
                        <select name="car_selector" id="top-selector">
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
                        <label for="">Fuel type</label>
                        <br>
                        <select name="fuel_select" id="top-selector">
                            <option value="" disabled selected hidden>Select a fuel type</option>
                            <?php
                                    // All types of fuel added in an error and then looped through and added to select field
                                    $fueltypes = array("Petrol", "Liquid Petroleum Gas (LPG)", "Diesel", "Electric", "Natural Gas", "Ethanol (FFV, E85, etc.)", "Hydrogen", "Other");
        
                                    foreach($fueltypes as $key => $val){
                                        echo "<option value='$val'>$val</option>";
                                    }
                                ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="row-price">
                        <!-- Selector for units, we want to know wether the price is in euros, usd or other -->
                        <label for="">Unit</label>
                        <br>
                        <select name="unit_selector" id="units" onchange=getUnit()>
                            <option value="" disabled selected hidden>Select your unit</option>
                            <option value="HRK">HRK</option>
                            <option value="EUR">EUR</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>
                    <div class="row-price">
                        <!-- Input the price, only a number. Should add string checking soon -->
                        <label for="">Fuel price per liter</label>
                        <label class="liter-price" id="liter-price">- / L</label>
                        <input type="text" name="price" id="price" placeholder="ex. 11" oninput=calculateLiters() maxlength="7">
                    </div>
                    <div class="row-price">
                        <!-- Input the price, only a number. Should add string checking soon -->
                        <label for="">Amount you spent</label>
                        <label class="amount-spent" id="amount-spent">-</label>
                        <input type="text" name="spent" id="spent" placeholder="ex. 300" oninput=calculateLiters() maxlength="5">
                    </div>
                </div>
                <div class="row">
                    <label for="">Liters (calculated)</label>
                    <br>
                    <input type="text" name="liters" id="liters" value="0.00 L" readonly="true">
                </div>
                <div class="row">
                    <!-- Input field for milage, also only a number and add string checking -->
                    <label for="">Milage</label>
                    <label class="milage-in-km">KM</label>
                    <input type="text" name="milage" id="milage" placeholder="ex. 255000" maxlength="7">
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
                                $carid = $_POST["car_selector"];
                                $fuel_type = $_POST["fuel_select"];
                                $unit = $_POST["unit_selector"];
                                $fuel_price = $_POST["price"];
                                $amount_spent = $_POST["spent"];
                                $liters = trim($_POST["liters"], " L");
                                $milage = $_POST["milage"];

                                // Check for empty fields and letter inputs in number fields
                                if($carid == ""){
                                    $error += 1;
                                    array_push($error_list, "You need to pick a car on which you are working on.");
                                }

                                if($fuel_type == ""){
                                    $error += 1;
                                    array_push($error_list, "You need to specify the fuel type poured into the car.");
                                }

                                if($unit == ""){
                                    $error += 1;
                                    array_push($error_list, "Unit has to be selected.");
                                }

                                if($fuel_price == "" || is_numeric($fuel_price) == false){
                                    $error += 1;
                                    array_push($error_list, "You didn't enter the price or the price contains a letter. If there is no price type 0.");
                                }
                                else{
                                    $fuel_price_format = number_format($fuel_price, 2, '.', ' ');
                                }

                                if($milage == "" || is_numeric($milage) == false){
                                    $error += 1;
                                    array_push($error_list, "You didn't enter the milage or milage contains a letter.");
                                }

                                if($amount_spent == "" || is_numeric($amount_spent) == false){
                                    $error += 1;
                                    array_push($error_list, "You didn't enter the amount you spent or it contains a letter.");
                                }
                                else{
                                    $amount_spent_format = number_format($amount_spent, 2, '.', ' ');
                                }

                                // If there are no errors insert data into the database table
                                if($error == 0){
                                    $query = "INSERT INTO gas (id, car_id, milage, liters, unit, price, gas_price, fuel_type) VALUES (NULL, $carid, $milage, $liters, '$unit', $amount_spent_format, $fuel_price_format, '$fuel_type')";
                                    $result = mysqli_query($userdb, $query);
                                    
                                    // Notify user of the upload status
                                    if($result){
                                        echo "<p>Status</p>";
                                        echo "<hr>";
                                        echo "<a class='gas-upload-status'><i class='fa-solid fa-circle-check'></i> Successfully added!</a>";
                                    }
                                    else{
                                        echo "<p>Status</p>";
                                        echo "<hr>";
                                        echo "<a class='gas-upload-status'><i class='fa-solid fa-circle-xmark'></i> Something went wrong on our part. Sorry about that!</a>";
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
        function calculateLiters(){
            var price = document.getElementById("price").value;
            var spent = document.getElementById("spent").value;
            var liters = spent / price;

            if(price == "" && spent == ""){
                document.getElementById("liters").value = "0.00 L";
            }
            else{
                document.getElementById("liters").value = liters.toFixed(2) + " L";
            }
        }
    </script>

    <script>
        function getUnit(){
            var unit = document.getElementById("units").value;

            document.getElementById("liter-price").innerHTML = unit + " / L";
            document.getElementById("amount-spent").innerHTML = unit;
        }
    </script>

    <script>
        function checkForString(){
            fuel_price = document.getElementById("price").value;
            amount = document.getElementById("spent").value;
            milage = document.getElementById("milage").value;

            if(isNaN(fuel_price) && isNaN(amount) && isNaN(milage)){
                document.getElementById("writeError").style.display = "block";
                document.getElementById("writeError").innerHTML = "Note: One or more fields contain letters and <b>must contain only</b> letters!";
            }
            else if(isNaN(fuel_price)){
                document.getElementById("writeError").style.display = "block";
                document.getElementById("writeError").innerHTML = "Note: <b>Fuel price</b> <b>must not</b> contain letters!";
            }
            else if(isNaN(amount)){
                document.getElementById("writeError").style.display = "block";
                document.getElementById("writeError").innerHTML = "Note: <b>Amount spent</b> <b>must not</b> contain letters!";
            }
            else if(isNaN(milage)){
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
</body>
</html>