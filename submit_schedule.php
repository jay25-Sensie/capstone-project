<?php
include 'connection.php';

// Your Infobip API credentials
$infobip_api_key = 'ac157f43605f77922500584d70356284-a0e348e7-0e38-4ee2-9a94-77d305e5a519'; 
$infobip_base_url = 'api.infobip.com';
$sender = '447491163443';

// Function to send SMS via Infobip
function sendSMS($phone_number, $message, $api_key, $sender, $base_url) {
    $url = "https://$base_url/sms/2/text/advanced";

    $data = [
        "messages" => [
            [
                "from" => $sender,
                "destinations" => [
                    [
                        "to" => $phone_number
                    ]
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
    curl_close($ch);
    
    return $response;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if 'pid' exists in the POST array
    if (!isset($_POST['pid'])) {
        die("Error: PID is missing.");
    }

    $pid = $_POST['pid'];
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
        $medicine_name = $medicine_names[$i]; // Get medicine name
        $doses_per_day = $doses_per_day_array[$i]; // Get doses per day
        $timings = $dose_timings_array[$i]; // Get timings for this medicine

        // Initialize variables for timings
        $timing1 = $timings[0] ?? null; // Get first timing
        $timing2 = $timings[1] ?? null; // Get second timing
        $timing3 = $timings[2] ?? null; // Get third timing
        $timing4 = $timings[3] ?? null; // Get fourth timing
        $timing5 = null; // Optional fifth timing, if not needed set as null

        // Check for duplicates (optional but good to avoid redundant entries)
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

        // If no duplicate found, insert the record
        if ($count == 0) {
            $stmt = $con->prepare("
                INSERT INTO medicine_schedule 
                (pid, medicine_name, doses_per_day, dose_timing_1, dose_timing_2, dose_timing_3, dose_timing_4, dose_timing_5, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->bind_param("isssssss", $pid, $medicine_name, $doses_per_day, $timing1, $timing2, $timing3, $timing4, $timing5);

            if ($stmt->execute()) {
                echo "<script>
                        alert('Medicine schedule created successfully!');
                        window.location.href = 'Doctor_Prescription.php'; // Redirect after alert
                      </script>";

                // Now send SMS reminders based on the timings
                $dose_timings = array_filter([$timing1, $timing2, $timing3, $timing4]); // Gather only non-empty timings

                foreach ($dose_timings as $timing) {
                    if (!empty($timing)) {
                        // Prepare the SMS message
                        $message = "Reminder: take your medicine $medicine_name at " . date('h:i A', strtotime($timing)); // Format time to 12-hour format

                        // Send the SMS using Infobip
                        $sms_response = sendSMS($phone_number, $message, $infobip_api_key, $sender, $infobip_base_url);

                        // Handle the response
                        $response_data = json_decode($sms_response, true);
                        if (isset($response_data['messages'][0]['status']['groupName']) && 
                            $response_data['messages'][0]['status']['groupName'] === "SENT") {
                            echo "SMS reminder sent successfully for $medicine_name at $timing to $phone_number.<br>";
                        } else {
                            echo "Failed to send SMS for $medicine_name at $timing. Response: $sms_response<br>";
                        }
                        
                        // Optional: Log the SMS response
                        file_put_contents('sms_log.txt', date('Y-m-d H:i:s') . " - $message - Response: $sms_response\n", FILE_APPEND);
                    }
                }
            } else {
                echo "Error: " . $stmt->error . "<br>";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Record for $medicine_name already exists.<br>";
        }
    }
}
?>
