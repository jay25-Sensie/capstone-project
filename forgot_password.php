<?php
session_start();
include("connection.php");
include("function.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];

    if (!empty($username)) {
        $username = mysqli_real_escape_string($con, $username);
        $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $token = bin2hex(random_bytes(50)); // Generate secure token
            $expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Insert token into database (assumes `password_reset` table exists)
            $insert_query = "INSERT INTO password_reset (user_id, token, expiry) 
                             VALUES ('{$user_data['userID']}', '$token', '$expiry')";
            mysqli_query($con, $insert_query);

            // Send email with reset link
            $reset_link = "http://yourdomain.com/reset_password.php?token=$token";
            $to = $user_data['username']; // Assuming the username is an email
            $subject = "Password Reset Request";
            $message = "Click the link below to reset your password:\n\n$reset_link\n\nThis link will expire in 1 hour.";
            $headers = "From: noreply@yourdomain.com";

            if (mail($to, $subject, $message, $headers)) {
                echo '<script>alert("Password reset email sent. Please check your inbox.");</script>';
            } else {
                echo '<script>alert("Failed to send email. Please try again later.");</script>';
            }
        } else {
            echo '<script>alert("User not found.");</script>';
        }
    } else {
        echo '<script>alert("Please provide your username.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <form method="post">
        <label for="username">Enter your email or username:</label><br>
        <input type="email" name="username" id="username" required><br><br>
        <input type="submit" value="Request Password Reset">
    </form>
</body>
</html>
