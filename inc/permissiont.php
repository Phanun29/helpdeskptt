<?php
// Function to check if the user has permission to add station
function canAddStation($rules_id, $conn)
{
    $query = "SELECT add_ticket_status FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['add_ticket_status'] == 1; // Check if add_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}

// Function to check if the user has permission to edit station
function canEditStation($rules_id, $conn)
{
    $query = "SELECT edit_ticket_status FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['edit_ticket_status'] == 1; // Check if edit_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}

// Function to check if the user has permission to delete station
function canDeleteStation($rules_id, $conn)
{
    $query = "SELECT delete_ticket_status FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['delete_ticket_status'] == 1; // Check if delete_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}
// Assume $user_id is fetched from session or database
$user_id = $fetch_info['users_id']; // Example user ID
// Fetch user details including rules_id
$query_user = "SELECT * FROM tbl_users WHERE users_id = $user_id";
$result_user = $conn->query($query_user);
if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $rules_id = $user['rules_id'];

    // Check if user has permission to add, edit, or delete stations
    $canAddStation = canAddStation($rules_id, $conn);
    $canEditStation = canEditStation($rules_id, $conn);
    $canDeleteStation = canDeleteStation($rules_id, $conn);
} else {
}
