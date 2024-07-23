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

// Define the target directory relative to the script
$target_dir = "../uploads/";

// Prepare the SQL statement to retrieve media paths from tbl_ticket_images
$query = "SELECT image_path FROM tbl_ticket_images WHERE ticket_id = ?";
$mediaPaths = []; // Initialize array to store media paths
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($media_path);

    // Fetch all media paths into an array
    while ($stmt->fetch()) {
        $mediaPaths[] = $media_path;
    }
    $stmt->close();
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

// Now delete records from tbl_ticket_images
$queryDeleteImages = "DELETE FROM tbl_ticket_images WHERE ticket_id = ?";
if ($stmtDeleteImages = $conn->prepare($queryDeleteImages)) {
    $stmtDeleteImages->bind_param("i", $id);
    if ($stmtDeleteImages->execute()) {
        error_log('Successfully deleted records from tbl_ticket_images for ticket_id: ' . $id);
        $stmtDeleteImages->close();
    } else {
        error_log('Error executing delete query for tbl_ticket_images: ' . $stmtDeleteImages->error);
        echo 'error';
        exit();
    }
} else {
    error_log('Error preparing delete statement for tbl_ticket_images: ' . $conn->error);
    echo 'error';
    exit();
}

// Proceed to delete the ticket from tbl_ticket
$queryDeleteTicket = "DELETE FROM tbl_ticket WHERE id = ?";
if ($stmtDeleteTicket = $conn->prepare($queryDeleteTicket)) {
    $stmtDeleteTicket->bind_param("i", $id);
    if ($stmtDeleteTicket->execute()) {
        echo 'success'; // Return success if everything deleted successfully
    } else {
        error_log('Error executing delete query for tbl_ticket: ' . $stmtDeleteTicket->error);
        echo 'fail';
    }
    $stmtDeleteTicket->close();
} else {
    error_log('Error preparing delete statement for tbl_ticket: ' . $conn->error);
    echo 'error';
}

// Close the database connection 
$conn->close();
