<?php
include "config.php";
include "../inc/header.php"; // Include your database connection file

$user_id = $fetch_info['users_id'];

$query_user = "
    SELECT u.*, r.list_user_status, r.add_user_status, r.edit_user_status, r.delete_user_status 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    if (!$user['list_user_status'] || !$user['delete_user_status']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}
// Check if the user_id is set in the query string
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $rules_id = $_GET['id'];

    // Prepare the SQL statement to delete the user rule
    $delete_query = "DELETE FROM tbl_users WHERE users_id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $rules_id);

    try {
        if ($stmt->execute()) {
            // Redirect to the users page with a success message
            $_SESSION['success_message'] = "user deleted successfully.";
            header("Location: users.php");
            exit();
        } else {
            // Handle other errors
            $_SESSION['error_message'] = "Error deleting permission: " . $stmt->error;
            header("Location: users.php");
            exit();
        }
    } catch (mysqli_sql_exception $e) {
        // Check if the error is a foreign key constraint violation
        if ($e->getCode() == 1451) { // 1451 is the code for cannot delete or update a parent row: a foreign key constraint fails
            $_SESSION['error_message'] = "Cannot delete this user because it is associated with existing ticket.";
        } else {
            // Handle other SQL exceptions
            $_SESSION['error_message'] = "Error deleting user: " . $e->getMessage();
        }
        header("Location: users.php");
        exit();
    }

    $stmt->close();
} else {
    // Redirect to the users page if user_id is not set or not valid
    $_SESSION['error_message'] = "Invalid user ID.";
    header("Location: users.php");
    exit();
}
