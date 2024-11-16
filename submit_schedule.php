<?php
include 'connection.php';

// Semaphore API credentials
// $semaphore_api_key = '598b6a6303a6fb12fe5a5f46d1af565f'; 
$sender_name = 'Thesis'; 

// Function to send SMS via Semaphore
function sendSMS($phone_number, $message, $api_key) {
    $url = "https://api.semaphore.co/api/v4/messages";

    $data = [
        "apikey" => $api_key,
        "number" => $phone_number,
        "message" => $message,
        "sendername" => 'Thesis'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "CURL Error: " . curl_error($ch);
    }

    curl_close($ch);

    return $response;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['pid'])) {
        echo "<script>alert('Error: PID is missing.');</script>";
        exit;
    }

    $pid = $_POST['pid'];
    $medicine_names = $_POST['medicine_name'];
    $doses_per_day_array = $_POST['doses_per_day'];
    $dose_timings_array = $_POST['dose_timings'];
    $meal_timings_array = isset($_POST['meal_time']) ? $_POST['meal_time'] : [];

    // Fetch the patient's phone number based on the PID
    $patient_stmt = $con->prepare("SELECT phone_number FROM patient_records WHERE pid = ?");
    $patient_stmt->bind_param("i", $pid);
    $patient_stmt->execute();
    $patient_stmt->bind_result($phone_number);
    $patient_stmt->fetch();
    $patient_stmt->close();

    if (empty($phone_number)) {
        echo "<script>alert('Error: Patient\'s phone number not found.');</script>";
        exit;
    }

    // phone number is formatted correctly (remove any extra "+" country code prefix)
    $phone_number = preg_replace('/^\+?63/', '63', $phone_number);

    for ($i = 0; $i < count($medicine_names); $i++) {
        $medicine_name = $medicine_names[$i];
        $doses_per_day = $doses_per_day_array[$i];
        $timings = $dose_timings_array[$i];
        $meal_timing = isset($meal_timings_array[$i]) ? $meal_timings_array[$i] : null; 
        $timing1 = $timings[0] ?? null;
        $timing2 = $timings[1] ?? null;
        $timing3 = $timings[2] ?? null;
        $timing4 = $timings[3] ?? null;
        $timing5 = null;

        // Check if the record already exists
        $checkStmt = $con->prepare("
            SELECT COUNT(*) FROM medicine_schedule 
            WHERE pid = ? AND medicine_name = ? AND doses_per_day = ? 
            AND dose_timing_1 = ? AND dose_timing_2 = ? 
            AND dose_timing_3 = ? AND dose_timing_4 = ?
        ");
        $checkStmt->bind_param("issssss", $pid, $medicine_name, $doses_per_day, $timing1, $timing2, $timing3, $timing4);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count == 0) {
            // Insert new record if it doesn't exist
            $stmt = $con->prepare("
                INSERT INTO medicine_schedule 
                (pid, medicine_name, doses_per_day, dose_timing_1, dose_timing_2, dose_timing_3, dose_timing_4, dose_timing_5, meal_timing, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->bind_param("issssssss", $pid, $medicine_name, $doses_per_day, $timing1, $timing2, $timing3, $timing4, $timing5, $meal_timing);

            if ($stmt->execute()) {
                echo "<script>alert('Medicine schedule created successfully!');</script>";

                $dose_timings = array_filter([$timing1, $timing2, $timing3, $timing4]);
                $meal_message = $meal_timing ? " with meal timing $meal_timing" : "";

                // Send SMS reminders for each timing
                foreach ($dose_timings as $timing) {
                    if (!empty($timing)) {
                        $message = "Reminder: Take your $medicine_name at " . date('h:i A', strtotime($timing)) . $meal_message;
                        
                        $sms_response = sendSMS($phone_number, $message, $semaphore_api_key);

                        // Decode the response to check the status
                        $response_data = json_decode($sms_response, true);
                        if (isset($response_data['status']) && $response_data['status'] === "Queued") {
                            echo "<script>alert('SMS reminder sent successfully for $medicine_name at $timing to $phone_number.'); window.location.href = 'Doctor_Prescription.php';</script>";
                        }

                        file_put_contents('sms_log.txt', date('Y-m-d H:i:s') . " - $message - Response: $sms_response\n", FILE_APPEND);
                    }
                }
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('Record for $medicine_name already exists.');</script>";
        }
    }
}
?>