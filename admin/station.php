<?php
include "config.php";
include "../inc/header.php";

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // Example user ID

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
        <?php include "../inc/nav.php" ?>
        <?php include "../inc/sidebar.php" ?>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Station</h1>
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
                                    unset($_SESSION['success_message']);
                                }

                                if (isset($_SESSION['error_message'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                    <strong>{$_SESSION['error_message']}</strong>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>";
                                    unset($_SESSION['error_message']);
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
                                    <a href="add_station.php" class="btn btn-primary ml-2">Add Station</a>
                                </div>
                            <?php endif; ?>
                            <br>
                            <table id="example1" class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Station ID</th>
                                        <th>Station Name</th>
                                        <th>Station Type</th>
                                        <th>Province</th>
                                        <?php if (!$canEditStation && !$canDeleteStation) : ?>
                                            <th style="display:none;"></th>
                                        <?php else : ?>
                                            <th>Option</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $station_query = "SELECT * FROM tbl_station ORDER BY id DESC";
                                    $station_result = $conn->query($station_query);
                                    $i = 1;

                                    if ($station_result->num_rows > 0) {
                                        while ($row = $station_result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td class='py-1'>{$i}</td>";
                                            echo "<td class='py-1'>{$row['station_id']}</td>";
                                            echo "<td class='py-1'>{$row['station_name']}</td>";
                                            echo "<td class='py-1'>{$row['station_type']}</td>";
                                            echo "<td class='py-1'>{$row['province']}</td>";
                                            if (!$canEditStation && !$canDeleteStation) {
                                                echo "<td style='display:none;'></td>";
                                            } else {
                                                echo "<td class='py-1'>";
                                                if ($canEditStation) {
                                                    echo "<a href='edit_station.php?id={$row['id']}' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                }
                                                if ($canDeleteStation) {
                                                    echo "<a href='delete_station.php?id={$row['id']}' class='btn btn-danger' onclick='return confirm(\"Are you sure you want to delete this item?\");'><i class='fa-solid fa-trash'></i></a>";
                                                }
                                                echo "</td>";
                                            }
                                            echo "</tr>";
                                            $i++;
                                        }
                                    } else {
                                        echo "<tr><td class='text-center' colspan='5'>Don't have Station!</td></tr>";
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
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <script>
        $(function() {
            $("#example1").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "buttons": ["csv", "excel", "pdf"]
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