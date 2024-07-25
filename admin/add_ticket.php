<?php
include "../inc/header.php"; // Include the header

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // User ID

$query_user = " SELECT r.list_ticket_status, r.add_ticket_status 
                FROM tbl_users u 
                JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
                WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);
if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    if (!$user['list_ticket_status'] || !$user['add_ticket_status']) {
        header("location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
    header("location: 404.php");
    exit();
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $station_id = $_POST['station_id'];
    $issue_description = $_POST['issue_description'];
    $issue_type = isset($_POST['issue_type']) ? implode(',', $_POST['issue_type']) : null; // Convert array to string without spaces
    if ($SLA_category != null) {
        $SLA_category = $_POST['SLA_category'];
    } else {
        $SLA_category = null;
    }
    $status = 'Open';
    $users_id = $fetch_info['users_id'];
    $user_create_ticket = $fetch_info['users_id'];
    date_default_timezone_set('Asia/Bangkok');
    $ticket_open = date('Y-m-d H:i:s');

    $sql = "SELECT station_name, station_type, province FROM tbl_station WHERE station_id = '$station_id'";
    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
        $_SESSION['error_message'] = "Error: Invalid Station ID.";
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        $row = $result->fetch_assoc();
        $station_name = $row['station_name'];
        $station_type = $row['station_type'];
        $province = $_POST['province'];

        // Generate Ticket ID
        $current_year = date("y");
        $current_month = date("m");
        $last_ticket_query = "SELECT MAX(ticket_id) AS max_ticket_id FROM tbl_ticket WHERE ticket_id LIKE 'POS$current_year$current_month%'";
        $last_ticket_result = $conn->query($last_ticket_query);
        $last_ticket_id = $last_ticket_result->fetch_assoc()['max_ticket_id'];
        $last_seq_number = intval(substr($last_ticket_id, -6));
        $new_seq_number = $last_seq_number + 1;
        $ticket_id = "POS$current_year$current_month" . str_pad($new_seq_number, 6, "0", STR_PAD_LEFT);

        // Handle file uploads
        $uploaded_images = [];
        if (!empty($_FILES['issue_image']['name'][0])) {
            $target_dir = "../uploads/$ticket_id/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

            foreach ($_FILES['issue_image']['name'] as $key => $image) {
                $image_extension = pathinfo($image, PATHINFO_EXTENSION);
                $unique_name = uniqid() . '.' . $image_extension;
                $target_file = $target_dir . $unique_name;
                if (move_uploaded_file($_FILES["issue_image"]["tmp_name"][$key], $target_file)) {
                    $uploaded_images[] = $target_file; // Save the file path
                } else {
                    $_SESSION['error_message'] = "Error uploading image: " . $image;
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            }
        }
        // Prepare the SQL query to insert the ticket
        $stmt = $conn->prepare("INSERT INTO tbl_ticket (ticket_id, station_id, station_name, station_type, province, issue_description, issue_type, SLA_category, status, users_id, user_create_ticket, ticket_open) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $ticket_id, $station_id, $station_name, $station_type, $province, $issue_description, $issue_type, $SLA_category, $status, $users_id, $user_create_ticket, $ticket_open);

        if ($stmt->execute()) {
            $image_insert_success = true;
            foreach ($uploaded_images as $target_file) {
                $query_media = "INSERT INTO tbl_ticket_images (ticket_id, image_path) VALUES ('$ticket_id', '$target_file')";
                if ($conn->query($query_media) == true) {
                    echo "success";
                } else {
                    echo "error" . $query_media . $conn->error;
                }
            }
            if ($image_insert_success) {
                $_SESSION['success_message'] = "New ticket added successfully";
            } else {
                $_SESSION['error_message'] = "Ticket added, but failed to save all images.";
            }
        } else {
            $_SESSION['error_message'] = "Error: " . $stmt->error;
        }
        $stmt->close();
        header("Location: ticket.php");
        exit();
    }
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
                            <h1 class="m-0">Add Ticket</h1>
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
                                <a href="ticket.php" class="btn btn-primary ">BACK</a>
                            </div>
                            <form method="POST" id="FormAddTicket" novalidate="" enctype="multipart/form-data">
                                <div class="card-body col">
                                    <div class="row">
                                        <div class="form-group col-sm-3">
                                            <label for="station_id">Station ID <span class="text-danger">*</span></label>
                                            <input class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)" required>
                                            <div id="suggestion_dropdown" class="dropdown-content"></div>
                                            <div id="error-message-stationID" class="text-danger" style="display:none;">Please select Station ID.</div>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="station_name">Station Name</label>
                                            <input type="text" name="station_name" class="form-control" id="station_name" placeholder="Station Name" readonly>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="station_type">Station Type</label>
                                            <input type="text" name="station_type" class="form-control" id="station_type" placeholder="Station Type" readonly>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="province">Province</label>
                                            <input type="text" name="province" class="form-control" id="province" placeholder="Station Type" readonly>
                                        </div>

                                        <div class="form-group col-sm-8">
                                            <label for="issue_description">Issue Description <span class="text-danger">*</span></label>
                                            <textarea id="issue_description" name="issue_description" class="form-control" rows="3" placeholder="Issue Description" required></textarea>
                                            <div id="error-message-issue-description" class="text-danger" style="display:none;">Please Input Issue Description</div>
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <label for="issue_image">Issue Image</label>
                                            <input type="file" class="form-control" id="issue_image" name="issue_image[]" multiple accept="image/*,video/*">
                                        </div>
                                        <!-- Display selected new images -->
                                        <div class="col-12 row mt-3" id="imagePreview">
                                        </div>

                                        <div class="form-group col-sm-4">
                                            <style>
                                                .choices {
                                                    margin-bottom: 0;
                                                }
                                            </style>
                                            <label for="issue_type">Issue Type <span class="text-danger">*</span></label>
                                            <select name="issue_type[]" id="issue_type" class="form-control" placeholder="-Select-" multiple required>
                                                <option value="Hardware">Hardware</option>
                                                <option value="Software">Software</option>
                                                <option value="Network">Network</option>
                                                <option value="Dispenser">Dispenser</option>
                                                <option value="Unassigned">Unassigned</option>
                                            </select>
                                            <div id="error-message-issueType" class="text-danger" style="display:none;">Please select at least one issue type.</div>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="SLA_category">SLA Category</label>
                                            <select name="SLA_category" id="SLA_category" class="form-control">
                                                <option value="">-Select-</option>
                                                <option value="CAT Hardware">CAT Hardware</option>
                                                <option value="CAT 1*">CAT 1*</option>
                                                <option value="CAT 2*">CAT 2*</option>
                                                <option value="CAT 3*">CAT 3*</option>
                                                <option value="CAT 4*">CAT 4*</option>
                                                <option value="CAT 4 Report*">CAT 4 Report*</option>
                                                <option value="CAT 5*">CAT 5*</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" name="Submit" value="Submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>

                            <script>
                                // message for not complete form
                                document.getElementById('FormAddTicket').addEventListener('submit', function(event) {
                                    var issueTypeSelect = document.getElementById('issue_type');
                                    var selectedOptions1 = Array.from(issueTypeSelect.selectedOptions);
                                    var errorMessageIssueType = document.getElementById('error-message-issueType');

                                    if (selectedOptions1.length === 0) {
                                        errorMessageIssueType.style.display = 'block';
                                        event.preventDefault(); // Prevent form submission
                                    } else {
                                        errorMessageIssueType.style.display = 'none';
                                    }

                                    var stationIDInput = document.getElementById('station_id');
                                    var errorMessageStationID = document.getElementById('error-message-stationID');

                                    if (stationIDInput.value.trim() === '') {
                                        errorMessageStationID.style.display = 'block';
                                        event.preventDefault(); // Prevent form submission
                                    } else {
                                        errorMessageStationID.style.display = 'none';
                                    }

                                    var issueDescriptionInput = document.getElementById('issue_description');
                                    var errorMessageIssueDescription = document.getElementById('error-message-issue-description');

                                    if (issueDescriptionInput.value.trim() === '') {
                                        errorMessageIssueDescription.style.display = 'block';
                                        event.preventDefault(); // Prevent form submission
                                    } else {
                                        errorMessageIssueDescription.style.display = 'none';
                                    }
                                });
                            </script>


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
    <!-- select multiple -->
    <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- select multiple -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var issueTypeChoices = new Choices('#issue_type', {
                removeItemButton: true,
                maxItemCount: 5,
                searchResultLimit: 5,
                renderChoiceLimit: 5
            });

            var usersIdChoices = new Choices('#users_id', {
                removeItemButton: true,
                maxItemCount: 5,
                searchResultLimit: 5,
                renderChoiceLimit: 5
            });
        });
    </script>
    <!-- auto fill station -->
    <script src="../scripts/get_suggestions_auto_fill_stationID.js"></script>

    <!-- previewMedia -->
    <script src="../scripts/previewImages.js"></script>
</body>

</html>