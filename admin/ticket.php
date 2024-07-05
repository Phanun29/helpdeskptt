<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";
// Fetch user details including rules_id and permissions in one query
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

    if ($listTicketAssign == 0) {
        // User type 1: Select all tickets
        $ticket_query = "
            SELECT 
                t.*, 
                REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
            FROM 
                tbl_ticket t
            LEFT JOIN 
                tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
            GROUP BY 
                t.ticket_id DESC
        ";
    } else {
        // User type 0: Select tickets assigned to the current user
        $user_id = $fetch_info['users_id']; // Assuming you have stored user ID in session

        $ticket_query = "
            SELECT 
                t.*, 
                REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
            FROM 
                tbl_ticket t
            LEFT JOIN 
                tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
            WHERE 
                FIND_IN_SET($user_id, t.users_id)
            GROUP BY 
                t.ticket_id DESC
        ";
    }

    if (!$listTicket) {
        header("location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}



$ticket_result = $conn->query($ticket_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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

                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">

                                <?php


                                if (isset($_SESSION['success_message'])) {
                                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                    <strong>{$_SESSION['success_message']}</strong>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>";
                                    unset($_SESSION['success_message']); // Clear the message after displaying
                                }

                                if (isset($_SESSION['error_message'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                    <strong>{$_SESSION['error_message']}</strong>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>";
                                    unset($_SESSION['error_message']); // Clear the message after displaying
                                }
                                ?>
                            </ol>
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
                            <style>
                                #filterForm1 {
                                    display: none;
                                    opacity: 0;
                                    transition: opacity 0.5s ease-in-out;
                                }

                                #filterForm1.show {
                                    display: block;
                                    opacity: 1;
                                }
                            </style>


                            <div class="card-header">
                                <?php if (isset($AddTicket) && $AddTicket) : ?>
                                    <a href="add_ticket.php" class="btn btn-primary ml-2">Add Ticket</a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-secondary" id="toggleFilterBtn">Filter</button>
                            </div>

                            <div class="card-header" id="filterForm1" style="display: none;">
                                <form id="filterForm" class="row">
                                    <div class="form-group col-sm-3">
                                        <label for="station_id">Station ID</label>
                                        <input class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)">
                                        <div id="suggestion_dropdown" class="dropdown-content"></div>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label for="issue_type">Issue Type</label>
                                        <select class="form-control" name="issue_type" id="issue_type">
                                            <option value="">Issue Type</option>
                                            <option value="Hardware">Hardware</option>
                                            <option value="Software">Software</option>
                                            <option value="Network">Network</option>
                                            <option value="Dispenser">Dispenser</option>
                                            <option value="Unassigned">Unassigned</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label for="priority">SLA Catego</label>
                                        <select name="priority" id="priority" class="form-control">
                                            <option value="">Priority</option>
                                            <option value="CAT Hardware">CAT Hardware</option>
                                            <option value="CAT 1*">CAT 1*</option>
                                            <option value="CAT 2*">CAT 2*</option>
                                            <option value="CAT 3*">CAT 3*</option>
                                            <option value="CAT 4*">CAT 4*</option>
                                            <option value="CAT 4 Report*">CAT 4 Report*</option>
                                            <option value="CAT 5*">CAT 5*</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control" style="width: 100%;">
                                            <option value="">Status</option>
                                            <option value="Open">Open</option>
                                            <option value="On Hold">On Hold</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Pending Vendor">Pending Vendor</option>
                                            <option value="Close">Closed</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-sm-3">
                                        <label for="users_id">Assign</label>
                                        <select name="users_id" id="users_id" class="form-control">
                                            <option value="">Assign</option>
                                            <?php
                                            $user_query1 = "SELECT users_id, users_name FROM tbl_users WHERE status = 1";
                                            $user_result1 = $conn->query($user_query1);
                                            $users = [];
                                            if ($user_result1 && $user_result1->num_rows > 0) {
                                                while ($row1 = $user_result1->fetch_assoc()) {
                                                    $users[] = $row1;
                                                }
                                            }
                                            if (!empty($users)) {
                                                for ($i = 0; $i < count($users); $i++) {
                                                    echo "<option value='" . $users[$i]['users_id'] . "'>" . $users[$i]['users_name'] . "</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No users found with status 1</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="ticket_open_from">Ticket Open From</label>
                                        <input type="date" name="ticket_open_from" id="ticket_open_from" class="form-control">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="ticket_open_to">Ticket Open To</label>
                                        <input type="date" name="ticket_open_to" id="ticket_open_to" class="form-control">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="ticket_close_from">Ticket Close From</label>
                                        <input type="date" name="ticket_close_from" id="ticket_close_from" class="form-control">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="ticket_close_to">Ticket Close To</label>
                                        <input type="date" name="ticket_close_to" id="ticket_close_to" class="form-control">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <button type="button" class="btn btn-primary">Filter <i class="fa-solid fa-filter"></i></button>
                                        <button type="reset" class="btn btn-danger" id="filterResetBtn">Clear</button>
                                    </div>
                                </form>
                            </div>
                            <script>
                                document.getElementById('toggleFilterBtn').addEventListener('click', function() {
                                    var filterForm = document.getElementById('filterForm1');
                                    if (filterForm.classList.contains('show')) {
                                        filterForm.classList.remove('show');
                                        setTimeout(function() {
                                            filterForm.style.display = 'none';
                                        }, 500); // Match the transition duration
                                    } else {
                                        filterForm.style.display = 'block';
                                        setTimeout(function() {
                                            filterForm.classList.add('show');
                                        }, 10); // Slight delay to trigger transition
                                    }
                                });
                            </script>
                            <br>
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
                                        <th>Description</th>
                                        <th>Image</th>
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
                                                // if ($listTicketAssign == 0) {
                                                //     echo "<a href='edit_ticket.php?id=" . $row['id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                // }
                                                if ($row['ticket_close'] === null) {

                                                    // Edit button if user has permission
                                                    if ($EditTicket) {
                                                        echo "<a href='edit_ticket.php?id=" . $row['id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                    }
                                                    if ($DeleteTicket) {

                                                        echo "<a href='delete_ticket.php?id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fa-solid fa-trash'></i></a>";
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
                                            echo "<td  class='py-1'>" . $row['issue_description'] . "</td>";
                                            if ($row['issue_image'] == !null) {
                                                echo "<td  class='py-1'><button class='btn btn-link' onclick='showImage(\"" . $row['issue_image'] . "\")'>Click to View</button></td>";
                                            } else {
                                                echo "<td class='text-center text-warning'>none</td>";
                                            }


                                            // // Handling multiple images
                                            // if (!empty($row['issue_image'])) {
                                            //     $images = explode(',', $row['issue_image']);
                                            //     echo "<td class='py-1'>";
                                            //     foreach ($images as $image) {
                                            //         echo "<img src='$image' style='width: 50px; height: auto; cursor: pointer;' onclick='showImage(\"$image\")'>";
                                            //     }
                                            //     echo "</td>";
                                            // } else {
                                            //     echo "<td class='text-center text-warning'>none</td>";
                                            // }
                                            echo "<td  class='py-1'>" . $row['issue_type'] . "</td>";
                                            echo "<td  class='py-1'>" . $row['priority'] . "</td>";
                                            echo "<td  class='py-1'>" . $row['status'] . "</td>";
                                            echo "<td  class='py-1'>" . $row['users_name'] . "</td>";
                                            echo "<td  class='py-1'>" . $row['ticket_open'] . "</td>";
                                            echo "<td  class='py-1'>" . $row['ticket_close'] . "</td>";
                                            echo "<td  class='py-1'>" . $row['comment'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
                                    }
                                    ?>
                            </table>
                            <!-- Ticket Details Modal -->
                            <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="ticketModalLabel">Ticket Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body py-0">
                                            <table class="table p-0">
                                                <tr>
                                                    <td class="p-1">Ticket ID:</td>
                                                    <td class="p-1"><span id="modalTicketId"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Station ID:</td>
                                                    <td class="p-1"><span id="modalStationId"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Station Name:</td>
                                                    <td class="p-1"><span id="modalStationName"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Station Type:</td>
                                                    <td class="p-1"><span id="modalStationType"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Description:</td>
                                                    <td class="p-1"><span id="modalDescription"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Issue Type:</td>
                                                    <td class="p-1"><span id="modalIssueType"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Priority:</td>
                                                    <td class="p-1"><span id="modalPriority"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Status:</td>
                                                    <td class="p-1"> <span id="modalStatus"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Assign:</td>
                                                    <td class="p-1"> <span id="modalAssign"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Open:</td>
                                                    <td class="p-1"> <span id="modalTicketOpen"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket On Hold:</td>
                                                    <td class="p-1"> <span id="modalTicketOnHold"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket In Progress</td>
                                                    <td class="p-1"> <span id="modalTicketInProgress"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Pending Vendor</td>
                                                    <td class="p-1"> <span id="modalTicketPendingVendor"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Close:</td>
                                                    <td class="p-1"> <span id="modalTicketClose"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Comment:</td>
                                                    <td> <span id="modalComment"></span></td>
                                                </tr>
                                                <tr>
                                                <tr>
                                                    <td class="p-1">Image:</td>
                                                    <td>
                                                        <img src="" id="modalIssueImage" alt="Issue Image" style="max-width: 200px; max-height: 200px; cursor: pointer; border:1px;" onclick="showImage(this.src)">
                                                    </td>
                                                </tr>

                                                </tr>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- /model ticket details -->
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
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- Page specific script -->

    <script>
        $(document).ready(function() {
            // Handle filter button click
            $('#filterForm button[type="button"]').on('click', function() {
                var formData = $('#filterForm').serialize();
                $.ajax({
                    url: 'process.php', // Replace with your PHP script handling filtering
                    type: 'GET', // or 'POST' depending on your preference
                    data: formData,
                    success: function(response) {
                        $('#ticketTableBody').html(response); // Update table body with filtered data
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            // Handle reset button click
            $('#filterResetBtn').on('click', function() {
                $('#filterForm')[0].reset();
                // Reset DataTable
                $('#ticketTable').DataTable().search('').draw();
            });
        });
    </script>
    <!-- auto fill station -->
    <style>
        .dropdown-content {
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content p {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            cursor: pointer;
        }

        .dropdown-content p:hover {
            background-color: #f1f1f1;
        }
    </style>
    <script src="../script/station_id_fill.js"></script>
    <script>
        $(document).ready(function() {
            $('#ticketTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'excel', 'pdf'
                ],
                pageLength: 10, // Default number of rows per page
                lengthMenu: [10, 20, 50, 100] // Options for number of rows per page
            });

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
                            url: 'testd.php',
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

    <script>
        $(function() {
            $("#example1").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "buttons": [, "csv", "excel", "pdf"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
    <!-- pop up details ticket -->
    <script>
        function showTicketDetails(ticket) {
            // Set text content and image source for single image
            document.getElementById('modalTicketId').textContent = ticket.ticket_id;
            document.getElementById('modalStationId').textContent = ticket.station_id;
            document.getElementById('modalStationName').textContent = ticket.station_name;
            document.getElementById('modalStationType').textContent = ticket.station_type;
            document.getElementById('modalDescription').textContent = ticket.issue_description;
            document.getElementById('modalIssueType').textContent = ticket.issue_type;
            document.getElementById('modalPriority').textContent = ticket.priority;
            document.getElementById('modalStatus').textContent = ticket.status;
            document.getElementById('modalAssign').textContent = ticket.users_name;
            document.getElementById('modalTicketOpen').textContent = ticket.ticket_open;
            document.getElementById('modalTicketOnHold').textContent = ticket.ticket_on_hold;
            document.getElementById('modalTicketInProgress').textContent = ticket.ticket_in_progress;
            document.getElementById('modalTicketPendingVendor').textContent = ticket.ticket_pending_vendor;
            document.getElementById('modalTicketClose').textContent = ticket.ticket_close;
            document.getElementById('modalComment').textContent = ticket.comment;
            document.getElementById('modalIssueImage').src = ticket.issue_image || '';

            // Display multiple images if available
            const modalIssueImages = document.getElementById('modalIssueImage'); // Corrected ID here
            if (ticket.issue_images) {
                const images = ticket.issue_images.split(',');
                images.forEach(image => {
                    const imgElement = document.createElement('img');
                    imgElement.src = image.trim(); // Trim to remove any leading/trailing whitespace
                    imgElement.style.width = '50px';
                    imgElement.style.cursor = 'pointer';
                    imgElement.onclick = () => showImage(image.trim());
                    modalIssueImages.appendChild(imgElement); // Append to modalIssueImages, corrected from modalIssueImage
                });
            }

            $('#ticketModal').modal('show');
        }

        function showImage(imageUrl) {
            $('#imageToShow').attr('src', imageUrl);
            $('#imageModal').modal('show');
        }
    </script>
    <!-- Modal for displaying images -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Image Viewer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="text-center">
                            <img style="width:600px;" id="modalImage" class="img-fluid">
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <span id="imageIndex"></span> / <span id="totalImages"></span> <!-- Display image index and total -->
                    <button type="button" class="btn btn-secondary" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                    <button type="button" class="btn btn-secondary " id="nextBtn"><i class="fas fa-chevron-right"></i></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Handle filter button click
            $('#filterForm button[type="button"]').on('click', function() {
                var formData = $('#filterForm').serialize();
                $.ajax({
                    url: 'process.php', // Replace with your PHP script handling filtering
                    type: 'GET', // or 'POST' depending on your preference
                    data: formData,
                    success: function(response) {
                        $('#ticketTableBody').html(response); // Update table body with filtered data
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            // Handle reset button click
            $('#filterResetBtn').on('click', function() {
                $('#filterForm')[0].reset();
                // Reset DataTable
                $('#example1').DataTable().search('').draw();
            });
        });
    </script>

    <script>
        function showImage(images) {
            var imageArray = images.split(',');
            var imageModal = $('#imageModal');
            var modalImage = document.getElementById('modalImage');
            var currentImageIndex = 0;

            function showCurrentImage() {
                modalImage.src = imageArray[currentImageIndex];
                $('#imageIndex').text(currentImageIndex + 1); // Display current image index
                $('#totalImages').text(imageArray.length); // Display total number of images
            }

            // Initial image display
            showCurrentImage();

            // Navigation buttons
            $('#nextBtn').click(function() {
                currentImageIndex = (currentImageIndex + 1) % imageArray.length;
                showCurrentImage();
            });

            $('#prevBtn').click(function() {
                currentImageIndex = (currentImageIndex - 1 + imageArray.length) % imageArray.length;
                showCurrentImage();
            });

            imageModal.modal('show');
        }
    </script>
</body>

</html>