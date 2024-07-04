<?php
include "config.php"; // Include your database connection file
session_start(); // Initialize session for messages

// Check if the rules_id is set in the query string
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $rules_id = $_GET['id'];

    // Prepare the SQL statement to delete the user rule
    $delete_query = "DELETE FROM tbl_station WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $rules_id);

    try {
        if ($stmt->execute()) {
            // Redirect to the station page with a success message
            $_SESSION['success_message'] = "Station deleted successfully.";
            header("Location: station.php");
            exit();
        } else {
            // Handle other errors
            $_SESSION['error_message'] = "Error deleting station: " . $stmt->error;
            header("Location: station.php");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Check if the error is a foreign key constraint violation
        if ($e->getCode() == 1451) { // 1451 is the code for cannot delete or update a parent row: a foreign key constraint fails
            $_SESSION['error_message'] = "Cannot delete this station because it is associated with existing tickets.";
        } else {
            // Handle other SQL exceptions
            $_SESSION['error_message'] = "Error deleting station: " . $e->getMessage();
        }
        header("Location: station.php");
        exit();
    }

    $stmt->close();
} else {
    // Redirect to the station page if id is not set or not valid
    $_SESSION['error_message'] = "Invalid station ID.";
    header("Location: station.php");
    exit();
}
