<?php

include "../inc/header_script.php";

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id'];

$query_user = "
    SELECT u.*, r.list_user_status, r.add_user_status, r.edit_user_status, r.delete_user_status 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    if (!$user['list_user_status'] || !$user['edit_user_status']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message_users'] = "User not found or permission check failed.";
}


$User_id = $_GET['id'];

// Check if the form was submitted for updating user information
if (isset($_POST['change_password'])) {
    // Get the new password and confirm password from the form
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Verify if the new password matches the confirm password
    if ($newPassword === $confirmPassword) {
        // Hash the new password
        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Prepare and execute the SQL query to update the user's password
        $updateSql = "UPDATE tbl_users SET password = '$hashedNewPassword' WHERE users_id = $User_id";
        if ($conn->query($updateSql) === TRUE) {
            // Password updated successfully
            $_SESSION['password_change_message'] = 'Password changed successfully.';
        } else {
            // Error updating password
            $_SESSION['password_change_error_message'] = 'Error occurred while changing password.';
        }
    } else {
        // New password and confirm password do not match
        $_SESSION['password_change_error_message'] = 'New password and confirm password do not match.';
    }

    // Redirect back to the same page to display the alert message
    header('Location: ' . $_SERVER['PHP_SELF'] . '?id=' . $User_id);
    exit();
}

// Check if form is submitted for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $User_id = $_GET['id']; // Assuming you're passing the user's ID through a GET parameter

    // Retrieve form data
    $users_name = $_POST['users_name'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $rules_id = $_POST['rules_id'];
    $company = $_POST['company'];

    // Update user query
    $update_query = "UPDATE tbl_users SET users_name = '$users_name', email = '$email', status = '$status', rules_id = '$rules_id' ,company ='$company' WHERE users_id = $User_id";

    if ($conn->query($update_query) === TRUE) {
        $_SESSION['success_message_users'] = "User updated successfully.";
    } else {
        $_SESSION['error_message_users'] = "Error updating user : " . $conn->error;
    }

    // Redirect back to users.php
    header("Location: users.php");
    exit();
}

