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

    if (!$user['list_station'] || !$user['edit_station']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
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

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Retrieve the current station_id
        $sql = "SELECT station_id FROM tbl_station WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("Station not found.");
        }
        $station = $result->fetch_assoc();
        $old_station_id = $station['station_id'];
        $stmt->close();

        // Temporarily disable foreign key checks
        $conn->query("SET foreign_key_checks = 0");

        // Update the station_id in tbl_ticket
        $sql = "UPDATE tbl_ticket SET station_id = ? ,station_name = ?, station_type = ?, province=? WHERE station_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $new_station_id, $station_name, $station_type, $province, $old_station_id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating tickets: " . $stmt->error);
        }
        $stmt->close();

        // Update the station in tbl_station
        $sql = "UPDATE tbl_station SET station_id = ?, station_name = ?, station_type = ?, province=? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $new_station_id, $station_name, $station_type, $province, $id);
        if (!$stmt->execute()) {
            throw new Exception("Error updating station: " . $stmt->error);
        }
        $stmt->close();

        // Re-enable foreign key checks
        $conn->query("SET foreign_key_checks = 1");

        // Commit the transaction
        $conn->commit();

        // Set success message and redirect
        $_SESSION['success_message'] = "Station updated successfully.";
        header("Location: edit_station.php?id=$id");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        $_SESSION['error_message'] = "Failed: " . $e->getMessage();
    }

    $conn->close();
} else {
    // Retrieve the station details for editing
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM tbl_station WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            echo "Station not found.";
            exit();
        }
        $station = $result->fetch_assoc();
        $stmt->close();
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
                            <h1 class="m-0">Station</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php
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
                        <div class="card-body p-0 ">
                            <div class="card-header">
                                <a href="station.php" class="btn btn-primary ml-2">BACK</a>
                            </div>

                            <form method="POST">
                                <input type="hidden" name="id" value="<?php echo isset($station['id']) ? $station['id'] : ''; ?>">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputStatioID">Station ID</label>
                                        <input type="text" class="form-control" id="station_id" name="station_id" value="<?php echo isset($station['station_id']) ? $station['station_id'] : ''; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputStatioName">Station Name</label>
                                        <input type="text" class="form-control" id="station_name" name="station_name" value="<?= isset($station['station_name']) ? $station['station_name'] : ''; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="station_type">Station Type</label>
                                        <select name="station_type" id="station_type" class="form-control select2bs4" style="width: 100%;" required>
                                            <option value="CoCo" <?php echo (isset($station['station_type']) && $station['station_type'] == 'CoCo') ? 'selected' : ''; ?>>CoCo</option>
                                            <option value="DoDo" <?php echo (isset($station['station_type']) && $station['station_type'] == 'DoDo') ? 'selected' : ''; ?>>DoDo</option>
                                        </select>
                                    </div>
                                    <?php
                                    // Example selected value
                                    $selected_province = isset($station['province']) ? $station['province'] : '';

                                    ?>
                                    <div class="form-group">
                                        <label>Province</label>
                                        <select name="province" class="form-control" style="width: 100%;" required>
                                            <option value="">-Select-</option>
                                            <?php
                                            $provinces = [
                                                "Phnom Penh", "Siem Reap", "Banteay Meanchey", "Kampong Speu", "Kampong Thom", "Prey Veng",
                                                "Kampot", "Battambang", "Preah Sihanouk", "Svay Rieng", "Kandal", "Kampong Chhnang",
                                                "Tboung Khmum", "Kep", "Pursat", "Koh Kong", "Kratie", "Preah Vihear", "Mondul Kiri",
                                                "Kampong Cham", "Pailin", "Stung Treng", "Oddar Meanchey", "Ratanak Kiri", "Takeo"
                                            ];

                                            foreach ($provinces as $province) {
                                                echo '<option value="' . $province . '"' . ($selected_province == $province ? ' selected' : '') . '>' . $province . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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