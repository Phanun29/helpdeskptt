<?php

include "../inc/header_script.php";
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
                REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name,
                 GROUP_CONCAT(DISTINCT ti.image_path SEPARATOR ',') AS image_paths
            FROM 
                tbl_ticket t
            LEFT JOIN 
                tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
            LEFT JOIN tbl_ticket_images ti ON t.ticket_id = ti.ticket_id
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
                REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name,
                 GROUP_CONCAT(DISTINCT ti.image_path SEPARATOR ',') AS image_paths
            FROM 
                tbl_ticket t
            LEFT JOIN 
                tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
                LEFT JOIN tbl_ticket_images ti ON t.ticket_id = ti.ticket_id    
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
                            <table id="tableTicket" class="table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <?php if ($EditTicket == 0 & $DeleteTicket == 0) {

                                            echo "<th style='display:none;'></th>";
                                        } else {
                                            echo " <th class='export-ignore'>Action</th>";
                                        } ?>
                                        <th>Ticket ID</th>
                                        <th>Station ID</th>
                                        <th>Station Name</th>
                                        <th>Station Type</th>
                                        <th>Province</th>
                                        <th>Description</th>
                                        <th class="export-ignore">Issue Image</th>
                                        <th>Issue Type</th>
                                        <th>SLA Category</th>
                                        <th>Status</th>
                                        <th>Assign</th>
                                        <th>Ticket Open</th>
                                        <th>Ticket on Hold</th>
                                        <th>Ticket In Progress</th>
                                        <th>Ticket Pending Vender</th>
                                        <th>Ticket Close</th>
                                        <th>Ticket Time</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                <tbody id="ticketTableBody">
                                    <?php
                                    $i = 1;
                                    if ($ticket_result->num_rows > 0) {
                                        while ($ticket = $ticket_result->fetch_assoc()) {
                                            echo "<tr id='ticket-" . $ticket['id'] . "'>";
                                            echo "<td class='py-2'>" . $i++ . "</td>";
                                            if ($EditTicket == 0 & $DeleteTicket == 0) {
                                                echo "<td style='display:none;'></td>";
                                            } else {
                                                echo "<td class='export-ignore py-1'>";
                                                if ($ticket['status'] != "Close") {
                                                    if ($EditTicket) {
                                                        // Your original ID
                                                        $original_id = $ticket['id'];

                                                        // Hash the ID to make it unique and consistent
                                                        $hashed_id = hash('sha256', $original_id);

                                                        // Encode the hash and take the first 10 characters
                                                        $encoded_id = substr(base64_encode($hashed_id), 0, 20);
                                                        echo "<a href='edit_ticket.php?id={$encoded_id}' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                    }
                                                    if ($DeleteTicket) {
                                                        echo "<button data-id='" . $ticket['id'] . "' class='btn btn-danger delete-btn'><i class='fa-solid fa-trash'></i></button>";
                                                    }
                                                } else if ($listTicketAssign == 0) {
                                                    echo "<a href='edit_ticket.php?id=" . $ticket['id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                }
                                                echo "</td>";
                                            }
                                            echo "<td class='py-1'><button class='btn btn-link' onclick='showTicketDetails(" . json_encode($ticket) . ")'>" . $ticket['ticket_id'] . "</button></td>";
                                            echo "<td class='py-1'>" . $ticket['station_id'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['station_name'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['station_type'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['province'] . "</td>";
                                            echo "<td class='py-1' style='font-family: Khmer, sans-serif;'>" . $ticket['issue_description'] . "</td>";
                                            $image_paths = explode(',', $ticket['image_paths']);
                                            if ($ticket['image_paths'] != null) {
                                                echo "<td class='export-ignore py-1'><button class='btn btn-link' onclick='showMedia(\"" . $ticket['image_paths'] . "\")'>Click to View</button></td>";
                                            } else {
                                                echo "<td class='export-ignore text-center text-warning'>none</td>";
                                            }
                                            echo "<td class='py-1'>" . $ticket['issue_type'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['SLA_category'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['status'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['users_name'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['ticket_open'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['ticket_on_hold'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['ticket_in_progress'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['ticket_pending_vendor'] . "</td>";
                                            echo "<td class='py-1'>" . $ticket['ticket_close'] . "</td>";
                                            if ($ticket['ticket_time'] != null) {
                                                echo "<td class='py-1'>" . $ticket['ticket_time'] . "</td>";
                                            } else {
                                                date_default_timezone_set('Asia/Bangkok');
                                                $ticketOpenTime = new DateTime($ticket['ticket_open']);
                                                $ticketCloseTime = new DateTime();
                                                // Calculate the difference
                                                $interval = $ticketCloseTime->diff($ticketOpenTime);

                                                // Format the difference
                                                $ticket_time = '';
                                                if ($interval->d > 0) {
                                                    $ticket_time .= $interval->d . 'd, ';
                                                }
                                                if ($interval->h > 0 || $interval->d > 0) {
                                                    $ticket_time .= $interval->h . 'h, ';
                                                }
                                                if ($interval->i > 0 || $interval->h > 0 || $interval->d > 0) {
                                                    $ticket_time .= $interval->i . 'm, ';
                                                }
                                                $ticket_time .= $interval->s . 's ago';

                                                // Output the formatted time difference
                                                echo "<td class='py-1'>" . htmlspecialchars($ticket_time) . "</td>";
                                            }

                                            echo "<td class='py-1' style='font-family: Khmer, sans-serif; font-weight: 400; font-style: normal;'>" . $ticket['comment'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
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
                                            <div class="text-center" style="display: flex;justify-content:center;">
                                                <img id="modalImage" class="img-fluid" style="width: 600px; display: none;">
                                                <video id="modalVideo" class="img-fluid" style="width: 600px; display: none;" controls></video>
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
                                                    <td class="p-1">Ticket Time:</td>

                                                    <td class="p-1"> <span id="modalTicketTime" class="word-wrap"></span></td>

                                                </tr>
                                                <tr>
                                                    <td class="p-1">Comment:</td>
                                                    <td class="p-1"> <span id="modalComment" class="word-wrap"></span></td>
                                                </tr>

                                            </table>
                                            <p class="p-0">Issue Media</p>
                                            <div id="modalIssueMedia" class="d-flex flex-wrap" onclick="showMedia(this.src)"></div>
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
    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#tableTicket").DataTable({
                "buttons": [, "csv", "excel", "pdf"],
                "lengthChange": true,
                "autoWidth": true,

            }).buttons().container().appendTo('#tableTicket_wrapper .col-md-6:eq(0)');
            $('#tableTicket2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true,
            });
        });
    </script>

    <!--script pop up details ticket -->
    <script>
        function showTicketDetails(ticket) {
            // Check if ticket_time is null and calculate it if needed
            if (!ticket.ticket_time) {
                const ticketOpenTime = new Date(ticket.ticket_open);
                const ticketCloseTime = new Date();
                const interval = new Date(ticketCloseTime - ticketOpenTime);

                const days = interval.getUTCDate() - 1;
                const hours = interval.getUTCHours();
                const minutes = interval.getUTCMinutes();
                const seconds = interval.getUTCSeconds();

                let ticket_time = '';
                if (days > 0) {
                    ticket_time += days + 'd, ';
                }
                if (hours > 0 || days > 0) {
                    ticket_time += hours + 'h, ';
                }
                if (minutes > 0 || hours > 0 || days > 0) {
                    ticket_time += minutes + 'm, ';
                }
                ticket_time += seconds + 's ago';

                ticket.ticket_time = ticket_time;
            }
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
            document.getElementById('modalTicketTime').textContent = ticket.ticket_time;
            document.getElementById('modalComment').textContent = ticket.comment;

            // Clear previous media
            const modalIssueMedia = document.getElementById('modalIssueMedia');
            modalIssueMedia.innerHTML = '';

            // Display multiple images and videos if available
            if (ticket.image_paths) {
                const media = ticket.image_paths.split(',');
                media.forEach(item => {
                    const trimmedItem = item.trim();
                    if (trimmedItem.match(/\.(jpeg|jpg|gif|png)$/i)) {
                        const imgElement = document.createElement('img');
                        imgElement.src = trimmedItem;
                        imgElement.style.width = '50px';
                        imgElement.style.cursor = 'pointer';
                        imgElement.onclick = () => showMedia(trimmedItem);
                        modalIssueMedia.appendChild(imgElement);
                    } else if (trimmedItem.match(/\.(mp4|webm|ogg)$/i)) {
                        const videoElement = document.createElement('video');
                        videoElement.src = trimmedItem;
                        videoElement.style.width = '50px';
                        videoElement.style.cursor = 'pointer';
                        videoElement.controls = false;
                        videoElement.onclick = () => showMedia(trimmedItem);
                        modalIssueMedia.appendChild(videoElement);
                    }
                });
            }

            $('#ticketModal').modal('show');

        }




        function showMedia(imageUrl) {
            $('#imageToShow').attr('src', imageUrl);
            $('#imageModal').modal('show');
        }
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


        });
    </script>



    <!--script pop up show image -->
    <script>
        function showMedia(media) {
            var mediaArray = media.split(',');
            var imageModal = $('#imageModal');
            var modalImage = document.getElementById('modalImage');
            var modalVideo = document.getElementById('modalVideo');
            var currentMediaIndex = 0;

            function showCurrentMedia() {
                var currentMedia = mediaArray[currentMediaIndex];
                if (currentMedia.match(/\.(jpeg|jpg|gif|png)$/i)) {
                    modalImage.src = currentMedia;
                    modalImage.style.display = 'block';
                    modalVideo.style.display = 'none';
                } else if (currentMedia.match(/\.(mp4|webm|ogg)$/i)) {
                    modalVideo.src = currentMedia;
                    modalVideo.style.display = 'block';
                    modalImage.style.display = 'none';
                }
                $('#imageIndex').text(currentMediaIndex + 1); // Display current media index
                $('#totalImages').text(mediaArray.length); // Display total number of media
            }

            // Initial media display
            showCurrentMedia();

            // Navigation buttons
            $('#nextBtn').click(function() {
                currentMediaIndex = (currentMediaIndex + 1) % mediaArray.length;
                showCurrentMedia();
            });

            $('#prevBtn').click(function() {
                currentMediaIndex = (currentMediaIndex - 1 + mediaArray.length) % mediaArray.length;
                showCurrentMedia();
            });

            imageModal.modal('show');
        }
    </script>
</body>

</html>