<?php
include "../inc/header_script.php";

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // Example user ID
$query_user =
    "SELECT u.*, r.list_ticket_status, r.add_ticket_status, r.edit_ticket_status, r.delete_ticket_status, r.list_ticket_assign
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);
if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $listTicketAssign = $user['list_ticket_assign'];

    if (!$user['list_ticket_status'] || !$user['edit_ticket_status']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message_ticket'] = "User not found or permission check failed.";
    header("Location: 404.php");
    exit();
}

if (isset($_GET['id'])) {
    $encoded_id = $_GET['id'];

    // Fetch all possible IDs and their encoded versions
    $id_query = "SELECT id FROM tbl_ticket";
    $result = $conn->query($id_query);

    $id = null;

    // Iterate through all the rows to find the matching encoded ID
    while ($row = $result->fetch_assoc()) {
        $hashed_id = hash('sha256', $row['id']);
        $check_encoded_id = substr(base64_encode($hashed_id), 0, 20);

        if ($check_encoded_id === $encoded_id) {
            $id = $row['id'];
            break;
        }
    }
    if ($id !== null) {
        // Fetch the station data with the matched ID
        $sql = "SELECT * FROM tbl_ticket WHERE id = $id";
        $station_result = $conn->query($sql);

        if ($station_result) {
            $station = $station_result->fetch_assoc();
            // Now you can work with $station, which contains the fetched data
        } else {
            echo "Error fetching station data.";
        }
    } else {
        echo "No matching station found.";
        header("Location: 404.php");
        exit();
    }
} else {
    header("Location: 404.php");
    exit();
}

