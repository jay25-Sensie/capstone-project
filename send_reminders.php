<!-- <?php
// send_reminders.php

date_default_timezone_set('Asia/Manila'); // Set the correct timezone

// Include your database connection
include 'connection.php'; 

// Infobip API credentials
$infobip_api_key = ''; // Replace with your actual Infobip API key
$infobip_base_url = '388g6j.api.infobip.com'; // Infobip base URL
$sender = '447491163443'; // Replace with your approved Sender ID

// Log function with file locking
function logMessage($message) {
    $log_file = 'send_reminders_log.txt';
    $fp = fopen($log_file, 'a');
    if ($fp) {
        // Acquire an exclusive lock
        if (flock($fp, LOCK_EX)) {
            file_put_contents($log_file, date('Y-m-d H:i:s') . " - " . $message . "\n", FILE_APPEND);
            flock($fp, LOCK_UN); // Release the lock
        } else {
            logMessage("Error: Unable to acquire file lock for send_reminders_log.txt");
            fclose($fp); // Close the file
            return; // Stop further processing
        }
        fclose($fp);
    } else {
        error_log("Unable to open log file: $log_file");
    }
}

// Function to send SMS via Infobip
function sendSMS($phone_number, $message, $api_key, $sender, $base_url) {
    $url = "https://$base_url/sms/2/text/advanced";

    $data = [
        "messages" => [
            [
                "from" => $sender,
                "destinations" => [
                    ["to" => $phone_number]
                ],
                "text" => $message
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: App ' . $api_key,
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        curl_close($ch);
        logMessage("cURL Error: $error_msg");
        return ['success' => false, 'error' => $error_msg];
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    logMessage("Infobip Response: $response"); // Log the response for debugging
    logMessage("HTTP Status Code: $http_code");

    if ($http_code >= 200 && $http_code < 300) {
        return ['success' => true, 'response' => $response];
    } else {
        return ['success' => false, 'error' => "Infobip API request failed. HTTP Status Code: $http_code"];
    }
}

// Get current time in HH:MM:00 format
$current_time = date('H:i:00');
logMessage("Current Time: $current_time");

// Prepare SQL to fetch schedules matching current time and pending status
$sql = "
    SELECT ms.id, pr.phone_number, ms.medicine_name, 
           ms.dose_timing_1, ms.status_timing_1,
           ms.dose_timing_2, ms.status_timing_2,
           ms.dose_timing_3, ms.status_timing_3,
           ms.dose_timing_4, ms.status_timing_4,
           ms.dose_timing_5, ms.status_timing_5
    FROM medicine_schedule ms
    JOIN patient_records pr ON ms.pid = pr.pid
    WHERE 
        (ms.dose_timing_1 = ? AND ms.status_timing_1 = 'Pending') OR
        (ms.dose_timing_2 = ? AND ms.status_timing_2 = 'Pending') OR
        (ms.dose_timing_3 = ? AND ms.status_timing_3 = 'Pending') OR
        (ms.dose_timing_4 = ? AND ms.status_timing_4 = 'Pending') OR
        (ms.dose_timing_5 = ? AND ms.status_timing_5 = 'Pending')
";

$stmt = $con->prepare($sql);
if (!$stmt) {
    logMessage("SQL Prepare Error: " . $con->error);
    exit;
}

$stmt->bind_param("sssss", $current_time, $current_time, $current_time, $current_time, $current_time);
$stmt->execute();
$result = $stmt->get_result();

// Log the number of records found
$record_count = $result->num_rows;
logMessage("Records found: $record_count");

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $phone_number = $row['phone_number'];
    $medicine_name = $row['medicine_name'];

    logMessage("Processing SMS for $medicine_name to $phone_number");

    // Iterate through each dose timing
    for ($i = 1; $i <= 5; $i++) {
        $dose_time = $row["dose_timing_$i"];
        $status = $row["status_timing_$i"];

        if ($dose_time == $current_time && $status === 'Pending') {
            // Validate phone number format
            if (preg_match('/^\+?[0-9]{10,15}$/', $phone_number)) {
                $message = "Reminder: Take your medicine '$medicine_name' now.";
                $sms_result = sendSMS($phone_number, $message, $infobip_api_key, $sender, $infobip_base_url);

                if ($sms_result['success']) {
                    logMessage("SMS sent to $phone_number for '$medicine_name' at $dose_time.");

                    // Update the sent status in the database
                    $update_sql = "UPDATE medicine_schedule SET status_timing_$i = 'Sent' WHERE id = ?";
                    $update_stmt = $con->prepare($update_sql);
                    if ($update_stmt) {
                        $update_stmt->bind_param("i", $id);
                        $update_stmt->execute();
                        $update_stmt->close();
                    } else {
                        logMessage("SQL Update Prepare Error: " . $con->error);
                    }
                } else {
                    logMessage("Failed to send SMS to $phone_number. Error: {$sms_result['error']}");
                }

                // Log the SMS attempt
                file_put_contents('sms_log.txt', date('Y-m-d H:i:s') . " - To: $phone_number - Message: $message - Status: " . ($sms_result['success'] ? 'Success' : 'Failed') . "\n", FILE_APPEND);
            } else {
                logMessage("Invalid phone number format for $phone_number.");
            }
        }
    }
}

$stmt->close();
$con->close();
?> -->