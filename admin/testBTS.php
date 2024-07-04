<?php
include "config.php";
include "../inc/header.php";
include "../inc/permissiont.php";
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
$ticket_result = $conn->query($ticket_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataTables Example</title>
    <?php include "../inc/head.php"; ?>
</head>

<body>

    <div class="container mt-5">
        <h2>Ticket Table</h2>
        <div>
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
                        <option value="Dispensor">Dispensor</option>
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
                    <button type="button" class="btn btn-primary">Filter</button>
                    <button type="reset" class="btn btn-danger" id="filterResetBtn">Clear</button>
                </div>
            </form>
        </div>

        <table id="example1" class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>#</th>
                    <?php if ($canEditStation || $canDeleteStation) : ?>
                        <th>Option</th>
                    <?php endif; ?>
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
                        echo "<td  class='py-1''>" . $i++ . "</td>";
                        //condition for button edit and delete
                        if ($canEditStation == 0 &  $canDeleteStation == 0) {
                            echo " <td style='display:none;'></td>";
                        } else {
                            echo "<td  class='py-1''>";
                            if ($row['ticket_close'] === null) {

                                // Edit button if user has permission
                                if ($canEditStation) {
                                    echo "<button data-id='{$row['id']}' class='btn btn-primary edit-btn'>Edit</button>";
                                }
                                if ($canDeleteStation) {

                                    echo "<button data-id='{$row['id']}' class='btn btn-danger delete-btn'>Delete</button>";
                                }
                            }
                            // Delete button if user has permission

                            echo "</td>";
                        }
                        echo "<td  class='py-1''><button class='btn btn-link' onclick='showTicketDetails(" . json_encode($row) . ")'>" . $row['ticket_id'] . "</button></td>";
                        echo "<td  class='py-1''>" . $row['station_id'] . "</></td>";
                        echo "<td  class='py-1''>" . $row['station_name'] . "</td>";
                        echo "<td  class='py-1''>" . $row['station_type'] . "</td>";
                        echo "<td  class='py-1''>" . $row['issue_description'] . "</td>";
                        //    echo "<td  class='py-1''><button class='btn text-primary link-underline-success' onclick='showImage(\"" . $row['issue_image'] . "\")'>click</button></td>";
                        // Make Image clickable to show in a popup
                        echo "<td  class='py-1''><button class='btn btn-link' onclick='showImage(\"" . $row['issue_image'] . "\")'>Click to View</button></td>";
                        echo "<td  class='py-1''>" . $row['issue_type'] . "</td>";
                        echo "<td  class='py-1''>" . $row['priority'] . "</td>";
                        echo "<td  class='py-1''>" . $row['status'] . "</td>";
                        echo "<td  class='py-1''>" . $row['users_name'] . "</td>";
                        echo "<td  class='py-1''>" . $row['ticket_open'] . "</td>";
                        echo "<td  class='py-1''>" . $row['ticket_close'] . "</td>";
                        echo "<td  class='py-1''>" . $row['comment'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
                }
                ?>
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
                        <div class="">
                            <div class="text-center">
                                <img style="width:600px;" id="modalImage" class="img-fluid">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <span id="imageIndex"></span> / <span id="totalImages"></span> <!-- Display image index and total -->
                        <button type="button" class="btn btn-secondary mb-3" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                        <button type="button" class="btn btn-secondary mb-3" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>




        <script>
            // function showImage(imageUrl) {
            //     $('#modalImage').attr('src', imageUrl);
            //     $('#imageModal').modal('show');
            // }

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
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
            $('#example1').DataTable({
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

</body>

</html>