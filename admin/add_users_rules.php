<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";

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

    $listUsersRules1 = $user['list_user_rules'];
    $AddUserRules = $user['add_user_rules'];
    $EditUserRules = $user['edit_user_rules'];
    $DeleteUserRules = $user['delete_user_rules'];

    if (!$listUsersRules1) {
        header("location: 404.php");
        exit();
    }
    if (!$AddUserRules) {
        header("location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $rules_name = $_POST['rules_name'];
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

    // Sanitize inputs
    $rules_name = $conn->real_escape_string($rules_name);

    // Save to database
    $query = "INSERT INTO tbl_users_rules (rules_name, add_user_status, edit_user_status, delete_user_status, list_user_status, 
                                            add_ticket_status, edit_ticket_status, delete_ticket_status, list_ticket_status, list_ticket_assign,
                                            add_station, edit_station, delete_station, list_station, 
                                            add_user_rules, edit_user_rules, delete_user_rules, list_user_rules) 
              VALUES ('$rules_name', $add_user_status, $edit_user_status, $delete_user_status, $list_user_status, 
                      $add_ticket_status, $edit_ticket_status, $delete_ticket_status, $list_ticket_status, $list_ticket_assign,
                      $add_station, $edit_station, $delete_station, $list_station, 
                      $add_user_rules, $edit_user_rules, $delete_user_rules, $list_user_rules)";

    if ($conn->query($query) === TRUE) {
        $_SESSION['success_message'] = "Users Rules added successfully!";
    } else {
        $_SESSION['error_message'] = "Error: " . $conn->error;
    }

    //Redirect to the same page 
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
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php
                                if (isset($_SESSION['success_message'])) {
                                    echo "<div class='alert alert-success alert-dismissible fade show mt-2 mb-0' role='alert'>
                                        <strong>{$_SESSION['success_message']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['success_message']); // Clear the message after displaying
                                }

                                if (isset($_SESSION['error_message'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show mt-2 mb-0' role='alert'>
                                        <strong>{$_SESSION['error_message']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['error_message']); // Clear the message after displaying
                                }
                                ?>
                            </ol>
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
                                    <div class="col-12">
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
                                    <div class="col-12">
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
                                    <div class="col-12">
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
                                    <div class="col-12">
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