// Fetch user data based on user ID
$User_id = $_GET['id']; // Assuming you're passing the user's ID through a GET parameter
$user_query = "SELECT * FROM tbl_users WHERE users_id = $User_id";
$user_result = $conn->query($user_query);
$row = $user_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <?php include "../inc/head.php"; ?>
    <style>
        .show-password {
            position: absolute;
            top: 50px;
            right: 25px;
            background: none;
            border: none;
            color: #495057;
            font-size: 20px;
            cursor: pointer;
        }

        .show-password-confirm {
            position: absolute;
            top: 119px;
            right: 25px;
            background: none;
            border: none;
            color: #495057;
            font-size: 20px;
            cursor: pointer;
        }
    </style>
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
                            <h1 class="m-0">update Users</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php
                                if (isset($_SESSION['password_change_message'])) {
                                    echo "<div class='alert alert-success alert-dismissible fade show mb-0' role='alert'>
                                        <strong>{$_SESSION['password_change_message']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['password_change_message']); // Clear the message after displaying
                                }

                                if (isset($_SESSION['password_change_error_message'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show mb-0' role='alert'>
                                        <strong>{$_SESSION['password_change_error_message']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['password_change_error_message']); // Clear the message after displaying
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
                                <a href="users.php" class="btn btn-primary ml-2">BACK</a>
                                <button type="button" id="changePasswordBtn" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                    Change Password
                                </button>

                            </div>
                            <form method="POST" action="">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="users_name">User Name</label>
                                                <input type="text" name="users_name" class="form-control" id="users_name" placeholder="Enter Name" value="<?= $row['users_name'] ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="email">Email address</label>
                                                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" value="<?= $row['email'] ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" class="form-control select2bs4" style="width: 100%;" required>
                                                    <option value="1" <?= ($row['status'] == '1') ? 'selected' : ''; ?>>Active</option>
                                                    <option value="0" <?= ($row['status'] == '0') ? 'selected' : ''; ?>>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="rules_id">Permission</label>
                                                <select id="rules_id" name="rules_id" class="form-control">
                                                    <?php
                                                    $rules_query = "SELECT rules_id, rules_name FROM tbl_users_rules";
                                                    $rules_result = $conn->query($rules_query);
                                                    if ($rules_result->num_rows > 0) {
                                                        while ($rule_row = $rules_result->fetch_assoc()) {
                                                            // Check if this option should be selected
                                                            $selected = ($rule_row['rules_id'] == $row['rules_id']) ? 'selected' : '';
                                                            echo "<option value='" . $rule_row['rules_id'] . "' $selected>" . $rule_row['rules_name'] . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company">Company</label>

                                                <select class="form-control" name="company" id="company">
                                                    <option value="ABA Bank" <?= $row['company'] == "ABA Bank" ? 'selec   ted' : '' ?>>ABA Bank</option>
                                                    <option value="Wing Bank" <?= $row['company'] == "ABA Bank" ? "selected" : '' ?>>Wing Bank</option>
                                                    <option value="PTTCL" <?= $row['company'] == "PTTCL" ? 'selected' : '' ?>>PTTCL</option>
                                                    <option value="PTT Digital Thailand" <?= $row['company'] == 'PTT Digital Thailand' ? 'selected' : '' ?>>PTT Digital Thailand</option>
                                                    <option value="PTT Digital Cambodia" <?= $row['company'] == "PTT Digital Cambodia" ? 'selected' : '' ?>>PTT Digital Cambodia</option>
                                                    <option value="MBA" <?= $row['company'] == "MBA" ? 'selected' : '' ?>>MBA</option>
                                                    <option value="SD" <?= $row['company'] == "SD" ? 'selected' : '' ?>>SD</option>
                                                    <option value="CamSys" <?= $row['company'] == "CamSys" ? "selected" : '' ?>>CamSys</option>
                                                    <option value="DIN" <?= $row['company'] == "DIN" ? "selected" : '' ?>>DIN</option>


                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Update</button>
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

    <!-- model chang password -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Change Password</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <!-- Modal Body -->
                <div class="modal-body">
                    <form action="" method="POST" onsubmit="return checkPasswordMatch();">

                        <div class="form-group">
                            <label for="new_password">New Password<span class="text-danger">*</span></label>
                            <div id="show_password" class="d-flex justify-content-between">
                                <input class="form-control" type="password" id="new_password" name="new_password" required>
                                <button type="button" class="show-password btn-sm " onclick="togglePasswordVisibility()"> <i class="fas fa-eye" id="togglePasswordIcon"></i></button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="" for="confirm_password">Confirm Password<span class="text-danger">*</span></label>
                            <div id="show_password" class="d-flex justify-content-between">
                                <input class="form-control" type="password" id="confirm_password" name="confirm_password" required>
                                <button type="button" class="show-password-confirm btn-sm " onclick="togglePasswordVisibilityConfirm()"> <i class="fas fa-eye" id="togglePasswordIconConfirm"></i></button>
                            </div>
                        </div>
                        <div class="col-12" style="text-align: right;">
             
                            <button class="btn btn-success float-end mt-3" type="submit" name="change_password">Change</button>
                        </div>



                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- auto close alert -->
    <script src="../scripts/auto_close_alert.js"></script>
    <!-- show password -->
    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("new_password");
            var togglePasswordIcon = document.getElementById("togglePasswordIcon");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                togglePasswordIcon.classList.remove("fa-eye");
                togglePasswordIcon.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                togglePasswordIcon.classList.remove("fa-eye-slash");
                togglePasswordIcon.classList.add("fa-eye");
            }
        }

        function togglePasswordVisibilityConfirm() {
            var passwordInput = document.getElementById("confirm_password");
            var togglePasswordIconConfirm = document.getElementById("togglePasswordIconConfirm");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                togglePasswordIconConfirm.classList.remove("fa-eye");
                togglePasswordIconConfirm.classList.add("fa-eye-slash");
            } else {
                passwordInput.type = "password";
                togglePasswordIconConfirm.classList.remove("fa-eye-slash");
                togglePasswordIconConfirm.classList.add("fa-eye");
            }
        }
    </script>


</body>

</html>