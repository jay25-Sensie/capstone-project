<?php
include 'connection.php';

// Your Infobip API credentials
$infobip_api_key = 'ac157f43605f77922500584d70356284-a0e348e7-0e38-4ee2-9a94-77d305e5a51'; 
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
    $dose_timings_array = $_POST['dose_timings']; // Array of timings for each dose
    $meal_timings_array = isset($_POST['meal_time']) ? $_POST['meal_time'] : []; // Default to empty array if meal_time is not set

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
        $meal_timing = isset($meal_timings_array[$i]) ? $meal_timings_array[$i] : 0; // Default to 0 if not set

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
                (pid, medicine_name, doses_per_day, dose_timing_1, dose_timing_2, dose_timing_3, dose_timing_4, dose_timing_5, meal_timing, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->bind_param("issssssss", $pid, $medicine_name, $doses_per_day, $timing1, $timing2, $timing3, $timing4, $timing5, $meal_timing);

            if ($stmt->execute()) {
                // Send SMS reminder to the patient (notification)
                $message = "Reminder: It's time to take your $medicine_name.";
                sendSMS($phone_number, $message, $infobip_api_key, $sender, $infobip_base_url);
                echo "<script>
                alert('Schedule saved and SMS sent successfully!')
                window.location.href = 'Doctor_Prescription.php'
                </script>";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "<script>alert('Duplicate entry found for this schedule!')</script>";
        }
    }
}
?>
