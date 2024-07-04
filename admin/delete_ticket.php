<?php
session_start();
include "config.php";

// Ensure the user is authenticated
if (!isset($_SESSION['email']) || !isset($_SESSION['password'])) {
    header("Location: ../index.php");
    exit();
}

// Check if the 'id' and 'page' parameters are set in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['page']) || !is_numeric($_GET['page'])) {
    header("HTTP/1.0 404 Not Found");
    include "404.php"; // Your custom 404 page
    exit();
}

$id = $_GET['id'];
$page = $_GET['page'];

// Prepare the SQL statement to retrieve the issue_image path and status
$query = "SELECT issue_image, status FROM tbl_ticket WHERE id = ?";
if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($issue_image_path, $ticket_status);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
    exit();
}

// Check if the ticket status is 'Close'
if ($ticket_status === 'Close') {
    $_SESSION['error_message'] = "Closed tickets cannot be deleted.";
    header("Location: ticket.php?page=" . $page);
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
        $_SESSION['success_message'] = "Ticket deleted successfully.";
        // Redirect to the ticket page with the current pagination
        header("Location: ticket.php?page=" . $page);
        exit();
    } else {
        echo "Error deleting record: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

// Close the database connection
$conn->close();
