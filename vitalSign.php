<?php
session_start();
include("connection.php");
include("function.php"); // Ensure this file contains necessary functions

// Function to sanitize input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $pid = isset($_POST['pid']) ? intval($_POST['pid']) : 0;
    $date = sanitize_input($_POST['date']);
    $bp = sanitize_input($_POST['bp']);
    $cr = sanitize_input($_POST['cr']);
    $rr = sanitize_input($_POST['rr']);
    $t = sanitize_input($_POST['t']);
    $wt = sanitize_input($_POST['wt']);
    $ht = sanitize_input($_POST['ht']);

    // Validate PID
    if ($pid > 0) {
        // Prepare the SQL query to insert vital signs
        $stmt = $con->prepare("INSERT INTO vital_signs (pid, date, bp, cr, rr, t, wt, ht) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $pid, $date, $bp, $cr, $rr, $t, $wt, $ht);

        // Execute the query
        if ($stmt->execute()) {
            // Close the statement
            $stmt->close();

            // Close the database connection
            $con->close();

            // Output JavaScript to show alert and redirect
            echo "<script>
                    alert('Vital signs successfully added!');
                    window.location.href = 'viewPatient_Admin.php?pid=$pid';
                  </script>";
            exit();
        } else {
            echo "<div class='alert alert-danger' style='text-align: center;'>Error inserting vital signs: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger' style='text-align: center;'>Invalid PID.</div>";
    }
}
?>
