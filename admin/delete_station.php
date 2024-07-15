<?php
session_start();
include "config.php";

// Ensure the user is authenticated
if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
    echo 'unauthorized';
    exit();
}

// Check if the 'id' parameter is set in the POST request
if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo 'invalid';
    exit();
}

$id = $_POST['id'];

// Prepare the SQL statement to delete the station
$query = "DELETE FROM tbl_station WHERE id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo 'success';
    } else {
        error_log('Error executing delete: ' . $stmt->error);
        echo 'fail';
    }
    $stmt->close();
} else {
    error_log('Error preparing delete statement: ' . $conn->error);
    echo 'error';
}

// Close the database connection
$conn->close();
?>
