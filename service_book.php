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
    <link rel="icon" type="image/png" href="img/favicon-32x32.png" sizes="32x32" />
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="service_book.css">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <title>CarData | My Service Book</title>
</head>
<body>
        <?php
        function deleteActivated(){
            echo "<div class='action-container'>";
                echo "<div class='action-column'>";
                echo "<h2><i class='fa-solid fa-circle-exclamation'></i>Deleting has been enabled</h2>";
                echo "<p>To delete data simply press the row you wish to delete and it will be gone for good. To stop deleting press the delete button again or the 'CANCEL' button.</p>";
                echo "<p>IMPORTANT: All deleted data is gone forever and cannot be returned, so delete responsibly and be careful what you click.</p>";
                echo "</div>";
                echo "<div class='action-column button-column'>";
                ?> <button onclick='location.href="service_book.php?carid=<?php echo $_GET["carid"] ?>&del=0"'>CANCEL</button>
                </div>
        <?php
            echo "</div>";

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

            if(isset($_GET["delid"])){
                $delid = $_GET["delid"];
                $delete_query = "DELETE FROM mods WHERE id = $delid";
                $delete_result = mysqli_query($userdb, $delete_query);

                $carid = $_GET["carid"];
                
                header("Refresh: 0; url=service_book.php?carid=$carid&del=1");
            }
        }

        if(isset($_GET["del"])){
            if($_GET["del"] == "1"){
                deleteActivated();
            }
        }
    ?>
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
            <div class="dropdown-mini mini-search">
                <div class="search">
                    <form action="" method="POST" autocomplete="off">
                        <div class="search-items">
                            <a href="#"><i class="fa-solid fa-magnifying-glass"></i></a>
                            <input type="text" name="search-table" id="search" placeholder="Search table" oninput=getSearchValue()>
                        </div>
                        <div class="search-items">
                            <?php
                                // if(isset($_POST["search-table"])){
                                //     if(strlen($_POST["search-table"]) > 0){
                                //         echo "<input type='submit' name='submit' id='submit' value='Reset'>";
                                //     }
                                //     else{
                                //         null;
                                //     }
                                // }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
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
                <button class="drop-btn active-btn">Service Book</button>
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
        <div class="page-name">
            <!-- <h3 class="page-name-text">Service Book</h3>
            <p class="page-name-text"><a href="index.php"><i class="fa-solid fa-house"></i></a> <span>-</span> <a href="#">My CarData</a> <span>-</span> <a href="#">Service Book</a></p> -->
        </div>
        <div class="clear"></div>
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
                                    echo "<option value='service_book.php?carid=0' selected>All cars</option>";
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
                                            echo "<option value='service_book.php?carid=$carid' selected>$year $carManuf $carModel</option>";
                                        }
                                        else{
                                            echo "<option value='service_book.php?carid=$carid'>$year $carManuf $carModel</option>";
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
                    <a id="delete-from-table" href="service_book.php?carid=<?php echo $_GET["carid"] ?>&del=<?php echo ($_GET["del"] === "1" ? "0" : "1") ?>"><i class='fa-solid fa-circle-xmark'></i></a>
                    <a href="add_service.php"><i class="fa-solid fa-plus"></i></a>
                </div>
                <!-- Table search, partly functional -->
            </div>
            <!-- Create a table -->
            <table class="mod-table">
                <thead>
                    <tr class="top-row">
                        <td class='tl'>Car name</td>
                        <td>Work type</td>
                        <td id="table-description">Description</td>
                        <td>Milage</td>
                        <td>Price</td>
                        <td class='tr'>Date</td>
                    </tr>
                </thead>
                <?php
                    echo "<tbody>";
                    // Get the car id from the url parameter
                    $carid = $_GET["carid"];
                    $search_value = "" ;

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
                        
                        // If something was written in the search bar it will check every column/row for data which contains the inputed characters
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
                                $date = date_create($row3["done_at"]);
                                $date_formated = date_format($date, "d/m/Y");

                                
                                // Unit formatting
                                if($unit == "eur"){
                                    $unit = "€";
                                }
                                else{
                                    $unit = strtoupper($unit);
                                }
                                
                                $delete_activated = $_GET["del"];

                                // Display information in table
                                echo "<tr onclick=location.href='service_book.php?carid=$carid&del=$delete_activated&delid=$repair_id'>";
                                echo "<td>$year $manuf $model</td>";
                                echo "<td>$repair</td>";
                                if(strlen($desc) < 1){
                                    echo "<td>N/A</td>";
                                }
                                else{
                                    echo "<td>$desc</td>";
                                }
                                echo "<td>$milage KM</td>";
                                echo "<td>$price $unit</td>";
                                echo "<td>$date_formated</td>";
                                // Button for deleting mods, currently disabled due to redesign
                                // echo "<td><a href='service_book.php?del=$repair_id&carid=0'><i class='fa-solid fa-circle-xmark'></i></a></td>";
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
                                $date = date_create($row["done_at"]);
                                $date_formated = date_format($date, "d/m/Y");
    
                                if($unit == "eur"){
                                    $unit = "€";
                                }
                                else{
                                    $unit = strtoupper($unit);
                                }

                                $delete_activated = $_GET["del"];
    
                                echo "<tr onclick=location.href='service_book.php?carid=$carid&del=$delete_activated&delid=$repair_id'>";
                                echo "<td>$year $manuf $model</td>";
                                echo "<td>$repair</td>";
                                echo "<td>$desc</td>";
                                echo "<td>$milage KM</td>";
                                echo "<td>$price $unit</td>";
                                echo "<td>$date_formated</td>";
                                // echo "<td><a href='service_book.php?del=$repair_id&carid=$carid'><i class='fa-solid fa-circle-xmark'></i></a></td>";
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
                            header("Refresh: 0; url=service_book.php?carid=0");
                        }
                        else{
                            header("Refresh: 0; url=service_book.php?carid=$carid");
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