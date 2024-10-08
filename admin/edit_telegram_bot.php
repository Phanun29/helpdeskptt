<?php

include "../inc/header_script.php";

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
    $_SESSION['error_message_station'] = "User not found or permission check failed.";
    header("Location: station.php");
    exit();
}
$id = $_GET['id'];
// Retrieve the station details for editing
if (isset($_GET['id'])) {
    $encoded_id = $_GET['id'];

    // Fetch all possible IDs and their encoded versions
    $id_query = "SELECT id FROM tbl_telegram_bot";
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
}
// Check if form is submitted for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $bot_name = $_POST['bot_name'];
    $token = $_POST['token'];
    $chat_id = $_POST['chat_id'];


    // Update user query
    $update_query = "UPDATE tbl_telegram_bot SET bot_name = '$bot_name', token = '$token', chat_id = '$chat_id' WHERE id = $id";

    if ($conn->query($update_query) === TRUE) {
        $_SESSION['success_message_telegram_bot'] = "telegram_bot updated successfully.";
    } else {
        $_SESSION['error_message_telegram_bot'] = "Error updating telegram_bot : " . $conn->error;
    }

    // Redirect back to users.php
    header("Location: telegram_bot.php");
    exit();
} else {
}
if ($id) {
    // Fetch the station data with the matched ID
    $sql = "SELECT* FROM tbl_telegram_bot WHERE id = $id";
    $telegram_bot_result = $conn->query($sql);
    if ($telegram_bot_result->num_rows > 0) {
        $row = $telegram_bot_result->fetch_assoc();
    }
} else {
  
    header("Location: 404.php");
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
                            <h1 class="m-0">Update bot</h1>
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
                                <a href="telegram_bot.php" class="btn btn-primary ">BACK</a>
                            </div>

                            <form method="POST">

                                <div class="card-body ">
                                    <div class="row">
                                        <div class="form-group col-12 col-md-12">
                                            <label for="bot_name">bot name</label>
                                            <input type="text" class="form-control" id="bot_name" name="bot_name" value="<?= $row['bot_name'] ?>" required>
                                        </div>
                                        <div class="form-group col-12 col-md-12">
                                            <label for="token">Token</label>
                                            <input type="text" class="form-control" id="token" name="token" value="<?= $row['token'] ?>" required>
                                        </div>
                                        <div class="form-group col-12 col-md-12">
                                            <label for="chat_id">Chat ID</label>
                                            <input type="text" class="form-control" id="chat_id" name="chat_id" value="<?= $row['chat_id'] ?>" required>
                                        </div>

                                        <div class="form-group col-12 col-md-12">
                                            <!-- <label>Province</label>
                                            <select name="province" class="form-control" style="width: 100%;" required>
                                                <option value="">-Select-</option>

                                            </select> -->
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