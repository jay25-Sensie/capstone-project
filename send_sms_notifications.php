<?php
// send_sms_notifications.php
include 'connection.php';

// Semaphore API credentials
$apiKey = 'your_semaphore_api_key'; // Your Semaphore API key

// Current time for checking notifications
$current_time = date('H:i');

// Get scheduled medications that need notification
$sql = "SELECT ms.patient_id, ms.medication_id, ms.scheduled_time, pr.phone_number 
        FROM medicine_schedule ms 
        JOIN patient_records pr ON ms.patient_id = pr.pid 
        WHERE ms.scheduled_time = '$current_time' AND ms.taken_status = 0";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patient_id = $row['patient_id'];
        $medication_id = $row['medication_id'];
        $phone_number = $row['phone_number'];

        // Prepare SMS message
        $message = "Reminder: It's time to take your medication.";

        // Send SMS notification via Semaphore API
        $url = "https://semapi.com/api/v4/message/send";
        $data = [
            'apikey' => $apiKey,
            'to' => $phone_number,
            'message' => $message,
        ];

        // Use cURL to send the SMS
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($ch);
        curl_close($ch);

        // Update the notification status (You may want to handle this better)
        $conn->query("UPDATE medicine_schedule SET taken_status = 1 WHERE patient_id = '$patient_id' AND medication_id = '$medication_id'");
    }
} else {
    echo "No notifications to send.";
}
?>
