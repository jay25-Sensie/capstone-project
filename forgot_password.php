<?php
session_start();
include("connection.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include Composer's autoloader, or manually include PHPMailer's files

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];

    // Validate username
    if (!empty($username)) {
        $query = $con->prepare("SELECT userID, username FROM users WHERE username = ? LIMIT 1");
        $query->bind_param("s", $username);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $user_data = $result->fetch_assoc();
            $user_id = $user_data['userID'];
            $email = $user_data['username'];

            $token = bin2hex(random_bytes(50)); // Generate secure token
            $expiry = date("Y-m-d H:i:s", strtotime("+1 day"));

            // Insert token into `password_reset` table
            $insert_token = $con->prepare("INSERT INTO password_reset (user_id, token, expiry) VALUES (?, ?, ?)");
            $insert_token->bind_param("iss", $user_id, $token, $expiry);

            if ($insert_token->execute()) {
                // Construct the reset link
                $reset_link = "http://localhost/WBHR_MS/reset_password.php?token=$token";

                // Prepare the email content
                $subject = "Password Reset Request";
                $message = "Hi,\n\nYou requested to reset your password. Click the link below to reset it:\n\n$reset_link\n\nThis link will expire in 1 hour.\n\nIf you did not request this, please ignore this email.";

                // Create an instance of PHPMailer
                $mail = new PHPMailer(true);

                try {
                    //Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send email
                    $mail->SMTPAuth = true;
                    $mail->Username = 'jayoredapse@gmail.com'; // SMTP username
                    $mail->Password = 'gdna vpis djio nzbl'; // SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('jayoredapse@gmail.com', 'IMSClinic');
                    $mail->addAddress($email);

                    // Content
                    $mail->isHTML(false); // Plain text email
                    $mail->Subject = $subject;
                    $mail->Body    = $message;

                    // Send the email
                    $mail->send();
                    echo '<div class="alert alert-success mt-3 text-center">Password reset link sent to your email. Please check your inbox.</div>';
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger mt-3 text-center">Mailer Error: ' . $mail->ErrorInfo . '</div>';
                }
            } else {
                echo '<div class="alert alert-danger mt-3 text-center">Error: Unable to generate reset link.</div>';
            }
        } else {
            echo '<div class="alert alert-danger mt-3 text-center">Email not found.</div>';
        }
    } else {
        echo '<div class="alert alert-warning mt-3 text-center">Please enter your email.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Forgot Password</h4>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="username" class="form-label">Enter your email:</label>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Enter your email" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Send Reset Link</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
