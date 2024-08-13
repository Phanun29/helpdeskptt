<?php

include "../inc/header_script.php";

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; //  user ID

$query_user = "
    SELECT u.*, r.list_station, r.add_station, r.edit_station, r.delete_station 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    $listStation1 = $user['list_station'];
    $canAddStation = $user['add_station'];
    $canEditStation = $user['edit_station'];
    $canDeleteStation = $user['delete_station'];

    if (!$listStation1) {
        header("location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message_station'] = "User not found or permission check failed.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <?php include "../inc/head.php" ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include "../inc/nav.php" ?>
        <?php include "../inc/sidebar.php" ?>
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Stations</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php
                                if (isset($_SESSION['success_message_station'])) {
                                    echo "<div class='alert alert-success alert-dismissible fade show mb-0' role='alert'>
                                        <strong>{$_SESSION['success_message_station']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['success_message_station']); // Clear the message after displaying
                                }

                                if (isset($_SESSION['error_message_station'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show  mb-0' role='alert'>
                                        <strong>{$_SESSION['error_message_station']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['error_message_station']); // Clear the message after displaying
                                }
                                ?>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body p-0" style="overflow: hidden;">
                            <?php if ($canAddStation) : ?>
                                <div class="card-header">
                                    <a href="add_station.php" id="" class="btn btn-primary">Add Station</a>
                                </div>
                            <?php endif; ?>
                            <br>
                            <table id="tableStation" class="table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <?php if (!$canEditStation && !$canDeleteStation) : ?>
                                            <th style="display:none;"></th>
                                        <?php else : ?>
                                            <th>Action</th>
                                        <?php endif; ?>
                                        <th>Station ID</th>
                                        <th>Station Name</th>
                                        <th>Station Type</th>
                                        <th>Province</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $station_query = "SELECT * FROM tbl_station ORDER BY id DESC";
                                    $station_result = $conn->query($station_query);
                                    $i = 1;

                                    if ($station_result->num_rows > 0) {
                                        while ($row = $station_result->fetch_assoc()) {
                                            echo "<tr id='stationId-{$row['id']}'>"; 
                                            echo "<td class='py-1'>{$i}</td>";
                                            if (!$canEditStation && !$canDeleteStation) {
                                                echo "<td style='display:none;'></td>";
                                            } else {
                                                echo "<td class='py-1'>";
                                                if ($canEditStation) {
                                                    echo "<a href='edit_station.php?id={$row['id']}' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                }
                                                if ($canDeleteStation) {
                                                    echo "<button data-id='" . $row['id'] . "' class='btn btn-danger delete-btn'><i class='fa-solid fa-trash'></i></button>";
                                                }
                                                echo "</td>";
                                            }
                                            echo "<td class='py-1'>{$row['station_id']}</td>";
                                            echo "<td class='py-1'>{$row['station_name']}</td>";
                                            echo "<td class='py-1'>{$row['station_type']}</td>";
                                            echo "<td class='py-1'>{$row['province']}</td>";

                                            echo "</tr>";
                                            $i++;
                                        }
                                    } else {
                                        echo "<tr><td class='text-center' colspan='5'>No stations found!</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </section>
        </div>
        <?php include "../inc/footer.php" ?>
    </div>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
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
    <script>
        $(function() {
            $("#tableStation").DataTable({
                "lengthChange": true,
                "autoWidth": false,
                "buttons": ["csv", "excel", "pdf"]
            }).buttons().container().appendTo('#tableStation_wrapper .col-md-6:eq(0)');
            $('#tableStation2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
    <!-- delete station -->
    <script>
        $(document).ready(function() {
            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                var stationId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this station!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_station.php',
                            type: 'POST',
                            data: {
                                id: stationId
                            },
                            success: function(response) {
                                console.log('Response:', response); // Debugging: Log the response
                                if (response === 'success') {
                                    console.log('Removing row with ID: #stationId-' + stationId); // Log the row being removed
                                    $('#stationId-' + stationId).remove(); // Remove the row with matching ID
                                    Swal.fire('Deleted!', 'Your station has been deleted.', 'success');
                                } else if (response === 'closed') {
                                    Swal.fire('Error!', 'Closed station cannot be deleted.', 'error');
                                } else {
                                    Swal.fire('Error!', 'Failed to delete station.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', status, error); // Debugging: Log AJAX errors
                                Swal.fire('Error!', 'An error occurred while deleting the station.', 'error');
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
            });
        });
    </script>
    <!-- auto close alert -->
    <script src="../scripts/auto_close_alert.js"></script>

</body>

</html>