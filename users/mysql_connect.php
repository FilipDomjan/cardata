<?php

    $server = "localhost";
    $user = "root";
    $password = "";

    $conn = new mysqli($server, $user, $password);

    if($conn->connect_error){
        die("Conenction failed: ". $conn->connect_error);
    }

?>