<?php

// Include config file
require_once "config.php";
// Define variables and initialize with empty values
$email = $username = $password = $confirm_password = "";
$email_err = $username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];

    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email";
    }
    else{
        $sql = "SELECT id FROM users WHERE email = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);

            $param_email = trim($_POST["email"]);

            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "This email has already been registered.";
                }
                else{
                    $email = trim($_POST["email"]);
                }
            }
            else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            mysqli_stmt_close($stmt);
        }
    }
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (firstname, lastname, email, username, password) VALUES (?, ?, ?, ?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssss", $param_firstname, $param_lastname, $param_email, $param_username, $param_password);
            
            // Set parameters
            $param_firstname = $firstname;
            $param_lastname = $lastname;
            $param_email = $email;
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                
                include_once("mysql_connect.php");
                
                // Once the user has been created, we create a new database in the
                // MySQL which is named after the users username
                $query = "CREATE DATABASE $username";
                $execute = mysqli_query($conn, $query);

                // If the database has been successfully created proceed with creation of tables
                // In the said database
                if($execute){
                    $servername = "localhost";
                    $user = "root";
                    $pass = "";
                    $dbname = "$username";

                    $connect = mysqli_connect($servername, $user, $pass, $dbname);

                    $database_error = "";
                    
                    // Create a table which will hold car information
                    if($connect){
                        $table = "CREATE TABLE cars(
                            id INT NOT NULL AUTO_INCREMENT,
                            manufacturer VARCHAR(50),
                            model VARCHAR(50),
                            chassis VARCHAR(50),
                            color VARCHAR(50),
                            vinyl VARCHAR(100),
                            model_year VARCHAR(4),
                            engine_config VARCHAR(30),
                            engine_capacity INT,
                            horsepower INT,
                            fuel_type VARCHAR(30),
                            transmission VARCHAR(30),
                            drivetrain VARCHAR(30),
                            PRIMARY KEY (id));";
                        $make_table_cars = mysqli_query($connect, $table);
                        
                        // If there was an error creating the table, show a message
                        if(!$make_table_cars){
                            $database_error = "<p class='db-err'>There was an error with creating your account...</p>";
                        }

                        // Create a table holding car work information
                        $table = "CREATE TABLE mods(
                            id INT NOT NULL AUTO_INCREMENT,
                            carid INT NOT NULL,
                            repair_type VARCHAR(10),
                            description VARCHAR(1000),
                            milage INT,
                            price INT,
                            unit VARCHAR(5),
                            done_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (id),
                            FOREIGN KEY (carid) REFERENCES cars(id));";
                        $make_table_mods = mysqli_query($connect, $table);
                        
                        // If there was an error creating the table, show a message
                        if(!$make_table_mods){
                            $database_error = "<p class='db-err'>There was an error with creating your account...</p>";
                        }

                        // If both tables were successfully created redirect to the index page
                        if($make_table_cars && $make_table_mods){
                            header("location: ../index.php");
                        }
                        // If not then tell the user that there was an error
                        else{
                            $database_error = "<p class='db-err'>There was an error with creating your account...</p>";
                        }
                    }

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
    <link rel="stylesheet" href="register.css">
    <title>CarData | Register</title>
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
                    echo "<a href='dashboard.php'>My CarData</a>";
                    echo "<a href='#'>Contact</a>";
                    echo "<a class='login'><i class='fa-solid fa-user'></i> $username</a>";
                    echo "<a href='logout.php' class='logout'><i class='fa-solid fa-right-from-bracket'></i><span>Logout</span></a>";
                }
                else{
                    echo "<a href='../index.php'>Home</a>";
                    echo "<a href='#'>Contact</a>";
                    echo "<a href='login.php'>Login</a>";
                }
            ?>
        </div>
    </div>
    <div class="form-container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="row top-row">
                <div class="first-last-name">
                    <label for="">First Name<span>*</span></label>
                    <br>
                    <input type="text" name="firstname" class="form-control" placeholder="ex. John" required>
                </div>
                <div class="first-last-name">
                    <label for="">Last Name</label>
                    <br>
                    <input type="text" name="lastname" class="form-control" placeholder="ex. Doe">
                </div>
            </div>
            <div class="row">
                <label for="">E-Mail<span>*</span></label>
                <br>
                <input type="email" name="email" class="form-control" placeholder="johndoe@gmail.com" required>
            </div>
            <div class="row">
                <label for="">Username<span>*</span></label>
                <br>
                <input type="text" name="username" class="form-control" placeholder="JohnDoe27" required>
            </div>
            <div class="row">
                <label for="">Password<span>*</span></label>
                <br>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="row">
                <label for="">Confirm password<span>*</span></label>
                <br>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <div class="row row-terms">
                <div class="terms">
                    <input type="checkbox" name="checkbox" id="checkbox" required>
                    <label>I agree to the Terms of Service</label>
                    <a href="login.php" class="go-to-login">Already have an account?</a>
                </div>
            </div>
            <div class="row">
                <input type="submit" name="submit" value="Register" id="submit_btn">
                <div class="clear"></div>
            </div>
        </form>
    </div>
</body>
</html>