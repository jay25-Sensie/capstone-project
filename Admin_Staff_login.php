<?php
session_start();

include("connection.php"); 
include("function.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        $username = mysqli_real_escape_string($con, $username);
        
        $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($con, $query);
        
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            if (password_verify($password, $user_data['password'])) {
                $_SESSION['userID'] = $user_data['userID'];
                
                // Check the role and redirect accordingly
                if ($user_data['role'] == 'admin') {
                    header("Location: Dashboard_Admin.php");
                    exit();
                } else {
                    echo '<script>alert("Access denied. You are not authorized to access this page.");</script>';
                }
                
            } else {
                echo '<script>alert("Incorrect Password");</script>';
            }
        } else {
            echo '<script>alert("User not found");</script>';
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
    <link rel="stylesheet" href="..//WBHR_MS/css/Admin_Staff_login.css">
    <title>Admin Staff Login Page</title>
</head>
<body>
<div class="con_2">
    <img src=".//img/logo.png" style="width:20%; height: 20%;" alt="">
    <h3>ADMIN STAFF LOGIN PAGE</h3>
    <div class="form2">
        <form method="post">
            <label for="username">Username:</label><br>
            <input type="email" name="username" id="username" required><br>
            <label for="password">Password:</label><br>
            <input type="password" name="password" id="password" required><br><br>
            <input type="submit" name="login" value="Log In" id="login_as">
        </form><br>
        <a href="Signin_Admin.php" id="signin">Sign In</a>
        <a href="#" id="fgotpass">Forgot Password?</a><br>
    </div>
</div>
</body>
</html>
