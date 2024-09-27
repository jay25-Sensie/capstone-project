<?php
function sendToSemaphore($pid, $message) {
    // Set your Semaphore API key
    $apiKey = 'YOUR_SEMAPHORE_API_KEY'; // Make sure to replace this with your actual API key
    
    $url = 'https://api.semaphore.co/api/v4/messages/send';
    
    // Prepare the data
    $data = [
        'to' => $pid, // Phone number of the patient
        'message' => $message,
    ];
    
    // Initialize cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    // Execute the request
    $result = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        error_log('Curl error: ' . curl_error($ch));
        return false; // Handle the error as needed
    }
    
    // Close the connection
    curl_close($ch);

    return $result; // Return the API response if needed
}
?>
