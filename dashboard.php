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
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <title>CarData | Dashboard</title>
</head>
<body>
    <div class="navbar not-fixed">
        <div class="links">
            <img src="img/cardata_logo_white.png" alt="logo">
            <?php
                error_reporting(0);

                // Navigation bar items change wether the user is logged in or not
                // If the user is logged in it will show users username, logout button and it will provide access to dashboard etc.
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
                <button class="drop-btn active-btn">Dashboard</button>
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
                    <a href="add_gas.php">Add to Gas Data</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Main container -->
    <div id="main">
        <div class="clear"></div>
        <!-- <div class="page-name">
            <h3 class="page-name-text">Dashboard</h3>
            <p class="page-name-text"><a href="index.php"><i class="fa-solid fa-house"></i></a> <span>-</span> <a href="#">My CarData</a> <span>-</span> <a href="#">Dashboard</a></p>
        </div>
        <div class="clear"></div> -->
        <div class="main-content">
            <?php
                // Connect to the main database holding user information
                include("connect_server.php");
                
                // Get the current users username
                $username = htmlspecialchars($_SESSION["username"]);
                
                // Select all the data from the database where the username is current users username
                $query = "SELECT * FROM users WHERE username = '$username'";
                $result = mysqli_query($db, $query);
            ?>
        </div>
        <div class="container">
            <div class="car-data-general">
                <div class="car-data-general-content">
                    <!-- General Car Data holds some basic information, how many cars the user owns,
                    Total money spending, number of tunes/mods/repairs/other etc. -->
                    <h2>General Car Data</h2>
                    <hr>
                    <div class="general-data-container">
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

                            // Total money spent

                            $query = "SELECT * FROM mods";
                            $result = mysqli_query($userdb, $query);

                            function numberConverter($num){
                                $pf = "";

                                if($num >= 10000 && $num < 1000000){
                                    $num = numberFormatPrecision(($num / 1000));
                                    $pf = "K";
                                }
                                else if($num >= 1000000 && $num < 1000000000){
                                    $num = numberFormatPrecision(($num / 1000000));
                                    $pf = "M";
                                }
                                else if($num >= 1000000000){
                                    $num = numberFormatPrecision(($num / 1000000000));
                                    $pf = "T";
                                }

                                echo "<p>$num"."$pf"."€</p>";
                            }
                            
                            function numberFormatPrecision($number, $precision = 1, $separator = '.')
                            {
                                $numberParts = explode($separator, $number);
                                $response = $numberParts[0];
                                if (count($numberParts)>1 && $precision > 0) {
                                    $response .= $separator;
                                    $response .= substr($numberParts[1], 0, $precision);
                                }
                                return $response;
                            }
                            if(mysqli_num_rows($result) == 0){
                                echo "<div class='general-data-item'>";
                                echo "<p>Service spendings</p>";
                                echo "<h1>0€</h1>";
                                echo "<h2>0 HRK</h2>";
                                echo "</div>";
                            }
                            else{
                                $total = 0;
                            
                                while($row = mysqli_fetch_assoc($result)){
                                    $value = $row["price"];
                                    $unit = $row["unit"];
                                    
                                    // Convert all units to HRK first
                                    // This is why we let user select the currency in the service adding section
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
                                $total_in_eur = numberFormatPrecision(((float)$total / 7.5));
                                $total = numberFormatPrecision(((float)$total));
                                $total_in_eur_k = 0;
                                $postfix = "";
                                $postfix_hrk = "";
                                }


                                // Show money spent in eur and hrk
                                echo "<div class='general-data-item'>";
                                echo "<p>Service spendings</p>";
                                if($total_in_eur >= 10000 && $total_in_eur < 1000000){
                                    $total_in_eur = numberFormatPrecision(($total_in_eur / 1000));
                                    $postfix = "K";
                                }
                                else if($total_in_eur >= 1000000 && $total_in_eur < 1000000000){
                                    $total_in_eur = numberFormatPrecision(($total_in_eur / 1000000));
                                    $postfix = "M";
                                }
                                else if($total_in_eur >= 1000000000){
                                    $total_in_eur = numberFormatPrecision(($total_in_eur / 1000000000));
                                    $postfix = "T";
                                }

                                if($total >= 10000 && $total < 1000000){
                                    $total = numberFormatPrecision(($total / 1000));
                                    $postfix_hrk = "K";
                                }
                                else if($total >= 1000000 && $total < 1000000000){
                                    $total = numberFormatPrecision(($total / 1000000));
                                    $postfix_hrk = "M";
                                }
                                else if($total >= 1000000000){
                                    $total = numberFormatPrecision(($total / 1000000000));
                                    $postfix_hrk = "T";
                                }
                                echo "<h1>$total_in_eur"."$postfix"."€</h1>";
                                echo "<h2>$total"."$postfix_hrk"." HRK</h2>";
                                echo "</div>";

                                
                            }
                            $query = "SELECT * FROM gas";
                            $result = mysqli_query($userdb, $query);

                            if(mysqli_num_rows($result) == 0){
                                echo "<div class='general-data-item'>";
                                echo "<p>Gas spendings</p>";
                                echo "<h1>0€</h1>";
                                echo "<h2>0 HRK</h2>";
                                echo "</div>";
                            }
                            else{
                                $total_gas = 0;
                            
                                while($row = mysqli_fetch_assoc($result)){
                                    $value_gas = $row["price"];
                                    $unit = $row["unit"];
                                    
                                    // Convert all units to HRK first
                                    // This is why we let user select the currency in the service adding section
                                    if(strtolower($unit) == "hrk"){
                                        $total_gas += $value_gas;
                                    }
                                    elseif(strtolower($unit) == "eur"){
                                        $value_gas *= 7.5;
                                        $total_gas += $value_gas;
                                    }
                                    elseif(strtolower($unit) == "usd"){
                                        $value_gas *= 6.5;
                                        $total_gas += $value_gas;
                                    }
                                
                                // Convert to EUR
                                $total_gas_in_eur = numberFormatPrecision(((float)$total_gas / 7.5));
                                $total_gas = numberFormatPrecision(((float)$total_gas));
                                $total_gas_in_eur_k = 0;
                                $postfix_gas = "";
                                $postfix_gas_hrk = "";
                                }


                                // Show money spent in eur and hrk
                                echo "<div class='general-data-item'>";
                                echo "<p>Gas spendings</p>";
                                if($total_gas_in_eur >= 10000 && $total_gas_in_eur < 1000000){
                                    $total_gas_in_eur = numberFormatPrecision(($total_gas_in_eur / 1000));
                                    $postfix_gas = "K";
                                }
                                else if($total_gas_in_eur >= 1000000 && $total_gas_in_eur < 1000000000){
                                    $total_gas_in_eur = numberFormatPrecision(($total_gas_in_eur / 1000000));
                                    $postfix_gas = "M";
                                }
                                else if($total_gas_in_eur >= 1000000000){
                                    $total_gas_in_eur = numberFormatPrecision(($total_gas_in_eur / 1000000000));
                                    $postfix_gas = "T";
                                }

                                if($total_gas >= 10000 && $total_gas < 1000000){
                                    $total_gas = numberFormatPrecision(($total_gas / 1000));
                                    $postfix_gas_hrk = "K";
                                }
                                else if($total_gas >= 1000000 && $total_gas < 1000000000){
                                    $total_gas = numberFormatPrecision(($total_gas / 1000000));
                                    $postfix_gas_hrk = "M";
                                }
                                else if($total_gas >= 1000000000){
                                    $total_gas = numberFormatPrecision(($total_gas / 1000000000));
                                    $postfix_gas_hrk = "T";
                                }
                                echo "<h1>$total_gas_in_eur"."$postfix_gas"."€</h1>";
                                echo "<h2>$total_gas"."$postfix_gas_hrk"." HRK</h2>";
                                echo "</div>";
                            }

                            // Get number of cars

                            $query = "SELECT * FROM cars";
                            $result = mysqli_query($userdb, $query);

                            $carnum = mysqli_num_rows($result);

                            echo "<div class='general-data-item'>";
                            echo "<p>Total cars owned</p>";
                            echo "<h1>$carnum</h1>";
                            echo "</div>";

                            

                            // Get number of repairs
                            $query = "SELECT * FROM mods WHERE repair_type = 'Repair'";
                            $result = mysqli_query($userdb, $query);

                            $repairnum = mysqli_num_rows($result);

                            echo "<div class='general-data-item'>";
                            echo "<p>Repairs</p>";
                            echo "<h1>$repairnum</h1>";
                            echo "</div>";

                            // Get number of mods done
                            
                            $query = "SELECT * FROM mods WHERE repair_type = 'Mod'";
                            $result = mysqli_query($userdb, $query);

                            $modnum = mysqli_num_rows($result);

                            echo "<div class='general-data-item'>";
                            echo "<p>Mods</p>";
                            echo "<h1>$modnum</h1>";
                            echo "</div>";

                            // Get number of tunes done

                            $query = "SELECT * FROM mods WHERE repair_type = 'Tune'";
                            $result = mysqli_query($userdb, $query);

                            $tunenum = mysqli_num_rows($result);

                            echo "<div class='general-data-item'>";
                            echo "<p>Tunes</p>";
                            echo "<h1>$tunenum</h1>";
                            echo "</div>";

                            // Get number of other stuff done to the car

                            $query = "SELECT * FROM mods WHERE repair_type = 'Other'";
                            $result = mysqli_query($userdb, $query);

                            $othernum = mysqli_num_rows($result);

                            echo "<div class='general-data-item'>";
                            echo "<p>Other</p>";
                            echo "<h1>$othernum</h1>";
                            echo "</div>";
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
    
    <!-- Monthly expenses chart data -->
    <?php
        $monthlyMods = "";
        $monthlyRepairs = "";
        $monthlyTunes = "";
        $monthlyOther = "";
        $expenses_per_day = [];

        $totalMonthlyMods = 0;
        $totalMonthlyRepairs = 0;
        $totalMonthlyTunes = 0;
        $totalMonthlyOther = 0;

        // Get all the expenses on Mods for the current month
        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE MONTH(done_at) = MONTH(CURRENT_DATE()) AND YEAR(done_at) = YEAR(CURRENT_DATE()) AND repair_type = 'Mod';";
        $result = mysqli_query($userdb, $query);

        $current_month = date("m");
        $current_year = date("Y");
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

        // Add 0 for all days below 10
        for($i = 1; $i <= $days_in_month; $i++){
            $expenses_per_day[$i] = 0;
        }

        while($row = mysqli_fetch_assoc($result)){
            $done_at = $row["done_at"];
            $price = $row["price"];
            $unit = $row["unit"];
            $total = convertUnit($price, $unit);
            $total = convertToEur($total);


            $done_at_converted = date("d", strtotime($done_at));

            if($done_at_converted < 10){
                $done_at_converted = intval($done_at_converted);
            }
            
            $expenses_per_day[$done_at_converted] += $total;
        }

        foreach($expenses_per_day as $val){
            $monthlyMods .= "$val, ";
            $totalMonthlyMods += $val;
        }

        $monthlyMods.trim($monthlyMods, ",");


        // Get all the expenses on Repairs for the current month
        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE MONTH(done_at) = MONTH(CURRENT_DATE()) AND YEAR(done_at) = YEAR(CURRENT_DATE()) AND repair_type = 'Repair';";
        $result = mysqli_query($userdb, $query);

        $current_month = date("m");
        $current_year = date("Y");
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

        for($i = 1; $i <= $days_in_month; $i++){
            $expenses_per_day[$i] = 0;
        }

        while($row = mysqli_fetch_assoc($result)){
            $done_at = $row["done_at"];
            $price = $row["price"];
            $unit = $row["unit"];
            $total = convertUnit($price, $unit);
            $total = convertToEur($total);

            $done_at_converted = date("d", strtotime($done_at));
            
            if($done_at_converted < 10){
                $done_at_converted = intval($done_at_converted);
            }

            $expenses_per_day[$done_at_converted] += $total;
        }

        foreach($expenses_per_day as $val){
            $monthlyRepairs .= "$val, ";
            $totalMonthlyRepairs += $val;
        }

        $monthlyRepairs.trim($monthlyRepairs, ",");

        // Get all the expenses on Tunes for the current month
        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE MONTH(done_at) = MONTH(CURRENT_DATE()) AND YEAR(done_at) = YEAR(CURRENT_DATE()) AND repair_type = 'Tune';";
        $result = mysqli_query($userdb, $query);

        $current_month = date("m");
        $current_year = date("Y");
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

        for($i = 1; $i <= $days_in_month; $i++){
            $expenses_per_day[$i] = 0;
        }

        while($row = mysqli_fetch_assoc($result)){
            $done_at = $row["done_at"];
            $price = $row["price"];
            $unit = $row["unit"];
            $total = convertUnit($price, $unit);
            $total = convertToEur($total);

            $done_at_converted = date("d", strtotime($done_at));

            if($done_at_converted < 10){
                $done_at_converted = intval($done_at_converted);
            }

            $expenses_per_day[$done_at_converted] += $total;
        }

        foreach($expenses_per_day as $val){
            $monthlyTunes .= "$val, ";
            $totalMonthlyTunes += $val;
        }

        $monthlyTunes.trim($monthlyTunes, ",");

        // Get all the expenses on Other for the current month
        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE MONTH(done_at) = MONTH(CURRENT_DATE()) AND YEAR(done_at) = YEAR(CURRENT_DATE()) AND repair_type = 'Other';";
        $result = mysqli_query($userdb, $query);

        $current_month = date("m");
        $current_year = date("Y");
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);

        for($i = 1; $i <= $days_in_month; $i++){
            $expenses_per_day[$i] = 0;
        }

        while($row = mysqli_fetch_assoc($result)){
            $done_at = $row["done_at"];
            $price = $row["price"];
            $unit = $row["unit"];
            $total = convertUnit($price, $unit);
            $total = convertToEur($total);

            $done_at_converted = date("d", strtotime($done_at));

            if($done_at_converted < 10){
                $done_at_converted = intval($done_at_converted);
            }

            $expenses_per_day[$done_at_converted] += $total;
        }

        foreach($expenses_per_day as $val){
            $monthlyOther .= "$val, ";
            $totalMonthlyOther += $val;
        }

        $monthlyOther.trim($monthlyOther, ",");
    ?>

    <div class="car-data-charts monthly-chart">
        <!-- Create a canvas for monthly spending report -->
        <div class="car-data-monthly-content">
            <h2>Monthly Services Report</h2>
            <hr>
            <div class="chart-switchers">
                <button id="all-currentmonth">All</button>
                <button id="mods-currentmonth">Mods</button>
                <button id="repairs-currentmonth">Repairs</button>
                <button id="tunes-currentmonth">Tunes</button>
                <button id="other-currentmonth">Other</button>
            </div>
            <div class="chart-canvas">
                <canvas id="CurrentMonthExpensesByCategory" style="width: 100%; height: 500px;"></canvas>
            </div>
            <div class="info-numbers">
                <hr class="info-wrapper">
                <div class="info-content">
                    <div class="info-column top-hr">
                        <h1>Total</h1>
                        <?php numberConverter(($totalMonthlyMods + $totalMonthlyRepairs + $totalMonthlyTunes + $totalMonthlyOther)) ?>
                    </div>
                    <div class="info-column">
                        <h1>Mods</h1>
                        <?php numberConverter($totalMonthlyMods) ?>
                    </div>
                    <div class="info-column">
                        <h1>Repairs</h1>
                        <?php numberConverter($totalMonthlyRepairs) ?>
                    </div>
                    <div class="info-column">
                        <h1>Tunes</h1>
                        <?php numberConverter($totalMonthlyTunes) ?>
                    </div>
                    <div class="info-column">
                        <h1>Other</h1>
                        <?php numberConverter($totalMonthlyOther) ?>
                    </div>
                </div>
                <hr class="info-wrapper">
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

        $totalWeeklyMods = 0;
        $totalWeeklyRepairs = 0;
        $totalWeeklyTunes = 0;
        $totalWeeklyOther = 0;
        
        while($row = mysqli_fetch_assoc($result)){
            $price = $row["price"];
            $unit = $row["unit"];
            // Format the date to show day name of the given date
            $date = date_create($row["done_at"]);
            $day = date_format($date, "D");

            $conv = convertUnit($price, $unit);
            $totalWeeklyMods += $conv;

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

            $conv = convertUnit($price, $unit);
            $totalWeeklyRepairs += $conv;

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

            $conv = convertUnit($price, $unit);
            $totalWeeklyTunes += $conv;

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

            $conv = convertUnit($price, $unit);
            $totalWeeklyOther += $conv;

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

    <div class="car-data-charts weekly-chart">
        <!-- Create a canvas for weekly spending report -->
        <div class="car-data-weekly-content">
            <h2>7-Day Services Report</h2>
            <hr>
            <div class="chart-switchers">
                <button id="all">All</button>
                <button id="mods">Mods</button>
                <button id="repairs">Repairs</button>
                <button id="tunes">Tunes</button>
                <button id="other">Other</button>
            </div>
            <div class="chart-canvas">
                <canvas id="WeeklyExpensesByCategory" style="width: 100%; height: 500px;"></canvas>
            </div>
            <div class="info-numbers">
                <hr class="info-wrapper">
                <div class="info-content">
                    <?php
                        $totalWeekly = convertToEur(($totalWeeklyMods + $totalWeeklyRepairs + $totalWeeklyTunes + $totalWeeklyOther));
                        $totalWeeklyMods = convertToEur($totalWeeklyMods);
                        $totalWeeklyRepairs = convertToEur($totalWeeklyRepairs);
                        $totalWeeklyTunes = convertToEur($totalWeeklyTunes);
                        $totalWeeklyOther = convertToEur($totalWeeklyOther);
                    ?>

                    <div class="info-column top-hr">
                        <h1>Total</h1>
                        <?php numberConverter($totalWeekly) ?>
                    </div>
                    <div class="info-column">
                        <h1>Mods</h1>
                        <?php numberConverter($totalWeeklyMods) ?>
                    </div>
                    <div class="info-column">
                        <h1>Repairs</h1>
                        <?php numberConverter($totalWeeklyRepairs) ?>
                    </div>
                    <div class="info-column">
                        <h1>Tunes</h1>
                        <?php numberConverter($totalWeeklyTunes) ?>
                    </div>
                    <div class="info-column">
                        <h1>Other</h1>
                        <?php numberConverter($totalWeeklyOther) ?>
                    </div>
                </div>
                <hr class="info-wrapper">
            </div>
        </div>
    </div>

    <!-- Get the 3 month expenses data for the chart from the MySQL Database for the last 3 months of users spending activity -->
    <?php
        $monthlyOnMods = "";
        $monthlyOnRepairs = "";
        $monthlyOnTunes = "";
        $monthlyOnOther = "";

        $totalMods = 0;
        $totalRepairs = 0;
        $totalTunes = 0;
        $totalOther = 0;

        // Monthly expenses on Mods

        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE repair_type = 'Mod' AND MONTH(done_at) != MONTH(CURRENT_DATE) AND done_at >= DATE(NOW()) + INTERVAL -3 MONTH";
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

            $conv = convertUnit($price, $unit);
            $totalMods += $conv;

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

        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE repair_type = 'Repair' AND MONTH(done_at) != MONTH(CURRENT_DATE) AND done_at >= DATE(NOW()) + INTERVAL -3 MONTH";
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

            $conv = convertUnit($price, $unit);
            $totalRepairs += $conv;

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

        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE repair_type = 'Tune' AND MONTH(done_at) != MONTH(CURRENT_DATE) AND done_at >= DATE(NOW()) + INTERVAL -3 MONTH";
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

            $conv = convertUnit($price, $unit);
            $totalTunes += $conv;

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

        $query = "SELECT price, unit, done_at, repair_type FROM mods WHERE repair_type = 'Other' AND MONTH(done_at) != MONTH(CURRENT_DATE) AND done_at >= DATE(NOW()) + INTERVAL -3 MONTH";
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

            $conv = convertUnit($price, $unit);
            $totalOther += $conv;

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

        for($i = 3; $i >= 1; $i--){
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

    <div class="car-data-charts three-month-chart">
        <!-- Create a canvas for three month spending report -->
        <div class="car-data-monthly-content">
            <h2>3-Month Services Report</h2>
            <hr>
            <div class="chart-switchers">
                <button id="all-monthly">All</button>
                <button id="mods-monthly">Mods</button>
                <button id="repairs-monthly">Repairs</button>
                <button id="tunes-monthly">Tunes</button>
                <button id="other-monthly">Other</button>
            </div>
            <div class="chart-canvas">
                <canvas id="MonthlyExpensesByCategory" style="width: 100%; height: 500px;"></canvas>
            </div>
            <div class="info-numbers">
                <hr class="info-wrapper">
                <div class="info-content">
                    <?php
                        $postfix_charts = "";

                        $threeMonthTotal = convertToEur($totalMods + $totalRepairs + $totalTunes + $totalOther);
                        $totalMods = convertToEur($totalMods);
                        $totalRepairs = convertToEur($totalRepairs);
                        $totalTunes = convertToEur($totalTunes);
                        $totalOther = convertToEur($totalOther);
                    ?>

                    <div class="info-column top-hr">
                        <h1>Total</h1>
                        <?php numberConverter($threeMonthTotal) ?>
                    </div>
                    <div class="info-column">
                        <h1>Mods</h1>
                        <?php numberConverter($totalMods) ?>
                    </div>
                    <div class="info-column">
                        <h1>Repairs</h1>
                        <?php numberConverter($totalRepairs) ?>
                    </div>
                    <div class="info-column">
                        <h1>Tunes</h1>
                        <?php numberConverter($totalTunes) ?>
                    </div>
                    <div class="info-column">
                        <h1>Other</h1>
                        <?php numberConverter($totalOther) ?>
                    </div>
                </div>
                <hr class="info-wrapper">
            </div>
        </div>
    </div>

    <div class="clear"></div>

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

        </div>
    </div>

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
        let chart = new Chart(weekly, {
            type: "line",
            data: {
                labels: days,
                datasets: [{
                    data: [<?php echo $weeklyOnMods?>],
                    borderColor: "#ff0090",
                    backgroundColor: "rgba(255, 0, 144, 0.2)",
                    label: "Mods",
                    fill: true
                }, {
                    data: [<?php echo $weeklyOnRepairs?>],
                    borderColor: "#00f2ff",
                    backgroundColor: "rgba(0, 242, 255, 0.2)",
                    label: "Repairs",
                    fill: true
                }, {
                    data: [<?php echo $weeklyOnTunes?>],
                    borderColor: "#6fff00",
                    backgroundColor: "rgba(111, 255, 0, 0.2)",
                    label: "Tunes",
                    fill: true
                }, {
                    data: [<?php echo $weeklyOnOther?>],
                    borderColor: "#ff8400",
                    backgroundColor: "rgba(255, 132, 0, 0.2)",
                    label: "Other",
                    fill: true
                }]
            },
            options: {
                legend: {
                    display: false, 
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values){
                                return value.toFixed(0) + ' EUR';
                            },
                            fontColor: "rgb(245,245,245)"
                        },
                        gridLines: {
                            color: "rgba(0,0,0,0)",
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            color: "rgba(0,0,0,0)",
                        },
                        ticks: {
                            fontColor: "rgb(245,245,245)"
                        }
                    }]
                },
                title: {
                    display: false,
                    fontSize: 20,
                },
                toolTips: {
                    callbacks: {
                        label: (item) => `${item.yLabel} €`,
                    }
                }
            }
        });

        document.getElementById("mods").addEventListener('click', () => {
            chart.config.data = {
                labels: days,
                datasets: [{
                    data: [<?php echo $weeklyOnMods ?>],
                    borderColor: "#ff0090",
                    backgroundColor: "rgba(255, 0, 144, 0.2)",
                    label: "Mods",
                    fill: true,
                }]
            }
            chart.update();
        });
        document.getElementById("repairs").addEventListener('click', () => {
            chart.config.data = {
                labels: days,
                datasets: [{
                    data: [<?php echo $weeklyOnRepairs?>],
                    borderColor: "#00f2ff",
                    backgroundColor: "rgba(0, 242, 255, 0.2)",
                    label: "Repairs",
                    fill: true,
                }]
            }
            chart.update();
        });
        document.getElementById("tunes").addEventListener('click', () => {
            chart.config.data = {
                labels: days,
                datasets: [{
                    data: [<?php echo $weeklyOnTunes?>],
                    borderColor: "#6fff00",
                    backgroundColor: "rgba(111, 255, 0, 0.2)",
                    label: "Tunes",
                    fill: true,
                }]
            }
            chart.update();
        });
        document.getElementById("other").addEventListener('click', () => {
            chart.config.data = {
                labels: days,
                datasets: [{
                    data: [<?php echo $weeklyOnOther?>],
                    borderColor: "#ff8400",
                    backgroundColor: "rgba(255, 132, 0, 0.2)",
                    label: "Other",
                    fill: true,
                }]
            }
            chart.update();
        });
        document.getElementById("all").addEventListener('click', () => {
            chart.config.data = {
                labels: days,
                datasets: [{
                    data: [<?php echo $weeklyOnMods?>],
                    borderColor: "#ff0090",
                    backgroundColor: "rgba(255, 0, 144, 0.2)",
                    label: "Mods",
                    fill: true
                }, {
                    data: [<?php echo $weeklyOnRepairs?>],
                    borderColor: "#00f2ff",
                    backgroundColor: "rgba(0, 242, 255, 0.2)",
                    label: "Repairs",
                    fill: true
                }, {
                    data: [<?php echo $weeklyOnTunes?>],
                    borderColor: "#6fff00",
                    backgroundColor: "rgba(111, 255, 0, 0.2)",
                    label: "Tunes",
                    fill: true
                }, {
                    data: [<?php echo $weeklyOnOther?>],
                    borderColor: "#ff8400",
                    backgroundColor: "rgba(255, 132, 0, 0.2)",
                    label: "Other",
                    fill: true
                }]
            }
            chart.update();
        });
    </script>

    <!-- Monthly chart -->
    <script>
        let current = new Date();

        const daysInThisMonth = new Date(current.getFullYear(), current.getMonth()+1, 0).getDate();

        
        days_array = [];
        for(let i = 1; i <= daysInThisMonth; i++){
            days_array.push(i);
        }

        days_array.join(',');

        var currentMonth = document.getElementById('CurrentMonthExpensesByCategory').getContext('2d');

        Chart.defaults.global.legend.labels.usePointStyle = true;

        // Create a bar chart with the data gathered with the previous code
        let currentMonthChart = new Chart(currentMonth, {
            type: "line",
            data: {
                labels: days_array,
                datasets: [{
                    data: [<?php echo $monthlyMods?>],
                    borderColor: "#ff0090",
                    backgroundColor: "rgba(255, 0, 144, 0.2)",
                    label: "Mods",
                    fill: true
                }, {
                    data: [<?php echo $monthlyRepairs?>],
                    borderColor: "#00f2ff",
                    backgroundColor: "rgba(0, 242, 255, 0.2)",
                    label: "Repairs",
                    fill: true
                }, {
                    data: [<?php echo $monthlyTunes?>],
                    borderColor: "#6fff00",
                    backgroundColor: "rgba(111, 255, 0, 0.2)",
                    label: "Tunes",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOther?>],
                    borderColor: "#ff8400",
                    backgroundColor: "rgba(255, 132, 0, 0.2)",
                    label: "Other",
                    fill: true
                }]
            },
            options: {
                legend: {
                    display: false, 
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values){
                                return value.toFixed(0) + ' EUR';
                            },
                            fontColor: "rgb(245,245,245)"
                        },
                        gridLines: {
                            color: "rgba(0,0,0,0)",
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            color: "rgba(0,0,0,0)",
                        },
                        ticks: {
                            fontColor: "rgb(245,245,245)"
                        }
                    }]
                },
                title: {
                    display: false,
                    fontSize: 20,
                },
                toolTips: {
                    callbacks: {
                        label: (item) => `${item.yLabel} €`,
                    }
                }
            }
        });

        document.getElementById("mods-currentmonth").addEventListener('click', () => {
            currentMonthChart.config.data = {
                labels: days_array,
                datasets: [{
                    data: [<?php echo $monthlyMods ?>],
                    borderColor: "#ff0090",
                    backgroundColor: "rgba(255, 0, 144, 0.2)",
                    label: "Mods",
                    fill: true,
                }]
            }
            currentMonthChart.update();
        });

        document.getElementById("repairs-currentmonth").addEventListener('click', () => {
            currentMonthChart.config.data = {
                labels: days_array,
                datasets: [{
                    data: [<?php echo $monthlyRepairs ?>],
                    borderColor: "#00f2ff",
                    backgroundColor: "rgba(0, 242, 255, 0.2)",
                    label: "Repairs",
                    fill: true,
                }]
            }
            currentMonthChart.update();
        });

        document.getElementById("tunes-currentmonth").addEventListener('click', () => {
            currentMonthChart.config.data = {
                labels: days_array,
                datasets: [{
                    data: [<?php echo $monthlyTunes ?>],
                    borderColor: "#6fff00",
                    backgroundColor: "rgba(111, 255, 0, 0.2)",
                    label: "Tunes",
                    fill: true,
                }]
            }
            currentMonthChart.update();
        });

        document.getElementById("other-currentmonth").addEventListener('click', () => {
            currentMonthChart.config.data = {
                labels: days_array,
                datasets: [{
                    data: [<?php echo $monthlyOther ?>],
                    borderColor: "#ff8400",
                    backgroundColor: "rgba(255, 132, 0, 0.2)",
                    label: "Other",
                    fill: true,
                }]
            }
            currentMonthChart.update();
        });

        document.getElementById("all-currentmonth").addEventListener('click', () => {
            currentMonthChart.config.data = {
                labels: days_array,
                datasets: [{
                    data: [<?php echo $monthlyMods?>],
                    borderColor: "#ff0090",
                    backgroundColor: "rgba(255, 0, 144, 0.2)",
                    label: "Mods",
                    fill: true
                }, {
                    data: [<?php echo $monthlyRepairs?>],
                    borderColor: "#00f2ff",
                    backgroundColor: "rgba(0, 242, 255, 0.2)",
                    label: "Repairs",
                    fill: true
                }, {
                    data: [<?php echo $monthlyTunes?>],
                    borderColor: "#6fff00",
                    backgroundColor: "rgba(111, 255, 0, 0.2)",
                    label: "Tunes",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOther?>],
                    borderColor: "#ff8400",
                    backgroundColor: "rgba(255, 132, 0, 0.2)",
                    label: "Other",
                    fill: true
                }]
            }
            currentMonthChart.update();
        });
    </script>

    <!-- Code for the 3 month expenses chart -->
    <script>
        // Get all the month names
        var monthNames = ["January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"];

        var today = new Date();
        var months = []

        // Get last current month and 2 months before the current month and sort them in an array
        for (i = 3; i >= 1; i--) {
            months.push(monthNames[(today.getMonth() - i)]);
        }

        var monthly = document.getElementById('MonthlyExpensesByCategory').getContext('2d');

        // Transform legend style from rectangles to circles
        Chart.defaults.global.legend.labels.usePointStyle = true;

        // Create a line chart with all the given information
        let monthlyChart = new Chart(monthly, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    data: [<?php echo $monthlyOnMods?>],
                    borderColor: "#ff0090",
                    backgroundColor: "rgba(255, 0, 144, 0.2)",
                    label: "Mods",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOnRepairs?>],
                    borderColor: "#00f2ff",
                    backgroundColor: "rgba(0, 242, 255, 0.2)",
                    label: "Repairs",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOnTunes?>],
                    borderColor: "#6fff00",
                    backgroundColor: "rgba(111, 255, 0, 0.2)",
                    label: "Tunes",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOnOther?>],
                    borderColor: "#ff8400",
                    backgroundColor: "rgba(255, 132, 0, 0.2)",
                    label: "Other",
                    fill: true

                }]
            },
            options: {
                legend: {
                    display: false, 
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false,
                            callback: function(value, index, values){
                                return value.toFixed(0) + ' EUR';
                            },
                            fontColor: "rgb(245,245,245)"
                        },
                        gridLines: {
                            color: "rgba(0,0,0,0)",
                        }
                    }],
                    xAxes: [{
                        gridLines: {
                            color: "rgba(0,0,0,0)",
                        },
                        ticks: {
                            fontColor: "rgb(245,245,245)"
                        }
                    }]
                },
                title: {
                    display: false,
                    text: "Spending in EUR for the last 3 Months",
                    fontSize: 20,
                },
                toolTips: {
                    callbacks: {
                        label: (item) => `${item.yLabel} EUR`,
                    }
                }
            }
        });

        document.getElementById("all-monthly").addEventListener('click', () => {
            monthlyChart.config.data = {
                labels: months,
                datasets: [{
                    data: [<?php echo $monthlyOnMods?>],
                    borderColor: "#ff0090",
                    backgroundColor: "rgba(255, 0, 144, 0.2)",
                    label: "Mods",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOnRepairs?>],
                    borderColor: "#00f2ff",
                    backgroundColor: "rgba(0, 242, 255, 0.2)",
                    label: "Repairs",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOnTunes?>],
                    borderColor: "#6fff00",
                    backgroundColor: "rgba(111, 255, 0, 0.2)",
                    label: "Tunes",
                    fill: true
                }, {
                    data: [<?php echo $monthlyOnOther?>],
                    borderColor: "#ff8400",
                    backgroundColor: "rgba(255, 132, 0, 0.2)",
                    label: "Other",
                    fill: true

                }]
            }
            monthlyChart.update()
        });
        document.getElementById("mods-monthly").addEventListener('click', () => {
            monthlyChart.config.data = {
                labels: months,
                datasets: [{
                    data: [<?php echo $monthlyOnMods?>],
                    borderColor: "#ff0090",
                    backgroundColor: "rgba(255, 0, 144, 0.2)",
                    label: "Mods",
                    fill: true
                }]
            }
            monthlyChart.update();
        });
        document.getElementById("repairs-monthly").addEventListener('click', () => {
            monthlyChart.config.data = {
                labels: months,
                datasets: [{
                    data: [<?php echo $monthlyOnRepairs?>],
                    borderColor: "#00f2ff",
                    backgroundColor: "rgba(0, 242, 255, 0.2)",
                    label: "Repairs",
                    fill: true
                }]
            }
            monthlyChart.update();
        });
        document.getElementById("tunes-monthly").addEventListener('click', () => {
            monthlyChart.config.data = {
                labels: months,
                datasets: [{
                    data: [<?php echo $monthlyOnTunes?>],
                    borderColor: "#6fff00",
                    backgroundColor: "rgba(111, 255, 0, 0.2)",
                    label: "Tunes",
                    fill: true
                }]
            }
            monthlyChart.update();
        });
        document.getElementById("other-monthly").addEventListener('click', () => {
            monthlyChart.config.data = {
                labels: months,
                datasets: [{
                    data: [<?php echo $monthlyOnOther?>],
                    borderColor: "#ff8400",
                    backgroundColor: "rgba(255, 132, 0, 0.2)",
                    label: "Other",
                    fill: true
                }]
            }
            monthlyChart.update();
        });
    </script>

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