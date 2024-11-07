<?php
include("connection.php");

// Fetch the search term from the query string
$search_term = isset($_GET['q']) ? $_GET['q'] : '';

// Prevent SQL Injection (Assuming $conn is the MySQL connection)
$search_term = $con->real_escape_string($search_term);

// Query to fetch matching patients based on the search term
$query = "SELECT * FROM patient_records WHERE pid LIKE '%$search_term%' OR name LIKE '%$search_term%' OR lastname LIKE '%$search_term%'";

// Execute the query
$result = $con->query($query);

// Check if any results are found
if ($result->num_rows > 0) {
    echo "<ul class='list-group'>";
    while ($row = $result->fetch_assoc()) {
        // Output the patient suggestions
        echo "<li class='list-group-item' data-pid='" . $row['pid'] . "'>" . $row['pid'] . " - " . htmlspecialchars($row['name']) . " " . htmlspecialchars($row['lastname']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<li class='list-group-item'>No matching patients found.</li>";
}
?>