<?php
session_start();

include("connection.php"); 
include("function.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        $username = mysqli_real_escape_string($con, $username);
        
        // Check if the user already exists
        $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($con, $query);
        
        if (mysqli_num_rows($result) > 0) {
            echo '<script>alert("User already exists.");</script>';
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert user with default role
            $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', 'admin')";
            $result = mysqli_query($con, $query);
            
            if ($result) {
                header("Location: Admin_Staff_login.php");
                exit(); 
            } else {
                echo "Error: " . mysqli_error($con);
            }
        }
        
    } else {
        echo '<script>alert("Please fill in all fields.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="..//WBHR_MS/css/Sign_in.css">
    <title>Sign In</title>
</head>
<body>
<div class="con_2">
    <img src=".//img/logo.png" style="width:20%; height: 20%;" alt="">
    <h3>ADMIN STAFF SIGN UP</h3>
    <div class="form2">
        <form method="post">
            <label for="username">Email:</label><br>
            <input type="email" name="username" id="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" name="password" id="password" required><br><br>
            <input type="submit" name="login" value="Sign In" id="login_as">
        </form><br><br><br>
    </div>
</div>

</body>
</html>
