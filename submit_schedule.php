<?php
// Include your database connection
include 'connection.php'; // Make sure this file defines $con

// Your Semaphore API key
$semaphore_api_key = 'YOUR_SEMAPHORE_API_KEY'; // Replace with your actual API key

// Function to send SMS via Semaphore
function sendSMS($phone_number, $message, $api_key) {
    $url = 'https://api.semaphore.co/api/v4/messages';

    $data = array(
        'apikey' => $api_key,
        'number' => $phone_number,
        'message' => $message,
        'sendername' => 'SEMAPHORE'
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
}

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
                echo "Record for $medicine_name added successfully.<br>";

                // Now send SMS reminders based on the timings
                $dose_timings = [$timing1, $timing2, $timing3, $timing4]; // Gather dose timings into an array

                foreach ($dose_timings as $timing) {
                    if (!empty($timing)) {
                        // Prepare the SMS message
                        $message = "Reminder: It's time to take your medicine $medicine_name at $timing.";

                        // Send the SMS using Semaphore
                        $sms_response = sendSMS($phone_number, $message, $semaphore_api_key);

                        // Output for debugging
                        echo "SMS reminder sent for $medicine_name at $timing to $phone_number. Response: $sms_response<br>";
                    }
                }
            } else {
                echo "Error: " . $stmt->error . "<br>";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Duplicate entry for $medicine_name with doses $doses_per_day and timings: $timing1, $timing2, $timing3, $timing4.<br>";
        }
    }

    // Close the connection
    $con->close();
}
?>