// Check if form is submitted
date_default_timezone_set('Asia/Bangkok');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather form data
    //   $id = $_GET['id'] ?? null; // Ensure to sanitize and validate $id
    $station_id = $_POST['station_id'] ?? null;
    $station_name = $_POST['station_name'] ?? null;
    $station_type = $_POST['station_type'] ?? null;
    $province = $_POST['province'] ?? null;
    $issue_description = $_POST['issue_description'] ?? null;
    $issue_types = isset($_POST['issue_type']) ? implode(', ', $_POST['issue_type']) : '';
    $SLA_category = $_POST['SLA_category'] ?? null;
    $status = $_POST['status'] ?? null;
    $users_id = isset($_POST['users_id']) ? implode(',', $_POST['users_id']) : null;
    $comment = $_POST['comment'] ?? null;

    // Fetch existing ticket details for validation
    $check_ticket_query = "SELECT status, ticket_open, ticket_on_hold, ticket_in_progress, ticket_pending_vendor, ticket_close ,issue_type ,SLA_category,users_id
    FROM tbl_ticket 
    WHERE id = $id";
    $ticket_result = $conn->query($check_ticket_query);

    if ($ticket_result->num_rows > 0) {
        $row = $ticket_result->fetch_assoc();

        // Record previous status and timestamps
        $prev_status = $row['status'];
        $prev_open = $row['ticket_open'];
        $prev_on_hold = $row['ticket_on_hold'];
        $prev_in_progress = $row['ticket_in_progress'];
        $prev_pending_vendor = $row['ticket_pending_vendor'];
        $prev_close = $row['ticket_close'];

        $prev_issue_type = $row['issue_type'];
        $prev_SLA_category = $row['SLA_category'];
        $prev_users_id = $row['users_id'];

        // Initialize timestamp variables
        $ticket_open = $prev_open;
        $ticket_on_hold = $prev_on_hold;
        $ticket_in_progress = $prev_in_progress;
        $ticket_pending_vendor = $prev_pending_vendor;
        $ticket_close = $prev_close;

        // Update timestamp based on status change
        switch ($status) {
            case 'On Hold':
                if ($prev_status != 'On Hold') {
                    $ticket_on_hold = date('Y-m-d H:i:s');
                }
                break;
            case 'In Progress':
                if ($prev_status != 'In Progress') {
                    $ticket_in_progress = date('Y-m-d H:i:s');
                }
                break;
            case 'Pending Vendor':
                if ($prev_status != 'Pending Vendor') {
                    $ticket_pending_vendor = date('Y-m-d H:i:s');
                }
                break;
            case 'Close':
                if ($prev_status != 'Close') {
                    // Set the current time as the ticket close time
                    $ticket_close = date('Y-m-d H:i:s');

                    // Calculate the difference between ticket open and close times
                    $ticketOpenTime = new DateTime($ticket_open);
                    $ticketCloseTime = new DateTime($ticket_close);
                    $interval = $ticketCloseTime->diff($ticketOpenTime);

                    // Format the difference
                    $ticket_time = '';
                    if ($interval->d > 0) {
                        $ticket_time .= $interval->d . 'd, ';
                    }
                    if ($interval->h > 0 || $interval->d > 0) {
                        $ticket_time .= $interval->h . 'h, ';
                    }
                    if ($interval->i > 0 || $interval->h > 0 || $interval->d > 0) {
                        $ticket_time .= $interval->i . 'm, ';
                    }
                    $ticket_time .= $interval->s . 's';
                }
                break;
            default:
                // Handle default case if needed
                break;
        }
        // If ticket is already closed and no status change, ensure ticket_time is set
        if ($prev_status == 'Close' && $ticket_time == '') {
            $ticketCloseTime = new DateTime($ticket_close);
            $interval = $ticketCloseTime->diff(new DateTime($ticket_open));

            if ($interval->d > 0) {
                $ticket_time .= $interval->d . 'd, ';
            }
            if ($interval->h > 0 || $interval->d > 0) {
                $ticket_time .= $interval->h . 'h, ';
            }
            if ($interval->i > 0 || $interval->h > 0 || $interval->d > 0) {
                $ticket_time .= $interval->i . 'm, ';
            }
            $ticket_time .= $interval->s . 's';
        }
        // Check if ticket status 
        if ($row['status'] == 'Close' && $listTicketAssign != 0) {
            $_SESSION['error_message_ticket'] = "Cannot edit a closed ticket.";
            header("Location: ticket.php");
            exit();
        }
    } else {
        $_SESSION['error_message_ticket'] = "Ticket not found.";
        header("Location: 404.php");
        exit();
    }

    // Check if ticket_id exists in tbl_ticket
    $check_ticket_query = "SELECT ticket_id FROM tbl_ticket WHERE id = '$id'";
    $ticket_result = $conn->query($check_ticket_query);
    if ($ticket_result->num_rows > 0) {

        // Fetch existing ticket details
        $row = $ticket_result->fetch_assoc();
        $existing_ticket_id = $row['ticket_id'];
        // Process existing images
        $uploadDir = "../uploads/$existing_ticket_id/";
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $uploadedFiles = [];
        $deletedFiles = [];

        // Handle deleted images
        if (!empty($_POST['delete_images'])) {
            foreach ($_POST['delete_images'] as $deleted_image) {
                // Validate and delete image file
                if (!empty($deleted_image) && file_exists($deleted_image)) {
                    unlink($deleted_image);

                    // Remove from database record
                    $existing_images_array = !empty($prev_issue_images) ? array_map('trim', explode(', ', $prev_issue_images)) : [];
                    $existing_images_array = array_diff($existing_images_array, [$deleted_image]);
                    $prev_issue_images = implode(', ', $existing_images_array);

                    // Delete from tbl_ticket_images
                    $delete_image_query = "DELETE FROM tbl_ticket_images WHERE image_path = '$deleted_image'";
                    if ($conn->query($delete_image_query) == true) {
                        echo "delete success";
                    } else {
                        echo "Error preparing delete statement: " . $conn->error;
                    }
                }
            }
        }

        // Define allowed file types for images and videos
        $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
        $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];

        // Handle uploaded files (images and videos)
        if (!empty($_FILES['issue_media']['name'][0])) {
            foreach ($_FILES['issue_media']['tmp_name'] as $key => $tmp_name) {
                // Check for upload errors
                if ($_FILES['issue_media']['error'][$key] !== UPLOAD_ERR_OK) {
                    echo "Error uploading file: " . $_FILES['issue_media']['error'][$key];
                    continue;
                }

                $file_name = $_FILES['issue_media']['name'][$key];
                $file_tmp = $tmp_name;
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $unique_name = uniqid() . '.' . $file_extension;
                $uploadPath = $uploadDir . $unique_name;

                // Determine if the file is an image or video
                if (in_array($file_extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                    // Validate image type
                    if (!in_array(exif_imagetype($file_tmp), $allowedImageTypes)) {
                        echo "Invalid image type for file: " . $file_name;
                        continue;
                    }

                    // Move image and insert path into tbl_ticket_images
                    if (move_uploaded_file($file_tmp, $uploadPath)) {
                        $image_insert_query = "INSERT INTO tbl_ticket_images (ticket_id, image_path) VALUES ('$existing_ticket_id', '$uploadPath')";
                        echo $conn->query($image_insert_query) ? "insert image success" : "Error preparing image insert statement: " . $conn->error;
                    } else {
                        echo "Error moving uploaded file: " . $file_name;
                    }
                } elseif (in_array($_FILES['issue_media']['type'][$key], $allowedVideoTypes)) {
                    // Move video and insert path into tbl_ticket_videos
                    if (move_uploaded_file($file_tmp, $uploadPath)) {
                        $video_insert_query = "INSERT INTO tbl_ticket_images (ticket_id, image_path) VALUES ('$existing_ticket_id', '$uploadPath')";
                        echo $conn->query($video_insert_query) ? "insert video success" : "Error preparing video insert statement: " . $conn->error;
                    } else {
                        echo "Error moving uploaded file: " . $file_name;
                    }
                } else {
                    echo "Invalid file type for file: " . $file_name;
                }
            }
        }
    } else {
        // Ticket not found, handle this case (redirect or error message)
        $_SESSION['error_message_ticket'] = "Ticket not found.";
        header("Location: 404.php");
        exit();
    }

    // Update ticket details in the database
    $update_query = "UPDATE tbl_ticket SET 
                        station_id = ?, 
                        station_name = ?, 
                        station_type = ?, 
                        province = ?, 
                        issue_description = ?, 
                        issue_type = ?, 
                        SLA_category = ?, 
                        status = ?, 
                        users_id = ?, 
                        comment = ?, 
                        ticket_on_hold = ?, 
                        ticket_in_progress = ?, 
                        ticket_pending_vendor = ?, 
                        ticket_close = ?,
                        ticket_time =?
                    WHERE id = ?";

    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param(
        'sssssssssssssssi',
        $station_id,
        $station_name,
        $station_type,
        $province,
        $issue_description,
        $issue_types,
        $SLA_category,
        $status,
        $users_id,
        $comment,
        $ticket_on_hold,
        $ticket_in_progress,
        $ticket_pending_vendor,
        $ticket_close,
        $ticket_time,
        $id
    );

    if ($stmt_update->execute()) {
        // Check if significant fields have changed
        if (
            $issue_types !== $prev_issue_type ||
            $SLA_category !== $prev_SLA_category ||
            $users_id !== $prev_users_id ||
            $status !== $prev_status
        ) {

            // Insert a record into tbl_ticket_track
            $modify_time = date('Y-m-d H:i:s');

            $insert_track_query = "INSERT INTO tbl_ticket_track (
                                        ticket_id,
                                        open_time,
                                        modify_time,
                                        modified_by,
                                        issue_type,
                                        SLA_category,
                                        assign,
                                        status
                                    ) VALUES (
                                        ?, ?, ?, ?, ?, ?, ?, ?
                                    )";

            $stmt_track = $conn->prepare($insert_track_query);
            $stmt_track->bind_param(
                'ssssssss',
                $existing_ticket_id,
                $ticket_open,
                $modify_time,
                $user_id,
                $issue_types,
                $SLA_category,
                $users_id,
                $status
            );

            if ($stmt_track->execute()) {
                $_SESSION['success_message_ticket'] = "Ticket updated successfully.";
            } else {
                $_SESSION['error_message_ticket'] = "Error tracking ticket: " . $stmt_track->error;
            }

            $stmt_track->close();
        } else {
            $_SESSION['success_message_ticket'] = "Ticket Updated Successfully.";
        }

        $stmt_update->close();
        header("Location: ticket.php");
        exit();
    } else {
        $_SESSION['error_message_ticket'] = "Error updating ticket: " . $stmt_update->error;
    }

    $stmt_update->close();
    header("Location: ticket.php");
    exit();
}

