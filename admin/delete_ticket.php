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

// Prepare the SQL statement to retrieve the issue_image path and status
$query = "SELECT issue_image, status FROM tbl_ticket WHERE id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($issue_image_path, $ticket_status);
    $stmt->fetch();
    $stmt->close();
} else {
    echo 'error';
    exit();
}

// Check if the ticket status is 'Close'
if ($ticket_status === 'Close') {
    echo 'closed';
    exit();
}

// Prepare the SQL statement to delete the ticket
$query = "DELETE FROM tbl_ticket WHERE id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        // If the deletion is successful, delete the image file
        if (!empty($issue_image_path)) {
            $image_paths = explode(',', $issue_image_path);
            foreach ($image_paths as $path) {
                $path = trim($path); // Ensure there are no leading/trailing spaces
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
        echo 'success';
    } else {
        echo 'fail';
    }
    $stmt->close();
} else {
    echo 'error';
}

// Close the database connection
$conn->close();
