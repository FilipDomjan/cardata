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
    <link rel="stylesheet" href="mycars.css">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <title>CarData | My Cars</title>
</head>
<body>
    <nav class="sidebar" id="mySidebar" onmouseover="toggleSidebar()" onmouseout="toggleSidebar()">
            <a href="index.php"><i class="fa-solid fa-house"></i><span>Home</span></a>
            <a href="home.php"><i class="fa-solid fa-gauge-simple"></i><span>Dashboard</span></a>
            <a href="#"><i class="fa-solid fa-car"></i><span>My Cars</span></a>
            <a href="carmods.php?carid=0"><i class="fa-solid fa-screwdriver-wrench"></i><span>Car Mods</span></a>
            <a href="addcar.php"><i class="fa-solid fa-circle-plus"></i><span>Add a car</span></a>
            <a><i class="fa-solid fa-user"></i><span><?php echo htmlspecialchars($_SESSION["username"]) ?></span></a>
            <a href="users/logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </nav>

    <div id="main">
        <div class="main-content">
            <h1>My Cars</h1>
            <hr>
            <div class="buttons">
                <a href="mycars.php?extra=1">Advanced view</a>
                <a href="mycars.php?extra=0" class="simple-view">Simple view</a>
            </div>
            <div class="mycars-container">
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
    
                        $deletecar = "DELETE FROM cars WHERE id = $delcar";
                        $delete_result = mysqli_query($userdb, $deletecar);

                        header("Refresh: 0; url=mycars.php");
    
                    }else{
                        $deletecar = null;
                    }

                    if(isset($_GET["extra"])){
                        $_SESSION["extra"] = $_GET["extra"]; 
                    }

                    if($_SESSION["extra"] == 0){
    
                        $query = "SELECT id, manufacturer, model, model_year FROM cars";
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
                            echo '<a class="delete-car" href="mycars.php?delcar='.$carid.'"><i class="fa-solid fa-circle-xmark"></i></a>';
                            echo "</div>";
                            }
                            
                        echo "<div class='mycar-item add-new-car'"."onclick="."location.href='addcar.php'>";
                            echo "<div class='mycar-item-content'>";
                                echo "<i class='fa-solid fa-car'></i>";
                                echo "<p>Add new car <span><i class='fa-solid fa-plus'></i></span></p>";
                            echo "</div>";
                        echo "</div>";
                        echo "<div class='clear'></div>";
                    }
                        
                    else{
    
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
                                    echo "<a href='mycars.php?delcar=$carid'>Delete</a>";
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
                        echo "<div class='mycar-item-large add-new-car-extra'"."onclick="."location.href='addcar.php'>";
                            echo "<div class='mycar-item-large-content'>";
                                echo "<i class='fa-solid fa-car'></i>";
                                echo "<p>Add new car <span><i class='fa-solid fa-plus'></i></span></p>";
                            echo "</div>";
                        echo "</div>";
                        echo "<div class='clear'></div>";
                }
                ?>
            </div>
        </div>
    </div>

    <script>
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