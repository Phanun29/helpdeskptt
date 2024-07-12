<?php
// // Include your database connection configuration
// include "config.php";

// // Retrieve filter parameters from POST
// $station_id = $_POST['station_id'] ?? '';
// $issue_type = $_POST['issue_type'] ?? '';
// $SLA_category = $_POST['SLA_category'] ?? '';
// $status = $_POST['status'] ?? '';
// $users_id = $_POST['users_id'] ?? '';
// $ticket_open_from = $_POST['ticket_open_from'] ?? '';
// $ticket_open_to = $_POST['ticket_open_to'] ?? '';
// $ticket_close_from = $_POST['ticket_close_from'] ?? '';
// $ticket_close_to = $_POST['ticket_close_to'] ?? '';

// // Construct SQL query based on filters (example, modify as per your schema)
// $query = "SELECT ticket_id, station_id, issue_type, SLA_category, status, users_name, ticket_open, ticket_close
//           FROM tbl_ticket
//           WHERE 1";

// if (!empty($station_id)) {
//     $query .= " AND station_id = '$station_id'";
// }
// if (!empty($issue_type)) {
//     $query .= " AND issue_type = '$issue_type'";
// }
// if (!empty($SLA_category)) {
//     $query .= " AND SLA_category = '$SLA_category'";
// }
// if (!empty($status)) {
//     $query .= " AND status = '$status'";
// }
// if (!empty($users_id)) {
//     $query .= " AND users_id = '$users_id'";
// }
// if (!empty($ticket_open_from)) {
//     $query .= " AND ticket_open >= '$ticket_open_from'";
// }
// if (!empty($ticket_open_to)) {
//     $query .= " AND ticket_open <= '$ticket_open_to'";
// }
// if (!empty($ticket_close_from)) {
//     $query .= " AND ticket_close >= '$ticket_close_from'";
// }
// if (!empty($ticket_close_to)) {
//     $query .= " AND ticket_close <= '$ticket_close_to'";
// }

// $result = mysqli_query($conn, $query);

// // Prepare data for DataTables format (JSON)
// $data = [];
// while ($row = mysqli_fetch_assoc($result)) {
//     $data[] = $row;
// }

// // Output JSON format for DataTables
// echo json_encode(['data' => $data]);

// // Close database connection
// mysqli_close($conn);
