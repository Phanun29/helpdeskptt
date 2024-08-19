<?php
include "../inc/header_script.php"; // Include the header

// Retrieve the current user's ID from the fetched user information
$user_id = $fetch_info['users_id']; //  user ID

// Construct the SQL query to fetch user details along with their associated permissions
$query_user = " SELECT r.list_station, r.add_station, r.edit_station, r.delete_station 
                FROM tbl_users u 
                JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
                WHERE u.users_id = $user_id";

// Execute the query
$result_user = $conn->query($query_user);

// Check if the query was successful and if any rows were returned
if ($result_user && $result_user->num_rows > 0) {
    // Fetch the user's data as an associative array
    $user = $result_user->fetch_assoc();

    // Check if the user has permission to list and add station
    if (!$user['list_station'] || !$user['add_station']) {
        // Redirect to a 404 error page if permissions are insufficient
        header("location: 404.php");
        exit();
    }
} else {
    // Set an error message if the user was not found or if permission check failed
    $_SESSION['error_message_station'] = "User not found or permission check failed.";
    header("location: 404.php");
    exit();
}

// Handle form submission for adding a new station
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $station_id = $_POST['station_id'];
    $station_name = $_POST['station_name'];
    $station_type = $_POST['station_type'];
    $province = $_POST['province'];

    $insert_station_query = "INSERT INTO tbl_station (station_id, station_name, station_type, province) 
            VALUES ('$station_id', '$station_name', '$station_type', '$province')";

    if ($conn->query($insert_station_query) === TRUE) {
        // Successful insertion
        $_SESSION['success_message_station'] = "New station created successfully";
        // Redirect to the page station to display messages
        header('Location: station.php');
        exit();
    } else {
        echo "Error: " . $insert_station_query . "<br>" . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>

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
                                <div class="card-body ">
                                    <div class="row">
                                        <div class="form-group col-12 col-md-12">
                                            <label for="exampleInputStatioID">Station ID <span class="text-danger">*</span></label>
                                            <input type="text" name="station_id" class="form-control" id="exampleInputStatioID" placeholder="Station ID" required>
                                        </div>
                                        <div class="form-group col-12 col-md-12">
                                            <label for="exampleInputStatioName">Station Name <span class="text-danger">*</span></label>
                                            <input type="text" name="station_name" class="form-control" id="exampleInputStatioName" placeholder="Station Name" required>
                                        </div>
                                        <div class="form-group col-12 col-md-12">
                                            <label>Station Type <span class="text-danger">*</span></label>
                                            <select name="station_type" class="form-control select2bs4" style="width: 100%;" required>
                                                <option value="">-Select-</option>
                                                <option value="COCO">COCO</option>
                                                <option value="DODO">DODO</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-12 col-md-12">
                                            <label>Province <span class="text-danger">*</span></label>
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
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
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