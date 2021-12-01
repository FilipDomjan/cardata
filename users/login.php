<?php

// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ../index.php");
    exit;
}

require_once("config.php");

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        // User can enter either username or email to login
        $sql = "SELECT id, email, username, password FROM users WHERE username = ? OR email = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_email);
            // Set parameters
            $param_username = $username;
            $param_email = $username;
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                // Check if username/email exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $email, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["extra"] = 0;                       
                            
                            // Redirect user to index page
                            header("location: ../index.php");

                        } else{
                            // Password is not valid, display a generic error message
                            echo "Invalid password";
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    echo "Invalid password";
                    // Username doesn't exist, display a generic error message
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../fonts/fontawesome/css/all.css">
    <link rel="stylesheet" href="login.css">
    <title>CarData | Login</title>
</head>
<body>
    <div class="navbar">
        <img src="../img/cardata_logo_white.png" alt="logo">
        <div class="links">
            <?php
                error_reporting(0);

                if($_SESSION["loggedin"] === true){
                    $username = htmlspecialchars($_SESSION["username"]);
                    echo "<a href='../index.php'>Home</a>";
                    echo "<a href='../dashboard.php'>My CarData</a>";
                    echo "<a href='#'>Contact</a>";
                    echo "<a class='login'><i class='fa-solid fa-user'></i> $username</a>";
                    echo "<a href='logout.php' class='logout'><i class='fa-solid fa-right-from-bracket'></i><span>Logout</span></a>";
                }
                else{
                    echo "<a href='../index.php'>Home</a>";
                    echo "<a href='#'>Contact</a>";
                    echo "<a href='#' class='active'>Login</a>";
                }
            ?>
        </div>
    </div>
    <div class="showcase">
        <div class="showcase-img">
            <img src="../img/cardata_logo_white.png" alt="logo">
        </div>
        <div class="form-container">
            <form action="" method="POST">
                <div class="container top">
                    <label for=""><i class="fa-solid fa-user"></i></label>
                    <input type="text" name="username" class="form-control" placeholder="USERNAME OR E-MAIL">
                    <!-- <span class="invalid-feedback"><?php echo $username_err; ?></span> -->
                </div>
                <div class="container">
                    <label for=""><i class="fa-solid fa-lock"></i></label>
                    <br>
                    <input type="password" name="password" class="form-control" placeholder="PASSWORD">
                    <!-- <span class="invalid-feedback"><?php echo $password_err; ?></span> -->
                </div>
                <div class="container">
                    <input type="submit" name="submit" value="Login" id="submit_btn">
                    <div class="clear"></div>
                    <a href="register.php" class='register'>Don't have an account?</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>