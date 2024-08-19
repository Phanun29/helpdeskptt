<?php
session_start();
include "config.php";
$id = $_POST['id'];

// Prepare the SQL statement to delete the station
$query = "DELETE FROM tbl_telegram_bot WHERE id = $id";
if ($conn->query($query) === TRUE) {
    echo 'success';
} else {
    echo "fail";
}
// Close the database connection
$conn->close();
