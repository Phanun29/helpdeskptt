<?php
session_start();
include "config.php";

// Validate and retrieve the ticket ID
$id = isset($_POST['id']) && is_numeric($_POST['id']) ? $_POST['id'] : exit('invalid');
$ticket_id = $conn->query("SELECT ticket_id FROM tbl_ticket WHERE id = $id")->fetch_assoc()['ticket_id'] ?? exit('invalid');

// Define the target directory
$ticket_dir = "../uploads/$ticket_id";

// Delete files and directory
if (is_dir($ticket_dir)) {
    array_map('unlink', glob("$ticket_dir/*"));
    rmdir($ticket_dir);
}

// Delete related records from tbl_ticket_track and tbl_ticket_images
$queries = [
    "DELETE FROM tbl_ticket_track WHERE ticket_id = '$ticket_id'",
    "DELETE FROM tbl_ticket_images WHERE ticket_id = '$ticket_id'",
    "DELETE FROM tbl_ticket WHERE id = '$id'"
];

foreach ($queries as $query) {
    if (!$conn->query($query)) {
        error_log('Error: ' . $conn->error);
        echo 'error';
        exit();
    }
}

echo "success";
$conn->close();
