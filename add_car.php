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
    <link rel="stylesheet" href="add_car.css">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <title>CarData | Add a car to collection</title>
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
                    <a href="#">Add a car</a>
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
                    <a href="add_gas.php">Add to Gas Data</a>
                </div>
            </div>
        </div>
    </div>

    <div id="main">
        <div class="clear"></div>
        <div class="main-form">
            <div class="form">
                <form action="" method="POST" autocomplete="off">
                    <div class="row">
                        <div class="row-model">
                            <!-- Input field for the car manufacturer -->
                            <label for="">Manufacturer</label>
                            <br>
                            <select name="carManufacturer" id="model">
                            <option value="" selected disabled hidden>Select a manufacturer</option>

                            <?php

                                // Manufacturers which will show on the top in a separate section for easy access
                                $mainManufacturers = array("Audi", "BMW", "Mercedes-Benz", "Volkswagen");

                                // All manufacturers in an array
                                $manufacturers = array("Abarth", "AC", "Acura", "Aixam", "Alfa Romeo", "Alpina", "Alfa Romeo", "Alpina", "Artega", "Asia Motors", "Aston Martin", "Audi", "Austin", "Austin Healey", "BAIC", "Bentley", "BMW", "Borgward", "Brilliance", "Bugatti", "Buick", "Cadillac", "Casalini"
                                , "Caterham", "Chatenet", "Chevrolet", "Chrysler", "Citroën", "Cobra", "Corvette", "Cupra", "Dacia", "Daewoo", "Daihatsu", "DeTomaso", "DFSK", "Dodge", "Donkervoort", "DS Automobiles", "Ferrari", "Fiat", "Fisker", "Ford", "GAC Gonow", "Gemballa", "GMC", "Grecav", "Hamann", "Holden"
                                , "Honda", "Hummer", "Hyundai", "Infiniti", "Isuzu", "Iveco", "Jaguar", "Jeep", "Kia", "Koenigsegg", "KTM", "Lada", "Lamborghini", "Lancia", "Land Rover", "Landwind", "Lexus", "Ligier", "Lincoln", "Lotus", "Mahindra", "Maserati", "Maybach", "Mazda", "McLaren", "Mercedes-Benz"
                                , "MG", "Microcar", "MINI", "Mitsubishi", "Morgan", "Nissan", "NSU", "Oldsmobile", "Opel", "Pagani", "Peugeot", "Piaggio", "Plymouth", "Polestar", "Pontiac", "Porsche", "Proton", "Renault", "Rolls-Royce", "Rover", "Ruf", "Rimac", "Saab", "Santana", "Seat", "Škoda", "Smart", "speedART"
                                , "Spyker", "SsangYong", "Subaru", "Suzuki", "Talbot", "Tata", "TECHART", "Tesla", "Toyota", "Trabant", "Triumph", "TVR", "Volkswagen", "Volvo", "Wartburg", "Westfield", "Wiesmann");

                                // Looping through the "mainManufacturers" array and adding them to easy access section
                                echo "<optgroup label='Easy access'>";
                                foreach($mainManufacturers as $key => $val){
                                    echo "<option value='$val'>$val</option>";
                                }
                                echo "</optgroup>";            
                                
                                // Looping through all manufacturers and adding them in the "All" section below
                                echo "<optgroup label='All'>";
                                foreach($manufacturers as $key => $val){
                                    echo "<option value='$val'>$val</option>";
                                }
                                echo "</optgroup>";
                            ?>

                            </select>
                        </div>

                        <!-- Input field for the car model. Note: Should be converted to the dropdown menu soon -->
                        <div class="row-model">
                            <label for="">Model</label>
                            <br>
                            <input type="text" name="model" id="model" placeholder="ex. 911 GT3" maxlength="30">
                        </div>
                    </div>
                    <div class="row">
                        <div class="row-tcy">
                            <!-- Input field for the car type -->
                            <label for="">Car Type</label>
                            <br>
                            <select name="cartype" id="tcy">
                                <option value="" selected disabled hidden>Select a model</option>
                                <?php
                                    // All car types in an array which are then looped through foreach and added into select input
                                    $carTypes = array("Sedan", "Coupe", "Sports Car", "Station Wagon", "Hatchback", "Convertible", "SUV", "Minivan", "Van", "Pickup truck", "Truck", "Other");
        
                                    foreach($carTypes as $key => $val){
                                        echo "<option value='$val'>$val</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="row-tcy">
                            <!-- Input field for the model year -->
                            <label for="">Model Year</label>
                            <br>
                            <select name="modelyear" id="tcy">

                                <!-- Showing models from year 1900 to the current date -->
                                <option value="" selected disabled hidden>Select your model year</option>
                                <?php
                                    for($i = date("Y") + 3; $i >= 1900; $i--){
                                        echo "<option value='$i'>$i</option>";
                                    }
                                    ?>
                            </select>
                        </div>
                        <div class="row-tcy">
                            <!-- Car color input field -->
                            <label for="">Color</label>
                            <input type="text" name="color" id="tcy" placeholder="ex. Shark Blue" maxlength="25">
                        </div>
                        <div class="row-tcy">
                            <!-- Vinyls input field -->
                            <label for="">Vinyl</label>
                            <input type="text" name="vinyl" id="tcy" placeholder="ex. White Stripe" maxlength="30">
                        </div>
                    </div>
                    <div class="row">
                        <div class="row-cth">
                            <!-- Engine capacity input field -->
                            <label for="">Engine Capacity</label>
                            <label class="engine-cap">cm3</label>
                            <input type="text" name="engcapacity" id="engcap" placeholder="ex. 4000" oninput=checkForString() maxlength="5">
                        </div>
                        <div class="row-cth">
                            <!-- Engine type select field -->
                            <label for="">Engine Type</label>
                            <br>
                            <select name="engtype" id="cth">
                                <!-- Set unselectable option which shows before user has selected anything -->
                                <option value="" selected disabled hidden>Select an engine configuration</option>
                                <?php
                                    // All engine configurations are added into arrays and then looped through and sorted into different groups
                                    
                                    $inlineEngines = array("Inline-Two", "Inline-Three", "Inline-Four", "Inline-Five", "Inline-Six", "Inline-Eight", "Inline-Ten", "Inline-Twelve", "Inline-Fourteen");
                                    $vEngines = array("V2", "V3", "V4", "V6", "V8", "V10", "V12", "V14", "V16", "V18", "V20", "V24", "VR5", "VR6");
                                    $flatEngines = array("Flat-Two", "Flat-Four", "Flat-Six", "Flat-Eight", "Flat-Twelve");
                                    $wEngines = array("W3", "W8", "W12", "W16", "W18");
                                ?> 
                                <optgroup label="Inline Engines">
                                    <?php
                                        foreach($inlineEngines as $key => $val){
                                            echo "<option value='$val'>$val</option>";
                                        }
                                    ?>
                                </optgroup>
                                <optgroup label="V Engines">
                                    <?php
                                        foreach($vEngines as $key => $val){
                                            echo "<option value='$val'>$val</option>";
                                        }
                                    ?>
                                </optgroup>
                                <optgroup label="Flat Engines">
                                    <?php
                                        foreach($flatEngines as $key => $val){
                                            echo "<option value='$val'>$val</option>";
                                        }
                                    ?>
                                </optgroup>
                                <optgroup label="W Engines">
                                    <?php
                                        foreach($wEngines as $key => $val){
                                            echo "<option value='$val'>$val</option>";
                                        }
                                    ?>
                                </optgroup>
                                <optgroup label="Electric">
                                    <option value="Electric Motors">Electric Motors</option>
                                </optgroup>
                                <optgroup label="Other engine types">
                                        <option value="other">Other</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="row-cth">
                            <!-- Input field for horsepower, accepts only numbers, if a string is detected upon input an error is thrown -->
                            <label for="">Horsepower</label>
                            <label class="engine-hp">HP</label>
                            <input type="text" name="horsepower" id="hp" placeholder="ex. 502" oninput="checkForString()" maxlength="6">
                        </div>
                    </div>
                    <div class="row">
                        <div class="row-ftd">
                            <!-- Fuel type select field -->
                            <label for="">Fuel Type</label>
                            <br>
                            <select name="fueltype" id="ftd">
                                <option value="" selected disabled hidden>Select fuel type</option>
                                <?php
                                    // All types of fuel added in an error and then looped through and added to select field
                                    $fueltypes = array("Petrol", "Petrol + LPG", "Diesel", "Electric", "Natural Gas", "Ethanol (FFV, E85, etc.)", "Hybrid (petrol/electric)", "Hybrid (diesel/electric)", "Hydrogen", "Plug-in Hybrid", "Other");
        
                                    foreach($fueltypes as $key => $val){
                                        echo "<option value='$val'>$val</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="row-ftd">
                            <!-- Select field for transmission type -->
                            <label for="">Transmission</label>
                            <br>
                            <select name="transtype" id="ftd">
                                <option value="" selected disabled hidden>Select transmission type</option>
                                <option value="automatic">Automatic</option>
                                <option value="semi-auto">Semi-Automatic</option>
                                <option value="manual">Manual gearbox</option>
                            </select>
                        </div>
                        <div class="row-ftd">
                            <!-- Select field for drivetrain type -->
                            <label for="">Drivetrain</label>
                            <br>
                            <select name="drivetrain" id="ftd">
                                <option value="" selected disabled hidden>Select drivetrain</option>
                                <option value="awd">All-Wheel Drive (AWD)</option>
                                <option value="4wd">Four-Wheel Drive (4WD)</option>
                                <option value="fwd">Front-Wheel Drive (FWD)</option>
                                <option value="rwd">Rear-Wheel Drive (RWD)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row last-row">
                        <!-- Submit button -->
                        <input type="submit" name="submit" id="submit" value="Submit">
                        <p id="writeError" style="display:none"></p>
                        <div class="clear"></div>
                        <div class="errors">
                            <?php
                                // If the submit button is pressed start the database entry process
                                if(isset($_POST["submit"])){

                                    // Get the users name, this is relevant for connection to the database, as every user has their own database with their
                                    // username being the name of their database
                                    $username = htmlspecialchars($_SESSION["username"]);


                                    // Connect to the MySQL Database
                                    $server = 'localhost';
                                    $user = 'root';
                                    $password = '';
                                    $database = "$username";
                                    
                                    $userdb = mysqli_connect($server, $user, $password, $database);
                                    
                                    if($userdb -> connect_errno){
                                        echo "Failed to connect to MySQL: ".$userdb -> connect_error;
                                        exit();
                                    }
                                
                                    $error = 0;
                                    $error_list = [];
                                    
                                    // Get all the values from the input fields
                                    // Added into try-catch so that if an error happens it wont crash everything
                                    try{
                                        $manufacturer = $_POST["carManufacturer"];
                                        $model = $_POST["model"];
                                        $ctype = $_POST["cartype"];
                                        $modelyear = $_POST["modelyear"];
                                        $color = $_POST["color"];
                                        $vinyl = $_POST["vinyl"];
                                        $engcapacity = $_POST["engcapacity"];
                                        $engtype = $_POST["engtype"];
                                        $hp = $_POST["horsepower"];
                                        $fuel = $_POST["fueltype"];
                                        $trans = $_POST["transtype"];
                                        $drivetrain = $_POST["drivetrain"];
                                    }
                                    catch(Exception $e){
                                        null;
                                    }

                                    if($manufacturer == ""){
                                        $error += 1;
                                        array_push($error_list, "Manufacturer has to be specified.");
                                    }
                                    if($model == ""){
                                        $error += 1;
                                        array_push($error_list, "Car model has to be specified.");
                                    }
                                    if($ctype == ""){
                                        $error += 1;
                                        array_push($error_list, "Car type has to be specified.");
                                    }
                                    if($modelyear == ""){
                                        $error += 1;
                                        array_push($error_list, "Model year has to be specified");
                                    }
                                    if($engcapacity == "" || is_numeric($engcapacity) == false){
                                        $error += 1;
                                        array_push($error_list, "Engine capacity is not specified or contains letters.");
                                    }
                                    if($engtype == ""){
                                        $error += 1;
                                        array_push($error_list, "Engine configuration has to be specified.");
                                    }
                                    if($hp == "" || is_numeric($hp) == false){
                                        $error += 1;
                                        array_push($error_list, "Horsepower is not specified or contains letters.");
                                    }
                                    if($fuel == ""){
                                        $error += 1;
                                        array_push($error_list, "Fuel type has to be specified.");
                                    }
                                    if($trans == ""){
                                        $error += 1;
                                        array_push($error_list, "Transmission type has to be specified.");
                                    }
                                    if($drivetrain == ""){
                                        $error += 1;
                                        array_push($error_list, "Drivetrain has to be specified.");
                                    }
                                    
                                    // If there are no errors proceed with entry to the database
                                    if($error == 0){
                                        
                                        // Every car is given a random id for more accurate accessing
                                        $car_id = rand(1, 999999);
                                        
                                        // Check if car id already exists
                                        $query = "SELECT id FROM cars WHERE id = $car_id";
                                        $result = mysqli_query($userdb, $query);
        
                                        $car_id_check = mysqli_num_rows($result);
                                        
                                        // If it exists generate a random number again until it finds the one which doesn't exist
                                        while($car_id_check > 0){
                                            $car_id = rand(1, 999999);
        
                                            $query = "SELECT id FROM cars WHERE id = $car_id";
                                            $result = mysqli_query($userdb, $query);
        
                                            $car_id_check = mysqli_num_rows($result);
                                        }
                                        
                                        // If everything is successful insert values into the database
                                        $query = "INSERT INTO cars (id, manufacturer, model, chassis, color, vinyl, model_year, engine_config, engine_capacity, horsepower, fuel_type, transmission, drivetrain) VALUES ($car_id, '$manufacturer', '$model', '$ctype', '$color', '$vinyl', '$modelyear', '$engtype', $engcapacity, $hp, '$fuel', '$trans', '$drivetrain')";
                                        $result = mysqli_query($userdb, $query);
                                        
                                        // Notify user of the upload status
                                        if($result){
                                            echo "<p>Status</p>";
                                            echo "<hr>";
                                            echo "<a class='car-upload-status'><i class='fa-solid fa-circle-check'></i> Successfully added!</a>";
                                        }
                                        else{
                                            echo "<p>Status</p>";
                                            echo "<hr>";
                                            echo "<a class='car-upload-status'><i class='fa-solid fa-circle-xmark'></i> Something went wrong on our part. Sorry about that!</a>";
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
                    </div>
                </form>
            </div>
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
        if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
        }
    </script>

    <script>
        // Check for a string in Engine capacity and Horsepower inputs
        function checkForString(){
            var x = document.getElementById("engcap").value;
            var y = document.getElementById("hp").value;

            if(isNaN(x) && isNaN(y)){
                document.getElementById("writeError").style.display = "block";
                document.getElementById("writeError").innerHTML = "Note: Engine Capacity nor Horsepower <b>must not contain letters!</b>";
            }
            else if(isNaN(x)){
                document.getElementById("writeError").style.display = "block";
                document.getElementById("writeError").innerHTML = "Note: Engine Capacity <b>must not contain letters!</b>";
            }
            else if(isNaN(y)){
                document.getElementById("writeError").style.display = "block";
                document.getElementById("writeError").innerHTML = "Note: Horsepower <b>must not contain letters!</b>";
            }
            else{
                document.getElementById("writeError").style.display = "none";
            }
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