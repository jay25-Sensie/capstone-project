<?php
// Include your database connection
include 'connection.php'; // Make sure this file defines $con

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging output
    echo "<pre>";
    print_r($_POST); // Print the entire POST array
    echo "</pre>";

    // Check if 'pid' exists in the POST array
    if (!isset($_POST['pid'])) {
        die("Error: PID is missing.");
    }

    $pid = $_POST['pid'];
    $medicine_name = $_POST['medicine_name'][0]; // Assuming a single medicine name is sent
    $doses_per_day = $_POST['doses_per_day'][0]; // Assuming a single doses per day is sent
    $timings = $_POST['dose_timings'][0]; // Access the first set of timings

    // Initialize variables for timings
    $timing1 = $timings[0] ?? null; // Get first timing
    $timing2 = $timings[1] ?? null; // Get second timing
    $timing3 = $timings[2] ?? null; // Get third timing
    $timing4 = $timings[3] ?? null; // Get fourth timing
    $timing5 = null; // Initialize as null if not used

    // Check for duplicates
    $checkStmt = $con->prepare("
        SELECT COUNT(*) FROM medicine_schedule 
        WHERE pid = ? AND medicine_name = ? AND doses_per_day = ? 
        AND dose_timing_1 = ? AND dose_timing_2 = ? 
        AND dose_timing_3 = ? AND dose_timing_4 = ? 
        AND dose_timing_5 = ?
    ");
    $checkStmt->bind_param("isssssss", $pid, $medicine_name, $doses_per_day, $timing1, $timing2, $timing3, $timing4, $timing5);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    // If no duplicate found, insert the record
    if ($count == 0) {
        $stmt = $con->prepare("
            INSERT INTO medicine_schedule 
            (pid, medicine_name, doses_per_day, dose_timing_1, dose_timing_2, dose_timing_3, dose_timing_4, dose_timing_5, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param("isssssss", $pid, $medicine_name, $doses_per_day, $timing1, $timing2, $timing3, $timing4, $timing5);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Record for $medicine_name added successfully.<br>";
        } else {
            echo "Error: " . $stmt->error . "<br>";
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Duplicate entry for $medicine_name with doses $doses_per_day and timings: $timing1, $timing2, $timing3, $timing4, $timing5.<br>";
    }

    // Close the connection
    $con->close();
}
?>
