<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";

// Check if user info is fetched correctly
if (!isset($fetch_info['users_id'])) {
    $_SESSION['error_message'] = "User ID not found.";
    header("location: 404.php");
    exit();
}

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // Example user ID

// Default values for pagination
$records_per_page = isset($_GET['length']) ? intval($_GET['length']) : 20;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Prepare and execute user details query
$query_user = "
    SELECT u.*, r.list_ticket_status, r.add_ticket_status, r.edit_ticket_status, r.delete_ticket_status, r.list_ticket_assign
    FROM tbl_users u
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id
    WHERE u.users_id = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $listTicket = $user['list_ticket_status'];
    $AddTicket = $user['add_ticket_status'];
    $EditTicket = $user['edit_ticket_status'];
    $DeleteTicket = $user['delete_ticket_status'];
    $listTicketAssign = $user['list_ticket_assign'];

    // Prepare ticket query based on user permissions
    if ($listTicketAssign == 0) {
        $ticket_query = "SELECT t.*, REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
                        FROM tbl_ticket t
                        LEFT JOIN tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
                        GROUP BY t.ticket_id DESC LIMIT ?, ?";
        $stmt_ticket = $conn->prepare($ticket_query);
        $stmt_ticket->bind_param("ii", $offset, $records_per_page);
    } else {
        $ticket_query = "SELECT t.*, REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
                        FROM tbl_ticket t
                        LEFT JOIN tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
                        WHERE FIND_IN_SET(?, t.users_id)
                        GROUP BY t.ticket_id DESC LIMIT ?, ?";
        $stmt_ticket = $conn->prepare($ticket_query);
        $stmt_ticket->bind_param("iii", $user_id, $offset, $records_per_page);
    }

    // Execute ticket query
    $stmt_ticket->execute();
    $ticket_result = $stmt_ticket->get_result();

    // Calculate total records and pages for pagination
    $total_query = "SELECT COUNT(*) as total FROM tbl_ticket";
    $total_result = $conn->query($total_query);
    $total_row = $total_result->fetch_assoc();
    $total_records = $total_row['total'];
    $total_pages = ceil($total_records / $records_per_page);
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
    header("location: 404.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
   
    <?php include "../inc/head.php"; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include "../inc/nav.php"; ?>
        <?php include "../inc/sidebar.php"; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Ticket</h1>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="card">
                        <div class="card-body p-0" style="overflow: hidden;">
                            <div class="card-header">
                                <?php if (isset($AddTicket) && $AddTicket) : ?>
                                    <a href="add_ticket.php" class="btn btn-primary ml-2">Add Ticket</a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-secondary" id="toggleFilterBtn">Filter</button>
                            </div>

                            <!-- <div class="card-header">
                                <div class="row">
                                    <div class="col-sm-6 ">
                                        <div class="dataTables_length" id="dataTable_length">
                                            show
                                            <label>
                                                <select id="entriesPerPage" aria-controls="dataTable" class="custom-select custom-select-sm form-control form-control-sm">
                                                    <option value="10" <?= ($records_per_page == 10) ? 'selected' : '' ?>>10</option>
                                                    <option value="25" <?= ($records_per_page == 25) ? 'selected' : '' ?>>25</option>
                                                    <option value="50" <?= ($records_per_page == 50) ? 'selected' : '' ?>>50</option>
                                                    <option value="100" <?= ($records_per_page == 100) ? 'selected' : '' ?>>100</option>
                                                </select>
                                            </label>
                                            entries
                                        </div>
                                    </div>
                                    <div class="col-sm-6 row">
                                        <div id="example1_filter" class="dataTables_filter col-12">
                                            Search:
                                            <label>
                                                <input type="search" class="form-control form-control-sm" placeholder="" aria-controls="example1">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <div class="row" style="overflow: auto;">
                                <table id="example1" class="table table-hover text-nowrap">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <?php if ($EditTicket == 0 & $DeleteTicket == 0) {

                                                echo "<th style='display:none;'></th>";
                                            } else {
                                                echo " <th>Option</th>";
                                            } ?>
                                            <th>Ticket ID</th>
                                            <th>Station ID</th>
                                            <th>Station Name</th>
                                            <th>Station Type</th>
                                            <th>Province</th>
                                            <th>Description</th>
                                            <th>Issue Image</th>
                                            <th>Issue Type</th>
                                            <th>SLA Category</th>
                                            <th>Status</th>
                                            <th>Assign</th>
                                            <th>Ticket Open</th>
                                            <th>Ticket Close</th>
                                            <th>Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ticketTableBody">
                                        <?php
                                        $i = 1;
                                        if ($ticket_result->num_rows > 0) {
                                            while ($row = $ticket_result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td  class='py-2'>" . $i++ . "</td>";
                                                //condition for button edit and delete
                                                if ($EditTicket == 0 &  $DeleteTicket == 0) {
                                                    echo " <td style='display:none;'></td>";
                                                } else {
                                                    echo "<td  class='py-1'>";
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
                                                    echo "</td>";
                                                }
                                                echo "<td  class='py-1'><button class='btn btn-link' onclick='showTicketDetails(" . json_encode($row) . ")'>" . $row['ticket_id'] . "</button></td>";

                                                echo "<td  class='py-1'>" . $row['station_id'] . "</></td>";
                                                echo "<td  class='py-1'>" . $row['station_name'] . "</td>";
                                                echo "<td  class='py-1'>" . $row['station_type'] . "</td>";
                                                echo "<td  class='py-1'>" . $row['province'] . "</td>";
                                                echo "<td  class='py-1' style='font-family: 'Khmer', sans-serif;'>" . $row['issue_description'] . "</td>";
                                                if ($row['issue_image'] == !null) {
                                                    echo "<td  class='py-1'><button class='btn btn-link' onclick='showImage(\"" . $row['issue_image'] . "\")'>Click to View</button></td>";
                                                } else {
                                                    echo "<td class='text-center text-warning'>none</td>";
                                                }
                                                echo "<td  class='py-1'>" . $row['issue_type'] . "</td>";
                                                echo "<td  class='py-1'>" . $row['SLA_category'] . "</td>";
                                                echo "<td  class='py-1'>" . $row['status'] . "</td>";
                                                echo "<td  class='py-1'>" . $row['users_name'] . "</td>";
                                                echo "<td  class='py-1'>" . $row['ticket_open'] . "</td>";
                                                echo "<td  class='py-1'>" . $row['ticket_close'] . "</td>";
                                                echo "<td  class='py-1' style='font-family: 'Khmer', sans-serif;font-weight: 400;font-style: normal;'>" . $row['comment'] . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Ticket Details Modal -->
                            <!-- <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <div class="dataTables_info" id="dataTable_info" role="status" aria-live="polite">
                                        Showing
                                        <?= $offset + 1 ?> to
                                        <?= min($offset + $records_per_page, $total_records) ?> of
                                        <?= $total_records ?> entries
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="dataTable_paginate">
                                        <ul class="pagination">
                                            <li class="paginate_button page-item previous <?= ($current_page == 1) ? 'disabled' : '' ?>" id="dataTable_previous"><a href="?page=<?= $current_page - 1 ?>&length=<?= $records_per_page ?>" aria-controls="dataTable" data-dt-idx="0" tabindex="0" class="page-link">Previous</a></li>
                                            <?php for ($page = 1; $page <= $total_pages; $page++) : ?>
                                                <li class="paginate_button page-item <?= ($current_page == $page) ? 'active' : '' ?>">
                                                    <a href="?page=<?= $page ?>&length=<?= $records_per_page ?>" aria-controls="dataTable" data-dt-idx="<?= $page ?>" tabindex="0" class="page-link">
                                                        <?= $page ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>
                                            <li class="paginate_button page-item next <?= ($current_page == $total_pages) ? 'disabled' : '' ?>" id="dataTable_next"><a href="?page=<?= $current_page + 1 ?>&length=<?= $records_per_page ?>" aria-controls="dataTable" data-dt-idx="7" tabindex="0" class="page-link">Next</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include "../inc/footer.php"; ?>
    </div>
    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../plugins/jszip/jszip.min.js"></script>
    <script src="../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>

    <!-- script pop up ticket details -->
    <script src="../scripts/pop_up_ticket_details.js"></script>
    <!-- script pop up image  -->
    <script src="../scripts/pop_up_images.js"></script>
    <!-- script delete ticket -->
    <script>
        $(document).ready(function() {
            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                var ticketId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this ticket!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_ticket.php',
                            type: 'POST',
                            data: {
                                id: ticketId
                            },
                            success: function(response) {
                                if (response === 'success') {
                                    $('#ticket-' + ticketId).remove();
                                    Swal.fire(
                                        'Deleted!',
                                        'Your ticket has been deleted.',
                                        'success'
                                    );
                                } else if (response === 'closed') {
                                    Swal.fire(
                                        'Error!',
                                        'Closed tickets cannot be deleted.',
                                        'error'
                                    );
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Failed to delete ticket.',
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                });
            });

            // Handle edit button click
            $(document).on('click', '.edit-btn', function() {
                var ticketId = $(this).data('id');
                // Redirect or load edit form page, passing ticketId
                window.location.href = 'edit_ticket.php?id=' + ticketId;
                // alert('Edit button clicked for ticket ID: ' + ticketId);
            });
        });
    </script>
    <!-- auto fill station id -->
    <script src="../scripts/get_suggestions_auto_fill_stationID.js"></script>


</body>

</html>