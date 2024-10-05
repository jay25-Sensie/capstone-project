<?php
session_start(); // Start the session

include("connection.php"); 
include("function.php"); // Include your function definitions

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Get username and password from POST request
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Check if username and password are not empty
    if (!empty($username) && !empty($password)) {
        $username = mysqli_real_escape_string($con, $username); // Sanitize the username
        
        // Query to get user data
        $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($con, $query);
        
        // Check if query was successful and user exists
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result); // Fetch user data
            
            // Verify the password
            if (password_verify($password, $user_data['password'])) {
                $_SESSION['userID'] = $user_data['userID']; // Store user ID in session
                $_SESSION['role'] = $user_data['role']; // Store user role in session
                $_SESSION['username'] = $user_data['username']; // Store username in session
                
                // Check the user's role and redirect accordingly
                if ($user_data['role'] == 'admin') {
                    header("Location: Dashboard_Admin.php");
                    exit();
                } elseif ($user_data['role'] == 'doctor') { // Fix: check for 'admin' role
                    header("Location: Dashboard_Doctor.php");
                    exit();
                } else {
                    echo '<script>alert("Access denied. Unknown user.");</script>';
                }
                
            } else {
                echo '<script>alert("Incorrect Password");</script>'; // Wrong password alert
            }
        } else {
            echo '<script>alert("User not found");</script>'; // User not found alert
        }
        
    } else {
        echo '<script>alert("Please fill in all fields.");</script>'; // Alert for empty fields
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
