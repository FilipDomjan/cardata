<?php
    session_start();
    
    // Check if the user is not logged in, then redirect the user to login page
    if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
        header("location: users/login.php");
        exit;
    }

    // carid variable is used to sort the table
    // Starting value is set to 0, meaning it will show all cars
    // If this value is changed it will only show car which has that id in the database
    $carid = "0";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="carmods.css">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <title>CarData | Mods</title>
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

    <!-- Main container -->
    <div id="main">
        <div class="page-name">
            <h3 class="page-name-text">Car Mods</h3>
            <p class="page-name-text"><a href="index.php"><i class="fa-solid fa-house"></i></a> <span>-</span> <a href="#">My CarData</a> <span>-</span> <a href="#">Car Mods</a></p>
            <div class="search">
                <form action="" method="POST">
                    <div class="search-items">
                        <a href="#"><i class="fa-solid fa-magnifying-glass"></i></a>
                        <input type="text" name="search-table" id="search" placeholder="Search table">
                    </div>
                    <div class="search-items">
                        <?php
                            if(isset($_POST["search-table"])){
                                if(strlen($_POST["search-table"]) > 0){
                                    echo "<input type='submit' name='submit' id='submit' value='Reset'>";
                                }
                                else{
                                    null;
                                }
                            }
                        ?>
                    </div>
                </form>
            </div>
        </div>
        <div class="clear"></div>
        <nav class="sidebar" id="mySidebar" onmouseover="toggleSidebar()" onmouseout="toggleSidebar()">
            <div class="sidebar-items">
                <a href="dashboard.php"><i class="fa-solid fa-gauge-simple"></i><span>Dashboard</span></a>
                <a href="mycars.php?"><i class="fa-solid fa-car"></i><span>My Cars</span></a>
                <a href="#"><i class="fa-solid fa-screwdriver-wrench"></i><span>Car Mods</span></a>
                <a href="addcar.php"><i class="fa-solid fa-circle-plus"></i><span>Add a car</span></a>
            </div>
        </nav>
        <!-- Car selector for sorting table -->
        <div class="table">
            <div class="table-tools">
                <div class="car-selector">
                    <form action="" method="GET">
                        <select name="carSelector" id="carSelect" onchange="window.location=this.value">
                            <?php
                                // Get the username for connection to database
                                // database_name = username
                                $username = htmlspecialchars($_SESSION["username"]);
        
                                // Connect to the database
                                $server = 'localhost';
                                $user = 'root';
                                $password = '';
                                $database = "$username";
                                
                                $userdb = mysqli_connect($server, $user, $password, $database);
                                
                                if($userdb -> connect_errno){
                                    echo "Failed to connect to MySQL: ".$userdb -> connect_error;
                                    exit();
                                }
                                
                                // Get all the users cars
                                $query = "SELECT id, manufacturer, model, model_year FROM cars";
                                $result = mysqli_query($userdb, $query);
                                
                                // If no cars are found in the database show "No cars" in the selector
                                if(mysqli_num_rows($result) == 0){
                                    echo "<option value='' selected disabled>";
                                    echo "No cars";
                                    echo "</option>";
                                }
                                // If there are cars detected show them in the selector
                                else{
                                    // First option is All cars which sets the carid to 0 and shows all cars
                                    echo "<option value='carmods.php?carid=0' selected>All cars</option>";
                                    while($row = mysqli_fetch_assoc($result)){
                                        $carManuf = $row["manufacturer"];
                                        $carModel = $row["model"];
                                        $year = $row["model_year"];
                                        $carid = $row["id"];
                                        
                                        // Without this if a user sorts by a car, and deletes something from the table it
                                        // will reset and show all cars right away, we don't want that, we want to stay sorted
                                        // This variable ensures that it stays sorted
                                        $pageID = $_GET["carid"];
        
        
                                        if($carid == $pageID){
                                            echo "<option value='carmods.php?carid=$carid' selected>$year $carManuf $carModel</option>";
                                        }
                                        else{
                                            echo "<option value='carmods.php?carid=$carid'>$year $carManuf $carModel</option>";
                                        }
                                    }
                                }
        
                                
                            ?>
                        </select>
                    </form>
                </div>
                <!-- Table editing tools: Edit information, delete data, add new data -->
                <div class="table-editors">
                    <a id="edit-table"><i class="fa-solid fa-pen"></i></a>
                    <a id="delete-from-table"><i class='fa-solid fa-circle-xmark'></i></a>
                    <a href="addmod.php"><i class="fa-solid fa-gear"></i></a>
                </div>
                <!-- Table search, partly functional -->
            </div>
            <!-- Create a table -->
            <table class="mod-table">
                <thead>
                    <tr class="top-row">
                        <td class='tl'><b>Car name</b></td>
                        <td><b>Work type</b></td>
                        <td><b>Description</b></td>
                        <td><b>Milage</b></td>
                        <td><b>Price</b></td>
                        <td class='tr'><b>Date</b></td>
                    </tr>
                </thead>
                <?php
                    echo "<tbody>";
                    // Get the car id from the url parameter
                    $carid = $_GET["carid"];
                    $search_value = "";

                    if(isset($_POST["search-table"]))
                    {
                        empty($search_value);
                        $search_value = $_POST["search-table"];
                    }
                    if(isset($_POST["submit"])){
                        $search_value = "";
                    }
                    
                    // If the car id is 0, then show mods from all cars
                    if($carid == "0"){
                        
                        if(strlen($search_value) > 0){
                            $query3 = "SELECT mods.id AS mod_id, carid, repair_type, description, milage, price, unit, done_at FROM mods 
                            JOIN cars ON cars.id = mods.carid WHERE cars.manufacturer LIKE '%$search_value%' OR cars.model LIKE '%$search_value%' OR cars.model_year LIKE '%$search_value%'
                            OR repair_type LIKE '%$search_value%' OR description LIKE '%$search_value%' OR milage LIKE '%$search_value%' OR price LIKE '%$search_value%'
                            OR done_at LIKE '%$search_value%'";
                            $result3 = mysqli_query($userdb, $query3);
                        }else{
                            $query3 = "SELECT mods.id AS mod_id, carid, repair_type, description, milage, price, unit, done_at FROM mods ORDER BY done_at DESC";
                            $result3 = mysqli_query($userdb, $query3);
                        }
                        // Get all the mods from the database

                        // If there are no mods detected show a message
                        if(mysqli_num_rows($result3) < 1){
                            echo "<tr>";
                            echo "<td colspan=6>No mods/repairs done yet</td>";
                            echo "</tr>";
                        }
                        // Else show all mods in the table
                        else{
                            while ($row3 = mysqli_fetch_assoc($result3)){
                                
                                // This part will get the car name, type and model year from the "cars" database
                                // This is why we used the id as value in the form for adding mods/repairs/tunes/other
                                $car_id = $row3["carid"];
                                
                                $query4 = "SELECT * FROM cars WHERE id=$car_id";
                                $result4 = mysqli_query($userdb, $query4);
    
                                while($row4 = mysqli_fetch_assoc($result4)){
                                    $manuf = $row4["manufacturer"];
                                    $model = $row4["model"];
                                    $year = $row4["model_year"];
                                }
                                
                                // Get all the information from the table (for each row in the table)
                                $repair_id = $row3["mod_id"];
                                $repair = $row3["repair_type"];
                                $desc = $row3["description"];
                                $milage = $row3["milage"];
                                $price = $row3["price"];
                                $unit = $row3["unit"];
                                $date = $row3["done_at"];
                                
                                // Unit formatting
                                if($unit == "eur"){
                                    $unit = "€";
                                }
                                else{
                                    $unit = strtoupper($unit);
                                }
                                
                                // Display information in table
                                echo "<tr>";
                                echo "<td>$year $manuf $model</td>";
                                echo "<td>$repair</td>";
                                echo "<td>$desc</td>";
                                echo "<td>$milage KM</td>";
                                echo "<td>$price $unit</td>";
                                echo "<td>$date</td>";
                                // Button for deleting mods, currently disabled due to redesign
                                // echo "<td><a href='carmods.php?del=$repair_id&carid=0'><i class='fa-solid fa-circle-xmark'></i></a></td>";
                                echo "</tr>";
                            }
                        }

    
                    }
                    else{
                        $query = "SELECT mods.id AS mod_id, carid, repair_type, description, milage, price, unit, done_at FROM mods WHERE carid=$carid ORDER BY done_at DESC";
                        $result = mysqli_query($userdb, $query);
                        
                        if(mysqli_num_rows($result) == 0){
                            echo "<tr>";
                            echo "<td colspan=6>No mods/repairs done to this car</td>";
                            echo "</tr>";
                        }
                        else{
                            // Same process as for all cars, just filtering only selected car
                            while($row = mysqli_fetch_assoc($result)){
                                $query5 = "SELECT * FROM cars WHERE id=$carid";
                                $result5 = mysqli_query($userdb, $query5);
    
                                while($row5 = mysqli_fetch_assoc($result5)){
                                    $manuf = $row5["manufacturer"];
                                    $model = $row5["model"];
                                    $year = $row5["model_year"];
                                }
                                
                                $repair_id = $row["mod_id"];
                                $repair = $row["repair_type"];
                                $desc = $row["description"];
                                $milage = $row["milage"];
                                $price = $row["price"];
                                $unit = $row["unit"];
                                $date = $row["done_at"];
    
                                if($unit == "eur"){
                                    $unit = "€";
                                }
                                else{
                                    $unit = strtoupper($unit);
                                }
    
                                echo "<tr>";
                                echo "<td>$year $manuf $model</td>";
                                echo "<td>$repair</td>";
                                echo "<td>$desc</td>";
                                echo "<td>$milage KM</td>";
                                echo "<td>$price $unit</td>";
                                echo "<td>$date</td>";
                                // echo "<td><a href='carmods.php?del=$repair_id&carid=$carid'><i class='fa-solid fa-circle-xmark'></i></a></td>";
                                echo "</tr>";
                            }
                        }
                    }
                    echo "</tbody>";
                    // Code for deleting mods
                    if(isset($_GET["del"])){
                        // If delete button is pressed, get the buttons value which is mods' id in the table
                        // And delete the said mod from the database
                        $del = $_GET["del"];
                        $query = "DELETE FROM mods WHERE id = $del";
                        $result = mysqli_query($userdb, $query);
                        
                        // This code ensures that we stay sorted once the mod has been deleted
                        if($carid == 0){
                            header("Refresh: 0; url=carmods.php?carid=0");
                        }
                        else{
                            header("Refresh: 0; url=carmods.php?carid=$carid");
                        }
                    }
                    else{
                        $query = null;
                    }
                    ?>
            </table>
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
    </div>

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