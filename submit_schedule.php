<?php
// Include your database connection
include 'connection.php'; // Make sure this file defines $con

// Include the SMS sending function
include 'send_sms.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patientId = $_POST['pid']; // Use patient_id here
    $medicineNames = $_POST['medicine_name'];
    $dosesPerDay = $_POST['doses_per_day'];

    // Check if medication_id is set
    if (!isset($_POST['medication_id'])) {
        error_log("Medication IDs not found in POST data.");
        echo "Error: Medication IDs are missing.";
        exit;
    }

    $medicationIds = $_POST['medication_id']; // Assuming you have a medication_id array

    // Iterate over each medicine to collect the timings
    foreach ($medicineNames as $index => $medicineName) {
        $doses = $dosesPerDay[$index];

        // Check if medication_id is available for this index
        if (!isset($medicationIds[$index])) {
            error_log("Medication ID not found for index $index.");
            echo "Error: Medication ID is missing for one of the medicines.";
            continue; // Skip this iteration
        }

        $medicationId = $medicationIds[$index]; // Get the medication ID

        // Collect timings for the current medicine
        for ($i = 0; $i < $doses; $i++) {
            if (isset($_POST['dose_timings'][$index][$i])) {
                $timing = $_POST['dose_timings'][$index][$i];

                // Prepare the SQL statement
                $sql = "INSERT INTO medicine_schedule (patient_id, medication_id, doses_per_day, scheduled_time) VALUES (?, ?, ?, ?)";
                $stmt = $con->prepare($sql); // Assuming $con is from connection.php
                if ($stmt === false) {
                    echo "Error in SQL preparation: " . $con->error;
                    exit;
                }
                
                $stmt->bind_param("iiis", $patientId, $medicationId, $doses, $timing);

                // Execute the statement
                if ($stmt->execute()) {
                    // Prepare and send the SMS
                    sendSMS($patientId, $medicineName, $timing);
                } else {
                    error_log("Failed to insert medicine schedule: " . $stmt->error);
                    echo "Error: Could not insert the data. " . $stmt->error;
                }
            } else {
                error_log("Dose timing not found for index $index and dose $i");
            }
        }
    }

    // Redirect or provide feedback to the user
    echo "Schedule submitted successfully.";
}

// Example sendSMS function (you need to implement this)
function sendSMS($patientId, $medicineName, $timing) {
    $message = "Reminder: Take your $medicineName at $timing.";
    sendToSemaphore($patientId, $message); // Call the function defined in send_sms.php
}
?>
