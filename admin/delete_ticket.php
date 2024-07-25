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

// Retrieve the ticket ID associated with the provided ID
$queryTicketId = "SELECT ticket_id FROM tbl_ticket WHERE id = $id";
$result = $conn->query($queryTicketId);
if ($result) {
    if ($row = $result->fetch_assoc()) {
        $ticket_id = $row['ticket_id'];
    }
} else {
    error_log('Error preparing statement to retrieve ticket ID: ' . $conn->error);
    echo 'error';
    exit();
}

if (empty($ticket_id)) {
    echo 'invalid';
    exit();
}

// Define the target directory relative to the script
$target_dir = "../uploads/";

// Prepare the SQL statement to retrieve media paths from tbl_ticket_images
$query = "SELECT image_path FROM tbl_ticket_images WHERE ticket_id = '$ticket_id'";
$mediaPaths = []; // Initialize array to store media paths
$result_ticket_id = $conn->query($query);
if ($result_ticket_id) {
    // Fetch all media paths into an array
    while ($row = $result_ticket_id->fetch_assoc()) {
        $mediaPaths[] = $row['image_path'];
    }
} else {
    error_log('Error preparing statement to retrieve media paths: ' . $conn->error);
    echo 'error';
    exit();
}

// Debug: Log media paths to ensure they're correct
error_log('Media Paths to Delete: ' . print_r($mediaPaths, true));

// Delete media files from the server
foreach ($mediaPaths as $relativePath) {
    // Convert relative path to absolute path
    $absolutePath = realpath($target_dir . ltrim($relativePath, '/')); // Ensure no leading slashes
    if ($absolutePath && file_exists($absolutePath)) {
        if (unlink($absolutePath)) {
            error_log('Successfully deleted file: ' . $absolutePath);
        } else {
            error_log('Error deleting file: ' . $absolutePath);
        }
    } else {
        error_log('File does not exist or invalid path: ' . $absolutePath);
    }
}

$ticket_dir = $target_dir . $ticket_id;
if (is_dir($ticket_dir)) {
    $files = glob($ticket_dir . '/*'); // Get all file names
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file); // Delete the file
        }
    }
    rmdir($ticket_dir); // Remove the directory
}
// Now delete records from tbl_ticket_images
$queryDeleteImages = "DELETE FROM tbl_ticket_images WHERE ticket_id = '$ticket_id'";
if ($conn->query($queryDeleteImages) == true) {
    error_log('Successfully deleted records from tbl_ticket_images for ticket_id: ' . $ticket_id);
} else {
    error_log('Error executing delete query for tbl_ticket_images: ' . $conn->error);
    echo 'error';
    exit();
}

// Proceed to delete the ticket from tbl_ticket
$queryDeleteTicket = "DELETE FROM tbl_ticket WHERE id = '$id'";
if ($conn->query($queryDeleteTicket) == true) {
    echo "success";
} else {
    echo "fail";
}

// Close the database connection
$conn->close();