// Fetch ticket details for display in the form
$ticket_query = "SELECT t.*, GROUP_CONCAT(ti.image_path SEPARATOR ',') AS image_paths 
                 FROM tbl_ticket t
                 LEFT JOIN tbl_ticket_images ti ON t.ticket_id = ti.ticket_id
                 WHERE t.id = $id
                 GROUP BY t.ticket_id";
$ticket_result = $conn->query($ticket_query);
if ($ticket_result->num_rows > 0) {
    $ticket = $ticket_result->fetch_assoc();
    // Extract image paths from the result
    $image_paths = !empty($ticket['image_paths']) ? explode(',', $ticket['image_paths']) : [];
} else {
    $_SESSION['error_message_ticket'] = "Ticket not found.";
    header("Location: 404.php");
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
                        <div class="col-sm-6 row">
                            <div>
                                <a href="ticket.php" class="btn btn-primary mx-2">BACK</a>
                            </div>

                            <h1 class="m-0">Update Ticket</h1>
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

                            <div class="card-header card-primary">
                                <h3 class="card-title">Ticket ID: <?= $ticket['ticket_id']; ?></h3>
                            </div>
                            <form method="POST" id="quickForm" novalidate="novalidate" enctype="multipart/form-data">

                                <div class="card-body col">
                                    <div class="row">
                                        <div class="form-group col-sm-3 ">
                                            <label for="station_id">Station ID <span class="text-danger">*</span></label>
                                            <input value="<?= $ticket['station_id'] ?>" class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)" raedonly>
                                            <div id="suggestion_dropdown" class="dropdown-content"></div>
                                        </div>

                                        <div class="form-group col-sm-3">
                                            <label for="station_name">Station Name</label>
                                            <input value="<?= $ticket['station_name'] ?>" type="text" name="station_name" class="form-control" id="station_name" placeholder="Station Name" readonly>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="station_type">Station Type</label>
                                            <input value="<?= $ticket['station_type'] ?>" type="text" name="station_type" class="form-control" id="station_type" placeholder="Station Type" readonly>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="province">Province</label>
                                            <input value="<?= $ticket['province'] ?>" type="text" name="province" class="form-control" id="province" placeholder="Station Type" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-8">
                                            <label for="issue_description">Issue Description</label>
                                            <textarea id="issue_description" name="issue_description" class="form-control" rows="3" placeholder="Issue Description"><?= ($ticket['issue_description']); ?></textarea>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="issue_image">Issue Image</label>
                                            <input type="file" id="issue_image" name="issue_media[]" class="form-control" multiple accept="image/*,video/*">
                                        </div>
                                        <div class="form-group col-sm-12 row mt-2">
                                            <?php
                                            if (!empty($image_paths)) {
                                                foreach ($image_paths as $image_path) {
                                                    // Check the file extension to determine if it's an image or a video
                                                    $file_extension = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));

                                                    // Determine if the file is an image or video
                                                    if (in_array($file_extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                                                        // Image
                                                        echo '<div class="image-container col-4 col-md-1" style="">';
                                                        echo '<img style="width:100%;" src="' . ($image_path) . '" alt="Issue Image" class="issue-image">';
                                                        echo '<button type="button" class="close-button btn-sm delete-image" data-image="' . ($image_path) . '">&times;</button>';
                                                        echo '</div>';
                                                    } elseif (in_array($file_extension, ['mp4', 'webm', 'ogg'])) {
                                                        // Video
                                                        echo '<div class="image-container col-4 col-md-1" style="">';
                                                        echo '<video style="width:100%;" src="' . ($image_path) . '" controls class="issue-video"></video>';
                                                        echo '<button type="button" class="close-button btn-sm delete-image" data-image="' . ($image_path) . '">&times;</button>';
                                                        echo '</div>';
                                                    }
                                                }
                                            }
                                            ?>

                                            <div class="col-12 row mt-3" id="imagePreview">
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Attach click event to delete buttons
                                            document.querySelectorAll('.delete-image').forEach(item => {
                                                item.addEventListener('click', function() {
                                                    const imageToDelete = this.dataset.image;

                                                    // If you want to visually remove the image immediately
                                                    this.closest('.image-container').remove();

                                                    // If you want to mark the image for deletion on form submit
                                                    const input = document.createElement('input');
                                                    input.type = 'hidden';
                                                    input.name = 'delete_images[]'; // Use an array to collect deleted image paths
                                                    input.value = imageToDelete;
                                                    document.getElementById('quickForm').appendChild(input);
                                                });
                                            });
                                        });
                                    </script>

                                    <div class=" row">
                                        <div class="form-group col-sm-4">
                                            <label for="issue_type">Issue Type</label>
                                            <select name="issue_type[]" id="issue_type" class="form-control" placeholder="-Select-" multiple required>
                                                <?=
                                                $issue_types = ['Hardware', 'Software', 'Network', 'Dispenser', 'ABA', 'FleetCard', 'ATG'];
                                                $selected_issue_types = explode(', ', $ticket['issue_type']);
                                                foreach ($issue_types as $issue_type) {
                                                    $selected = in_array(trim($issue_type), $selected_issue_types) ? 'selected' : '';
                                                    echo "<option value=\"$issue_type\" $selected>$issue_type</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="SLA_category">SLA Category</label>
                                            <!-- <select name="SLA_category" id="SLA_category" class="form-control " style="width: 100%;">

                                            </select> -->
                                            <select name="SLA_category" id="SLA_category" class="form-control" required>
                                                <option value="CAT Hardware" <?= ($ticket['SLA_category'] == 'CAT Hardware') ? 'selected' : ''; ?>>CAT Hardware</option>
                                                <option value="CAT 1" <?= ($ticket['SLA_category'] == 'CAT 1') ? 'selected' : ''; ?>>CAT 1</option>
                                                <option value="CAT 2" <?= ($ticket['SLA_category'] == 'CAT 2') ? 'selected' : ''; ?>>CAT 2</option>
                                                <option value="CAT 3" <?= ($ticket['SLA_category'] == 'CAT 3') ? 'selected' : ''; ?>>CAT 3</option>
                                                <option value="CAT 4" <?= ($ticket['SLA_category'] == 'CAT 4') ? 'selected' : ''; ?>>CAT 4</option>
                                                <option value="CAT 4 Report" <?= ($ticket['SLA_category'] == 'CAT 4 Report') ? 'selected' : ''; ?>>CAT 4 Report</option>
                                                <option value="CAT 5" <?= ($ticket['SLA_category'] == 'CAT 5') ? 'selected' : ''; ?>>CAT 5</option>
                                                <option value="Other" <?= ($ticket['SLA_category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                                            </select>

                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control" style="width: 100%;">
                                                <option value="On Hold" <?= ($ticket['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                                                <option value="In Progress" <?= ($ticket['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                                <option value="Pending Vendor" <?= ($ticket['status'] == 'Pending Vendor') ? 'selected' : ''; ?>>Pending Vendor</option>
                                                <option value="Close" <?= ($ticket['status'] == 'Close') ? 'selected' : ''; ?>>Close</option>
                                            </select>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-4">
                                            <label for="users_id">Assign</label>
                                            <select name="users_id[]" class="form-control" id="users_id" placeholder='-select-' multiple>
                                                <?php
                                                // Check selected issue types
                                                $show_hardware_software_companies = in_array('Hardware', $selected_issue_types) || in_array('Software', $selected_issue_types);
                                                $show_network_companies = in_array('Network', $selected_issue_types);
                                                $show_dispenser_atg_companies = in_array('Dispenser', $selected_issue_types) || in_array('ATG', $selected_issue_types);
                                                $show_aba_companies = in_array('ABA', $selected_issue_types);
                                                $show_fleetcard_companies = in_array('FleetCard', $selected_issue_types);

                                                // Determine the company condition
                                                if ($show_hardware_software_companies) {
                                                    $company_condition = "AND company IN ('PTTCL', 'PTT Digital Thailand', 'PTT Digital Cambodia')";
                                                } elseif ($show_network_companies) {
                                                    $company_condition = "AND company = 'PTTCL'";
                                                } elseif ($show_dispenser_atg_companies) {
                                                    $company_condition = "AND company IN ('PTTCL', 'MBA', 'SD', 'CamSys', 'DIN')";
                                                } elseif ($show_aba_companies) {
                                                    $company_condition = "AND company IN ('PTTCL', 'ABA Bank')";
                                                } elseif ($show_fleetcard_companies) {
                                                    $company_condition = "AND company IN ('PTTCL', 'Wing Bank')";
                                                } else {
                                                    $company_condition = '';
                                                }

                                                // Fetch users based on the condition
                                                $user_query = "SELECT users_id, users_name FROM tbl_users WHERE status = 1 $company_condition";
                                                $user_result = $conn->query($user_query);
                                                $assigned_users = explode(',', $ticket['users_id']);
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
                                            <textarea name="comment" id="comment" class="form-control" rows="3" placeholder="Comment"><?= htmlspecialchars($ticket['comment']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-3">
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
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- select multiple -->
    <script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var issueTypeChoices = new Choices('#issue_type', {
                removeItemButton: true,
                maxItemCount: 100,
                searchResultLimit: 100,
                renderChoiceLimit: 100
            });

            var usersIdChoices = new Choices('#users_id', {
                removeItemButton: true,
                maxItemCount: 100,
                searchResultLimit: 100,
                renderChoiceLimit: 100
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

    <script>
        $(document).ready(function() {
            const $stationId = $('#station_id');
            const $quickForm = $('#quickForm');

            $stationId.on('blur', function() {
                fetchStationDetails($(this).val());
            });

            $quickForm.on('submit', function(event) {
                if (!this.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                $(this).addClass('was-validated');
            });
        });

        function fetchStationDetails(station_id) {
            $.post('get_station_details.php', {
                station_id
            }, function(response) {
                const {
                    success,
                    station_name = '',
                    station_type = '',
                    province = ''
                } = response;
                $('#station_name').val(station_name);
                $('#station_type').val(station_type);
                $('#province').val(province);
            }, 'json');
        }

        function showSuggestions(str) {
            if (str === "") {
                $("#suggestion_dropdown").empty();
                return;
            }
            $.get("get_suggestions.php", {
                q: str
            }, function(response) {
                $("#suggestion_dropdown").html(response);
            });
        }

        function selectSuggestion(station_id) {
            $("#station_id").val(station_id).blur();
            $("#suggestion_dropdown").empty();
        }
    </script>
    <!-- preview media -->
    <script src="../scripts/previewImages.js"></script>
    <!-- assign by issue type -->

    <!-- condition sla category -->
    <script>
        // Define the available SLA options
        const slaOptions = {
            'Hardware': [{
                value: 'CAT Hardware',
                text: 'CAT Hardware'
            }],
            'Software': [{
                    value: 'CAT 1',
                    text: 'CAT 1'
                },
                {
                    value: 'CAT 2',
                    text: 'CAT 2'
                },
                {
                    value: 'CAT 3',
                    text: 'CAT 3'
                },

                {
                    value: 'CAT 4',
                    text: 'CAT 4'
                },
                {
                    value: 'CAT 4 Report',
                    text: 'CAT 4 Report'
                },
                {
                    value: 'CAT 5',
                    text: 'CAT 5'
                }
            ],
            'Other': [{
                value: 'Other',
                text: 'Other'
            }]
        };

        // Reference to the issue type and SLA category select elements
        const issueTypeSelect = document.getElementById('issue_type');
        const slaCategorySelect = document.getElementById('SLA_category');

        // Preselected category from PHP
        const preSelectedCategory = '<?= $ticket['SLA_category']; ?>';

        // Function to add an option to the SLA category select element
        function addOption(value, text) {
            const opt = document.createElement('option');
            opt.value = value;
            opt.textContent = text;
            if (value === preSelectedCategory) {
                opt.selected = true;
            }
            slaCategorySelect.appendChild(opt);
        }

        // Event listener for when the issue type selection changes
        issueTypeSelect.addEventListener('change', function() {
            const selectedValues = Array.from(issueTypeSelect.selectedOptions).map(option => option.value);

            // Clear current options in SLA Category dropdown
            slaCategorySelect.innerHTML = '';

            if (selectedValues.includes('Software') && !selectedValues.includes('Hardware') && selectedValues.length === 1) {
                // If only Software is selected
                slaOptions['Software'].forEach(option => addOption(option.value, option.text));
            } else if (selectedValues.includes('Hardware') && !selectedValues.includes('Software') && selectedValues.length === 1) {
                // If only Hardware is selected
                slaOptions['Hardware'].forEach(option => addOption(option.value, option.text));
            } else if (selectedValues.includes('Software') && selectedValues.includes('Hardware') && selectedValues.length === 2) {
                // If both Software and Hardware are selected
                slaOptions['Hardware'].concat(slaOptions['Software']).forEach(option => addOption(option.value, option.text));
            } else if (selectedValues.includes('Software') && selectedValues.includes('Hardware') && selectedValues.length > 2) {
                // If Software, Hardware, and other options are selected
                slaOptions['Hardware'].concat(slaOptions['Software']).concat(slaOptions['Other']).forEach(option => addOption(option.value, option.text));
            } else if (selectedValues.includes('Software') && !selectedValues.includes('Hardware')) {
                // If Software is selected along with any other option that is not Hardware
                slaOptions['Software'].concat(slaOptions['Other']).forEach(option => addOption(option.value, option.text));
            } else if (selectedValues.includes('Hardware') && !selectedValues.includes('Software')) {
                // If Hardware is selected along with any other option that is not Software
                slaOptions['Hardware'].concat(slaOptions['Other']).forEach(option => addOption(option.value, option.text));
            } else {
                // If neither Software nor Hardware is selected, or other combinations
                slaOptions['Other'].forEach(option => addOption(option.value, option.text));
            }
        });

        // Trigger the change event to set the options based on pre-selected values
        issueTypeSelect.dispatchEvent(new Event('change'));
    </script>

</body>

</html>