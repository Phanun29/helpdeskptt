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
$query = "DELETE FROM tbl_users WHERE users_id = $id";
if ($conn->query($query) === true) {
    echo 'success';
} else {
    echo 'fail';
}

// Close the database connection
$conn->close();
