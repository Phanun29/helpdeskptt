<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";

// Function to check if 
function listUsers1($rules_id, $conn)
{
    $query = "SELECT list_user_status FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['list_user_status'] == 1; // Check if delete_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}
// Function to check if the user has permission to add station
function AddUsers($rules_id, $conn)
{
    $query = "SELECT add_user_status FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['add_user_status'] == 1; // Check if add_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}



// Function to check if the user has permission to edit station
function EditUsers($rules_id, $conn)
{
    $query = "SELECT edit_user_status FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['edit_user_status'] == 1; // Check if edit_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}

// Function to check if the user has permission to delete station
function DeleteUsers($rules_id, $conn)
{
    $query = "SELECT delete_user_status FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['delete_user_status'] == 1; // Check if delete_status is set to 1 (allowed)
    }
    return false; // Default to false if no permission found
}
// Assume $user_id is fetched from session or database
$user_id = $fetch_info['users_id']; // Example user ID

// Fetch user details including rules_id
$query_user = "SELECT * FROM tbl_users WHERE users_id = $user_id";
$result_user = $conn->query($query_user);
if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $rules_id = $user['rules_id'];

    // Check if user has permission to add, edit, or delete stations
    $AddUsers = AddUsers($rules_id, $conn);
    $EditUsers = EditUsers($rules_id, $conn);
    $DeleteUsers = DeleteUsers($rules_id, $conn);
    $listUsers1 = listUsers1($rules_id, $conn);
    // Redirect to 404 page if user doesn't have permission to list users
    if (!$listUsers1) {
        header("Location: 404.php");
        exit;
    }
} else {
    // Handle error if user not found or permission check fails
    $_SESSION['error_message'] = "User not found or permission check failed.";
    // header("Location: users_rules.php"); // Redirect to appropriate page
    // exit;

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
                            <h1 class="m-0">User</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <!-- <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php"> <i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol> -->
                            <?php
                            // session_start(); // Start the session at the beginning of your file

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
                        <div class="card-body p-0">



                            <?php if (isset($AddUsers) && $AddUsers) : ?>
                                <div class="card-header">
                                    <a href="add_users.php" class="btn btn-primary ml-2">Add Users</a>
                                </div>
                            <?php endif; ?>

                            <br>
                            <table id="example1" class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Users Name</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Rules</th>
                                        <th>Status</th>
                                        <!-- <th>Option</th> -->
                                        <?php if ($EditUsers == 0 & $DeleteUsers == 0) {

                                            echo "<th style='display:none;'></th>";
                                        } else {
                                            echo " <th>Option</th>";
                                        } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Modify the query to join tbl_users with tbl_rules
                                    $user_query = "SELECT tbl_users.*, tbl_users_rules.rules_name 
                                    FROM tbl_users 
                                    LEFT JOIN tbl_users_rules ON tbl_users.rules_id = tbl_users_rules.rules_id 
                                    ";
                                    $user_result = $conn->query($user_query);
                                    $i = 1;
                                    if ($user_result->num_rows > 0) {
                                        while ($row = $user_result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td class='py-1'>" . $i++ . "</td>";
                                            echo "<td class='py-1'>" . $row['users_name'] . "</td>";
                                            echo "<td class='py-1'>" . $row['email'] . "</td>";
                                            echo "<td class='py-1'>" . $row['company'] . "</td>";
                                            echo "<td class='py-1'>" . $row['rules_name'] . "</td>"; // Display rules_name instead of rules_id
                                            if ($row['status'] === '1') {
                                                echo "<td class='py-1'>active</td>";
                                            } else {
                                                echo "<td class='py-1'>Inactive</td>";
                                            }
                                            if ($EditUsers == 0 &  $EditUsers == 0) {
                                                echo " <td style='display:none;'></td>";
                                            } else {
                                                echo "<td class='py-1'>";
                                                // Edit button if user has permission
                                                if ($EditUsers) {
                                                    echo "<a href='edit_users.php?id=" . $row['users_id'] . "' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                }
                                                // Delete button if user has permission
                                                if ($DeleteUsers) {
                                                    echo "<a href='delete_users.php?id=" . $row['users_id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fa-solid fa-trash'></i></a>";
                                                }
                                                echo "</td>";
                                            }
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
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                // "responsive": true,

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
</body>

</html>