<?php
include "config.php"; // Ensure your database connection is included

// Initialize variables for filters (assuming they are posted from index.php)
$station_id = isset($_POST['station_id']) ? $_POST['station_id'] : '';
$issue_type = isset($_POST['issue_type']) ? $_POST['issue_type'] : '';
$priority = isset($_POST['priority']) ? $_POST['priority'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';
$users_id = isset($_POST['users_id']) ? $_POST['users_id'] : '';
$ticket_open_from = isset($_POST['ticket_open_from']) ? $_POST['ticket_open_from'] : '';
$ticket_open_to = isset($_POST['ticket_open_to']) ? $_POST['ticket_open_to'] : '';
$ticket_close_from = isset($_POST['ticket_close_from']) ? $_POST['ticket_close_from'] : '';
$ticket_close_to = isset($_POST['ticket_close_to']) ? $_POST['ticket_close_to'] : '';

// Base query
$ticket_query = "
    SELECT 
        t.*, 
        REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
    FROM 
        tbl_ticket t
    LEFT JOIN 
        tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
    WHERE 1=1";

// Append conditions based on filter parameters
if (!empty($station_id)) {
    $ticket_query .= " AND t.station_id = '$station_id'";
}

if (!empty($issue_type)) {
    $ticket_query .= " AND t.issue_type = '$issue_type'";
}

if (!empty($priority)) {
    $ticket_query .= " AND t.priority = '$priority'";
}

if (!empty($status)) {
    $ticket_query .= " AND t.status = '$status'";
}

if (!empty($users_id)) {
    $ticket_query .= " AND FIND_IN_SET('$users_id', t.users_id)";
}

if (!empty($ticket_open_from)) {
    $ticket_query .= " AND t.ticket_open >= '$ticket_open_from'";
}

if (!empty($ticket_open_to)) {
    $ticket_query .= " AND t.ticket_open <= '$ticket_open_to'";
}

if (!empty($ticket_close_from)) {
    $ticket_query .= " AND t.ticket_close >= '$ticket_close_from'";
}

if (!empty($ticket_close_to)) {
    $ticket_query .= " AND t.ticket_close <= '$ticket_close_to'";
}

$ticket_query .= " GROUP BY t.ticket_id DESC";

$ticket_result = $conn->query($ticket_query);

// Build HTML for table rows
$html = '';
$i = 1;
if ($ticket_result->num_rows > 0) {
    while ($row = $ticket_result->fetch_assoc()) {
        $html .= "<tr>";
        $html .= "<td class='py-1'>" . $i++ . "</td>";
        // Add other table columns as per your current implementation
        $html .= "</tr>";
    }
} else {
    $html = "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
}

echo $html;
