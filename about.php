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
    <link rel="stylesheet" href="about.css">
    <title>CarData | About Us</title>
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
                    echo "<a href='contact.php'>Contact</a>";
                    echo "<a href='about.php' class='about active'>About</a>";
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
                    echo "<a href='contact.php'>Contact</a>";
                    echo "<a href='about.php' class='about active'>About</a>";
                    echo "<a href='users/register.php' class='login-links reg-link'>Register</a>";
                    echo "<a href='users/login.php' class='login-links log-link'>Sign In</a>";
                }
            ?>
        </div>
    </div>
    <div class="about-container">
        <h1>About Us</h1>
        <hr>
        <p class='about-us-para'>We are happy to let you know us better. Here is all the information about CarData team, who we are and our goals.</p>
        <!-- <div class="text-changers">
            <a onclick="currentSlide(1)">Who are we</a>
            <a onclick="currentSlide(2)">Origin</a>
            <a onclick="currentSlide(3)">Future</a>
            <a onclick="currentSlide(4)">Team</a>
            <div class="underline" id="ul"></div>
        </div> -->
        <div class="about-us about-us-top">
            <div class="text">
                <h2>Passionate car enthusiasts</h2>
                <p>CarData is an innovative website launched by a passionate car enthusiast/developer from Croatia and it has only one goal, to shape the way we take care of our metal pets. Using our services we hope to change your perspective
                    on car ownership and hopefully make it easier for you to keep track of your car repairs, finances and other. We are constantly evolving, upgrading the website and services, making the experience better with
                    each update.  
                </p>
            </div>
            <img src="img/about/ferrari.png" alt="">
        </div>
        <div class="about-us align-right">
            <div class="text">
                <h2>Our Origin</h2>
                <p>Passion for cars spiked an idea to create CarData. Idea was to build a website which will track all the repairs to be done on his first car. Initial idea was not
                    the website as it is today, but only a service book in which the data would be stored and account system to separate data. After the initial site was finished, we've seen potential to grow the website into something much bigger than
                    just a simple table. That thought has driven us to build an entire arsenal of services and analytics available to the public. The entire project took a little more than 2 months to build with dozens of design and functionality variations.
                </p>
            </div>
            <img src="img/about/mclaren_polka.png" alt="">
        </div>
        <div class="about-us">
            <div class="text">
                <h2>What are we planning</h2>
                <p>We don't know what the future holds, but we can hope its bright. We want to grow the website and make our presence known. As our popularity grows, our services will grow with it. We have great plans for the website in the future and want to
                    make them possible. Using the minds of our users and their ideas we want to add services that we didn't think of yet, which will make the experience better in the end. We are open to all the ideas and are really looking forward to hearing your thoughts
                    and ideas on making the website better and more user-friendly.
                </p>
            </div>
            <img src="img/about/amg_polka.png" alt="">
        </div>
        <div class="about-us  align-right">
            <div class="text">
                <h2>CarData Team</h2>
                <p>As of present time CarData Team is composed of a single developer, couple BETA testers and security experts. But we do plan on expanding our team depending on the sites success.</p>
            </div>
            <img src="img/about/huayra_polka.png" alt="">
        </div>
        <!-- <div class="join-cardata">
            <p>Join us today!</p>
            <button onclick="location.href='users/register.php'">BECOME A MEMBER</button>
            <a href="users/login.php">Already a member?</a>
        </div> -->
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

    <!-- <script>
        var slideIndex = 1;
        showSlides(slideIndex);

        // Thumbnail image controls
        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n){
            var i;
            var slides = document.getElementsByClassName("about-us");
            var underline = document.getElementById("ul");
            if(n > slides.length) {slideIndex = 1}
            if(n < 1) {slideIndex = slides.length}
            for(i = 0; i < slides.length; i++){
                slides[i].style.display = "none";
            }
            
            if(slideIndex == 1){
                underline.style.left = "0%";
            }
            else if(slideIndex == 2){
                underline.style.left = "25%";
            }
            else if(slideIndex == 3){
                underline.style.left = "50%";
            }
            else if(slideIndex == 4){
                underline.style.left = "75%";
            }

            slides[slideIndex-1].style.display = "block";
        }
    </script> -->
</body>
</html>