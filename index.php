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
    <link rel="stylesheet" href="style.css">
    <title>CarData | Car ownership made easy</title>
</head>
<body>
    <?php
        $cookie_value = "v1-0-1";
        $cookie_name = "news-$cookie_value";

        if(!isset($_COOKIE[$cookie_name])){
    ?>
            <div class="news" id="news">
                <div class="news-container">
                    <div class="whats-new">
                        <p>CarData Updated</p>
                        <span>10. April 2022</span>
                    </div>
                    <div class="news-image">
                        <img src="img/showcase/showcase-wp-edited.jpg" alt="update">
                    </div>
                    <div class="news-content">
                        <div class="update-item">
                            <h1>STYLING CHANGES</h1>
                            <hr>
                            <ul>
                                <li><span>NEW</span> <b>Dark/Light Mode</b></li>
                                <li>A highly requested feature finally arrives in CarData. You can now switch between <b>Light and Dark mode in the account dropdown menu</b>. </li>
                                <li><b>We worked hard to make this feature possible and are happy</b> to finally bring it to you. We hope that you will enjoy both dark and light theme of this page.</li>
                                <li><b>Feature is accessible through account dropdown menu in the navigation bar to the far right.</b> Meaning you have to be logged in to change the theme. For now.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="exit-button">
                        <button onclick=closePopUp()>LET'S GO!</button>
                    </div>
                </div>
            </div>
    <?php
        setcookie($cookie_name, $cookie_value);
        };
    ?>
    <div class="navbar" id="navbar">
        <div class="links">
            <img src="img/cardata_logo_white.png" alt="logo">
            <?php
                error_reporting(0);

                // Navigation bar items change wether the user is logged in or not
                // If the user is logged in it will show users username and logout button
                if($_SESSION["loggedin"] === true){
                    $username = htmlspecialchars($_SESSION["username"]);
                    echo "<a href='index.php' class='active'>Home</a>";
                    echo "<a href='contact.php'>Contact</a>";
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
                    echo "<a href='#' class='active'>Home</a>";
                    echo "<a href='contact.php'>Contact</a>";
                    echo "<a href='about.php' class='about'>About</a>";
                    echo "<a href='users/register.php' class='login-links reg-link'>Register</a>";
                    echo "<a href='users/login.php' class='login-links log-link'>Sign In</a>";
                }
            ?>
        </div>
    </div>
    <?php
        include_once("connect_server.php");

        $query = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($db, $query);

        while($row = mysqli_fetch_assoc($result)){
            $fname = $row["firstname"];
        }

        $greet = "";
        $time = date("H");
        
        if($time >= 6 && $time <= 11){
            $greet = "Good morning, $fname";
        }
        elseif($time >= 12 && $time <= 19){
            $greet = "Good afternoon, $fname";
        }
        else{
            $greet = "Good evening, $fname";
        }
    ?>
    <div class="showcase">
        <div class="container">
            <div class="slogan">
                <h1><?php echo $_SESSION["loggedin"] === true ? "$greet" : "Upgrade your car ownership game with CarData." ?></h1>
            </div>
            <div class="bottom-text">
                <p><?php echo $_SESSION["loggedin"] === true ? "As a member full access to CarData has been unlocked to you. Explore the Service Book, check out the Dashboard or many other features simply by clicking the button below or you can access any of the services through the navigation bar aswell." : "Join CarData today and explore what we are all about. At completely zero cost you can start taking care of your vehicle like never before. We are happy to have you as the part of our community." ?></p>
                <?php echo $_SESSION["loggedin"] === true ? "<button "."onclick="."location.href='dashboard.php'>EXPLORE</button>" : "<button "."onclick="."location.href='users/register.php'>JOIN</button>" ?>
            </div>
        </div>
    </div>
    <div class="who-are-we">
        <div class="container" id="about">
        <h1>SERVICES</h1>
        <hr>
            <p class="what-is-cardata">There is a wide selection of services available to you upon sign-up which you can use whenever you want. Check out what we offer in the tiles below.</p>
            <div class="tiles">
                <div class="tile">
                    <div class="icon">
                        <i class="fa-solid fa-chart-line"></i>   
                    </div>
                    <h2>Analytics</h2>
                    <p>CarData uses information you provide it to calculate a lot of other information. Like your monthly and weekly spending and much more, which can all be accessed through the Dashboard.</p>
                </div>
                <div class="tile">
                    <div class="icon">
                        <i class="fa-solid fa-gas-pump"></i>
                    </div>
                    <h2>Gas Stops</h2>
                    <p>Whenever you take a trip to the gas station you can enter the details of your refueling into CarData and it will keep you posted on how much you spend on gas.</p>
                </div>
                <div class="tile last-tile">
                    <div class="icon">
                        <i class="fa-solid fa-screwdriver-wrench"></i>
                    </div>
                    <h2>Repair tracking</h2>
                    <p>CarData helps you keep track of all the work you do on your cars. Wether it be modding, tuning or repairs simply enter the details of the work you did, and it stays written forever. Unless, you delete it that is..</p>
                </div>
            </div>
        </div>
    </div>
        <div class="how-does-it-work">
            <h1>FEATURES</h1>
            <hr>
            <div class="content-switchers">
                <div class="highlight" id="highlight"></div>
                <div class="switcher-buttons">
                    <a onclick="currentSlide(1)" class="switcher active-switcher">SERVICE BOOK</a>
                    <a onclick="currentSlide(2)" class="switcher">GAS DATA</a>
                    <a onclick="currentSlide(3)" class="switcher">ANALYTICS</a>
                </div>
            </div>
            <div class="container">
                <div class="cardata-container fade">
                    <div class="cardata-image">
                        <img src="img/services/service_book_vertical.png" alt="">
                    </div>
                    <div class="cardata-description">
                        <h1>Service Book</h1>
                        <p>Service Book is the main star of the show. It keeps all the important service data safe once you enter it. It will keep information like date of service, milage of the car at the time of service, price of service etc. And most importantly nobody can see this information but you.</p>
                        <button onclick="location.href='service_book.php?carid=0'">CHECK IT OUT</button>
                    </div>
                </div>
                <div class="cardata-container fade">
                    <div class="cardata-image">
                        <img src="img/services/gas_data_vertical.png" alt="">
                    </div>
                    <div class="cardata-description">
                        <h1>Gas Data</h1>
                        <p>Gas Data makes it easy for you to track your spending on gas, it can be quite expensive and we want to keep an eye on it. Gas Data will keep information like the gas price at the time of refill, how many liters was poured in, when was the refill and most importantly how much you paid.</p>
                        <button onclick="location.href='gas_data.php'">CHECK IT OUT</button>
                    </div>
                </div>
                <div class="cardata-container fade">
                    <div class="cardata-image">
                        <img src="img/services/reports_vertical.png" alt="">
                    </div>
                    <div class="cardata-description">
                        <h1>Analytics</h1>
                        <p>Analytics are located in the dashboard and they offer a variety of data that might be of interest to you. They track your spending and add them to the graphs which show you all the data that accumulated over time.</p>
                        <button onclick="location.href='dashboard.php'">CHECK IT OUT</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="join-us">
        <h1>WHY JOIN</h1>
        <hr>
        <p>Having any doubts? Let us tell you a little more.</p>
        <div class="container">
            <div class="extra-tiles">
                <div class="extra-tile tile-down">
                    <img src="img/services/arrow-wp.png">
                    <h1>Expendability</h1>
                    <hr>
                    <p>We are completely expandable. Services are not final and at any time we can add new things that people want to see and could help them further. We are completely open to such things and you can at any time suggest features through our contact form.</p>
                </div>
                <div class="extra-tile">
                    <img src="img/services/cost-wp.png">
                    <h1>Cost</h1>
                    <hr>
                    <p>As mentioned before CarData services are completely free. All the features are available to you upon sign up and there are no secret costs. Due to this we may start showing ads to keep the site going. But we will include fairly priced memberships to get rid of ads.</p>
                </div>
                <div class="extra-tile tile-down">
                    <img src="img/services/easy-wp.png">
                    <h1>Usability</h1>
                    <hr>
                    <p>Website has been designed to be as easy as possible to use, everything is presented to you in a nice readable fashion. This is where we are also expendable, if some things you feel are hard to reach, we can surely work on it and make it better.</p>
                </div>
                <div class="extra-tile">
                    <img src="img/services/customer-wp.png">
                    <h1>User support</h1>
                    <hr>
                    <p>We love all our users and we want to help them with any problem they have regarding the site. We are very easy to reach and will answer to as many messages as possible in the quickest time possible. Any problem you have feel free to contact us.</p>
                </div>
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
        var slideIndex = 1;
        showSlides(slideIndex);

        // Next/previous controls
        function plusSlides(n) {
        showSlides(slideIndex += n);
        }

        // Thumbnail image controls
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n){
            var i;
            var slides = document.getElementsByClassName("cardata-container");
            var slider = document.getElementById("highlight");
            var switcher = document.getElementsByClassName("switcher");
            if(n > slides.length) {slideIndex = 1}
            if(n < 1) {slideIndex = slides.length}
            for(i = 0; i < slides.length; i++){
                slides[i].style.display = "none";
            }
            for(i = 0; i < switcher.length; i++){
                switcher[i].className = switcher[i].className.replace(" active-switcher", "");
            }

            if(slideIndex == 1){
                slider.style.left = "0%";
            }
            else if(slideIndex == 2){
                slider.style.left = "33.33%";
            }
            else{
                slider.style.left = "66.66%";
            }

            slides[slideIndex-1].style.display = "block";
            switcher[slideIndex-1].className += " active-switcher";
        }
    </script>

    <!-- Close update popup -->
    <script>
        function closePopUp(){
            document.getElementById("news").style.display = "none";
        }
    </script>

    <!-- Scripts for animating elements -->
    <script>
        
    </script>
</body>
</html>