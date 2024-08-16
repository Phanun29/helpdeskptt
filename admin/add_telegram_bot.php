<?php
include "../inc/header_script.php";
// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; //  user ID

$query_user = " SELECT u.*, r.list_station, r.list_ticket_status, r.list_user_status, r.list_user_rules  , r.list_ticket_assign
                FROM tbl_users u 
                JOIN tbl_users_rules r 
                ON u.rules_id = r.rules_id 
                WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    if ($user['list_ticket_assign']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $chat_id = $_POST['chat_id'];
    


    $insert_user_query = "INSERT INTO tbl_telegram_bot (token, chat_id ) 
            VALUES ('$token', '$chat_id')";
    if ($conn->query($insert_user_query) == TRUE) {
        $_SESSION['success_message_users'] = "bot added successfully.";
    } else {
        $_SESSION['error_message_users'] = "Error adding user: " . $conn->error;
    }


    // Redirect to the page user to display messages
    header('Location: telegram_bot.php');
    exit();
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
                            <h1 class="m-0">Add Telegram Bot</h1>
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
                                <a href="users.php" class="btn btn-primary ml-2">BACK</a>
                            </div>
                            <form method="POST" action="">
                                <div class="card-body">
                                    <div class="row">
                                        <!-- <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="users_name"> Name <span class="text-danger">*</span></label>
                                                <input type="text" name="users_name" class="form-control" id="users_name" placeholder="Enter Name" >
                                            </div>
                                        </div> -->
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="token">token <span class="text-danger">*</span></label>
                                                <input type="text" name="token" class="form-control" id="token" placeholder="Enter token" required>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="chat_id">chat id <span class="text-danger">*</span></label>
                                                <input type="text" name="chat_id" class="form-control" id="chat_id" placeholder="token" required>

                                            </div>

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