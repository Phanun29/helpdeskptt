<?php

include "../inc/header_script.php";


// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id'];

$query_user = "
    SELECT u.*, r.list_user_rules, r.add_user_rules, r.edit_user_rules, r.delete_user_rules 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    if (!$user['list_user_rules'] || !$user['edit_user_rules']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message_users_rules'] = "User not found or permission check failed.";
}
if (isset($_GET['id'])) {
    //Decryption id
    $encoded_id = $_GET['id'];

    // Fetch all possible IDs and their encoded versions
    $id_query = "SELECT rules_id FROM tbl_users_rules";
    $result = $conn->query($id_query);

    $id = null;

    // Iterate through all the rows to find the matching encoded ID
    while ($row = $result->fetch_assoc()) {
        $hashed_id = hash('sha256', $row['rules_id']);
        $check_encoded_id = substr(base64_encode($hashed_id), 0, 20);

        if ($check_encoded_id === $encoded_id) {
            $rules_id = $row['rules_id'];
            break;
        }
    }
} else {
    header("Location: 404.php");
    exit();
}

if ($rules_id) {
    // Fetch existing data (assuming you have an ID or some identifier to fetch the record)

    // Retrieve data from the database
    $user_rules_query = "SELECT * FROM tbl_users_rules WHERE rules_id = $rules_id";
    $user_rules_result = $conn->query($user_rules_query);
    $users_rules = $user_rules_result->fetch_assoc();
} else {
    header("Location: 404.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $rules_name = $_POST['users_name'];
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // Initialize permission statuses
    $add_user_status = in_array('add_user', $permissions) ? 1 : 0;
    $edit_user_status = in_array('edit_user', $permissions) ? 1 : 0;
    $delete_user_status = in_array('delete_user', $permissions) ? 1 : 0;
    $list_user_status = in_array('list_user', $permissions) ? 1 : 0;

    $add_ticket_status = in_array('add_ticket', $permissions) ? 1 : 0;
    $edit_ticket_status = in_array('edit_ticket', $permissions) ? 1 : 0;
    $delete_ticket_status = in_array('delete_ticket', $permissions) ? 1 : 0;
    $list_ticket_status = in_array('list_ticket', $permissions) ? 1 : 0;
    $list_ticket_assign = in_array('list_ticket_assign', $permissions) ? 1 : 0;

    $add_station = in_array('add_station', $permissions) ? 1 : 0;
    $edit_station = in_array('edit_station', $permissions) ? 1 : 0;
    $delete_station = in_array('delete_station', $permissions) ? 1 : 0;
    $list_station = in_array('list_station', $permissions) ? 1 : 0;

    $add_user_rules = in_array('add_user_rules', $permissions) ? 1 : 0;
    $edit_user_rules = in_array('edit_user_rules', $permissions) ? 1 : 0;
    $delete_user_rules = in_array('delete_user_rules', $permissions) ? 1 : 0;
    $list_user_rules = in_array('list_user_rules', $permissions) ? 1 : 0;

    $list_telegram_bot = in_array('list_telegram_bot', $permissions) ? 1 : 0;
    $list_ticket_track = in_array('list_ticket_track', $permissions) ? 1 : 0;
    // Create a comma-separated list of permissions for menu_status


    // Sanitize inputs
    $rules_name = $conn->real_escape_string($rules_name);


    // Update in database
    $query = "UPDATE tbl_users_rules 
              SET rules_name = ?, add_user_status = ?, edit_user_status = ?, delete_user_status = ?, list_user_status = ?,
                  add_ticket_status = ?, edit_ticket_status = ?, delete_ticket_status = ?, list_ticket_status = ?,list_ticket_assign=?,
                  add_station = ?, edit_station = ?, delete_station = ?, list_station = ?,
                  add_user_rules = ?, edit_user_rules = ?, delete_user_rules = ?, list_user_rules = ? , list_telegram_bot=? , list_ticket_track =?
              WHERE rules_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "siiiiiiiiiiiiiiiiiiss",
        $rules_name,
        $add_user_status,
        $edit_user_status,
        $delete_user_status,
        $list_user_status,
        $add_ticket_status,
        $edit_ticket_status,
        $delete_ticket_status,
        $list_ticket_status,
        $list_ticket_assign,
        $add_station,
        $edit_station,
        $delete_station,
        $list_station,
        $add_user_rules,
        $edit_user_rules,
        $delete_user_rules,
        $list_user_rules,
        $list_telegram_bot,
        $list_ticket_track,

        $rules_id
    );

    if ($stmt->execute()) {
        $_SESSION['success_message_users_rules'] = "User Rules updated successfully!";
    } else {
        $_SESSION['error_message_users_rules'] = "Error: " . $stmt->error;
    }
    $stmt->close();

    // Redirect to the same page to avoid form resubmission on refresh
    header("Location: users_rules.php ");
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
                                <a href="users_rules.php" class="btn btn-primary ml-2">BACK</a>
                            </div>
                            <form method="POST" action="">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="users_name">User Name <span class="text-danger">*</span></label>
                                                <input type="text" name="users_name" class="form-control" id="users_name" placeholder="Enter Name" value="<?= htmlspecialchars($users_rules['rules_name']); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Permissions: Users -->
                                    <div class="col-12 mt-2">
                                        <h6>USERS</h6>
                                    </div>
                                    <div class="row card-footer">
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="add_user">Add User</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="add_user" id="add_user" <?= $users_rules['add_user_status'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="edit_user">Edit User</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="edit_user" id="edit_user" <?= $users_rules['edit_user_status'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="delete_user">Delete User</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="delete_user" id="delete_user" <?= $users_rules['delete_user_status'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_user">List User</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_user" id="list_user" <?= $users_rules['list_user_status'] ? 'checked' : ''; ?>>
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
                                                <input type="checkbox" name="permissions[]" value="add_ticket" id="add_ticket" <?= $users_rules['add_ticket_status'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="edit_ticket">Edit Ticket</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="edit_ticket" id="edit_ticket" <?= $users_rules['edit_ticket_status'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="delete_ticket">Delete Ticket</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="delete_ticket" id="delete_ticket" <?= $users_rules['delete_ticket_status'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_ticket">List Ticket</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_ticket" id="list_ticket" <?= $users_rules['list_ticket_status'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_ticket_assign">List Ticket Assign</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_ticket_assign" id="list_ticket_assign" value="delete_user" id="delete_user" <?= $users_rules['list_ticket_assign'] ? 'checked' : ''; ?>>
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
                                                <input type="checkbox" name="permissions[]" value="add_station" id="add_station" <?= $users_rules['add_station'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="edit_station">Edit Station</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="edit_station" id="edit_station" <?= $users_rules['edit_station'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="delete_station">Delete Station</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="delete_station" id="delete_station" <?= $users_rules['delete_station'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_station">List Station</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_station" id="list_station" <?= $users_rules['list_station'] ? 'checked' : ''; ?>>
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
                                                <input type="checkbox" name="permissions[]" value="add_user_rules" id="add_user_rules" <?= $users_rules['add_user_rules'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="edit_user_rules">Edit User Rules</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="edit_user_rules" id="edit_user_rules" <?= $users_rules['edit_user_rules'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="delete_user_rules">Delete User Rules</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="delete_user_rules" id="delete_user_rules" <?= $users_rules['delete_user_rules'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_user_rules">List User Rules</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_user_rules" id="list_user_rules" <?= $users_rules['list_user_rules'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row card-header">
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_ticket_track">List Ticket Track</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_ticket_track" id="list_ticket_track" <?= $users_rules['list_ticket_track'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 row">
                                            <div class="col-8">
                                                <label for="list_telegram_bot">List Telegram Bot</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="checkbox" name="permissions[]" value="list_telegram_bot" id="list_telegram_bot" <?= $users_rules['list_telegram_bot'] ? 'checked' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" name="submit" class="btn btn-primary">Update</button>
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