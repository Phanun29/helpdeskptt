<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";


// Function to check if the user has permission to add station
function AddTicket($rules_id, $conn)
{
    $query = "SELECT add_station FROM tbl_users_rules WHERE rules_id = $rules_id";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['add_station'] == 1; // Check if add_status is set to 1 (allowed)
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
    $AddTicket = AddTicket($rules_id, $conn);
    // Redirect to 404 page if user doesn't have permission to list users
    if (!$AddTicket) {
        header("Location: 404.php");
        exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $station_id = $_POST['station_id'];
    $station_name = $_POST['station_name'];
    $station_type = $_POST['station_type'];

    $sql = "INSERT INTO tbl_station (station_id, station_name, station_type) 
              VALUES ('$station_id', '$station_name', '$station_type')";

    if ($conn->query($sql) === TRUE) {
        // Successful insertion
        $_SESSION['success_message'] = "New station created successfully";
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
//$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                        <div class="col-sm-6">
                            <h1 class="m-0">Add Station</h1>
                        </div>
                        <div class="col-sm-6">
                            <!-- <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php"> <i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li class="breadcrumb-item active">Ticket</li>
                            </ol> -->
                            <!-- alert -->
                            <?php if (isset($_SESSION['success_message'])) : ?>
                                <div class="alert alert-success alert-dismissible fade show " role="alert">
                                    <strong><?php echo $_SESSION['success_message']; ?></strong>
                                    <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert(this)"></button>
                                </div>
                                <?php unset($_SESSION['success_message']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="card">

                        <div class="card-body p-0 ">
                            <div class="card-header">
                                <a href="station.php" class="btn btn-primary ml-2">BACK</a>
                            </div>

                            <form method="POST">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputStatioID">Station ID</label>
                                        <input type="text" name="station_id" class="form-control" id="exampleInputStatioID" placeholder="Station ID" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputStatioName">Station Name</label>
                                        <input type="text" name="station_name" class="form-control" id="exampleInputStatioName" placeholder="Station Name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Station Type</label>
                                        <select name="station_type" class="form-control select2bs4" style="width: 100%;" required>
                                            <option value="">Select</option>
                                            <option value="CoCo">CoCo</option>
                                            <option value="DoDo">DoDO</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
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
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
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