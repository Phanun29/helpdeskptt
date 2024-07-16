<?php
include "config.php";
include "../inc/header.php";
$user_id = $fetch_info['users_id']; // Example user ID
$query_user = "
    SELECT u.*, r.list_ticket_status, r.add_ticket_status, r.edit_ticket_status, r.delete_ticket_status ,r.list_ticket_assign
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    $listTicket = $user['list_ticket_status'];
    $AddTicket = $user['add_ticket_status'];
    $EditTicket = $user['edit_ticket_status'];
    $DeleteTicket = $user['delete_ticket_status'];
    $listTicketAssign = $user['list_ticket_assign'];

    if (!$listTicket) {
        header("location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}
// Base ticket query
$ticket_query = "
    SELECT 
        t.*, 
        REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
    FROM 
        tbl_ticket t
    LEFT JOIN 
        tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
";

// Adjust query based on user type
if ($listTicketAssign == 1) {
    $ticket_query .= " WHERE FIND_IN_SET($user_id, t.users_id)";
} else {
    $ticket_query .= " WHERE 1=1"; // Default condition for further appending
}

// Add conditions based on submitted form data
$conditions = [];

// Add conditions based on submitted form data
if (!empty($_GET['station_id'])) {
    $station_id = $conn->real_escape_string($_GET['station_id']);
    $ticket_query .= " AND t.station_id = '$station_id'";
}

if (!empty($_GET['province'])) {
    $province = $conn->real_escape_string($_GET['province']);
    $ticket_query .= " AND t.province = '$province'";
}
if (!empty($_GET['issue_type'])) {
    $issue_type = $conn->real_escape_string($_GET['issue_type']);
    $ticket_query .= " AND t.issue_type LIKE '%$issue_type%'";
}

if (!empty($_GET['SLA_category'])) {
    $SLA_category = $conn->real_escape_string($_GET['SLA_category']);
    $ticket_query .= " AND t.SLA_category = '$SLA_category'";
}

if (!empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $ticket_query .= " AND t.status = '$status'";
}

if (!empty($_GET['users_id'])) {
    $users_id = $conn->real_escape_string($_GET['users_id']);
    $ticket_query .= " AND FIND_IN_SET('$users_id', t.users_id)";
}

if (!empty($_GET['ticket_open_from'])) {
    $ticket_open_from = $conn->real_escape_string($_GET['ticket_open_from']);
    $ticket_query .= " AND t.ticket_open >= '$ticket_open_from'";
}

if (!empty($_GET['ticket_open_to'])) {
    $ticket_open_to = $conn->real_escape_string($_GET['ticket_open_to']);
    $ticket_query .= " AND t.ticket_open <= '$ticket_open_to'";
}

if (!empty($_GET['ticket_close_from'])) {
    $ticket_close_from = $conn->real_escape_string($_GET['ticket_close_from']);
    $ticket_query .= " AND t.ticket_close >= '$ticket_close_from'";
}

if (!empty($_GET['ticket_close_to'])) {
    $ticket_close_to = $conn->real_escape_string($_GET['ticket_close_to']);
    $ticket_query .= " AND t.ticket_close <= '$ticket_close_to'";
}

// Add more conditions for other filters like issue_type, SLA_category, status, etc.
$ticket_query .= "
    GROUP BY 
        t.ticket_id DESC";

$ticket_result = $conn->query($ticket_query);

// Output HTML for table rows based on filtered results
if ($ticket_result->num_rows > 0) {
    $i = 1; // Initialize row count
    while ($row = $ticket_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td class='py-1'>" . $i++ . "</td>";

        // Conditionally display edit/delete buttons based on permissions
        echo "<td class='export-ignore py-1'>";
        if ($EditTicket || $DeleteTicket) {
            if ($row['ticket_close'] === null) {
                // Edit button if user has permission
                if ($EditTicket) {
                    echo "<a href='edit_ticket.php?id=" . $row['id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                }
                if ($DeleteTicket) {
                    echo "<button data-id='{$row['id']}' class='btn btn-danger delete-btn'><i class='fa-solid fa-trash'></i></button>";
                }
            } else if ($listTicketAssign == 0) {
                echo "<a href='edit_ticket.php?id=" . $row['id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
            }
        }
        echo "</td>";
        // Output other table columns based on $row data
        echo "<td class='py-1'><button class='btn btn-link' onclick='showTicketDetails(" . json_encode($row) . ")'>" . $row['ticket_id'] . "</button></td>";
        echo "<td class='py-1'>" . $row['station_id'] . "</td>";
        echo "<td class='py-1'>" . $row['station_name'] . "</td>";
        echo "<td class='py-1'>" . $row['station_type'] . "</td>";
        echo "<td class='py-1'>" . $row['province'] . "</td>";
        echo "<td class='py-1'>" . $row['issue_description'] . "</td>";
        if ($row['issue_image'] == !null) {
            echo "<td  class='export-ignore py-1'><button class='btn btn-link' onclick='showImage(\"" . $row['issue_image'] . "\")'>Click to View</button></td>";
        } else {
            echo "<td class='export-ignore text-center text-warning'>none</td>";
        }
        if ($issue_type == !null) {
            echo "<td class='py-1'>" . $issue_type . "</td>";
        } else {
            echo "<td class='py-1'>" . $row['issue_type'] . "</td>";
        }
        // echo "<td class='py-1'>" . $row['issue_type'] . "</td>";
        echo "<td class='py-1'>" . $row['SLA_category'] . "</td>";
        echo "<td class='py-1'>" . $row['status'] . "</td>";
        // if ($users_id == !null) {
        //     echo "<td class='py-1'>" . $users_id . "</td>";
        // } else {
        //     echo "<td class='py-1'>" . $row['users_name'] . "</td>";
        // }
        echo "<td class='py-1'>" . $row['users_name'] . "</td>";
        echo "<td class='py-1'>" . $row['ticket_open'] . "</td>";
        echo "<td class='py-1'>" . $row['ticket_close'] . "</td>";
        echo "<td class='py-1'>" . $row['comment'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
}
