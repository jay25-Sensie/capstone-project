<?php
include("connection.php");

// Get current time in the format 'H:i'
$current_time = date('H:i');

// Query to select medicine schedules that match the current time
$sql = "SELECT ms.pid, ms.medicine_name, p.phone_number 
        FROM medicine_schedule ms 
        JOIN patient_records p ON ms.pid = p.pid 
        WHERE ms.schedule_time = '$current_time' AND ms.taken_status = 0"; // Ensure column name matches your DB
$result = $con->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $phone_number = $row['phone_number'];
        $medicine_name = $row['medicine_name'];

        // Send SMS via your chosen service
        send_sms($phone_number, "It's time to take your medicine: $medicine_name");

        // Update taken_status to 1 after sending the notification
        $update_sql = "UPDATE medicine_schedule SET taken_status = 1 
                       WHERE pid = '{$row['pid']}' AND medicine_name = '$medicine_name'";
        $con->query($update_sql);
    }
}

$con->close();

function send_sms($number, $message) {
    $apiKey = 'YOUR_SEMAPHORE_API_KEY'; // Replace with your Semaphore API key
    $url = "https://app.semaphore.co/api/v4/messages/send";

    $data = [
        'to' => $number,
        'message' => $message,
        'from' => 'YOUR_SENDER_ID', // Replace with your sender ID
        'apiKey' => $apiKey,
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    // Log the result (for debugging)
    file_put_contents('sms_log.txt', print_r($result, true), FILE_APPEND);
}
?>
