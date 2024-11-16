<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];

    if (!empty($token) && !empty($new_password)) {
        $token = mysqli_real_escape_string($con, $token);
        $query = "SELECT * FROM password_reset WHERE token = '$token' AND expiry > NOW() LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $reset_data = mysqli_fetch_assoc($result);

            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Update password in the users table
            $update_query = "UPDATE users SET password = '$hashed_password' WHERE userID = '{$reset_data['user_id']}'";
            mysqli_query($con, $update_query);

            // Delete the token
            $delete_query = "DELETE FROM password_reset WHERE token = '$token'";
            mysqli_query($con, $delete_query);

            echo '<script>alert("Password reset successful. Please log in.");</script>';
            header("Location: login.php");
            exit();
        } else {
            echo '<script>alert("Invalid or expired token.");</script>';
        }
    } else {
        echo '<script>alert("Please provide all required fields.");</script>';
    }
} elseif (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    echo '<script>alert("Invalid request.");</script>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <form method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="new_password">New Password:</label><br>
        <input type="password" name="new_password" id="new_password" required><br><br>
        <input type="submit" value="Reset Password">
    </form>
</body>
</html>
