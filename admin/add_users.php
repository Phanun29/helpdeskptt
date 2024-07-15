<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";

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

    $listUsers = $user['list_user_status'];
    $AddUsers = $user['add_user_status'];
    $EditUsers = $user['edit_user_status'];
    $DeleteUsers = $user['delete_user_status'];

    if (!$listUsers) {
        header("location: 404.php");
        exit();
    }
    if (!$AddUsers) {
        header("location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}

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
        $check_email_query = "SELECT * FROM tbl_users WHERE email = ?";
        $stmt = $conn->prepare($check_email_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['error_message'] = "Email already exists.";
        } else {
            // Insert new user into the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO tbl_users (users_name, email, password, rules_id,company, status) VALUES (?, ?, ?, ?, ?,?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("sssiss", $users_name, $email, $hashed_password, $rules_id, $company, $status);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "User added successfully.";
            } else {
                $_SESSION['error_message'] = "Error adding user: " . $conn->error;
            }
        }
    }

    // Redirect to the same page to display messages
    header('Location: ' . $_SERVER['REQUEST_URI']);
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
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="company">Company <span class="text-danger">*</span></label>

                                                <select class="form-control" name="company" id="company" required>
                                                    <option value="">select</option>
                                                    <option value="PTTCL">PTTCL</option>
                                                    <option value="PTTDigital">PTTDigital</option>

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

                                    <div class="">
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