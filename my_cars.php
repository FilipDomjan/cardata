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
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="my_cars.css">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <title>CarData | My car collection</title>
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
                <button class="drop-btn active-btn">My Cars</button>
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
    
    <div id="main">
        <div class="clear"></div>
        <!-- <div class="page-name">
            <h3 class="page-name-text">My Cars</h3>
            <p class="page-name-text"><a href="index.php"><i class="fa-solid fa-house"></i></a> <span>-</span> <a href="#">My CarData</a> <span>-</span> <a href="#">My Cars</a></p>
        </div> -->
        <div class="buttons">
                <a href="my_cars.php?extra=1"><i class="fa-solid fa-list"></i></a>
                <a href="my_cars.php?extra=0" class="simple-view"><i class="fa-regular fa-square"></i></a>
        </div>
        <div class="clear"></div>
        <div class="main-content">
                <?php
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

                    if(isset($_GET["delcar"])){
                        $delcar = $_GET["delcar"];
    
                        $delete = "DELETE FROM mods WHERE carid = $delcar";
                        $delete_result = mysqli_query($userdb, $delete);

                        $delete = "DELETE FROM gas WHERE car_id = $delcar";
                        $delete_result = mysqli_query($userdb, $delete);

                        $delete = "DELETE FROM cars WHERE id = $delcar";
                        $delete_result = mysqli_query($userdb, $delete);
                        
                        header("Refresh: 0; url=my_cars.php");
    
                    }else{
                        $deletecar = null;
                    }

                    if(isset($_GET["extra"])){
                        $extra = $_GET["extra"];
                        $_SESSION["extra"] = $extra;
                    }

                    if($_SESSION["extra"] == 0){
                        echo "<div class='mycars-container'>";
                        $query = "SELECT id, manufacturer, model, model_year FROM cars ORDER BY manufacturer";
                        $result = mysqli_query($userdb, $query);
    
                        while($row = mysqli_fetch_assoc($result)){
                            $carManuf = $row["manufacturer"];
                            $carModel = $row["model"];
                            $modelYear = $row["model_year"];
                            $carid = $row["id"];
                            
                            echo "<div class='mycar-item'>";
                            echo "<div class='mycar-item-content'>";
                            echo "<div class='mycar-icon'>";
                            echo "<i class='fa-solid fa-car'></i>";
                            echo "</div>";
                            echo "<div class='mycar-name'>";
                            echo "<p>$modelYear $carManuf $carModel</p>";
                            echo "</div>";
                            echo "</div>";
                            echo '<a class="delete-car" href="my_cars.php?pd='.$carid.'"><i class="fa-solid fa-circle-xmark"></i></a>';
                            echo "</div>";
                            }
                            
                        echo "<div class='mycar-item add-new-car'"."onclick="."location.href='add_car.php'>";
                            echo "<div class='mycar-item-content'>";
                                echo "<i class='fa-solid fa-car'></i>";
                                echo "<p>Add new car <span><i class='fa-solid fa-plus'></i></span></p>";
                            echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                        
                    else{
                        
                        echo "<div class='mycars-extended-container'>";
                        $query = "SELECT cars.id as car_id, manufacturer, model, chassis, color, vinyl, model_year, engine_config, engine_capacity, horsepower, fuel_type, transmission, drivetrain FROM cars";
                        $result = mysqli_query($userdb, $query);

                        while($row = mysqli_fetch_assoc($result)){
                            $carid = $row["car_id"];
                            $carManuf = $row["manufacturer"];
                            $carModel = $row["model"];
                            $cartype = $row["chassis"];
                            $color = $row["color"];
                            $vinyl = $row["vinyl"];
                            $year = $row["model_year"];
                            $engine = $row["engine_config"];
                            $enginecap = $row["engine_capacity"];
                            $hp = $row["horsepower"];
                            $fueltype = $row["fuel_type"];
                            $trans = $row["transmission"];
                            $drivetrain = $row["drivetrain"];

                            if(empty($vinyl)){
                                $vinyl = "None";
                            }

                            
                            echo "<div class='car-data-container'>";
                                echo "<div class='car-data-content'>";
                                    echo "<div class='car-name-delete'>";
                                    echo "<h2>$year $carManuf $carModel</h2>";
                                    echo "<a href='my_cars.php?pd=$carid'>Delete</a>";
                                    echo "<div class='clear'></div>";
                                    echo "</div>";
                                    echo "<hr>";
                                    echo "<div class='extra-row'>";
                                        echo "<div class='row-mmy'>";
                                        echo "<h4>Manufacturer</h4>";
                                        echo "<p>$carManuf</p>";
                                        echo "</div>";
                                        echo "<div class='row-mmy'>";
                                        echo "<h4>Model</h4>";
                                        echo "<p>$carModel</p>";
                                        echo "</div>";
                                        echo "<div class='row-mmy'>";
                                        echo "<h4>Model Year</h4>";
                                        echo "<p>$year</p>";
                                        echo "</div>";
                                    echo "</div>";
                                    echo "<div class='extra-row'>";
                                        echo "<div class='row-cc'>";
                                            echo "<h4>Car Type</h4>";
                                            echo "<p>$cartype</p>";
                                        echo "</div>";
                                        echo "<div class='row-cc'>";
                                            echo "<h4>Color</h4>";
                                            echo "<p>$color</p>";
                                        echo "</div>";
                                        echo "<div class='row-cc'>";
                                            echo "<h4>Vinyls</h4>";
                                            echo "<p>$vinyl</p>";
                                        echo "</div>";
                                    echo "</div>";
                                    echo "<div class='extra-row'>";
                                        echo "<div class='row-eeh'>";
                                            echo "<h4>Engine type</h4>";
                                            echo "<p>$engine</p>";
                                        echo "</div>";
                                        echo "<div class='row-eeh'>";
                                            echo "<h4>Engine Capacity</h4>";
                                            echo "<p>$enginecap cm3</p>";
                                        echo "</div>";
                                        echo "<div class='row-eeh'>";
                                            $hp_in_kw = number_format((float)$hp / 1.341, 0, '.', '');;
                                            echo "<h4>Horsepower</h4>";
                                            echo "<p>$hp_in_kw kW / $hp HP</p>";
                                        echo "</div>";
                                    echo "</div>";
                                    echo "<div class='extra-row bottom-row'>";
                                        echo "<div class='row-ftd'>";
                                            echo "<h4>Fuel type</h4>";
                                            echo "<p>$fueltype</p>";
                                        echo "</div>";
                                        echo "<div class='row-ftd'>";
                                            echo "<h4>Transmission</h4>";
                                            if($trans == "manual"){
                                                echo "<p>Manual</p>";
                                            }
                                            elseif($trans == "automatic"){
                                                echo "<p>Automatic</p>";
                                            }
                                            elseif($trans == "semi-auto"){
                                                echo "<p>Semi-Automatic</p>";
                                            }
                                        echo "</div>";
                                        echo "<div class='row-ftd'>";
                                            echo "<h4>Drivetrain</h4>";
                                            if($drivetrain == "rwd"){
                                                echo "<p>Rear Wheel Drive (RWD)</p>";
                                            }
                                            elseif($drivetrain == "fwd"){
                                                echo "<p>Front Wheel Drive (FWD)</p>";
                                            }
                                            elseif($drivetrain == "4wd"){
                                                echo "<p>4-Wheel Drive (4WD)</p>";
                                            }
                                            elseif($drivetrain == "awd"){
                                                echo "<p>All-Wheel Drive (AWD)</p>";
                                            }
                                        echo "</div>";
                                    echo "</div>";
                                echo "</div>";
                            echo "</div>";
                        }
                        
                        echo "<div class='clear'></div>";
                        echo "<div class='mycar-item-large add-new-car-extra'"."onclick="."location.href='add_car.php'>";
                            echo "<div class='mycar-item-large-content'>";
                                echo "<i class='fa-solid fa-car'></i>";
                                echo "<p>Add new car <span><i class='fa-solid fa-plus'></i></span></p>";
                            echo "</div>";
                        echo "</div>";
                        echo "</div>";
                }
                ?>
            </div>
    </div>

    <?php
        if(isset($_GET["pd"])){
            $delete_id = $_GET["pd"];

            $query = "SELECT manufacturer, model, model_year FROM cars WHERE id = $delete_id";
            $result = mysqli_query($userdb, $query);

            $row = mysqli_fetch_assoc($result);

            $manufacturer = $row["manufacturer"];
            $model =  $row["model"];
            $model_year = $row["model_year"];

            echo "<div class='confirm-delete'>";
                echo "<p>Are you sure you want to delete <u>$model_year $manufacturer $model</u>?</p>";
                echo "<div class='confirm-buttons'>";
                    echo '<button class="proceed-delete" onclick="location.href=\'my_cars.php?delcar='.$delete_id.'\'">Delete</button>';
                    echo '<button class="cancel-delete" onclick="location.href=\'my_cars.php\'">Cancel</button>';
                echo "</div>";
            echo "</div>";
        }
        else{
            null;
        }
    ?>

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