<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";

// Initialize session for storing messages
$ticket_id = $_GET['id']; // Assuming you're passing the ticket ID through a GET parameter

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather form data
    $station_id = $_POST['station_id'];
    $station_name = $_POST['station_name'];
    $station_type = $_POST['station_type'];
    $issue_description = $_POST['issue_description'];
    $issue_image = $_FILES['issue_image']['name']; // Assuming you handle file upload separately
    $issue_types = implode(', ', $_POST['issue_type']);
    $priority = $_POST['priority'];
    $status = $_POST['status'];
    if (isset($_POST['users_id'])) {
        $users_id = implode(',', $_POST['users_id']);
    } else {
        $users_id = null; // or handle this case appropriately
    }

    $comment = $_POST['comment'];

    date_default_timezone_set('Asia/Bangkok');


    // Check if the status 
    if ($status == 'On Hold') {
        $ticket_on_hold = date('Y-m-d H:i:s');
    }
    if ($status == 'In Progress') {
        $ticket_in_progress = date('Y-m-d H:i:s');
    }
    if ($status == 'Pending Vendor') {
        $ticket_pending_vendor = date('Y-m-d H:i:s');
    }
    if ($status == 'Close') {
        $ticket_close = date('Y-m-d H:i:s');
    } else {
        $ticket_close = NULL;
    }
    // Process multiple file uploads
    $uploaded_images = [];
    if (!empty($_FILES['issue_image']['name'][0])) {
        $target_dir = "../uploads/";
        foreach ($_FILES['issue_image']['name'] as $key => $image) {
            $target_file = $target_dir . basename($image);
            if (move_uploaded_file($_FILES["issue_image"]["tmp_name"][$key], $target_file)) {
                $uploaded_images[] = $target_file;
            } else {
                $_SESSION['error_message'] = "Error uploading image: " . $image;
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit();
            }
        }
    }
    // Convert the array of image paths to a comma-separated string
    $issue_image_paths = implode(',', $uploaded_images);
    try {
        // Check if the ticket exists and is editable
        $check_ticket_query = "SELECT * FROM tbl_ticket WHERE id = ?";
        $stmt_check_ticket = $conn->prepare($check_ticket_query);
        $stmt_check_ticket->bind_param('i', $ticket_id);
        $stmt_check_ticket->execute();
        $ticket_result = $stmt_check_ticket->get_result();

        if ($ticket_result->num_rows > 0) {
            $row = $ticket_result->fetch_assoc();

            // Check if ticket status is 'Close'
            if ($row['status'] == 'Close') {
                $_SESSION['error_message'] = "Cannot edit a closed ticket.";
                // header("Location: 404.php");
                header("Location: ticket.php");
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Ticket not found.";
            header("Location: 404.php");
            exit;
        }

        $stmt_check_ticket->close();

        // Prepare the update query
        $update_query = "UPDATE tbl_ticket SET 
                        station_id = ?, 
                        station_name = ?, 
                        station_type = ?, 
                        issue_description = ?, 
                        issue_image = ?, 
                        issue_type = ?, 
                        priority = ?, 
                        status = ?, 
                        users_id = ?, 
                        comment = ?, 
                        ticket_on_hold=?,
                        ticket_in_progress=?,
                        ticket_pending_vendor=?,
                        ticket_close = ?
                        WHERE id = ?";

        // Prepare the statement
        $stmt = $conn->prepare($update_query);
        if (!$stmt) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        // Bind the parameters
        $stmt->bind_param(
            'ssssssssssssssi',
            $station_id,
            $station_name,
            $station_type,
            $issue_description,
            $issue_image_paths,
            $issue_types,
            $priority,
            $status,
            $users_id,
            $comment,
            $ticket_on_hold,
            $ticket_in_progress,
            $ticket_pending_vendor,
            $ticket_close,
            $ticket_id
        );

        // Execute the update
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Ticket updated successfully";
        } else {
            // Check for duplicate entry error
            if ($conn->errno == 1062) { // MySQL error code for duplicate entry
                $_SESSION['error_message'] = "Duplicate entry error: The selected users already assigned to this ticket.";
            } else {
                throw new Exception("Error updating ticket: " . $stmt->error);
            }
        }

        // Close the statement
        $stmt->close();

        // Redirect to the page or do any additional handling after update
        header("Location: edit_ticket.php?id=$ticket_id");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        header("Location: edit_ticket.php?id=$ticket_id");
        exit();
    }
}

