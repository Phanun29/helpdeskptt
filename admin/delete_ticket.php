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
$uploadDir = '../uploads/'; // Ensure this is defined in the scope

// Initialize variables
$imagePaths = []; // Array to store image paths

// Prepare the SQL statement to retrieve image paths from tbl_ticket_images
$query = "SELECT image_path FROM tbl_ticket_images WHERE ticket_id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image_path);

    // Fetch all image paths into an array
    while ($stmt->fetch()) {
        $imagePaths[] = $image_path;
    }
    $stmt->close();
} else {
    error_log('Error preparing statement to retrieve image paths: ' . $conn->error);
    echo 'error';
    exit();
}

// Delete images from server and records from tbl_ticket_images
if (!empty($imagePaths)) {
    foreach ($imagePaths as $path) {
        $path = trim($path); // Ensure no leading/trailing spaces
        $fullPath = realpath($uploadDir . $path); // Get absolute path

        // Log the full path for debugging
        error_log('Attempting to delete file: ' . $fullPath);

        if ($fullPath && file_exists($fullPath)) {
            if (!unlink($fullPath)) {
                error_log('Failed to delete file: ' . $fullPath . ' Error: ' . print_r(error_get_last(), true));
                echo 'error';
                exit();
            } else {
                error_log('Successfully deleted file: ' . $fullPath);
            }
        } else {
            error_log('File does not exist or path is invalid: ' . $fullPath);
        }
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

// After deleting images and records, proceed to delete the ticket from tbl_ticket
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
