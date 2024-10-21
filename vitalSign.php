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
        // Check if the patient already has vital signs saved
        $stmt_check = $con->prepare("SELECT pid FROM vital_signs WHERE pid = ? ORDER BY date DESC LIMIT 1");
        $stmt_check->bind_param("i", $pid);
        $stmt_check->execute();
        $stmt_check->store_result();

        // If the patient has existing records, update; otherwise, insert new data
        if ($stmt_check->num_rows > 0) {
            // Update existing vital signs record
            $stmt = $con->prepare("UPDATE vital_signs SET date = ?, bp = ?, cr = ?, rr = ?, t = ?, wt = ?, ht = ? WHERE pid = ?");
            $stmt->bind_param("sssssssi", $date, $bp, $cr, $rr, $t, $wt, $ht, $pid);
        } else {
            // Insert new vital signs record
            $stmt = $con->prepare("INSERT INTO vital_signs (pid, date, bp, cr, rr, t, wt, ht) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssss", $pid, $date, $bp, $cr, $rr, $t, $wt, $ht);
        }

        // Execute the query
        if ($stmt->execute()) {
            // Close the statement
            $stmt->close();
            $stmt_check->close();

            // Close the database connection
            $con->close();

            // Output JavaScript to show alert and redirect
            echo "<script>
                    alert('Vital signs successfully added/updated!');
                    window.location.href = 'viewPatient_Admin.php?pid=$pid';
                  </script>";
            exit();
        } else {
            echo "<div class='alert alert-danger' style='text-align: center;'>Error inserting or updating vital signs: " . $stmt->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger' style='text-align: center;'>Invalid PID.</div>";
    }
}

// Display height and weight for the given PID
if (isset($_POST['pid'])) {
    $pid = intval($_POST['pid']);

    // Prepare and execute a query to fetch height and weight
    $stmt_display = $con->prepare("SELECT ht, wt FROM vital_signs WHERE pid = ? ORDER BY date DESC LIMIT 1");
    $stmt_display->bind_param("i", $pid);
    $stmt_display->execute();
    $stmt_display->bind_result($height, $weight);
    $stmt_display->fetch();
    
    if ($stmt_display->num_rows > 0) {
        echo "<div class='alert alert-info' style='text-align: center;'>
                Height: $height cm<br>
                Weight: $weight kg
              </div>";
    } else {
        echo "<div class='alert alert-warning' style='text-align: center;'>No records found for PID: $pid.</div>";
    }
    
    // Close the display statement
    $stmt_display->close();
}

// Close the database connection
$con->close();
?>