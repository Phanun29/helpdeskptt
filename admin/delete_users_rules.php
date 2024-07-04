<?php
include "config.php"; // Include your database connection file
session_start(); // Initialize session for messages

// Check if the user_id is set in the query string
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $rules_id = $_GET['id'];

    // Prepare the SQL statement to delete the user rule
    $delete_query = "DELETE FROM tbl_users_rules WHERE rules_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $rules_id);

    try {
        if ($stmt->execute()) {
            // Redirect to the users page with a success message
            $_SESSION['success_message'] = "Permission deleted successfully.";
            header("Location: users_rules.php");
            exit();
        } else {
            // Handle other errors
            $_SESSION['error_message'] = "Error deleting permission: " . $stmt->error;
            header("Location: users_rules.php");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Check if the error is a foreign key constraint violation
        if ($e->getCode() == 1451) { // 1451 is the code for cannot delete or update a parent row: a foreign key constraint fails
            $_SESSION['error_message'] = "Cannot delete this permission because it is associated with existing users.";
        } else {
            // Handle other SQL exceptions
            $_SESSION['error_message'] = "Error deleting permission: " . $e->getMessage();
        }
        header("Location: users_rules.php");
        exit();
    }

    $stmt->close();
} else {
    // Redirect to the users page if user_id is not set or not valid
    $_SESSION['error_message'] = "Invalid permission ID.";
    header("Location: users_rules.php");
    exit();
}
