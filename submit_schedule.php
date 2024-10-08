<?php
// submit_schedule.php

// Include your database connection
include 'connection.php'; // Ensure this file defines $con

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (
        empty($_POST['pid']) ||
        empty($_POST['medicine_name']) ||
        empty($_POST['doses_per_day']) ||
        empty($_POST['dose_timings'])
    ) {
        die("Error: Missing required fields.");
    }

    // Get form data
    $pid = intval($_POST['pid']);
    $medicine_names = $_POST['medicine_name']; // Array of medicine names
    $doses_per_day_array = $_POST['doses_per_day']; // Array of doses per day
    $dose_timings_array = $_POST['dose_timings']; // Array of timings

    // Fetch the patient's phone number based on the PID
    $patient_stmt = $con->prepare("SELECT phone_number FROM patient_records WHERE pid = ?");
    $patient_stmt->bind_param("i", $pid);
    $patient_stmt->execute();
    $patient_stmt->bind_result($phone_number);
    $patient_stmt->fetch();
    $patient_stmt->close();

    if (empty($phone_number)) {
        die("Error: Patient's phone number not found.");
    }

    // Loop through each medicine entry
    for ($i = 0; $i < count($medicine_names); $i++) {
        $medicine_name = trim($medicine_names[$i]); // Get medicine name
        $doses_per_day = intval($doses_per_day_array[$i]); // Get doses per day
        $timings = $dose_timings_array[$i]; // Get timings for this medicine

        // Assuming timings are in the format "HH:MM"
        $timing1 = isset($timings[0]) ? $timings[0] : NULL;
        $timing2 = isset($timings[1]) ? $timings[1] : NULL;
        $timing3 = isset($timings[2]) ? $timings[2] : NULL;
        $timing4 = isset($timings[3]) ? $timings[3] : NULL;
        $timing5 = isset($timings[4]) ? $timings[4] : NULL;

        // Prepare SQL statement to insert into the medicine_schedule table
        $insert_stmt = $con->prepare("INSERT INTO medicine_schedule 
            (pid, medicine_name, doses_per_day, dose_timing_1, dose_timing_2, dose_timing_3, dose_timing_4, dose_timing_5, created_at, updated_at, status_timing_1, status_timing_2, status_timing_3, status_timing_4, status_timing_5)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'Pending', 'Pending', 'Pending', 'Pending', 'Pending')");
        
        if ($insert_stmt) {
            // Initialize variables for binding
            $param_pid = $pid;
            $param_medicine_name = $medicine_name;
            $param_doses_per_day = $doses_per_day;
            $param_timing1 = $timing1;
            $param_timing2 = $timing2;
            $param_timing3 = $timing3;
            $param_timing4 = $timing4;
            $param_timing5 = $timing5;

            // Create the types string based on parameters
            $types = "isssssss"; // 7 parameters: 1 int, 6 strings

            // Prepare final parameters array for binding
            $final_params = [
                &$param_pid,
                &$param_medicine_name,
                &$param_doses_per_day,
                &$param_timing1,
                &$param_timing2,
                &$param_timing3,
                &$param_timing4,
                &$param_timing5,
            ];

            // Use call_user_func_array for dynamic parameter binding
            call_user_func_array([$insert_stmt, 'bind_param'], array_merge([$types], $final_params));
            $insert_stmt->execute();
            $insert_stmt->close();
        } else {
            // Log error if the statement preparation fails
            error_log("Insert statement preparation failed: " . $con->error);
        }
    }

    // Close the database connection
    $con->close();

    // Display a success alert and redirect
    echo "<script type='text/javascript'>
            alert('Medicine schedule created successfully!');
          </script>";
    exit;
}
?>