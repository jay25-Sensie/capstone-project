<?php   

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "wbhrms_db";

    $con = new mysqli($host, $username, $password, $database);

    if($con->connect_error){
        echo $con->connect_error;
    }else{
        return $con;
    }

?>