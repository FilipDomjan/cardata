<?php
    session_start();
    
    // Check if the user is not logged in, then redirect the user to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: users/login.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
    <script src="https://cdn.rawgit.com/moment/moment/2.21.0/min/moment.min.js"></script>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <title>CarData | Home</title>
</head>
<body>
    <nav class="sidebar" id="mySidebar" onmouseover="toggleSidebar()" onmouseout="toggleSidebar()">
        <div class="sidebar-items">
            <a href="index.php"><i class="fa-solid fa-house"></i><span>Home</span></a>
            <a href="#"><i class="fa-solid fa-gauge-simple"></i><span>Dashboard</span></a>
            <a href="mycars.php"><i class="fa-solid fa-car"></i><span>My Cars</span></a>
            <a href="carmods.php?carid=0"><i class="fa-solid fa-screwdriver-wrench"></i><span>Car Mods</span></a>
            <a href="addcar.php"><i class="fa-solid fa-circle-plus"></i><span>Add a car</span></a>
            <a><i class="fa-solid fa-user"></i><span><?php echo htmlspecialchars($_SESSION["username"]) ?></span></a>
            <a href="users/logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
        </div>
    </nav>

    <!-- Main container -->
    <div id="main">
        <div class="main-content">
            <?php
                // Connect to the main database holding user information
                include("connect_server.php");

                // Get the current users username
                $username = htmlspecialchars($_SESSION["username"]);

                // Select all the data from the database where the username is current users username
                $query = "SELECT * FROM users WHERE username = '$username'";
                $result = mysqli_query($db, $query);

                // Get the first name so that we can show the welcome message
                while($row = mysqli_fetch_assoc($result)){
                    $firstname = $row["firstname"];
                }

                // Welcome message changes as the day progresses
                $hour = date("H", time());
                
                if($hour >= 4 && $hour <= 11){
                    echo "<h1>Good morning, $firstname</h1>";
                }
                elseif($hour >= 11 && $hour <= 19){
                    echo "<h1>Good afternoon, $firstname</h1>";
                }
                else{
                    echo "<h1>Good evening, $firstname</h1>";
                }
            ?>
            <hr>
        </div>
        <div class="container">
            <div class="car-data user">
                <div class="car-data-content">
                    <h2>User data</h2>
                    <hr>
                    <div class="column">
                        <?php
                            // Still connected to the main database, get all other information regarding the user
                            $query = "SELECT * FROM users WHERE username = '$username'";
                            $result = mysqli_query($db, $query);

                            while($row = mysqli_fetch_assoc($result)){
                                $usrid = $row["id"];
                                $joined = $row["created_at"];
                                $firstname = $row["firstname"];
                                $lastname = $row["lastname"];
                                $email = $row["email"];
                            }
                            
                            // Display that information in the tile
                            echo "<p>First name: $firstname</p>";
                            echo "<p>Last name: $lastname</p>";
                            echo "<p>E-mail: $email</p>";
                            echo "<p>Username: $username</p>";
                            echo "<p>Joined: $joined</p>";
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="car-data">
                <div class="car-data-content">
                    <!-- General Car Data holds some basic information, how many cars the user owns,
                    Total money spending, number of tunes/mods/repairs/other etc. -->
                    <h2>General Car Data</h2>
                    <hr>
                    <div class="column">
                        <?php

                            // Now we connect to the users database
                            // database_name = username
                            $username = htmlspecialchars($_SESSION["username"]);

                            $server = 'localhost';
                            $user = 'root';
                            $password = '';
                            $database = "$username";

                            $userdb = mysqli_connect($server, $user, $password, $database);

                            if($userdb -> connect_errno){
                                echo "Failed to connect to MySQL: ".$userdb -> connect_error;
                                exit();
                            }

                            // Get number of cars

                            $query = "SELECT * FROM cars";
                            $result = mysqli_query($userdb, $query);

                            $carnum = mysqli_num_rows($result);

                            echo "<p>Total cars owned: $carnum</p>";

                            // Get number of repairs
                            $query = "SELECT * FROM mods WHERE repair_type = 'Repair'";
                            $result = mysqli_query($userdb, $query);

                            $repairnum = mysqli_num_rows($result);

                            echo "<p>Repairs done: $repairnum</p>";

                            // Get number of mods done
                            
                            $query = "SELECT * FROM mods WHERE repair_type = 'Mod'";
                            $result = mysqli_query($userdb, $query);

                            $modnum = mysqli_num_rows($result);

                            echo "<p>Mods done: $modnum</p>";

                            // Get number of tunes done

                            $query = "SELECT * FROM mods WHERE repair_type = 'Tune'";
                            $result = mysqli_query($userdb, $query);

                            $tunenum = mysqli_num_rows($result);

                            echo "<p>Tunes done: $tunenum</p>";

                            // Get number of other stuff done to the car

                            $query = "SELECT * FROM mods WHERE repair_type = 'Other'";
                            $result = mysqli_query($userdb, $query);

                            $othernum = mysqli_num_rows($result);

                            echo "<p>Other stuff done: $othernum</p>";

                            // Total money spent

                            $query = "SELECT * FROM mods";
                            $result = mysqli_query($userdb, $query);
                            
                            if(mysqli_num_rows($result) == 0){
                                echo "<p>Money spent: No money spent</p>";
                            }
                            else{
                                $total = 0;
                            
                                while($row = mysqli_fetch_assoc($result)){
                                    $value = $row["price"];
                                    $unit = $row["unit"];
                                    
                                    // Convert all units to HRK first
                                    // This is why we let user select the currency in the mod adding section
                                    if(strtolower($unit) == "hrk"){
                                        $total += $value;
                                    }
                                    elseif(strtolower($unit) == "eur"){
                                        $value *= 7.5;
                                        $total += $value;
                                    }
                                    elseif(strtolower($unit) == "usd"){
                                        $value *= 6.5;
                                        $total += $value;
                                    }
                                
                                // Convert to EUR
                                $total_in_eur = number_format((float)$total / 7.5, 2, '.', '');
                                $total = number_format((float)$total, 2, '.', '');
                                }
                                // Show money spent in eur and hrk
                                echo "<p>Money spent: $total_in_eur € ~ $total HRK</p>";
                            }

                        ?>
                    </div>
                    <div class="column column-two">
                        <?php

                            // Get most repaired car

                            $query = "SELECT * FROM mods";
                            $result = mysqli_query($userdb, $query);

                            $car_array = array();

                            while($row = mysqli_fetch_assoc($result)){
                                $car_id = $row["carid"];

                                $query2 = "SELECT * FROM mods WHERE carid = $car_id AND repair_type = 'Repair'";
                                $result2 = mysqli_query($userdb, $query2);
                                
                                $num_row = mysqli_num_rows($result2);

                                if($num_row > 0){
                                    $car_array[$car_id] = $num_row;
                                }
                            }

                            if(empty($car_array)){
                                echo "<p>Most repaired car: No repaired cars</p>";
                            }
                            else{
                                $max_value = max($car_array);
                                $key = array_search($max_value, $car_array);
    
                                $query = "SELECT * FROM cars WHERE id=$key";
                                $result = mysqli_query($userdb, $query);
    
                                while($row = mysqli_fetch_assoc($result)){
                                    $year = $row["model_year"];
                                    $manuf = $row["manufacturer"];
                                    $model = $row["model"];
    
                                    echo "<p>Most repaired car: $year $manuf $model</p>";
                                }
                            }

                            // Get most modded car

                            $query = "SELECT * FROM mods";
                            $result = mysqli_query($userdb, $query);

                            $car_array2 = array();

                            while($row = mysqli_fetch_assoc($result)){
                                $car_id = $row["carid"];

                                $query2 = "SELECT * FROM mods WHERE carid = $car_id AND repair_type = 'Mod'";
                                $result2 = mysqli_query($userdb, $query2);
                                
                                $num_row = mysqli_num_rows($result2);

                                if($num_row > 0){
                                    $car_array2[$car_id] = $num_row;
                                }
                            }

                            if(empty($car_array2)){
                                echo "<p>Most modded car: No modded cars</p>";
                            }
                            else{
                                $max_value = max($car_array2);
                                $key = array_search($max_value, $car_array2);
    
                                $query = "SELECT * FROM cars WHERE id=$key";
                                $result = mysqli_query($userdb, $query);
    
                                while($row = mysqli_fetch_assoc($result)){
                                    $year = $row["model_year"];
                                    $manuf = $row["manufacturer"];
                                    $model = $row["model"];
    
                                    echo "<p>Most modded car: $year $manuf $model</p>";
                                }
                            }

                            // Get most tuned car

                            $query = "SELECT * FROM mods";
                            $result = mysqli_query($userdb, $query);

                            $car_array3 = array();

                            while($row = mysqli_fetch_assoc($result)){
                                $car_id = $row["carid"];

                                $query2 = "SELECT * FROM mods WHERE carid = $car_id AND repair_type = 'Tune'";
                                $result2 = mysqli_query($userdb, $query2);
                                
                                $num_row = mysqli_num_rows($result2);

                                if($num_row > 0){
                                    $car_array3[$car_id] = $num_row;
                                }
                            }

                            if(empty($car_array3)){
                                echo "<p>Most tuned car: No tuned cars</p>";
                            }
                            else{
                                $max_value = max($car_array3);
                                $key = array_search($max_value, $car_array3);
    
                                $query = "SELECT * FROM cars WHERE id=$key";
                                $result = mysqli_query($userdb, $query);
    
                                while($row = mysqli_fetch_assoc($result)){
                                    $year = $row["model_year"];
                                    $manuf = $row["manufacturer"];
                                    $model = $row["model"];
    
                                    echo "<p>Most tuned car: $year $manuf $model</p>";
                                }
                            }

                            // Get which money user spent the most money on

                            $query = "SELECT * FROM cars";
                            $result = mysqli_query($userdb, $query);

                            $car_array4 = array();

                            while($row = mysqli_fetch_assoc($result)){
                                $car_id = $row["id"];

                                $query2 = "SELECT carid, price, unit FROM mods WHERE carid=$car_id";
                                $result2 = mysqli_query($userdb, $query2);

                                $total = 0;

                                while($row2 = mysqli_fetch_assoc($result2)){
                                    $price = $row2["price"];
                                    $unit = $row2["unit"];
                                    $carid = $row2["carid"];
    
                                    if(strtolower($unit) == "hrk"){
                                        $total += $price;
                                    }
                                    elseif(strtolower($unit) == "eur"){
                                        $price *= 7.5;
                                        $total += $price;
                                    }
                                    elseif(strtolower($unit) == "usd"){
                                        $price *= 6.5;
                                        $total += $price;
                                    }
                                }

                                $total_in_eur = number_format((float)$total / 7.5, 2, '.', '');

                                $car_array4[$car_id] = $total_in_eur;
                
                            }

                            if(empty($car_array4) || max($car_array4) == 0){
                                echo "<p>Most money spent on: No money spent on cars</p>";
                            }
                            else{
                                $max_value = max($car_array4);
                                $key = array_search($max_value, $car_array4);
    
                                $query = "SELECT * FROM cars WHERE id=$key";
                                $result = mysqli_query($userdb, $query);
    
                                while($row = mysqli_fetch_assoc($result)){
                                    $year = $row["model_year"];
                                    $manuf = $row["manufacturer"];
                                    $model = $row["model"];
                                    
                                    echo "<p>Most money spent on: $year $manuf $model<br>($max_value €)</p>";
                                }
                            }
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="car-data">
                <!-- Create a canvas for weekly spending report -->
                <div class="car-data-content">
                    <h2>Weekly report</h2>
                    <hr>
                    <div class="chart-canvas">
                        <canvas id="WeeklyExpensesByCategory" style="width: 100%; height: 500px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="car-data">
                <!-- Create a canvas for monthly spending report -->
                <div class="car-data-content">
                    <h2>Monthly report</h2>
                    <hr>
                    <div class="chart-canvas">
                        <canvas id="MonthlyExpensesByCategory" style="width: 100%; height: 500px;"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Get the weekly data for the chart from the MySQL Database for the last week of users activity -->
    <?php
        // Get data for expenses on Mods

        $query = "SELECT repair_type, price, unit, done_at FROM mods WHERE done_at >= DATE(NOW()) + INTERVAL -6 DAY AND repair_type = \"Mod\"";
        $result = mysqli_query($userdb, $query);

        // We get the date at which the work has been done and then get which day of the week was it
        // Then we calculate spending for that day and sort it into variables

        $monTotalMods = 0;
        $thuTotalMods = 0;
        $wedTotalMods = 0;
        $tueTotalMods = 0;
        $friTotalMods = 0;
        $satTotalMods = 0;
        $sunTotalMods = 0;
        
        while($row = mysqli_fetch_assoc($result)){
            $price = $row["price"];
            $unit = $row["unit"];
            // Format the date to show day name of the given date
            $date = date_create($row["done_at"]);
            $day = date_format($date, "D");

            // Check whats the day name and calculate spending for that day
            // Then sort it into variables
            // Also convert all spending to HRK first (should be added into function instead of writing the whole thing)
            if($day == "Mon"){
                if(strtolower($unit) == "hrk"){
                    $monTotalMods += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $monTotalMods += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $monTotalMods += $price;
                }
            }
            elseif($day == "Tue"){
                if(strtolower($unit) == "hrk"){
                    $tueTotalMods += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $tueTotalMods += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $tueTotalMods += $price;
                }
            }
            elseif($day == "Wed"){
                if(strtolower($unit) == "hrk"){
                    $wedTotalMods += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $wedTotalMods += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $wedTotalMods += $price;
                }
            }
            elseif($day == "Thu"){
                if(strtolower($unit) == "hrk"){
                    $thuTotalMods += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $thuTotalMods += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $thuTotalMods += $price;
                }
            }
            elseif($day == "Fri"){
                if(strtolower($unit) == "hrk"){
                    $friTotalMods += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $friTotalMods += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $friTotalMods += $price;
                }
            }
            elseif($day == "Sat"){
                if(strtolower($unit) == "hrk"){
                    $satTotalMods += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $satTotalMods += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $satTotalMods += $price;
                }
            }
            elseif($day == "Sun"){
                if(strtolower($unit) == "hrk"){
                    $sunTotalMods += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $sunTotalMods += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $sunTotalMods += $price;
                }
            }
        }

        // Get data for expenses on Repairs

        $query = "SELECT repair_type, price, unit, done_at FROM mods WHERE done_at >= DATE(NOW()) + INTERVAL -6 DAY AND repair_type = \"Repair\"";
        $result = mysqli_query($userdb, $query);

        // Same thing applies for repairs as for mods
        $monTotalRepairs = 0;
        $thuTotalRepairs = 0;
        $wedTotalRepairs = 0;
        $tueTotalRepairs = 0;
        $friTotalRepairs = 0;
        $satTotalRepairs = 0;
        $sunTotalRepairs = 0;

        while($row = mysqli_fetch_assoc($result)){
            $price = $row["price"];
            $unit = $row["unit"];
            $date = date_create($row["done_at"]);
            $day = date_format($date, "D");

            if($day == "Mon"){
                if(strtolower($unit) == "hrk"){
                    $monTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $monTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $monTotalRepairs += $price;
                }
            }
            elseif($day == "Tue"){
                if(strtolower($unit) == "hrk"){
                    $tueTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $tueTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $tueTotalRepairs += $price;
                }
            }
            elseif($day == "Wed"){
                if(strtolower($unit) == "hrk"){
                    $wedTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $wedTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $wedTotalRepairs += $price;
                }
            }
            elseif($day == "Thu"){
                if(strtolower($unit) == "hrk"){
                    $thuTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $thuTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $thuTotalRepairs += $price;
                }
            }
            elseif($day == "Fri"){
                if(strtolower($unit) == "hrk"){
                    $friTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $friTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $friTotalRepairs += $price;
                }
            }
            elseif($day == "Sat"){
                if(strtolower($unit) == "hrk"){
                    $satTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $satTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $satTotalRepairs += $price;
                }
            }
            elseif($day == "Sun"){
                if(strtolower($unit) == "hrk"){
                    $sunTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $sunTotalRepairs += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $sunTotalRepairs += $price;
                }
            }
        }

        // Get data for expenses on Repairs

        $query = "SELECT repair_type, price, unit, done_at FROM mods WHERE done_at >= DATE(NOW()) + INTERVAL -6 DAY AND repair_type = \"Tune\"";
        $result = mysqli_query($userdb, $query);

        // Same process used again for tunes
        $monTotalTunes = 0;
        $thuTotalTunes = 0;
        $wedTotalTunes = 0;
        $tueTotalTunes = 0;
        $friTotalTunes = 0;
        $satTotalTunes = 0;
        $sunTotalTunes = 0;

        while($row = mysqli_fetch_assoc($result)){
            $price = $row["price"];
            $unit = $row["unit"];
            $date = date_create($row["done_at"]);
            $day = date_format($date, "D");

            if($day == "Mon"){
                if(strtolower($unit) == "hrk"){
                    $monTotalTunes += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $monTotalTunes += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $monTotalTunes += $price;
                }
            }
            elseif($day == "Tue"){
                if(strtolower($unit) == "hrk"){
                    $tueTotalTunes += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $tueTotalTunes += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $tueTotalTunes += $price;
                }
            }
            elseif($day == "Wed"){
                if(strtolower($unit) == "hrk"){
                    $wedTotalTunes += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $wedTotalTunes += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $wedTotalTunes += $price;
                }
            }
            elseif($day == "Thu"){
                if(strtolower($unit) == "hrk"){
                    $thuTotalTunes += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $thuTotalTunes += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $thuTotalTunes += $price;
                }
            }
            elseif($day == "Fri"){
                if(strtolower($unit) == "hrk"){
                    $friTotalTunes += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $friTotalTunes += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $friTotalTunes += $price;
                }
            }
            elseif($day == "Sat"){
                if(strtolower($unit) == "hrk"){
                    $satTotalTunes += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $satTotalTunes += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $satTotalTunes += $price;
                }
            }
            elseif($day == "Sun"){
                if(strtolower($unit) == "hrk"){
                    $sunTotalTunes += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $sunTotalTunes += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $sunTotalTunes += $price;
                }
            }
        }

        // Get data for expenses on Other stuff

        $query = "SELECT repair_type, price, unit, done_at FROM mods WHERE done_at >= DATE(NOW()) + INTERVAL -6 DAY AND repair_type = \"Other\"";
        $result = mysqli_query($userdb, $query);

        // Same process for tunes as well
        $monTotalOther = 0;
        $thuTotalOther = 0;
        $wedTotalOther = 0;
        $tueTotalOther = 0;
        $friTotalOther = 0;
        $satTotalOther = 0;
        $sunTotalOther = 0;

        while($row = mysqli_fetch_assoc($result)){
            $price = $row["price"];
            $unit = $row["unit"];
            $date = date_create($row["done_at"]);
            $day = date_format($date, "D");

            if($day == "Mon"){
                if(strtolower($unit) == "hrk"){
                    $monTotalOther += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $monTotalOther += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $monTotalOther += $price;
                }
            }
            elseif($day == "Tue"){
                if(strtolower($unit) == "hrk"){
                    $tueTotalOther += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $tueTotalOther += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $tueTotalOther += $price;
                }
            }
            elseif($day == "Wed"){
                if(strtolower($unit) == "hrk"){
                    $wedTotalOther += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $wedTotalOther += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $wedTotalOther += $price;
                }
            }
            elseif($day == "Thu"){
                if(strtolower($unit) == "hrk"){
                    $thuTotalOther += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $thuTotalOther += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $thuTotalOther += $price;
                }
            }
            elseif($day == "Fri"){
                if(strtolower($unit) == "hrk"){
                    $friTotalOther += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $friTotalOther += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $friTotalOther += $price;
                }
            }
            elseif($day == "Sat"){
                if(strtolower($unit) == "hrk"){
                    $satTotalOther += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $satTotalOther += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $satTotalOther += $price;
                }
            }
            elseif($day == "Sun"){
                if(strtolower($unit) == "hrk"){
                    $sunTotalOther += $price;
                }
                elseif(strtolower($unit) == "eur"){
                    $price *= 7.5;
                    $sunTotalOther += $price;
                }
                elseif(strtolower($unit) == "usd"){
                    $price *= 6.5;
                    $sunTotalOther += $price;
                }
            }
        }

        // Convert HRK to EUR for all expenses

        // We sort mods, repairs, tunes, other into a string, which is formatted later
        $weeklyOnMods = "";
        $weeklyOnRepairs = "";
        $weeklyOnTunes = "";
        $weeklyOnOther = "";

        // All the amounts are converted to EUR and formatted to only 2 decimal points

        $monTotalMods = number_format((float)$monTotalMods / 7.5, 2, '.', '');
        $tueTotalMods = number_format((float)$tueTotalMods / 7.5, 2, '.', '');
        $wedTotalMods = number_format((float)$wedTotalMods / 7.5, 2, '.', '');
        $thuTotalMods = number_format((float)$thuTotalMods / 7.5, 2, '.', '');
        $friTotalMods = number_format((float)$friTotalMods / 7.5, 2, '.', '');
        $satTotalMods = number_format((float)$satTotalMods / 7.5, 2, '.', '');
        $sunTotalMods = number_format((float)$sunTotalMods / 7.5, 2, '.', '');

        $monTotalRepairs = number_format((float)$monTotalRepairs / 7.5, 2, '.', '');
        $tueTotalRepairs = number_format((float)$tueTotalRepairs / 7.5, 2, '.', '');
        $wedTotalRepairs = number_format((float)$wedTotalRepairs / 7.5, 2, '.', '');
        $thuTotalRepairs = number_format((float)$thuTotalRepairs / 7.5, 2, '.', '');
        $friTotalRepairs = number_format((float)$friTotalRepairs / 7.5, 2, '.', '');
        $satTotalRepairs = number_format((float)$satTotalRepairs / 7.5, 2, '.', '');
        $sunTotalRepairs = number_format((float)$sunTotalRepairs / 7.5, 2, '.', '');
        
        $monTotalTunes = number_format((float)$monTotalTunes / 7.5, 2, '.', '');
        $tueTotalTunes = number_format((float)$tueTotalTunes / 7.5, 2, '.', '');
        $wedTotalTunes = number_format((float)$wedTotalTunes / 7.5, 2, '.', '');
        $thuTotalTunes = number_format((float)$thuTotalTunes / 7.5, 2, '.', '');
        $friTotalTunes = number_format((float)$friTotalTunes / 7.5, 2, '.', '');
        $satTotalTunes = number_format((float)$satTotalTunes / 7.5, 2, '.', '');
        $sunTotalTunes = number_format((float)$sunTotalTunes / 7.5, 2, '.', '');

        $monTotalOther = number_format((float)$monTotalOther / 7.5, 2, '.', '');
        $tueTotalOther = number_format((float)$tueTotalOther / 7.5, 2, '.', '');
        $wedTotalOther = number_format((float)$wedTotalOther / 7.5, 2, '.', '');
        $thuTotalOther = number_format((float)$thuTotalOther / 7.5, 2, '.', '');
        $friTotalOther = number_format((float)$friTotalOther / 7.5, 2, '.', '');
        $satTotalOther = number_format((float)$satTotalOther / 7.5, 2, '.', '');
        $sunTotalOther = number_format((float)$sunTotalOther / 7.5, 2, '.', '');



        // Sort all expenses in an array according to todays day
        // So for example if today is Monday, we wanna sort last 7 days in order like (TuesdayExpenses, WednesdayExpenses, ThursdayExpenses, FridayExpenses, SaturdayExpenses, SundayExpenses, MondayExpenses)
        // So when the next day comes it will again be in the correct order showing (WednesdayExpenses, ThursdayExpenses, FridayExpenses, SaturdayExpenses, SundayExpenses, MondayExpenses, TuesdayExpenses) and so on...
        // This is so we can show the expenses in correct order in the graph

        $date = date('d-m-Y', time());
       
        for($i = 6; $i >= 0; $i--){
            $day = date("l", strtotime($i." days ago"));

            if($day == "Monday"){
                $weeklyOnMods .= "$monTotalMods, ";
                $weeklyOnRepairs .= "$monTotalRepairs, ";
                $weeklyOnTunes .= "$monTotalTunes, ";
                $weeklyOnOther .= "$monTotalOther, ";
            }
            elseif($day == "Tuesday"){
                $weeklyOnMods .= "$tueTotalMods, ";
                $weeklyOnRepairs .= "$tueTotalRepairs, ";
                $weeklyOnTunes .= "$tueTotalTunes, ";
                $weeklyOnOther .= "$tueTotalOther, ";
            }
            elseif($day == "Wednesday"){
                $weeklyOnMods .= "$wedTotalMods, ";
                $weeklyOnRepairs .= "$wedTotalRepairs, ";
                $weeklyOnTunes .= "$wedTotalTunes, ";
                $weeklyOnOther .= "$wedTotalOther, ";
            }
            elseif($day == "Thursday"){
                $weeklyOnMods .= "$thuTotalMods, ";
                $weeklyOnRepairs .= "$thuTotalRepairs, ";
                $weeklyOnTunes .= "$thuTotalTunes, ";
                $weeklyOnOther .= "$thuTotalOther, ";
            }
            elseif($day == "Friday"){
                $weeklyOnMods .= "$friTotalMods, ";
                $weeklyOnRepairs .= "$friTotalRepairs, ";
                $weeklyOnTunes .= "$friTotalTunes, ";
                $weeklyOnOther .= "$friTotalOther, ";
            }
            elseif($day == "Saturday"){
                $weeklyOnMods .= "$satTotalMods, ";
                $weeklyOnRepairs .= "$satTotalRepairs, ";
                $weeklyOnTunes .= "$satTotalTunes, ";
                $weeklyOnOther .= "$satTotalOther, ";
            }
            elseif($day == "Sunday"){
                $weeklyOnMods .= "$sunTotalMods, ";
                $weeklyOnRepairs .= "$sunTotalRepairs, ";
                $weeklyOnTunes .= "$sunTotalTunes, ";
                $weeklyOnOther .= "$sunTotalOther, ";
            }
        }
        

        $weeklyOnMods = trim($weeklyOnMods, ",");
        $weeklyOnRepairs = trim($weeklyOnRepairs, ",");
        $weeklyOnTunes = trim($weeklyOnTunes, ",");
        $weeklyOnOther = trim($weeklyOnOther, ",");
    ?>

    <!-- Code for the weekly expenses chart -->
    <script>

        // Format the day to our liking
        function formatDate(date){
        var dd = date.getDate();
        var mm = date.getMonth()+1;
        var yyyy = date.getFullYear();
        if(dd<10) {dd='0'+dd}
        if(mm<10) {mm='0'+mm}
        date = mm+'/'+dd+'/'+yyyy;
        return date
        }

        // Get the name of the day on the given date
        function getDayName(dateStr, locale)
        {
            var date = new Date(dateStr);
            return date.toLocaleDateString(locale, { weekday: 'long' });        
        }

        // Get and insert last 7 days from the current day in the array (including the current day)
        days = [];
        for(var i = 6; i >= 0; i--){
            var d = new Date();
            d.setDate(d.getDate() - i);
            d = formatDate(d)
            day = getDayName(d, "EN");
            days.push(day);
        }

        days.join(',');

        var weekly = document.getElementById('WeeklyExpensesByCategory').getContext('2d');

        Chart.defaults.global.legend.labels.usePointStyle = true;

        // Create a bar chart with the data gathered with the previous code
        let barChart = new Chart(weekly, {
            type: "bar",
            data: {
                labels: days,
                datasets: [{
                    data: [<?php echo $weeklyOnMods?>],
                    borderColor: "#00a2ff",
                    backgroundColor: "rgba(0, 162, 255, 1)",
                    label: "Mods",
                    fill: true
                }, {
                    data: [<?php echo $weeklyOnRepairs?>],
                    borderColor: "#b300ff",
                    backgroundColor: "rgba(179, 0, 255, 1)",
                    label: "Repairs",
                    fill: true
                }, {
                    data: [<?php echo $weeklyOnTunes?>],
                    borderColor: "#ff003c",
                    backgroundColor: "rgba(255, 0, 60, 1)",
                    label: "Tunes",
                    fill: true
                }, {
                    data: [<?php echo $weeklyOnOther?>],
                    borderColor: "#00ff37",
                    backgroundColor: "rgba(0, 255, 55, 1)",
                    label: "Other",
                    fill: true
                }]
            },
            options: {
                legend: {
                    display: true, 
                },
                scales: {
                    yaxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values){
                                return value + ' EUR';
                            }
                        }
                    }]
                },
                title: {
                    display: true,
                    text: "Spending in EUR for the last 7 Days",
                    fontSize: 20,
                },
                tooltips: {
                    callbacks: {
                        label: (item) => `${item.yLabel} EUR`,
                    }
                }
            }
        });
    </script>

    <!-- Get the monthly expenses data for the chart from the MySQL Database for the last 3 months of users spending activity -->
    <?php
        $monthlyOnMods = "";
        $monthlyOnRepairs = "";
        $monthlyOnTunes = "";
        $monthlyOnOther = "";

        // Monthly expenses on Mods

        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE repair_type = 'Mod' AND done_at >= DATE(NOW()) + INTERVAL -2 MONTH";
        $result = mysqli_query($userdb, $query);

        // Similar process like we used for weekly spending is used on monthly
        // Just this time for each month
        // This shows only rows where repair type is "Mod"
        $janSpentOnMods = 0;
        $febSpentOnMods = 0;
        $marSpentOnMods = 0;
        $aprSpentOnMods = 0;
        $maySpentOnMods = 0;
        $junSpentOnMods = 0;
        $julSpentOnMods = 0;
        $augSpentOnMods = 0;
        $sepSpentOnMods = 0;
        $octSpentOnMods = 0;
        $novSpentOnMods = 0;
        $decSpentOnMods = 0;

        // Check whats the month name using the date in the database (for each row in the table)
        while($row = mysqli_fetch_assoc($result)){
            $date = $row["done_at"];
            $price = $row["price"];
            $unit = $row["unit"];

            $month_name = date("F", strtotime($date));

            // We can see that here we used a function for converting units
            // Instead of what we did for weekly
            if($month_name == "January"){
                $convertedPrice = convertUnit($price, $unit);
                $janSpentOnMods += $convertedPrice;
            }
            elseif($month_name == "February"){
                $convertedPrice = convertUnit($price, $unit);
                $febSpentOnMods += $convertedPrice;
            }
            elseif($month_name == "March"){
                $convertedPrice = convertUnit($price, $unit);
                $marSpentOnMods += $convertedPrice;
            }
            elseif($month_name == "April"){
                $convertedPrice = convertUnit($price, $unit);
                $aprSpentOnMods += $convertedPrice;
            }
            elseif($month_name == "May"){
                $convertedPrice = convertUnit($price, $unit);
                $maySpentOnMods += $convertedPrice;
            }
            elseif($month_name == "June"){
                $convertedPrice = convertUnit($price, $unit);
                $junSpentOnMods += $convertedPrice;
            }
            elseif($month_name == "July"){
                $convertedPrice = convertUnit($price, $unit);
                $julSpentOnMods += $convertedPrice;
            }
            elseif($month_name == "August"){
                $convertedPrice = convertUnit($price, $unit);
                $augSpentOnMods += $convertedPrice;
            }
            elseif($month_name == "September"){
                $convertedPrice = convertUnit($price, $unit);
                $sepSpentOnMods += $convertedPrice;
            }
            elseif($month_name == "October"){
                $convertedPrice = convertUnit($price, $unit);
                $octSpentOnMods += $convertedPrice;
            }
            elseif($month_name == "November"){
                $convertedPrice = convertUnit($price, $unit);
                $novSpentOnMods += $convertedPrice;
            }
            else{
                $convertedPrice = convertUnit($price, $unit);
                $decSpentOnMods += $convertedPrice;
            }
        }


        // Monthly expenses on Repairs

        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE repair_type = 'Repair' AND done_at >= DATE(NOW()) + INTERVAL -2 MONTH";
        $result = mysqli_query($userdb, $query);
        
        // Everything is the same for repairs
        $janSpentOnRepairs = 0;
        $febSpentOnRepairs = 0;
        $marSpentOnRepairs = 0;
        $aprSpentOnRepairs = 0;
        $maySpentOnRepairs = 0;
        $junSpentOnRepairs = 0;
        $julSpentOnRepairs = 0;
        $augSpentOnRepairs = 0;
        $sepSpentOnRepairs = 0;
        $octSpentOnRepairs = 0;
        $novSpentOnRepairs = 0;
        $decSpentOnRepairs = 0;

        while($row = mysqli_fetch_assoc($result)){
            $date = $row["done_at"];
            $price = $row["price"];
            $unit = $row["unit"];

            $month_name = date("F", strtotime($date));

            if($month_name == "January"){
                $convertedPrice = convertUnit($price, $unit);
                $janSpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "February"){
                $convertedPrice = convertUnit($price, $unit);
                $febSpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "March"){
                $convertedPrice = convertUnit($price, $unit);
                $marSpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "April"){
                $convertedPrice = convertUnit($price, $unit);
                $aprSpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "May"){
                $convertedPrice = convertUnit($price, $unit);
                $maySpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "June"){
                $convertedPrice = convertUnit($price, $unit);
                $junSpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "July"){
                $convertedPrice = convertUnit($price, $unit);
                $julSpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "August"){
                $convertedPrice = convertUnit($price, $unit);
                $augSpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "September"){
                $convertedPrice = convertUnit($price, $unit);
                $sepSpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "October"){
                $convertedPrice = convertUnit($price, $unit);
                $octSpentOnRepairs += $convertedPrice;
            }
            elseif($month_name == "November"){
                $convertedPrice = convertUnit($price, $unit);
                $novSpentOnRepairs += $convertedPrice;
            }
            else{
                $convertedPrice = convertUnit($price, $unit);
                $decSpentOnRepairs += $convertedPrice;
            }
        }

        // Monthly expenses on Tunes

        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE repair_type = 'Tune' AND done_at >= DATE(NOW()) + INTERVAL -2 MONTH";
        $result = mysqli_query($userdb, $query);
        
        // Same for tunes as well
        $janSpentOnTunes = 0;
        $febSpentOnTunes = 0;
        $marSpentOnTunes = 0;
        $aprSpentOnTunes = 0;
        $maySpentOnTunes = 0;
        $junSpentOnTunes = 0;
        $julSpentOnTunes = 0;
        $augSpentOnTunes = 0;
        $sepSpentOnTunes = 0;
        $octSpentOnTunes = 0;
        $novSpentOnTunes = 0;
        $decSpentOnTunes = 0;

        while($row = mysqli_fetch_assoc($result)){
            $date = $row["done_at"];
            $price = $row["price"];
            $unit = $row["unit"];

            $month_name = date("F", strtotime($date));

            if($month_name == "January"){
                $convertedPrice = convertUnit($price, $unit);
                $janSpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "February"){
                $convertedPrice = convertUnit($price, $unit);
                $febSpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "March"){
                $convertedPrice = convertUnit($price, $unit);
                $marSpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "April"){
                $convertedPrice = convertUnit($price, $unit);
                $aprSpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "May"){
                $convertedPrice = convertUnit($price, $unit);
                $maySpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "June"){
                $convertedPrice = convertUnit($price, $unit);
                $junSpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "July"){
                $convertedPrice = convertUnit($price, $unit);
                $julSpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "August"){
                $convertedPrice = convertUnit($price, $unit);
                $augSpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "September"){
                $convertedPrice = convertUnit($price, $unit);
                $sepSpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "October"){
                $convertedPrice = convertUnit($price, $unit);
                $octSpentOnTunes += $convertedPrice;
            }
            elseif($month_name == "November"){
                $convertedPrice = convertUnit($price, $unit);
                $novSpentOnTunes += $convertedPrice;
            }
            else{
                $convertedPrice = convertUnit($price, $unit);
                $decSpentOnTunes += $convertedPrice;
            }
        }


        // Monthly expenses on Other

        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE repair_type = 'Other' AND done_at >= DATE(NOW()) + INTERVAL -2 MONTH";
        $result = mysqli_query($userdb, $query);
        
        // Same for spending on other stuff
        $janSpentOnOther = 0;
        $febSpentOnOther = 0;
        $marSpentOnOther = 0;
        $aprSpentOnOther = 0;
        $maySpentOnOther = 0;
        $junSpentOnOther = 0;
        $julSpentOnOther = 0;
        $augSpentOnOther = 0;
        $sepSpentOnOther = 0;
        $octSpentOnOther = 0;
        $novSpentOnOther = 0;
        $decSpentOnOther = 0;

        while($row = mysqli_fetch_assoc($result)){
            $date = $row["done_at"];
            $price = $row["price"];
            $unit = $row["unit"];

            $month_name = date("F", strtotime($date));

            if($month_name == "January"){
                $convertedPrice = convertUnit($price, $unit);
                $janSpentOnOther += $convertedPrice;
            }
            elseif($month_name == "February"){
                $convertedPrice = convertUnit($price, $unit);
                $febSpentOnOther += $convertedPrice;
            }
            elseif($month_name == "March"){
                $convertedPrice = convertUnit($price, $unit);
                $marSpentOnOther += $convertedPrice;
            }
            elseif($month_name == "April"){
                $convertedPrice = convertUnit($price, $unit);
                $aprSpentOnOther += $convertedPrice;
            }
            elseif($month_name == "May"){
                $convertedPrice = convertUnit($price, $unit);
                $maySpentOnOther += $convertedPrice;
            }
            elseif($month_name == "June"){
                $convertedPrice = convertUnit($price, $unit);
                $junSpentOnOther += $convertedPrice;
            }
            elseif($month_name == "July"){
                $convertedPrice = convertUnit($price, $unit);
                $julSpentOnOther += $convertedPrice;
            }
            elseif($month_name == "August"){
                $convertedPrice = convertUnit($price, $unit);
                $augSpentOnOther += $convertedPrice;
            }
            elseif($month_name == "September"){
                $convertedPrice = convertUnit($price, $unit);
                $sepSpentOnOther += $convertedPrice;
            }
            elseif($month_name == "October"){
                $convertedPrice = convertUnit($price, $unit);
                $octSpentOnOther += $convertedPrice;
            }
            elseif($month_name == "November"){
                $convertedPrice = convertUnit($price, $unit);
                $novSpentOnOther += $convertedPrice;
            }
            else{
                $convertedPrice = convertUnit($price, $unit);
                $decSpentOnOther += $convertedPrice;
            }
        }

        // Convert all spending for each month (and for each repair type) in EUR
        // We also created a function for HRK to EUR conversion
        $janSpentOnMods = convertToEur($janSpentOnMods);
        $febSpentOnMods = convertToEur($febSpentOnMods);
        $marSpentOnMods = convertToEur($marSpentOnMods);
        $aprSpentOnMods = convertToEur($aprSpentOnMods);
        $maySpentOnMods = convertToEur($maySpentOnMods);
        $junSpentOnMods = convertToEur($junSpentOnMods);
        $julSpentOnMods = convertToEur($julSpentOnMods);
        $augSpentOnMods = convertToEur($augSpentOnMods);
        $sepSpentOnMods = convertToEur($sepSpentOnMods);
        $octSpentOnMods = convertToEur($octSpentOnMods);
        $novSpentOnMods = convertToEur($novSpentOnMods);
        $decSpentOnMods = convertToEur($decSpentOnMods);

        $janSpentOnRepairs = convertToEur($janSpentOnRepairs);
        $febSpentOnRepairs = convertToEur($febSpentOnRepairs);
        $marSpentOnRepairs = convertToEur($marSpentOnRepairs);
        $aprSpentOnRepairs = convertToEur($aprSpentOnRepairs);
        $maySpentOnRepairs = convertToEur($maySpentOnRepairs);
        $junSpentOnRepairs = convertToEur($junSpentOnRepairs);
        $julSpentOnRepairs = convertToEur($julSpentOnRepairs);
        $augSpentOnRepairs = convertToEur($augSpentOnRepairs);
        $sepSpentOnRepairs = convertToEur($sepSpentOnRepairs);
        $octSpentOnRepairs = convertToEur($octSpentOnRepairs);
        $novSpentOnRepairs = convertToEur($novSpentOnRepairs);
        $decSpentOnRepairs = convertToEur($decSpentOnRepairs);

        $janSpentOnTunes = convertToEur($janSpentOnTunes);
        $febSpentOnTunes = convertToEur($febSpentOnTunes);
        $marSpentOnTunes = convertToEur($marSpentOnTunes);
        $aprSpentOnTunes = convertToEur($aprSpentOnTunes);
        $maySpentOnTunes = convertToEur($maySpentOnTunes);
        $junSpentOnTunes = convertToEur($junSpentOnTunes);
        $julSpentOnTunes = convertToEur($julSpentOnTunes);
        $augSpentOnTunes = convertToEur($augSpentOnTunes);
        $sepSpentOnTunes = convertToEur($sepSpentOnTunes);
        $octSpentOnTunes = convertToEur($octSpentOnTunes);
        $novSpentOnTunes = convertToEur($novSpentOnTunes);
        $decSpentOnTunes = convertToEur($decSpentOnTunes);

        $janSpentOnOther = convertToEur($janSpentOnOther);
        $febSpentOnOther = convertToEur($febSpentOnOther);
        $marSpentOnOther = convertToEur($marSpentOnOther);
        $aprSpentOnOther = convertToEur($aprSpentOnOther);
        $maySpentOnOther = convertToEur($maySpentOnOther);
        $junSpentOnOther = convertToEur($junSpentOnOther);
        $julSpentOnOther = convertToEur($julSpentOnOther);
        $augSpentOnOther = convertToEur($augSpentOnOther);
        $sepSpentOnOther = convertToEur($sepSpentOnOther);
        $octSpentOnOther = convertToEur($octSpentOnOther);
        $novSpentOnOther = convertToEur($novSpentOnOther);
        $decSpentOnOther = convertToEur($decSpentOnOther);

        // Sort all expenses in the same fashion as we did Weekly
        // We want to show only last 3 months including the current one

        for($i = 2; $i >= 0; $i--){
            $month = date("F", strtotime($i. " months ago"));

            if($month == "January"){
                $monthlyOnMods .= "$janSpentOnMods, ";
                $monthlyOnRepairs .= "$janSpentOnRepairs, ";
                $monthlyOnTunes .= "$janSpentOnTunes, ";
                $monthlyOnOther .= "$janSpentOnOther, ";
            }
            elseif($month == "February"){
                $monthlyOnMods .= "$febSpentOnMods, ";
                $monthlyOnRepairs .= "$febSpentOnRepairs, ";
                $monthlyOnTunes .= "$febSpentOnTunes, ";
                $monthlyOnOther .= "$febSpentOnOther, ";
            }
            elseif($month == "March"){
                $monthlyOnMods .= "$marSpentOnMods, ";
                $monthlyOnRepairs .= "$marSpentOnRepairs, ";
                $monthlyOnTunes .= "$marSpentOnTunes, ";
                $monthlyOnOther .= "$marSpentOnOther, ";
            }
            elseif($month == "April"){
                $monthlyOnMods .= "$aprSpentOnMods, ";
                $monthlyOnRepairs .= "$aprSpentOnRepairs, ";
                $monthlyOnTunes .= "$aprSpentOnTunes, ";
                $monthlyOnOther .= "$aprSpentOnOther, ";
            }
            elseif($month == "May"){
                $monthlyOnMods .= "$maySpentOnMods, ";
                $monthlyOnRepairs .= "$maySpentOnRepairs, ";
                $monthlyOnTunes .= "$maySpentOnTunes, ";
                $monthlyOnOther .= "$maySpentOnOther, ";
            }
            elseif($month == "June"){
                $monthlyOnMods .= "$junSpentOnMods, ";
                $monthlyOnRepairs .= "$junSpentOnRepairs, ";
                $monthlyOnTunes .= "$junSpentOnTunes, ";
                $monthlyOnOther .= "$junSpentOnOther, ";
            }
            elseif($month == "July"){
                $monthlyOnMods .= "$julSpentOnMods, ";
                $monthlyOnRepairs .= "$julSpentOnRepairs, ";
                $monthlyOnTunes .= "$julSpentOnTunes, ";
                $monthlyOnOther .= "$julSpentOnOther, ";
            }
            elseif($month == "August"){
                $monthlyOnMods .= "$augSpentOnMods, ";
                $monthlyOnRepairs .= "$augSpentOnRepairs, ";
                $monthlyOnTunes .= "$augSpentOnTunes, ";
                $monthlyOnOther .= "$augSpentOnOther, ";
            }
            elseif($month == "September"){
                $monthlyOnMods .= "$sepSpentOnMods, ";
                $monthlyOnRepairs .= "$sepSpentOnRepairs, ";
                $monthlyOnTunes .= "$sepSpentOnTunes, ";
                $monthlyOnOther .= "$sepSpentOnOther, ";
            }
            elseif($month == "October"){
                $monthlyOnMods .= "$octSpentOnMods, ";
                $monthlyOnRepairs .= "$octSpentOnRepairs, ";
                $monthlyOnTunes .= "$octSpentOnTunes, ";
                $monthlyOnOther .= "$octSpentOnOther, ";
            }
            elseif($month == "November"){
                $monthlyOnMods .= "$novSpentOnMods, ";
                $monthlyOnRepairs .= "$novSpentOnRepairs, ";
                $monthlyOnTunes .= "$novSpentOnTunes, ";
                $monthlyOnOther .= "$novSpentOnOther, ";
            }
            elseif($month == "December"){
                $monthlyOnMods .= "$decSpentOnMods, ";
                $monthlyOnRepairs .= "$decSpentOnRepairs, ";
                $monthlyOnTunes .= "$decSpentOnTunes, ";
                $monthlyOnOther .= "$decSpentOnOther, ";
            }
        }

        $monthlyOnMods = trim($monthlyOnMods, ",");
        $monthlyOnRepairs = trim($monthlyOnRepairs, ",");
        $monthlyOnTunes = trim($monthlyOnTunes, ",");
        $monthlyOnOther = trim($monthlyOnOther, ",");
    ?>

    <!-- Code for the monthly expenses chart -->
    <script>
        // Get all the month names
        var monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"];

        var today = new Date();
        var months = []

        // Get last current month and 2 months before the current month and sort them in an array
        for (i = 2; i >= 0; i--) {
            months.push(monthNames[(today.getMonth() - i)]);
        }

        var monthly = document.getElementById('MonthlyExpensesByCategory').getContext('2d');

        // Transform legend style from rectangles to circles
        Chart.defaults.global.legend.labels.usePointStyle = true;

        // Create a line chart with all the given information
        let lineChart = new Chart(monthly, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    data: [<?php echo $monthlyOnMods?>],
                    borderColor: "#00a2ff",
                    backgroundColor: "rgba(0, 162, 255, 0.15)",
                    label: "Mods",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOnRepairs?>],
                    borderColor: "#b300ff",
                    backgroundColor: "rgba(179, 0, 255, 0.15)",
                    label: "Repairs",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOnTunes?>],
                    borderColor: "#ff003c",
                    backgroundColor: "rgba(255, 0, 60, 0.15)",
                    label: "Tunes",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOnOther?>],
                    borderColor: "#00ff37",
                    backgroundColor: "rgba(0, 255, 55, 0.15)",
                    label: "Other",
                    fill: true

                }]
            },
            options: {
                legend: {
                    display: true, 
                },
                scales: {
                    yaxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values){
                                return value + ' EUR';
                            }
                        }
                    }]
                },
                title: {
                    display: true,
                    text: "Spending in EUR for the last 3 Months",
                    fontSize: 20,
                },
                tooltips: {
                    callbacks: {
                        label: (item) => `${item.yLabel} EUR`,
                    }
                }
            }
        })
    </script>

    <!-- Function for converting the units from EUR or USD to HRK -->
    <?php
        function convertUnit($price, $tender){
            $total = 0;

            if(strtolower($tender) == "hrk"){
                $total += $price;
            }
            elseif(strtolower($tender) == "eur"){
                $price *= 7.5;
                $total += $price;
            }
            elseif(strtolower($tender) == "usd"){
                $price *= 6.5;
                $total += $price;
            }

            return $total;
        }

        // Function for converting HRK to EUR
        function convertToEur($price){
            $eur = number_format((float)$price / 7.5, 2, '.', '');

            return $eur;
        }
    ?>

    <script>
        // Sidebar toggler
        var mini = true;
        function toggleSidebar() {
            if (mini) {
            document.getElementById("mySidebar").style.width = "200px";
            // document.getElementById("main").style.marginLeft = "210px";
            this.mini = false;
        } else {
            document.getElementById("mySidebar").style.width = "70px";
            // document.getElementById("main").style.marginLeft = "80px";
            this.mini = true;
        }
        }
    </script>
</body>
</html>