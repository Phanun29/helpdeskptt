<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";


// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id'];

$query_user = "
    SELECT u.*, r.list_user_rules, r.add_user_rules, r.edit_user_rules, r.delete_user_rules 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    $listUsersRules1 = $user['list_user_rules'];
    $AddUserRules = $user['add_user_rules'];
    $EditUserRules = $user['edit_user_rules'];
    $DeleteUserRules = $user['delete_user_rules'];

    if (!$listUsersRules1) {
        header("location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
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
                                    <a href="add_users_rules.php" class="btn btn-primary ml-2">Add Users Rules</a>
                                </div>
                            <?php endif; ?>
                            <br>

                            <table id="example1" class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Rules Name</th>
                                        <?php if ($EditUserRules == 0 & $DeleteUserRules == 0) {

                                            echo "<th style='display:none;'></th>";
                                        } else {
                                            echo " <th>Option</th>";
                                        } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $station_query = "SELECT * FROM tbl_users_rules ORDER BY rules_id DESC ";
                                    $station_result = $conn->query($station_query);
                                    $i =  1;
                                    if ($station_result->num_rows > 0) {
                                        while ($row = $station_result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td  class='py-1'>" . $i++ . "</td>";
                                            echo "<td  class='py-1'>" . $row['rules_name'] . "</td>";

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
                                                    echo "<a href='delete_users_rules.php?id=" . $row['rules_id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fa-solid fa-trash'></i></a>";
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