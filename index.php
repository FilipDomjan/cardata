<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="fonts/fontawesome/css/all.css">
    <link rel="stylesheet" href="style.css">
    <title>CarData | Car ownership made easy</title>
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
                    echo "<a class='active'>Home</a>";
                    echo "<a href='dashboard.php'>My CarData</a>";
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
    <div class="showcase">
        <div class="showcase-container">
            <h1>Only the best<br>for our cars.</h1>
            <button class="analytics" onclick="location.href='dashboard.php'">Dashboard</button>
            <button class="other" onclick="location.href='mycars.php'">My Cars</button>
        </div>
        <div class="scroll-down">
            <i class="fa-solid fa-arrow-down"></i>
        </div>
    </div>
    <div class="container" id="about">
        <div class="who-are-we">
            <h1>What is CarData?</h1>
            <p class="what-is-cardata">CarData is an interactive website which helps you take care of your car. How? Well simply by remembering all the work you've done to it.</p>
            <div class="tiles">
                <div class="tile">
                    <i class="fa-solid fa-chart-line" style="color: purple;"></i>
                    <h2>Analytics</h2>
                    <p>Using the carefully designed systems we can analize your spending and show it to you in a clean, readable way.</p>
                </div>
                <div class="tile">
                    <i class="fa-solid fa-dollar-sign" style="color: lime;"></i>
                    <h2>Financial Monitoring</h2>
                    <p>Using our built in system you can keep track of all your spending. When, where and how much did you spend on certain part of your car.</p>
                </div>
                <div class="tile last-tile">
                    <i class="fa-solid fa-screwdriver-wrench" style="color: darkred;"></i>
                    <h2>Car work tracking</h2>
                    <p>CarData helps you keep track of all the work you do on your cars. Simply enter the details of the work you did, and it stays written forever. Unless, you delete it that is..</p>
                </div>
            </div>
        </div>
    </div>
    <div class="how-does-it-work">
        <h1>How does it work?</h1>
        <div class="container">
            <div class="hdiw-column">
                <h2>Data tracking</h2>
                <p>Using the well thought out systems, CarData gives you the opportunity to easily keep notes of everything related to your car. Repairs, mods, tunes, even how much you spent on gas! All this information can be provided to CarData for safe-keeping.
                    And any time you want to view it, simply login into your dashboard and all the data is there for you to see.
                </p>
            </div>
            <div class="hdiw-column">
                <h2>Financial tracking</h2>
                <p>Using the data you provided, it will calculate all your spending in three separate ways.<br><br>1. Daily spending - during the current day.<br>2. Weekly spending - in the last 7 days.<br>3. Monthly spending -
                    during the current month.
                </p>
            </div>
            <div class="hdiw-column last-column">
                <h2>Completely free</h2>
                <p>CarData is completely free to use. Yes, for real. All the features are available to you upon sign up completely free of cost.</p>
            </div>
        </div>
    </div>
    <div class="join-us">
        <h1>Join us!</h1>
        <div class="join-us-content">
            <p>Join CarData today and make your car ownership as easy as it gets.</p>
            <button onclick="location.href='users/register.php'">Get started</button>
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