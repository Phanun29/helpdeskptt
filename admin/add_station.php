<?php
include "config.php"; // Include your database connection configuration
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
    if (!$canAddStation) {
        header("location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $station_id = $_POST['station_id'];
    $station_name = $_POST['station_name'];
    $station_type = $_POST['station_type'];
    $province = $_POST['province'];

    $sql = "INSERT INTO tbl_station (station_id, station_name, station_type,province) 
              VALUES ('$station_id', '$station_name', '$station_type','$province')";

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
                                            <option value="">-Select-</option>
                                            <option value="CoCo">CoCo</option>
                                            <option value="DoDo">DoDO</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Province</label>
                                        <select name="province" class="form-control" style="width: 100%;" required>
                                            <option value="">-Select-</option>
                                            <option value="Phnom Penh">Phnom Penh</option>
                                            <option value="Siem Reap">Siem Reap </option>
                                            <option value="Banteay Meanchey">Banteay Meanchey </option>
                                            <option value="Kampong Speu">Kampong Speu </option>
                                            <option value="Kampong Thom">Kampong Thom </option>
                                            <option value="Prey Veng">Prey Veng </option>
                                            <option value="Kampot">Kampot </option>
                                            <option value="Battambang">Battambang </option>
                                            <option value="Preah Sihanouk">Preah Sihanouk </option>
                                            <option value="Svay Rieng">Svay Rieng </option>
                                            <option value="Kandal">Kandal </option>
                                            <option value="Kampong Chhnang">Kampong Chhnang </option>
                                            <option value="Tboung Khmum">Tboung Khmum </option>
                                            <option value="Kep">Kep </option>
                                            <option value="Pursat">Pursat </option>
                                            <option value="Koh Kong">Koh Kong </option>
                                            <option value="Kratie">Kratie </option>
                                            <option value="Preah Vihear">Preah Vihear </option>
                                            <option value="Mondul Kiri">Mondul Kiri </option>
                                            <option value="Kampong Cham">Kampong Cham </option>
                                            <option value="Pailin">Pailin </option>
                                            <option value="Stung Treng">Stung Treng </option>
                                            <option value="Oddar Meanchey">Oddar Meanchey </option>
                                            <option value="Ratanak Kiri">Ratanak Kiri </option>
                                            <option value="Takeo">Takeo </option>

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
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>

</body>

</html>