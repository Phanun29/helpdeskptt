<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";
// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // Example user ID
$status = isset($_GET['status']) ? $_GET['status'] : ''; // Get status from URL parameter
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

    // Determine user type and adjust query accordingly
    if ($listTicketAssign == 0) {
        // User type 1: Select all tickets with the specified status
        $ticket_query = "
            SELECT 
                t.*, 
                REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
            FROM 
                tbl_ticket t
            LEFT JOIN 
                tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
            " . ($status ? "WHERE t.status = '$status'" : "") . "
            GROUP BY 
                t.ticket_id DESC
        ";
    } else {
        // User type 0: Select tickets assigned to the current user with the specified status
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
                " . ($status ? "AND t.status = '$status'" : "") . "
            GROUP BY 
                t.ticket_id DESC
        ";
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}

$ticket_result = $conn->query($ticket_query);
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
                        <div class="col-sm-6 row">
                            <div> <a href="index.php" class="btn btn-primary mx-2">BACK</a></div>

                            <h1 class="m-0">Ticket</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">

                                <?php
                                if (isset($_SESSION['success_message'])) {
                                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                    <strong>{$_SESSION['success_message']}</strong>
                                    <button type='button' class='btn-close' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>
                                </div>";
                                    unset($_SESSION['success_message']); // Clear the message after displaying
                                }

                                if (isset($_SESSION['error_message'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                    <strong>{$_SESSION['error_message']}</strong>
                                    <button type='button' class='btn-close' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'></button>
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
                                        <th>Province</th>
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
                                            echo "<td  class='py-1' style='font-family: 'Khmer', sans-serif;font-weight: 400;font-style: normal;'>" . $row['issue_description'] . "</td>";
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
                            </table>
                            <!-- Ticket Details Modal -->

                            <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header py-1">
                                            <h5 class="modal-title" id="ticketModalLabel">Ticket Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body py-0">
                                            <table class="table p-0 modal-table" style='font-family: "Khmer", sans-serif;font-weight: 400;font-style: normal;'>
                                                <tr>
                                                    <td class="p-1">Ticket ID:</td>
                                                    <td class="p-1"><span id="modalTicketId" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Station ID:</td>
                                                    <td class="p-1"><span id="modalStationId" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Station Name:</td>
                                                    <td class="p-1"><span id="modalStationName" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Station Type:</td>
                                                    <td class="p-1"><span id="modalStationType" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Province:</td>
                                                    <td class="p-1"><span id="modalProvince" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Description:</td>
                                                    <td class="p-1"><span id="modalDescription" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Issue Type:</td>
                                                    <td class="p-1"><span id="modalIssueType" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">SLA_category:</td>
                                                    <td class="p-1"><span id="modalSLA_category" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Status:</td>
                                                    <td class="p-1"> <span id="modalStatus" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Assign:</td>
                                                    <td class="p-1"> <span id="modalAssign" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Open:</td>
                                                    <td class="p-1"> <span id="modalTicketOpen" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket On Hold:</td>
                                                    <td class="p-1"> <span id="modalTicketOnHold" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket In Progress:</td>
                                                    <td class="p-1"> <span id="modalTicketInProgress" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Pending Vendor:</td>
                                                    <td class="p-1"> <span id="modalTicketPendingVendor" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Close:</td>
                                                    <td class="p-1"> <span id="modalTicketClose" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Comment:</td>
                                                    <td class="p-1"> <span id="modalComment" class="word-wrap"></span></td>
                                                </tr>

                                            </table>
                                            <p class="p-0">Issue Images</p>
                                            <div id="modalIssueImages" onclick="showImage(this.src)"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /model ticket details -->
                            <!--script pop up details ticket -->
                            <script>
                                function showTicketDetails(ticket) {
                                    // Set text content for single fields
                                    document.getElementById('modalTicketId').textContent = ticket.ticket_id;
                                    document.getElementById('modalStationId').textContent = ticket.station_id;
                                    document.getElementById('modalStationName').textContent = ticket.station_name;
                                    document.getElementById('modalStationType').textContent = ticket.station_type;
                                    document.getElementById('modalProvince').textContent = ticket.province;
                                    document.getElementById('modalDescription').textContent = ticket.issue_description;
                                    document.getElementById('modalIssueType').textContent = ticket.issue_type;
                                    document.getElementById('modalSLA_category').textContent = ticket.SLA_category;
                                    document.getElementById('modalStatus').textContent = ticket.status;
                                    document.getElementById('modalAssign').textContent = ticket.users_name;
                                    document.getElementById('modalTicketOpen').textContent = ticket.ticket_open;
                                    document.getElementById('modalTicketOnHold').textContent = ticket.ticket_on_hold;
                                    document.getElementById('modalTicketInProgress').textContent = ticket.ticket_in_progress;
                                    document.getElementById('modalTicketPendingVendor').textContent = ticket.ticket_pending_vendor;
                                    document.getElementById('modalTicketClose').textContent = ticket.ticket_close;
                                    document.getElementById('modalComment').textContent = ticket.comment;

                                    // Set image source for main image
                                    document.getElementById('modalIssueImages').src = ticket.issue_image || '';

                                    // Clear previous images
                                    const modalIssueImages = document.getElementById('modalIssueImages');
                                    modalIssueImages.innerHTML = '';

                                    // Display multiple images if available
                                    if (ticket.issue_image) {
                                        const images = ticket.issue_image.split(',');
                                        images.forEach(image => {
                                            const imgElement = document.createElement('img');
                                            imgElement.src = image.trim();
                                            imgElement.style.width = '50px';
                                            imgElement.style.cursor = 'pointer';
                                            imgElement.onclick = () => showImage(image.trim());
                                            modalIssueImages.appendChild(imgElement);
                                        });
                                    }

                                    $('#ticketModal').modal('show');
                                }

                                function showImage(imageUrl) {
                                    $('#imageToShow').attr('src', imageUrl);
                                    $('#imageModal').modal('show');
                                }
                            </script>
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
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "buttons": [, "csv", "excel", "pdf"],
                "lengthChange": false,
                "autoWidth": true,

            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true,
            });
        });
    </script>


    <!-- delete -->
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

    <!--script pop up show image -->
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