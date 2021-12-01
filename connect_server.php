<?php

$server   = 'localhost';
$user = 'root';
$password = '';
$database = 'cars';

$db = mysqli_connect($server, $user, $password, $database);

if($db -> connect_errno){
    echo "Failed to connect to MySQL: ".$db -> connect_error;
    exit();
}

?>