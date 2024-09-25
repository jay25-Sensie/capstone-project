<?php
// submit_schedule.php


include("connection.php");

// Get the patient ID from the form
$pid = isset($_POST['pid']) ? htmlspecialchars($_POST['pid']) : '';

// Initialize an array to store medicine data
$medicine_data = [];

// Loop through the posted medicine data
for ($i = 0; $i < count($_POST['medicine_name']); $i++) {
    // Get the medicine name and schedule details
    $medicine_name = htmlspecialchars($_POST['medicine_name'][$i]);
    $time_1 = isset($_POST['time_1_taken'][$i]) ? (int)$_POST['time_1_taken'][$i] : 0;
    $time_2 = isset($_POST['time_2_taken'][$i]) ? (int)$_POST['time_2_taken'][$i] : 0;
    $time_3 = isset($_POST['time_3_taken'][$i]) ? (int)$_POST['time_3_taken'][$i] : 0;
    $before_time_1 = isset($_POST['before_time_1_taken'][$i]) ? (int)$_POST['before_time_1_taken'][$i] : 0;
    $before_time_2 = isset($_POST['before_time_2_taken'][$i]) ? (int)$_POST['before_time_2_taken'][$i] : 0;
    $before_time_3 = isset($_POST['before_time_3_taken'][$i]) ? (int)$_POST['before_time_3_taken'][$i] : 0;

    // Store data in the array
    $medicine_data[] = [
        'pid' => $pid,
        'medicine_name' => $medicine_name,
        'time_1' => $time_1,
        'time_2' => $time_2,
        'time_3' => $time_3,
        'before_time_1' => $before_time_1,
        'before_time_2' => $before_time_2,
        'before_time_3' => $before_time_3,
    ];
}

// Prepare and execute insert statements for each medicine
$stmt = $con->prepare("INSERT INTO medicine_schedule (pid, medicine_name, time_1_taken, before_time_1_taken, time_2_taken, before_time_2_taken, time_3_taken, before_time_3_taken) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

foreach ($medicine_data as $data) {
    $stmt->bind_param("ssiiiiii", $data['pid'], $data['medicine_name'], $data['time_1'], $data['before_time_1'], $data['time_2'], $data['before_time_2'], $data['time_3'], $data['before_time_3']);
    $stmt->execute();
}


// Close the statement and connection
$stmt->close();
$con->close();

// Redirect back to the prescribe page or another confirmation page
header("Location: prescribe.php?pid=" . $pid . "&success=1");
exit;
?>
