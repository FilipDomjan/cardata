<?php
    session_start();
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
    <link rel="stylesheet" href="contact.css">
    <title>CarData | Contact Us</title>
</head>
<body>
    <div class="navbar" id="navbar">
        <div class="links">
            <img src="img/cardata_logo_white.png" alt="logo">
            <?php
                error_reporting(0);

                // Navigation bar items change wether the user is logged in or not
                // If the user is logged in it will show users username and logout button
                if($_SESSION["loggedin"] === true){
                    $username = htmlspecialchars($_SESSION["username"]);
                    echo "<a href='index.php'>Home</a>";
                    echo "<a href='contact.php' class='active'>Contact</a>";
                    echo "<a href='about.php' class='about'>About</a>";
                    echo "<div class='dropdown'>";
                    echo "<button class='drop-btn'"."onclick="."location.href='dashboard.php'>My Cardata<i class='fa-solid fa-caret-down'></i></button>";
                    echo "<div class='dropdown-content'>";
                    echo "<p class='top-paragraph'>General</p>";
                    echo "<hr>";
                    echo "<a href='dashboard.php'><i class='fa-solid fa-gauge-simple'></i><span>Dashboard</span></a>";
                    echo "<p>Cars</p>";
                    echo "<hr>";
                    echo "<a href='my_cars.php'><i class='fa-solid fa-car'></i><span>My Cars</span></a>";
                    echo "<a href='add_car.php'><i class='fa-solid fa-circle-plus'></i><span>Add a car</span></a>";
                    echo "<p>Car Data</p>";
                    echo "<hr>";
                    echo "<a href='service_book.php?carid=0'><i class='fa-solid fa-table-list'></i><span>Service Book</span></a>";
                    echo "<a href='add_service.php'><i class='fa-solid fa-screwdriver-wrench'></i><span>Add to Service Book</span></a>";
                    echo "<p>Gas Data</p>";
                    echo "<hr>";
                    echo "<a href='gas_data.php'><i class='fa-solid fa-gas-pump'></i><span>Gas Data</span></a>";
                    echo "<a href='add_gas.php' class='last-item'><i class='fa-solid fa-droplet'></i><span>Add to Gas Data</span></a>";
                    echo "</div>";
                    echo "</div>";
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
                    echo "<a href='index.php'>Home</a>";
                    echo "<a href='#' class='active'>Contact</a>";
                    echo "<a href='about.php' class='about'>About</a>";
                    echo "<a href='users/register.php' class='login-links reg-link'>Register</a>";
                    echo "<a href='users/login.php' class='login-links log-link'>Sign In</a>";
                }
            ?>
        </div>
    </div>
    <div class="container">
        <div class="contact-container">
            <h1>Contact Us</h1>
            <hr>
            <p>Have any questions? We'd love to hear from you!</p>
        </div>
        <div class="contact-form">
            <form action="" method="POST" autocomplete="off">
                <?php
                    include_once("connect_server.php");

                    $query = "SELECT * FROM users WHERE username = '$username'";
                    $result = mysqli_query($db, $query);

                    $row = mysqli_fetch_assoc($result);

                    $email = $row["email"];

                ?>
                <div class="row">
                    <label for="">E-Mail</label>
                    <input type="text" name="email" id="email" placeholder="Your E-Mail" value="<?php echo $email ?>" required>
                    <div class="underline"></div>
                </div>
                <div class="row">
                    <label for="">Message</label>
                    <textarea name="message" id="msg" cols="30" rows="10" placeholder="Write your message in this field" required></textarea>
                    <div class="underline textarea-underline"></div>
                </div>
                <div class="row">
                    <input type="submit" id="submit" value="Send">
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
</body>
</html>