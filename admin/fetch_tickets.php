<?php
include "config.php";

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$entries = isset($_GET['entries']) ? (int)$_GET['entries'] : 10;
$offset = ($page - 1) * $entries;

$query_total = "SELECT COUNT(*) AS total FROM tbl_ticket";
$result_total = $conn->query($query_total);
$total_tickets = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_tickets / $entries);

$query_tickets = "SELECT t.*, REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
    FROM tbl_ticket t
    LEFT JOIN tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
    GROUP BY t.ticket_id DESC
    LIMIT $offset, $entries";
$result_tickets = $conn->query($query_tickets);

$tickets = '';
while ($row = $result_tickets->fetch_assoc()) {
    $tickets .= "<tr>";
    $tickets .= "<td  class='py-2'>" . $i++ . "</td>";
    if ($EditTicket == 0 && $DeleteTicket == 0) {
        $tickets .= " <td style='display:none;'></td>";
    } else {
        $tickets .= "<td  class='py-1'>";
        if ($row['ticket_close'] === null) {
            if ($EditTicket) {
                $tickets .= "<a href='edit_ticket.php?id=" . $row['id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
            }
            if ($DeleteTicket) {
                $tickets .= "<button data-id='{$row['id']}' class='btn btn-danger delete-btn'><i class='fa-solid fa-trash'></i></button>";
            }
        } else if ($listTicketAssign == 0) {
            $tickets .= "<a href='edit_ticket.php?id=" . $row['id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
        }
        $tickets .= "</td>";
    }
    $tickets .= "<td  class='py-1'><button class='btn btn-link' onclick='showTicketDetails(" . json_encode($row) . ")'>" . $row['ticket_id'] . "</button></td>";
    $tickets .= "<td  class='py-1'>" . $row['station_id'] . "</td>";
    $tickets .= "<td  class='py-1'>" . $row['station_name'] . "</td>";
    $tickets .= "<td  class='py-1'>" . $row['station_type'] . "</td>";
    $tickets .= "<td  class='py-1'>" . $row['province'] . "</td>";
    $tickets .= "<td  class='py-1' style='font-family: 'Khmer', sans-serif;'>" . $row['issue_description'] . "</td>";
    if ($row['issue_image'] != null) {
        $tickets .= "<td  class='py-1'><button class='btn btn-link' onclick='showImage(\"" . $row['issue_image'] . "\")'>Click to View</button></td>";
    } else {
        $tickets .= "<td class='text-center text-warning'>none</td>";
    }
    $tickets .= "<td  class='py-1'>" . $row['issue_type'] . "</td>";
    $tickets .= "<td  class='py-1'>" . $row['SLA_category'] . "</td>";
    $tickets .= "<td  class='py-1'>" . $row['status'] . "</td>";
    $tickets .= "<td  class='py-1'>" . $row['users_name'] . "</td>";
    $tickets .= "<td  class='py-1'>" . $row['ticket_open'] . "</td>";
    $tickets .= "<td  class='py-1'>" . $row['ticket_close'] . "</td>";
    $tickets .= "<td  class='py-1' style='font-family: 'Khmer', sans-serif;font-weight: 400;font-style: normal;'>" . $row['comment'] . "</td>";
    $tickets .= "</tr>";
}

echo json_encode(['tickets' => $tickets, 'totalPages' => $total_pages]);
?>
