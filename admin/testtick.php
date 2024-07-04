<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";
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
                            <h1 class="m-0">Ticket</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php"> <i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li class="breadcrumb-item active">Ticket</li>
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

                        <style>
                            .col-sm-12 {
                                overflow: auto;
                            }

                            #example1_wrapper>.row:first-child {
                                margin: 0 10px;
                            }
                        </style>
                        <!-- /.card-header -->
                        <div class="card-body p-0 ">
                            <div class="card-header row">
                                <a href="create_ticket.php" class="btn btn-primary ml-2">Add Ticket</a>
                            </div>
                            <br>
                            <table class="table table-hover text-nowrap" id="example1" style="font-size: 15px;overflow:auto;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ID Ticket</th>
                                        <th>ID Station</th>
                                        <th>Station Name</th>
                                        <th>Station Type</th>
                                        <th>Description</th>
                                        <th>Image</th>
                                        <th>Issue Type</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Assign</th>
                                        <th>Ticket Open</th>
                                        <th>Ticket Close</th>
                                        <th>Comment</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $user_query = "
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
                                    $user_result = $conn->query($user_query);
                                    $i = 1;
                                    if ($user_result->num_rows > 0) {
                                        while ($row = $user_result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td class='pt-1'>" . $i++ . "</td>";
                                            echo "<td class='pt-1'>" . $row['ticket_id'] . "</td>";
                                            echo "<td class='pt-1'>" . $row['station_id'] . "</td>";
                                            echo "<td class='pt-1'>" . $row['station_name'] . "</td>";
                                            echo "<td class='pt-1'>" . $row['station_type'] . "</td>";
                                            echo "<td class='pt-1'>" . $row['issue_description'] . "</td>";
                                            echo "<td class='pt-1'><button class='btn text-primary link-underline-success' onclick='showImage(\"" . $row['issue_image'] . "\")'>click</button></td>";
                                            echo "<td class='pt-1'>" . $row['issue_type'] . "</td>";
                                            echo "<td class='pt-1'>" . $row['priority'] . "</td>";
                                            echo "<td class='pt-1'>" . $row['status'] . "</td>";
                                            echo "<td class='pt-1'>" . $row['users_name'] . "</td>"; // Display user_names instead of users_id
                                            echo "<td class='pt-1'>" . $row['ticket_open'] . "</td>";
                                            echo "<td class='pt-1'>" . $row['ticket_close'] . "</td>";
                                            echo "<td class='pt-1'>" . $row['comment'] . "</td>";
                                            echo "<td class='pt-1'>";
                                            echo "<a href='edit_ticket.php?id=" . $row['id'] . "' class='btn btn-primary'>Edit</a> ";
                                            echo "<a href='delete_ticket.php?id=" . $row['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
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