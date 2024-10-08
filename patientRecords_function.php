<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("connection.php");

// Function to sanitize input data
function sanitize_input($con, $data) {
    return mysqli_real_escape_string($con, htmlspecialchars(strip_tags($data)));
}

// Function to check if a patient already exists
function check_patient_exists($con, $name, $lastname, $phone_number, $pid = null) {
    $query = "SELECT * FROM patient_records WHERE name = '$name' AND lastname = '$lastname' AND phone_number = '$phone_number' ";
    if ($pid) {
        $query .= " AND pid != $pid";
    }
    $result = mysqli_query($con, $query);
    return mysqli_num_rows($result) > 0;
}

// Function to format the phone number for the Philippines
function format_phone_number($phone_number) {
    // Remove all non-digit characters
    $phone_number = preg_replace('/\D/', '', $phone_number);

    // Check if the phone number starts with '0' and replace it with '+63'
    if (strlen($phone_number) === 11 && substr($phone_number, 0, 1) === '0') {
        return '+63' . substr($phone_number, 1); // Remove leading 0 and prepend +63
    } elseif (strlen($phone_number) === 10) {
        return '+63' . $phone_number; // If it has 10 digits, prepend +63
    }
    return $phone_number; // Return as is if it doesn't need formatting
}

// Function to check if vital signs already exist for a patient on a specific date
function check_vital_signs_exist($con, $pid, $date) {
    $query = "SELECT * FROM vital_signs WHERE pid = '$pid' AND date = '$date'";
    $result = mysqli_query($con, $query);
    return mysqli_num_rows($result) > 0;
}

// Function to get the last recorded height and weight for a patient
function get_last_height_weight($con, $pid) {
    $query = "SELECT ht, wt FROM vital_signs WHERE pid = '$pid' ORDER BY date DESC LIMIT 1";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return array('ht' => $row['ht'], 'wt' => $row['wt']);
    } else {
        return array('ht' => null, 'wt' => null);
    }
}

// Add/Update patient, Update status, Add Vital Signs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_patient']) || isset($_POST['update_patient']) || isset($_POST['update_status'])) {
        // ... (Patient record validation code remains the same) ...
    } elseif (isset($_POST['save_vital_signs'])) {
        $pid = intval($_POST['pid']);
        $date = sanitize_input($con, $_POST['date']);
        $bp = intval($_POST['bp']); // Assuming blood pressure is entered as a single integer
        $cr = intval($_POST['cr']);
        $rr = intval($_POST['rr']);
        $t = floatval($_POST['t']); // Temperature can be a decimal
        $wt = isset($_POST['wt']) ? floatval($_POST['wt']) : null; // Weight can be a decimal
        $ht = isset($_POST['ht']) ? intval($_POST['ht']) : null; // Height can be a decimal

        // Validate vital signs (using ranges)
        $validation_passed = true;

        // Blood Pressure
        if ($bp < 60 || $bp > 200) {
            echo "<script>alert('Blood pressure must be between 60 and 200.');</script>";
            $validation_passed = false;
        }

        // Heart Rate
        if ($cr < 40 || $cr > 200) {
            echo "<script>alert('Heart rate must be between 40 and 200.');</script>";
            $validation_passed = false;
        }

        // Respiratory Rate
        if ($rr < 8 || $rr > 30) {
            echo "<script>alert('Respiratory rate must be between 8 and 30.');</script>";
            $validation_passed = false;
        }

        // Temperature (Celsius)
        if ($t < 35 || $t > 42) {
            echo "<script>alert('Temperature must be between 35 and 42 degrees Celsius.');</script>";
            $validation_passed = false;
        }

        // Check if vital signs already exist for this patient on this date
        if (check_vital_signs_exist($con, $pid, $date)) {
            echo "<script>alert('Vital signs already exist for this patient on this date.');</script>";
            $validation_passed = false;
        }

        if ($validation_passed) {
            // Insert vital signs into the database
            $query = "INSERT INTO vital_signs (pid, date, bp, cr, rr, t, wt, ht) VALUES ('$pid', '$date', '$bp', '$cr', '$rr', '$t', '$wt', '$ht')";

            if (mysqli_query($con, $query)) {
                header("Location: patientRecords.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($con);
            }
        } else {
            // If validation failed, stay on the form page
            echo "<script>window.history.back();</script>";
            exit();
        }
    }
}

// Fetch all patient records
$query = "SELECT * FROM patient_records";
$result = mysqli_query($con, $query);

if (!$result) {
    die("Database query failed: " . mysqli_error($con));
}

$patients = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>