<?php
include "../inc/header_script.php"; // Include the header

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // User ID from session or fetched information

// Query to fetch user permissions
$query_user = "SELECT r.list_user_rules, r.add_user_rules 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user); // Execute the query

// Check if the query returned a result
if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc(); // Fetch user permissions
    // Redirect to 404 if the user does not have the required permissions
    if (!$user['list_user_rules'] || !$user['add_user_rules']) {
        header("location: 404.php");
        exit();
    }
} else {
    // Set error message and redirect if the user is not found or permission check failed
    $_SESSION['error_message_users_rules'] = "User not found or permission check failed.";
    header("location: 404.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $rules_name = $conn->real_escape_string($_POST['rules_name']); // Sanitize rules_name input
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : []; // Fetch permissions array

    // Initialize permission statuses to 0 (not allowed)
    $permissions_status = [
        'add_user' => 0,
        'edit_user' => 0,
        'delete_user' => 0,
        'list_user' => 0,
        'add_ticket' => 0,
        'edit_ticket' => 0,
        'delete_ticket' => 0,
        'list_ticket' => 0,
        'list_ticket_assign' => 0,
        'add_station' => 0,
        'edit_station' => 0,
        'delete_station' => 0,
        'list_station' => 0,
        'add_user_rules' => 0,
        'edit_user_rules' => 0,
        'delete_user_rules' => 0,
        'list_user_rules' => 0,
        'list_ticket_track' => 0,
        'list_telegram_bot' => 0
    ];

    // Set selected permissions to 1 (allowed)
    foreach ($permissions as $permission) {
        if (isset($permissions_status[$permission])) {
            $permissions_status[$permission] = 1;
        }
    }

    // Prepare the SQL query to save to the database
    $stmt = $conn->prepare("
        INSERT INTO tbl_users_rules 
        (rules_name, add_user_status, edit_user_status, delete_user_status, list_user_status, 
         add_ticket_status, edit_ticket_status, delete_ticket_status, list_ticket_status, list_ticket_assign,
         add_station, edit_station, delete_station, list_station, 
         add_user_rules, edit_user_rules, delete_user_rules, list_user_rules ,list_ticket_track, list_telegram_bot) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    // Bind parameters to the prepared statement
    $stmt->bind_param(
        "siiiiiiiiiiiiiiiiiii",
        $rules_name,
        $permissions_status['add_user'],
        $permissions_status['edit_user'],
        $permissions_status['delete_user'],
        $permissions_status['list_user'],
        $permissions_status['add_ticket'],
        $permissions_status['edit_ticket'],
        $permissions_status['delete_ticket'],
        $permissions_status['list_ticket'],
        $permissions_status['list_ticket_assign'],
        $permissions_status['add_station'],
        $permissions_status['edit_station'],
        $permissions_status['delete_station'],
        $permissions_status['list_station'],
        $permissions_status['add_user_rules'],
        $permissions_status['edit_user_rules'],
        $permissions_status['delete_user_rules'],
        $permissions_status['list_user_rules'],
        $permissions_status['list_ticket_track'],
        $permissions_status['list_telegram_bot']
    );

    // Execute the prepared statement and set session messages based on success or failure
    if ($stmt->execute()) {
        $_SESSION['success_message_users_rules'] = "Users Rules added successfully!";
    } else {
        $_SESSION['error_message_users_rules'] = "Error: " . $stmt->error;
    }

    // Close the prepared statement
    $stmt->close();
    // Redirect to the users_rules.php page
    header("Location: users_rules.php");
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
                            <h1 class="m-0">Add Users Rules</h1>
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
                                <a href="users_rules.php" class="btn btn-primary">BACK</a>
                            </div>
                            <form method="POST" action="">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="rules_name">Rules Name <span class="text-danger">*</span></label>
                                                <input type="text" name="rules_name" class="form-control" id="rules_name" placeholder="Enter Name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Permissions: Users -->
                                    <div class="col-12 mt-2">
                                        <h6>USERS</6>
                                    </div>

                                    <div class="row card-footer ">
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="add_user">Add User</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="add_user" id="add_user">
                                            </div>


                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="edit_user">Edit User</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="edit_user" id="edit_user">
                                            </div>


                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="delete_user">Delete User</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="delete_user" id="delete_user">
                                            </div>

                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_user">List User</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_user" id="list_user">
                                            </div>
                                        </div>

                                    </div>
                                    <!-- Permissions: Tickets -->
                                    <div class="col-12 mt-2">
                                        <h6>TICKETS</h6>
                                    </div>
                                    <div class="row card-footer">
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="add_ticket">Add Ticket</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="add_ticket" id="add_ticket">
                                            </div>

                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="edit_ticket">Edit Ticket</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="edit_ticket" id="edit_ticket">
                                            </div>


                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="delete_ticket">Delete Ticket</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="delete_ticket" id="delete_ticket">
                                            </div>


                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_ticket">List Ticket</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_ticket" id="list_ticket">
                                            </div>

                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_ticket_assign">List Ticket Assign</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_ticket_assign" id="list_ticket_assign">
                                            </div>

                                        </div>

                                    </div>
                                    <!-- Permissions: Stations -->
                                    <div class="col-12 mt-2">
                                        <h6>STATIONS</h6>
                                    </div>
                                    <div class="row card-footer">

                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="add_station">Add Station</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="add_station" id="add_station">
                                            </div>


                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="edit_station">Edit Station</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="edit_station" id="edit_station">
                                            </div>


                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="delete_station">Delete Station</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="delete_station" id="delete_station">
                                            </div>


                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_station">List Station</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_station" id="list_station">
                                            </div>


                                        </div>

                                    </div>
                                    <!-- Permissions: User Rules -->
                                    <div class="col-12 mt-2">
                                        <h6>USERS RULES</h6>
                                    </div>
                                    <div class="row card-footer">

                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="add_user_rules">Add User Rules</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="add_user_rules" id="add_user_rules">
                                            </div>


                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="edit_user_rules">Edit User Rules</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="edit_user_rules" id="edit_user_rules">
                                            </div>

                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="delete_user_rules">Delete User Rules</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="delete_user_rules" id="delete_user_rules">
                                            </div>


                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_user_rules">List User Rules</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_user_rules" id="list_user_rules">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row card-header">
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_ticket_track">List Ticket Track</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_ticket_track" id="list_ticket_track">
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_telegram_bot">List Telegram Bot</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_telegram_bot" id="list_telegram_bot">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" name="submit" class="btn btn-primary">Submit</button>
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