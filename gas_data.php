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
    <link rel="stylesheet" href="gas_data.css">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <title>CarData | Gas Data</title>
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
                ?> <button onclick='location.href="gas_data.php?del=0"'>CANCEL</button>
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
                $delete_query = "DELETE FROM gas WHERE id = $delid";
                $delete_result = mysqli_query($userdb, $delete_query);

                $carid = $_GET["carid"];
                
                header("Refresh: 0; url=gas_data.php?del=1");
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
                <button class="drop-btn active-btn">Gas Data</button>
                <div class="dropdown-mini-content">
                    <a href="add_gas.php">Add to Gas Data</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main container -->
    <div id="main">
        <div class="clear"></div>
        <!-- Car selector for sorting table -->
        <div class="table">
            <div class="table-tools">
                <!-- Table editing tools: Edit information, delete data, add new data -->
                <div class="table-editors">
                    <a id="edit-table"><i class="fa-solid fa-pen"></i></a>
                    <a id="delete-from-table" href="gas_data.php?del=<?php echo ($_GET["del"] === "1" ? "0" : "1") ?>"><i class='fa-solid fa-circle-xmark'></i></a>
                    <a href="add_gas.php"><i class="fa-solid fa-plus"></i></a>
                </div>
                <!-- Table search, partly functional -->
            </div>
            <!-- Create a table -->
            <table class="mod-table">
                <thead>
                    <tr class="top-row">
                        <td class='tl'>Car name</td>
                        <td>Liters</td>
                        <td>Gas Price</td>
                        <td>Paid</td>
                        <td>Milage</td>
                        <td class='tr'>Date</td>
                    </tr>
                </thead>
                <tbody>
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

                        $query = "SELECT * FROM gas ORDER BY date_added DESC";
                        $result = mysqli_query($userdb, $query);

                        if($result){
                            if(mysqli_num_rows($result) > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    $id = $row["id"];
                                    $carid = $row["car_id"];
                                    $milage = $row["milage"];
                                    $liters = $row["liters"];
                                    $price = $row["price"];
                                    $gas_price = $row["gas_price"];
                                    $unit = $row["unit"];
                                    $date = date_create($row["date_added"]);
                                    $date_formated = date_format($date, "d/m/Y H:i:s");

                                    $query2 = "SELECT * FROM cars WHERE id=$carid";
                                    $result2 = mysqli_query($userdb, $query2);

                                    $row2 = mysqli_fetch_assoc($result2);

                                    $car_year = $row2["model_year"];
                                    $car_model = $row2["model"];
                                    $car_manufacturer = $row2["manufacturer"];
                                    
                                    $delete_activated = $_GET["del"];

                                    echo "<tr onclick=location.href='gas_data.php?del=$delete_activated&delid=$id'>";
                                        echo "<td>$car_year $car_manufacturer $car_model</td>";
                                        echo "<td>$liters L</td>";
                                        echo "<td>$gas_price $unit</td>";
                                        echo "<td>$price $unit</td>";
                                        echo "<td>$milage KM</td>";
                                        echo "<td>$date_formated</td>";
                                    echo "</tr>";
                                }
                                
                            }
                            else{
                                echo "<tr>";
                                echo "<td colspan='6'>No gas data recorded.</td>";
                                echo "</tr>";
                            }
                        }
                        else{
                            echo "<tr>";
                            echo "<td colspan='6'>Something went wrong..</td>";
                            echo "</tr>";
                        }
                    ?>
                </tbody>
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