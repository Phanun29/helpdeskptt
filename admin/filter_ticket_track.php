<?php
session_start();
require 'config.php'; // Include database connection

// Base ticket query
$ticket_query =
    "SELECT 
        t.*, 
        GROUP_CONCAT(assigned_users.users_name ORDER BY assigned_users.users_name SEPARATOR ', ') as assigned_users,
        modified_by_user.users_name as modified_by_name
    FROM 
        tbl_ticket_track t
    LEFT JOIN 
        tbl_users assigned_users ON FIND_IN_SET(assigned_users.users_id, t.assign) > 0
    LEFT JOIN 
        tbl_users modified_by_user ON t.modified_by = modified_by_user.users_id
    WHERE 
        1=1
";

// Append conditions based on GET parameters
if (!empty($_GET['ticket_id'])) {
    $ticket_id = $conn->real_escape_string($_GET['ticket_id']);
    $ticket_query .= " AND t.ticket_id = '$ticket_id'";
}

$ticket_query .= " GROUP BY t.id";

// Execute query and fetch results
$ticket_result = $conn->query($ticket_query);
?>
<table class="table table-bordered" id="tableTicket">
    <thead>
        <tr>
            <th>#</th>
            <th>Ticket ID</th>
            <th>Time Open</th>
            <th>Modify Time</th>
            <th>By</th>
            <th>Issue Type</th>
            <th>Assign</th>
            <th>SLA Category</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($ticket_result->num_rows > 0) : ?>
            <?php $i = 1;
            while ($row = $ticket_result->fetch_assoc()) : ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= $row['ticket_id'] ?></td>
                    <td><?= $row['open_time'] ?></td>
                    <td><?= $row['modify_time'] ?></td>
                    <td><?= $row['modified_by_name'] ?></td>
                    <td><?= $row['issue_type'] ?></td>
                    <td><?= $row['assigned_users'] ?></td>
                    <td><?= $row['SLA_category'] ?></td>
                    <td><?= $row['status'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else : ?>
            <tr>
                <td colspan="9" style="text-align:center;">No ticket found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php $conn->close(); ?>