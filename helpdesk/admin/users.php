<?php
include "../inc/header_script.php";

// Retrieve the current user's ID from the fetched user information
$user_id = $fetch_info['users_id'];

// Construct the SQL query to fetch user details along with their associated permissions
$query_user = "
    SELECT u.*, r.list_user_status, r.add_user_status, r.edit_user_status, r.delete_user_status 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";
// Execute the query
$result_user = $conn->query($query_user);

// Check if the query was successful and if any rows were returned
if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    $listUsers = $user['list_user_status'];
    $AddUsers = $user['add_user_status'];
    $EditUsers = $user['edit_user_status'];
    $DeleteUsers = $user['delete_user_status'];

    if (!$listUsers) {
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
        <?php include "../inc/nav.php" ?>
        <?php include "../inc/sidebar.php" ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Users</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php
                                if (isset($_SESSION['success_message_users'])) {
                                    echo "<div class='alert alert-success alert-dismissible fade show mb-0' role='alert'>
                                        <strong>{$_SESSION['success_message_users']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['success_message_users']); // Clear the message after displaying
                                }

                                if (isset($_SESSION['error_message_users'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show mb-0' role='alert'>
                                        <strong>{$_SESSION['error_message_users']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['error_message_users']); // Clear the message after displaying
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
                            <?php if (isset($AddUsers) && $AddUsers) : ?>
                                <div class="card-header">
                                    <a href="add_users.php" id="add_ticket" class="btn btn-primary ">Add Users</a>
                                </div>
                            <?php endif; ?>

                            <br>
                            <table id="tableUser" class="table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <?php if ($EditUsers == 0 && $DeleteUsers == 0) : ?>
                                            <th style='display:none;'></th>
                                        <?php else : ?>
                                            <th>Option</th>
                                        <?php endif; ?>
                                        <th>Users Name</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Users Rules</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Modify the query to join tbl_users with tbl_rules
                                    $user_query = "SELECT tbl_users.*, tbl_users_rules.rules_name 
                                    FROM tbl_users 
                                    LEFT JOIN tbl_users_rules ON tbl_users.rules_id = tbl_users_rules.rules_id 
                                    ORDER BY users_id DESC
                                    ";
                                    $user_result = $conn->query($user_query);
                                    $i = 1;
                                    if ($user_result->num_rows > 0) {
                                        while ($row = $user_result->fetch_assoc()) {
                                            echo "<tr id='user-" . $row['users_id'] . "'>";
                                            echo "<td class='py-1'>" . $i++ . "</td>";
                                            if ($EditUsers == 0 && $DeleteUsers == 0) {
                                                echo "<td style='display:none;'></td>";
                                            } else {
                                                echo "<td class='py-1'>";
                                                // Edit button if user has permission
                                                if ($EditUsers) {
                                                    echo "<a href='edit_users.php?id=" . $row['users_id'] . "' class='btn btn-primary edit-btn'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                }
                                                // Delete button if user has permission
                                                if ($DeleteUsers) {
                                                    echo "<button data-id='" . $row['users_id'] . "' class='btn btn-danger delete-btn'><i class='fa-solid fa-trash'></i></button>";
                                                }
                                                echo "</td>";
                                            }
                                            echo "<td class='py-1'>" . $row['users_name'] . "</td>";
                                            echo "<td class='py-1'>" . $row['email'] . "</td>";
                                            echo "<td class='py-1'>" . $row['company'] . "</td>";
                                            echo "<td class='py-1'>" . $row['rules_name'] . "</td>"; // Display rules_name instead of rules_id
                                            echo "<td class='py-1'>" . ($row['status'] === '1' ? 'active' : 'Inactive') . "</td>";

                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td class='text-center' colspan='7'>No users found!</td></tr>";
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
            $("#tableUser").DataTable({
                // "responsive": true,
                "lengthChange": true,
                "autoWidth": false,
                "buttons": [, "csv", "excel", "pdf"]
            }).buttons().container().appendTo('#tableUser_wrapper .col-md-6:eq(0)');
            $('#tableUser2').DataTable({
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

    <!-- delete user -->
    <script>
        $(document).ready(function() {
            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                var userId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this user!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_users.php', // Adjust URL to your delete script
                            type: 'POST',
                            data: {
                                id: userId
                            },
                            success: function(response) {
                                console.log('Response:', response); // Debugging: Log the response
                                if (response === 'success') {
                                    console.log('Removing row with ID: #user-' + userId); // Log the row being removed
                                    $('#user-' + userId).remove(); // Remove the row from the table
                                    Swal.fire('Deleted!', 'Your user has been deleted.', 'success');
                                } else {
                                    Swal.fire('Error!', 'Failed to delete user.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', status, error); // Debugging: Log AJAX errors
                                Swal.fire('Error!', 'An error occurred while deleting the user.', 'error');
                            }
                        });
                    }
                });
            });

            // Handle edit button click
            $(document).on('click', '.edit-btn', function() {
                var userId = $(this).data('id');
                // Redirect or load edit form page, passing userId
                window.location.href = 'edit_users.php?id=' + userId;
            });
        });
    </script>

</body>

</html>