// Fetch ticket details for display in the form
$ticket_query = "SELECT * FROM tbl_ticket WHERE id = ?";
$stmt = $conn->prepare($ticket_query);
$stmt->bind_param('i', $ticket_id);
$stmt->execute();
$ticket_result = $stmt->get_result();

if ($ticket_result->num_rows > 0) {
    $row = $ticket_result->fetch_assoc();
} else {
    $_SESSION['error_message'] = "Ticket not found.";
    header("Location: 404.php");
    exit;
}

$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
                        <div class="col-sm-6 row">
                            <a href="ticket.php" class="btn btn-primary mx-2">BACK</a>
                            <h1 class="m-0">Update Ticket</h1>
                        </div>
                        <div class="col-sm-6">
                            <!-- <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="index.php"> <i class="nav-icon fas fa-tachometer-alt"></i> Dashboard</a></li>
                                <li class="breadcrumb-item active">Ticket</li>
                            </ol> -->
                            <?php if (isset($_SESSION['success_message'])) : ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong><?php echo $_SESSION['success_message']; ?></strong>
                                    <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert(this)"></button>
                                </div>
                                <?php unset($_SESSION['success_message']); ?>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['error_message'])) : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong><?php echo $_SESSION['error_message']; ?></strong>
                                    <button type="button" class="btn-close" aria-label="Close" onclick="closeAlert(this)"></button>
                                </div>
                                <?php unset($_SESSION['error_message']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="card">

                        <div class="card-body p-0 card-primary">
                            <!-- <div class="card-header">
                                
                            </div> -->
                            <div class="card-header card-primary">
                                <h3 class="card-title">Ticket ID: <?= $row['ticket_id']; ?></h3>
                            </div>

                            <form method="POST" id="quickForm" novalidate="novalidate" enctype="multipart/form-data">
                                <div class="card-body col">
                                    <div class="row">
                                        <div class="form-group col-sm-4 ">
                                            <label for="station_input">Station ID <span class="text-danger">*</span></label>
                                            <input value="<?php echo $row['station_id'] ?>" class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)" raedonly>
                                            <div id="suggestion_dropdown" class="dropdown-content"></div>
                                        </div>


                                        <div class="form-group col-sm-4">
                                            <label for="station_name">Station Name</label>
                                            <input value="<?php echo $row['station_name'] ?>" type="text" name="station_name" class="form-control" id="station_name" placeholder="Station Name" readonly>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="station_type">Station Type</label>
                                            <input value="<?php echo $row['station_type'] ?>" type="text" name="station_type" class="form-control" id="station_type" placeholder="Station Type" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-8">
                                            <label for="issue_description">Issue Description</label>
                                            <textarea id="issue_description" name="issue_description" class="form-control" rows="3" placeholder="Issue Description"><?php echo htmlspecialchars($row['issue_description']); ?></textarea>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label for="issue_image">Issue Image</label>
                                            <div class="input-group col-12">
                                                <div class="custom-image">
                                                    <input type="file" id="issue_image" name="issue_image[]" class="form-control" multiple>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-4">
                                            <label for="issue_type">Issue Type</label>
                                            <select name="issue_type[]" class="form-control" id="issue_type" placeholder="Select up to 2 tags" multiple>
                                                <?php
                                                $issue_types = ['Hardware', 'Software', 'Network', 'Dispenser', 'Unassigned'];
                                                $selected_issue_types = explode(', ', $row['issue_type']);
                                                foreach ($issue_types as $issue_type) {
                                                    $selected = in_array(trim($issue_type), $selected_issue_types) ? 'selected' : '';
                                                    echo "<option value=\"$issue_type\" $selected>$issue_type</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="priority">SLA Category</label>
                                            <select name="priority" id="priority" class="form-control select2bs4" style="width: 100%;">
                                                <option value="CAT Hardware" <?php echo ($row['priority'] == 'CAT Hardware') ? 'selected' : ''; ?>>CAT Hardware</option>
                                                <option value="CAT 1*" <?php echo ($row['priority'] == 'CAT 1*') ? 'selected' : ''; ?>>CAT 1*</option>
                                                <option value="CAT 2*" <?php echo ($row['priority'] == 'CAT 2*') ? 'selected' : ''; ?>>CAT 2*</option>
                                                <option value="CAT 3*" <?php echo ($row['priority'] == 'CAT 3*') ? 'selected' : ''; ?>>CAT 3*</option>
                                                <option value="CAT 4*" <?php echo ($row['priority'] == 'CAT 4*') ? 'selected' : ''; ?>>CAT 4*</option>
                                                <option value="CAT 4 Report*" <?php echo ($row['priority'] == 'CAT 4 Report*') ? 'selected' : ''; ?>>CAT 4 Report*</option>
                                                <option value="CAT 5*" <?php echo ($row['priority'] == 'CAT 5*') ? 'selected' : ''; ?>>CAT 5*</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control select2bs4" style="width: 100%;">

                                                <option value="On Hold" <?php echo ($row['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                                                <option value="In Progress" <?php echo ($row['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="Pending Vendor" <?php echo ($row['status'] == 'Pending Vendor') ? 'selected' : ''; ?>>Pending Vendor</option>
                                                <option value="Close" <?php echo ($row['status'] == 'Close') ? 'selected' : ''; ?>>Close</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-4">
                                            <label for="users_id">Assign</label>
                                            <select name="users_id[]" class="form-control" id="users_id" placeholder='-select-' multiple>

                                                <?php
                                                $user_query = "SELECT users_id, users_name FROM tbl_users WHERE status = 1";
                                                $user_result = $conn->query($user_query);
                                                $assigned_users = explode(',', $row['users_id']);
                                                if ($user_result->num_rows > 0) {
                                                    while ($user_row = $user_result->fetch_assoc()) {
                                                        $selected = in_array($user_row['users_id'], $assigned_users) ? 'selected' : '';
                                                        echo "<option value=\"" . $user_row['users_id'] . "\" $selected>" . $user_row['users_name'] . "</option>";
                                                    }
                                                } else {
                                                    echo "<option value=\"\">No active users found</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-8">
                                            <label for="comment">Comment</label>
                                            <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Comment"><?php echo htmlspecialchars($row['comment']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="">
                                        <button type="submit" name="Submit" value="Submit" class="btn btn-primary">Submit</button>
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
    <!-- DataTables  & Plugins -->
    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../plugins/jszip/jszip.min.js"></script>
    <script src="../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#example1").DataTable({
                "lengthChange": false,
                "autoWidth": false,
                "buttons": [, "csv", "excel", "pdf"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>
    <!-- select multiple -->
    <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var issueTypeChoices = new Choices('#issue_type', {
                removeItemButton: true,
                maxItemCount: 5,
                searchResultLimit: 3,
                renderChoiceLimit: 3
            });

            var usersIdChoices = new Choices('#users_id', {
                removeItemButton: true,
                maxItemCount: 5,
                searchResultLimit: 3,
                renderChoiceLimit: 3
            });
        });
    </script>
    <!-- auto fill station -->
    <style>
        .dropdown-content {
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content p {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            cursor: pointer;
        }

        .dropdown-content p:hover {
            background-color: #f1f1f1;
        }
    </style>

    <!-- auto fill station -->
    <script>
        $(document).ready(function() {
            $('#station_id').blur(function() {
                var station_id = $(this).val();
                fetchStationDetails(station_id);
            });

            $('#quickForm').on('submit', function(event) {
                if (!this.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                $(this).addClass('was-validated');
            });
        });

        function fetchStationDetails(station_id) {
            $.ajax({
                url: 'get_station_details.php',
                type: 'POST',
                data: {
                    station_id: station_id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#station_name').val(response.station_name);
                        $('#station_type').val(response.station_type);
                    } else {
                        $('#station_name').val('');
                        $('#station_type').val('');
                    }
                }
            });
        }

        function showSuggestions(str) {
            if (str == "") {
                document.getElementById("suggestion_dropdown").innerHTML = "";
                return;
            } else {
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        document.getElementById("suggestion_dropdown").innerHTML = this.responseText;
                    }
                };
                xmlhttp.open("GET", "get_suggestions.php?q=" + str, true);
                xmlhttp.send();
            }
        }

        function selectSuggestion(station_id) {
            document.getElementById("station_id").value = station_id;
            document.getElementById("suggestion_dropdown").innerHTML = "";
            $('#station_id').blur();
        }
    </script>

</body>

</html>