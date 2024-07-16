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

    if (!$user['list_user_status'] || !$user['edit_user_status']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}

// Check if form is submitted for update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_GET['id']; // Assuming you're passing the user's ID through a GET parameter

    // Retrieve form data
    $users_name = $_POST['users_name'];
    $email = $_POST['email'];
    $status = $_POST['status'];
    $rules_id = $_POST['rules_id'];
    $company = $_POST['company'];

    // Update user query
    $update_query = "UPDATE tbl_users SET users_name = '$users_name', email = '$email', status = '$status', rules_id = '$rules_id' ,company ='$company' WHERE users_id = $user_id";

    if ($conn->query($update_query) === TRUE) {
        $_SESSION['success_message'] = "User updated successfully.";
    } else {
        $_SESSION['error_message'] = "Error updating user : " . $conn->error;
    }

    // Redirect back to users.php
    header("Location: users.php");
    exit();
}

// Fetch user data based on user ID
$user_id = $_GET['id']; // Assuming you're passing the user's ID through a GET parameter
$user_query = "SELECT * FROM tbl_users WHERE users_id = $user_id";
$user_result = $conn->query($user_query);
$row = $user_result->fetch_assoc();
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
                                                <label for="users_name">User Name</label>
                                                <input type="text" name="users_name" class="form-control" id="users_name" placeholder="Enter Name" value="<?php echo $row['users_name'] ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="email">Email address</label>
                                                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" value="<?php echo $row['email'] ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="status">Status</label>
                                                <select name="status" class="form-control select2bs4" style="width: 100%;" required>
                                                    <option value="1" <?php echo ($row['status'] == '1') ? 'selected' : ''; ?>>Active</option>
                                                    <option value="0" <?php echo ($row['status'] == '0') ? 'selected' : ''; ?>>Inactive</option>
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

                                                    <option value="PTTCL" <?php echo $row['company'] == 'PTTCL' ? 'selected' : ''; ?>>PTTCL</option>
                                                    <option value="PTTDigital" <?php echo $row['company']  == 'PTTDigital' ? 'selected' : ''; ?>>PTTDigital</option>

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