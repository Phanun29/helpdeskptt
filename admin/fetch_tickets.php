<?php
include "config.php";

$station_id = $_POST['station_id'] ?? '';
$issue_type = $_POST['issue_type'] ?? '';
$priority = $_POST['priority'] ?? '';
$status = $_POST['status'] ?? '';
$users_id = $_POST['users_id'] ?? '';
$ticket_open_from = $_POST['ticket_open_from'] ?? '';
$ticket_open_to = $_POST['ticket_open_to'] ?? '';
$ticket_close_from = $_POST['ticket_close_from'] ?? '';
$ticket_close_to = $_POST['ticket_close_to'] ?? '';

$where = [];

if ($station_id) {
    $where[] = "t.station_id LIKE '%$station_id%'";
}
if ($issue_type) {
    $where[] = "t.issue_type = '$issue_type'";
}
if ($priority) {
    $where[] = "t.priority = '$priority'";
}
if ($status) {
    $where[] = "t.status = '$status'";
}
if ($users_id) {
    $where[] = "FIND_IN_SET('$users_id', t.users_id)";
}
if ($ticket_open_from && $ticket_open_to) {
    $where[] = "t.ticket_open BETWEEN '$ticket_open_from' AND '$ticket_open_to'";
}
if ($ticket_close_from && $ticket_close_to) {
    $where[] = "t.ticket_close BETWEEN '$ticket_close_from' AND '$ticket_close_to'";
}

$where_sql = '';
if (count($where) > 0) {
    $where_sql = 'WHERE ' . implode(' AND ', $where);
}

$columns = [
    0 => 't.ticket_id',
    1 => 't.station_id',
    2 => 't.station_name',
    3 => 't.station_type',
    4 => 't.issue_description',
    5 => 't.issue_image',
    6 => 't.issue_type',
    7 => 't.priority',
    8 => 't.status',
    9 => 't.users_name',
    10 => 't.ticket_open',
    11 => 't.ticket_close',
    12 => 't.comment'
];

$sql = "SELECT t.*, REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name 
        FROM tbl_ticket t 
        LEFT JOIN tbl_users u ON FIND_IN_SET(u.users_id, t.users_id) 
        $where_sql 
        GROUP BY t.ticket_id 
        ORDER BY t.ticket_id DESC";

$query = $conn->query($sql);

$totalData = $query->num_rows;
$totalFiltered = $totalData;

$sql .= " LIMIT " . $_POST['start'] . " ," . $_POST['length'] . "";
$query = $conn->query($sql);

$data = [];

while ($row = $query->fetch_assoc()) {
    $nestedData = [];
    $nestedData[] = $row['ticket_id'];
    $nestedData[] = $row['station_id'];
    $nestedData[] = $row['station_name'];
    $nestedData[] = $row['station_type'];
    $nestedData[] = $row['issue_description'];
    $nestedData[] = '<button class="btn btn-link" onclick="showImage(\'' . $row['issue_image'] . '\')">Click to View</button>';
    $nestedData[] = $row['issue_type'];
    $nestedData[] = $row['priority'];
    $nestedData[] = $row['status'];
    $nestedData[] = $row['users_name'];
    $nestedData[] = $row['ticket_open'];
    $nestedData[] = $row['ticket_close'];
    $nestedData[] = $row['comment'];
    $data[] = $nestedData;
}

$json_data = [
    "draw" => intval($_POST['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
];

echo json_encode($json_data);
