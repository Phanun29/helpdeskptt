<?php

include "config.php";
include "../inc/header.php";

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id'];
$query_user = "
    SELECT u.*, r.list_ticket_status, r.add_ticket_status, r.edit_ticket_status, r.delete_ticket_status 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";
$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    if (!$user['list_ticket_status'] || !$user['delete_ticket_status']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
    header("Location: ticket.php?id=" . $_GET['id']);
    exit();
}

$id = $_GET['id'];
$query = "SELECT issue_image, status FROM tbl_ticket WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($issue_image_path, $ticket_status);
$stmt->fetch();
$stmt->close();

if ($ticket_status === 'Close') {
    $_SESSION['error_message'] = "Closed tickets cannot be deleted.";
    header("Location: ticket.php?id=" . $id);
    exit();
}

$query = "DELETE FROM tbl_ticket WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if (!empty($issue_image_path)) {
        $image_paths = array_map('trim', explode(',', $issue_image_path));
        foreach ($image_paths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
    $_SESSION['success_message'] = "Ticket deleted successfully.";
} else {
    $_SESSION['error_message'] = "Error deleting record: " . $stmt->error;
}

$stmt->close();
$conn->close();
header("Location: ticket.php?id=" . $id);
exit();
