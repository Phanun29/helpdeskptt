<?php
session_start();
include "config.php";

// Check if the 'id' parameter is set in the POST request
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo 'invalid';
    exit();
}

$id = $_POST['id'];

// Prepare the SQL statement to delete the station
$query = "DELETE FROM tbl_station WHERE id = $id";
if ($conn->query($query) === TRUE) {
    echo 'success';
} else {
    echo "fail";
}
// Close the database connection
$conn->close();
