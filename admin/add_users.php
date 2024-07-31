<?php

include "../inc/header_script.php";
// Retrieve the current user's ID from the fetched user information
$user_id = $fetch_info['users_id'];

// Construct the SQL query to fetch user details along with their associated permissions
$query_user =
    "SELECT u.*, r.list_user_status, r.add_user_status, r.edit_user_status, r.delete_user_status 
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

// Execute the query
$result_user = $conn->query($query_user);

// Check if the query was successful and if any rows were returned
if ($result_user && $result_user->num_rows > 0) {
    // Fetch the user's data as an associative array
    $user = $result_user->fetch_assoc();

    // Check if the user has permission to list and add users
    if (!$user['list_user_status'] || !$user['add_user_status']) {
        // Redirect to a 404 error page if permissions are insufficient
        header("location: 404.php");
        exit();
    }
} else {
    // Set an error message if the user was not found or if permission check failed
    $_SESSION['error_message'] = "User not found or permission check failed.";
    header("location: 404.php");
    exit();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $users_name = $_POST['users_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rules_id = $_POST['rules_id'];
    $company = $_POST['company'];
    $status = $_POST['status'];
    // Validate inputs
    if (empty($users_name) || empty($email) || empty($password)) {
        $_SESSION['error_message'] = "All fields are required.";
    } else {
        // Check if email already exists
        $check_email_query = "SELECT * FROM tbl_users WHERE email = '$email'";
        $result_email = $conn->query($check_email_query);
        if ($result_email->num_rows > 0) {
            $_SESSION['error_message'] = "Email already exists.";
        } else {
            // Insert new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_user_query = "INSERT INTO tbl_users (users_name, email, password, rules_id,company, status) 
            VALUES ('$users_name', '$email', '$hashed_password', '$rules_id', '$company', '$status')";
            if ($conn->query($insert_user_query) == TRUE) {
                $_SESSION['success_message'] = "User added successfully.";
            } else {
                $_SESSION['error_message'] = "Error adding user: " . $conn->error;
            }
        }
    }
    // Redirect to the page user to display messages
    header('Location: users.php');
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
                            <h1 class="m-0">Add Users</h1>
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
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="users_name">User Name <span class="text-danger">*</span></label>
                                                <input type="text" name="users_name" class="form-control" id="users_name" placeholder="Enter Name" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="email">Email address <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="password">Password <span class="text-danger">*</span></label>
                                                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                                                <button type="button" class="show-password btn-sm " onclick="togglePasswordVisibility()"> <i class="fas fa-eye" id="togglePasswordIcon"></i></button>
                                            </div>
                                            <style>
                                                .show-password {
                                                    position: absolute;
                                                    top: 33px;
                                                    right: 11px;
                                                    background: none;
                                                    border: none;
                                                    color: #495057;
                                                    font-size: 20px;
                                                    cursor: pointer;
                                                }
                                            </style>
                                            <script>
                                                function togglePasswordVisibility() {
                                                    var passwordInput = document.getElementById("password");
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
                                            </script>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company">Company <span class="text-danger">*</span></label>
                                                <select class="form-control" name="company" id="company" required>
                                                    <option value="">select</option>
                                                    <option value="ABA Bank">ABA Bank</option>
                                                    <option value="Wing Bank">Wing Bank</option>
                                                    <option value="PTTCL">PTTCL</option>
                                                    <option value="PTT Digital Thailand">PTT Digital Thailand</option>
                                                    <option value="PTT Digital Cambodia">PTT Digital Cambodia</option>
                                                    <option value="MBA">MBA</option>
                                                    <option value="SD">SD</option>
                                                    <option value="CamSys">CamSys</option>
                                                    <option value="DIN">DIN</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="status">Status <span class="text-danger">*</span></label>
                                                <select name="status" class="form-control select2bs4" style="width: 100%;" required>
                                                    <option value="">select</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="rules_id">Users Rules <span class="text-danger">*</span></label>
                                                <select id="rules_id" name="rules_id" class="form-control" required>
                                                    <?php
                                                    $rules_query = "SELECT rules_id, rules_name FROM tbl_users_rules";
                                                    $rules_result = $conn->query($rules_query);
                                                    if ($rules_result->num_rows > 0) {
                                                        while ($row = $rules_result->fetch_assoc()) {
                                                            echo "<option value='" . $row['rules_id'] . "'>" . $row['rules_name'] . "</option>";
                                                        }
                                                    }
                                                    $conn->close();
                                                    ?>
                                                </select>
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