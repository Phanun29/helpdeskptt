<?php
include "../inc/header_script.php";


// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id'];

// Construct the SQL query to fetch user details along with their associated permissions
$query_user = "
    SELECT u.*, r.list_user_rules, r.add_user_rules, r.edit_user_rules, r.delete_user_rules 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";
// Execute the query
$result_user = $conn->query($query_user);
// Check if the query was successful and if any rows were returned
if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    $listUsersRules1 = $user['list_user_rules'];
    $AddUserRules = $user['add_user_rules'];
    $EditUserRules = $user['edit_user_rules'];
    $DeleteUserRules = $user['delete_user_rules'];
    // Check if the user has permission to list and add station

    if (!$listUsersRules1) {
        // Redirect to a 404 error page if permissions are insufficient
        header("location: 404.php");
        exit();
    }
} else {
    // Set an error message if the user was not found or if permission check failed
    $_SESSION['error_message'] = "User not found or permission check failed.";
    header("location: 404.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <?php include "../inc/head.php" ?>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php
        include "../inc/nav.php";
        include "../inc/sidebar.php"
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Users Rules</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php
                                if (isset($_SESSION['success_message'])) {
                                    echo "<div class='alert alert-success alert-dismissible fade show  mb-0' role='alert'>
                                        <strong>{$_SESSION['success_message']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['success_message']); // Clear the message after displaying
                                }

                                if (isset($_SESSION['error_message'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show  mb-0' role='alert'>
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
                        <!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body p-0" style="overflow: hidden;">
                            <?php if (isset($AddUserRules) && $AddUserRules) : ?>
                                <div class="card-header">
                                    <a href="add_users_rules.php" id="add_ticket" class="btn btn-primary ">Add Users Rules</a>
                                </div>
                            <?php endif; ?>
                            <br>

                            <table id="tableUserRules" class="table_users_rules table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <?php if ($EditUserRules == 0 & $DeleteUserRules == 0) {
                                            echo "<th style='display:none;'></th>";
                                        } else {
                                            echo " <th>Action</th>";
                                        } ?>
                                        <th>Rules Name</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $station_query = "SELECT * FROM tbl_users_rules ORDER BY rules_id DESC ";
                                    $station_result = $conn->query($station_query);
                                    $i =  1;
                                    if ($station_result->num_rows > 0) {
                                        while ($row = $station_result->fetch_assoc()) {
                                            echo "<tr id='userRules-{$row['rules_id']}'>";
                                            echo "<td  class='py-1'>" . $i++ . "</td>";
                                            if ($EditUserRules == 0 &  $DeleteUserRules == 0) {
                                                echo "<td style='display:none;'></td>";
                                            } else {
                                                echo "<td  class='py-1'>";
                                                // Edit button if user has permission
                                                if ($EditUserRules) {
                                                    echo "<a href='edit_users_rules.php?id=" . $row['rules_id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                }
                                                // Delete button if user has permission
                                                if ($DeleteUserRules) {
                                                    //echo "<a href='delete_users_rules.php?id=" . $row['rules_id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fa-solid fa-trash'></i></a>";
                                                    echo "<button data-id='" . $row['rules_id'] . "' class='btn btn-danger delete-btn'><i class='fa-solid fa-trash'></i></button>";
                                                }
                                                echo "</td>";
                                            }
                                            echo "<td  class='py-1'>" . $row['rules_name'] . "</td>";


                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td class='text-center' colspan='6'>No users found!</td></tr>";
                                    }
                                    ?>
                                </tbody>

                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include "../inc/footer.php" ?>
    </div>
    <!-- ./wrapper -->

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
            $("#tableUserRules").DataTable({
                "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "buttons": [, "csv", "excel", "pdf"]
            }).buttons().container().appendTo('#tableUserRules_wrapper .col-md-6:eq(0)');
            $('#tableUserRules2').DataTable({
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
    <!-- auto close alert -->
    <script src="../scripts/auto_close_alert.js"></script>
    <!-- delete user rules-->
    <script>
        $(document).ready(function() {
            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                var userRules = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this users rules!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_users_rules.php', // Adjust URL to your delete script
                            type: 'POST',
                            data: {
                                id: userRules
                            },
                            success: function(response) {
                                console.log('Response:', response); // Debugging: Log the response
                                if (response === 'success') {
                                    console.log('Removing row with ID: #userRules-' + userRules); // Log the row being removed
                                    $('#userRules-' + userRules).remove(); // Remove the row from the table
                                    Swal.fire('Deleted!', 'Your user Rules has been deleted.', 'success');
                                } else {
                                    Swal.fire('Error!', 'Failed to delete user rules.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', status, error); // Debugging: Log AJAX errors
                                Swal.fire('Error!', 'An error occurred while deleting the user rules.', 'error');
                            }
                        });
                    }
                });
            });


        });
    </script>
</body>

</html>