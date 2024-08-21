<?php

include "../inc/header_script.php";

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // Example user ID

$query_user =
    "SELECT u.*, r.list_station, r.add_station, r.edit_station, r.delete_station 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    if (!$user['list_station'] || !$user['edit_station']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message_station'] = "User not found or permission check failed.";
    header("Location: station.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $id = $_POST['id']; // The primary key of the station
    $new_station_id = $_POST['station_id'];
    $station_name = $_POST['station_name'];
    $station_type = $_POST['station_type'];
    $province = $_POST['province'];
    $chat_id = $_POST['chat_id'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Retrieve the current station_id
        $station_id_query = "SELECT station_id FROM tbl_station WHERE id = $id";
        $result = $conn->query($station_id_query);

        if ($result->num_rows === 0) {
            throw new Exception("Station not found.");
        }
        $station = $result->fetch_assoc();
        $old_station_id = $station['station_id'];

        // Temporarily disable foreign key checks
        $conn->query("SET foreign_key_checks = 0");
        // Update the station_id in tbl_ticket
        $sql_tbl_ticket = "UPDATE tbl_ticket SET station_id = '$new_station_id' ,station_name = '$station_name', station_type = '$station_type', province = '$province' WHERE station_id = '$old_station_id'";
        if (!$conn->query($sql_tbl_ticket)) {
            throw new Exception("Error updating tbl_ticket" . $conn->$error);
        }
        // Update the station in tbl_station
        $sql_tbl_station = "UPDATE tbl_station SET station_id = '$new_station_id' ,station_name = '$station_name', station_type = '$station_type', province = '$province', chat_id = '$chat_id'  WHERE id = '$id'";
        if (!$conn->query($sql_tbl_station)) {
            throw new Exception("Error updating tbl_station" . $conn->$error);
        }
        // Re-enable foreign key checks
        $conn->query("SET foreign_key_checks = 1");

        // // Commit the transaction
        $conn->commit();
        // Set success message and redirect
        $_SESSION['success_message_station'] = "Station updated successfully.";
        header("Location: station.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        $_SESSION['error_message_station'] = "Failed: " . $e->getMessage();
    }

    //  $conn->close();
} else {

    // Retrieve the station details for editing
    if (isset($_GET['id'])) {
        $encoded_id = $_GET['id'];

        // Fetch all possible IDs and their encoded versions
        $id_query = "SELECT id FROM tbl_station";
        $result = $conn->query($id_query);

        $id = null;

        // Iterate through all the rows to find the matching encoded ID
        while ($row = $result->fetch_assoc()) {
            $hashed_id = hash('sha256', $row['id']);
            $check_encoded_id = substr(base64_encode($hashed_id), 0, 20);

            if ($check_encoded_id === $encoded_id) {
                $id = $row['id'];
                break;
            }
        }

        if ($id !== null) {
            // Fetch the station data with the matched ID
            $sql = "SELECT * FROM tbl_station WHERE id = $id";
            $station_result = $conn->query($sql);

            if ($station_result) {
                $station = $station_result->fetch_assoc();
                // Now you can work with $station, which contains the fetched data
            } else {
                echo "Error fetching station data.";
            }
        } else {
            echo "No matching station found.";
            header("Location: 404.php");
            exit();
        }
    } else {
        // Redirect back to the station list page if no station ID is provided
        header("Location: station.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <?php include "../inc/head.php" ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include "../inc/top_nav_bar.php" ?>
        <?php include "../inc/sidebar.php" ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Update Station</h1>
                        </div><!-- /.col -->
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
                        <div class="card-body p-0 ">
                            <div class="card-header">
                                <a href="station.php" class="btn btn-primary ">BACK</a>
                            </div>

                            <form method="POST">
                                <input type="hidden" name="id" value="<?= isset($station['id']) ? $station['id'] : ''; ?>">
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="form-group col-12 col-md-12">
                                            <label for="exampleInputStatioID">Station ID</label>
                                            <input type="text" class="form-control" id="station_id" name="station_id" value="<?= isset($station['station_id']) ? $station['station_id'] : ''; ?>" required>
                                        </div>
                                        <div class="form-group col-12 col-md-12">
                                            <label for="exampleInputStatioName">Station Name</label>
                                            <input type="text" class="form-control" id="station_name" name="station_name" value="<?= isset($station['station_name']) ? $station['station_name'] : ''; ?>" required>
                                        </div>
                                        <div class="form-group col-12 col-md-12">
                                            <label for="station_type">Station Type</label>
                                            <select name="station_type" id="station_type" class="form-control select2bs4" style="width: 100%;" required>
                                                <option value="COCO" <?= (isset($station['station_type']) && $station['station_type'] == 'COCO') ? 'selected' : ''; ?>>COCO</option>
                                                <option value="DODO" <?= (isset($station['station_type']) && $station['station_type'] == 'DODO') ? 'selected' : ''; ?>>DODO</option>
                                            </select>
                                        </div>
                                        <?php
                                        //  selected provinve
                                        $selected_province = isset($station['province']) ? $station['province'] : '';

                                        ?>
                                        <div class="form-group col-12 col-md-12">
                                            <label>Province</label>
                                            <select name="province" class="form-control" style="width: 100%;" required>
                                                <option value="">-Select-</option>
                                                <?php
                                                $provinces = [
                                                    "Phnom Penh",
                                                    "Siem Reap",
                                                    "Banteay Meanchey",
                                                    "Kampong Speu",
                                                    "Kampong Thom",
                                                    "Prey Veng",
                                                    "Kampot",
                                                    "Battambang",
                                                    "Preah Sihanouk",
                                                    "Svay Rieng",
                                                    "Kandal",
                                                    "Kampong Chhnang",
                                                    "Tboung Khmum",
                                                    "Kep",
                                                    "Pursat",
                                                    "Koh Kong",
                                                    "Kratie",
                                                    "Preah Vihear",
                                                    "Mondul Kiri",
                                                    "Kampong Cham",
                                                    "Pailin",
                                                    "Stung Treng",
                                                    "Oddar Meanchey",
                                                    "Ratanak Kiri",
                                                    "Takeo"
                                                ];

                                                foreach ($provinces as $province) {
                                                    echo '<option value="' . $province . '"' . ($selected_province == $province ? ' selected' : '') . '>' . $province . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label for="chat_id">Chat ID</label>
                                            <input type="text" class="form-control" name="chat_id" id="chat_id" value="<?= $station['chat_id'] ?>">
                                        </div>

                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>

                            </form>
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

    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>


</body>

</html>