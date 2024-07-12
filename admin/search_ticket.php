<?php
include "config.php"; // Include your database connection configuration

$search = isset($_GET['search']) ? $_GET['search'] : '';
$records_per_page = isset($_GET['length']) ? intval($_GET['length']) : 10;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

$search_query = "
    SELECT 
        t.*, 
        REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
    FROM 
        tbl_ticket t
    LEFT JOIN 
        tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
    WHERE 
        t.ticket_id LIKE '%$search%' OR 
        t.station_name LIKE '%$search%' OR 
        t.issue_description LIKE '%$search%' OR 
        t.issue_type LIKE '%$search%' OR 
        t.SLA_category LIKE '%$search%' OR 
        t.status LIKE '%$search%' OR 
        u.users_name LIKE '%$search%'
    GROUP BY 
        t.ticket_id DESC
    LIMIT $offset, $records_per_page 
";
$search_result = $conn->query($search_query);

$total_query = "
    SELECT COUNT(*) as total 
    FROM 
        tbl_ticket t
    LEFT JOIN 
        tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
    WHERE 
        t.ticket_id LIKE '%$search%' OR 
        t.station_name LIKE '%$search%' OR 
        t.issue_description LIKE '%$search%' OR 
        t.issue_type LIKE '%$search%' OR 
        t.SLA_category LIKE '%$search%' OR 
        t.status LIKE '%$search%' OR 
        u.users_name LIKE '%$search%'
";
$total_result = $conn->query($total_query);
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];
$total_pages = ceil($total_records / $records_per_page);

$tickets = [];

if ($search_result->num_rows > 0) {
    while ($row = $search_result->fetch_assoc()) {
        $tickets[] = $row;
    }
}

echo json_encode([
    'tickets' => $tickets,
    'total_records' => $total_records,
    'total_pages' => $total_pages,
    'current_page' => $current_page,
    'records_per_page' => $records_per_page
]);
?>
