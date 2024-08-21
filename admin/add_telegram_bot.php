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
    $_SESSION['error_message_telegram_bot'] = "User not found or permission check failed.";
}
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bot_name = $_POST['bot_name'];
    $token = $_POST['token'];
    $chat_id = isset($_POST['chat_id']) ? implode(',', $_POST['chat_id']) : null;


    // Insert each chat_id into the database
    $insert_user_query = "INSERT INTO tbl_telegram_bot (bot_name, token, chat_id) 
                              VALUES ('$bot_name', '$token', '$chat_id')";

    if ($conn->query($insert_user_query) === TRUE) {
        $success++;
    } else {
        $errors[] = "Error adding telegram_bot for chat_id $chat_id: " . $conn->error;
    }


    // Set session messages
    if ($success > 0) {
        $_SESSION['success_message_telegram_bot'] = "$success telegram_bot(s) added successfully.";
    }

    if (!empty($errors)) {
        $_SESSION['error_message_telegram_bot'] = implode("<br>", $errors);
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
        <?php include "../inc/top_nav_bar.php"; ?>
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
                                <a href="telegram_bot.php" class="btn btn-primary ml-2">BACK</a>
                            </div>

                            <form method="POST">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">

                                            <label for="bot_name"> Name <span class="text-danger">*</span></label>
                                            <input type="text" name="bot_name" class="form-control" id="bot_name" placeholder="Enter Bot Name">

                                        </div>
                                        <div class="col-12">

                                            <label for="token">Token <span class="text-danger">*</span></label>
                                            <input type="text" name="token" class="form-control" id="token" placeholder="Enter token" required>

                                        </div>

                                        <div class="form-group col-12">

                                            <label for="chat_id">Chat ID<span class="text-danger">*</span></label>
                                            <select name="chat_id[]" id="chat_id" class="form-control" multiple placeholder='Select chat id' required>
                                                <option value="">select chat id</option>
                                                <?php
                                                // Fetch users based on the condition
                                                $station_query = "SELECT station_name, chat_id FROM tbl_station";
                                                $station_result = $conn->query($station_query);

                                                if ($station_result->num_rows > 0) {
                                                    while ($station_row = $station_result->fetch_assoc()) {

                                                        echo "<option value=" . $station_row['chat_id'] . ">" . $station_row['station_name'] . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=\"\">No active users found</option>";
                                                }
                                                ?>
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
                    <!-- /.card-body -->
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
    <!-- select multiple -->
    <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chatIDChoices = new Choices('#chat_id', {
                removeItemButton: true,
                maxItemCount: 100,
                searchResultLimit: 100,
                renderChoiceLimit: 100
            });

        });
    </script>



</body>

</html>