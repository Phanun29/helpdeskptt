<?php
include "config.php"; // Include your database connection configuration
include "../inc/header.php";

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // Example user ID

$query_user = "
    SELECT u.*, r.list_ticket_status, r.add_ticket_status, r.edit_ticket_status, r.delete_ticket_status, r.list_ticket_assign
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = ?";

$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param('i', $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $listTicketAssign = $user['list_ticket_assign'];

    if (!$user['list_ticket_status'] || !$user['edit_ticket_status']) {
        header("Location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
    header("Location: 404.php");
    exit();
}

// Initialize session for storing messages
$ticket_id = $_GET['id'] ?? null; // Assuming you're passing the ticket ID through a GET parameter

if (!is_numeric($ticket_id)) {
    header("Location: 404.php");
    exit();
}
// Check if form is submitted
date_default_timezone_set('Asia/Bangkok');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Gather form data
    $ticket_id = $_GET['id'] ?? null; // Ensure to sanitize and validate $ticket_id
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
    $check_ticket_query = "SELECT status, ticket_open, ticket_on_hold, ticket_in_progress, ticket_pending_vendor, ticket_close, issue_image 
    FROM tbl_ticket 
    WHERE id = ?";
    $stmt_check_ticket = $conn->prepare($check_ticket_query);
    $stmt_check_ticket->bind_param('i', $ticket_id);
    $stmt_check_ticket->execute();
    $ticket_result = $stmt_check_ticket->get_result();

    if ($ticket_result->num_rows > 0) {
        $row = $ticket_result->fetch_assoc();

        // Record previous status and timestamps
        $prev_status = $row['status'];
        $prev_open = $row['ticket_open'];
        $prev_on_hold = $row['ticket_on_hold'];
        $prev_in_progress = $row['ticket_in_progress'];
        $prev_pending_vendor = $row['ticket_pending_vendor'];
        $prev_close = $row['ticket_close'];
        $prev_issue_images = $row['issue_image'];

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
                if ($prev_status == 'On Hold') {
                    $ticket_in_progress = date('Y-m-d H:i:s');
                }
                break;
            case 'Pending Vendor':
                if ($prev_status == 'In Progress') {
                    $ticket_pending_vendor = date('Y-m-d H:i:s');
                }
                break;
            case 'Close':
                if ($prev_status == 'Pending Vendor') {
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

        // Check if ticket status is 'Close'
        if ($row['status'] == 'Close' && $listTicketAssign != 0) {
            $_SESSION['error_message'] = "Cannot edit a closed ticket.";
            header("Location: ticket.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Ticket not found.";
        header("Location: 404.php");
        exit();
    }

    // Process existing images
    $uploadDir = '../uploads/';
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
                $delete_image_query = "DELETE FROM tbl_ticket_images WHERE image_path = ?";
                $stmt_delete_image = $conn->prepare($delete_image_query);
                if ($stmt_delete_image) {
                    $stmt_delete_image->bind_param("s", $deleted_image);
                    if (!$stmt_delete_image->execute()) {
                        echo "Error deleting image record: " . $stmt_delete_image->error;
                    }
                    $stmt_delete_image->close();
                } else {
                    echo "Error preparing delete statement: " . $conn->error;
                }
            }
        }
    }





    // Define allowed file types for images and videos
    $allowedImageTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF];
    $allowedVideoTypes = ['video/mp4', 'video/webm', 'video/ogg'];

    // Check if ticket_id exists in tbl_ticket
    $check_ticket_query = "SELECT ticket_id FROM tbl_ticket WHERE id = ?";
    $stmt_check_ticket = $conn->prepare($check_ticket_query);
    $stmt_check_ticket->bind_param('i', $ticket_id);
    $stmt_check_ticket->execute();
    $ticket_result = $stmt_check_ticket->get_result();

    if ($ticket_result->num_rows > 0) {
        // Existing ticket found, proceed with updating and image handling

        // Fetch existing ticket details
        $row = $ticket_result->fetch_assoc();
        $existing_ticket_id = $row['ticket_id'];

        // Handle uploaded files (images and videos)
        if (!empty($_FILES['issue_media']['name'][0])) {
            foreach ($_FILES['issue_media']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['issue_media']['error'][$key] !== UPLOAD_ERR_OK) {
                    echo "Error uploading file: " . $_FILES['issue_media']['error'][$key];
                    continue; // Skip to the next iteration if there's an error
                }

                $file_name = $_FILES['issue_media']['name'][$key];
                $file_tmp = $_FILES['issue_media']['tmp_name'][$key];
                $file_type = $_FILES['issue_media']['type'][$key];
                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Determine if the file is an image or video
                if (in_array($file_extension, ['jpeg', 'jpg', 'png', 'gif'])) {
                    // Handle images
                    $imageType = exif_imagetype($file_tmp);
                    if (!in_array($imageType, $allowedImageTypes)) {
                        echo "Invalid image type for file: " . $file_name;
                        continue; // Skip to the next iteration if the file type is not allowed
                    }
                    $unique_name = uniqid() . '.' . $file_extension;
                    $uploadPath = $uploadDir . $unique_name;

                    // Move the uploaded file to the destination directory
                    if (move_uploaded_file($file_tmp, $uploadPath)) {
                        // Insert image path into tbl_ticket_images
                        $image_insert_query = "INSERT INTO tbl_ticket_images (ticket_id, image_path) VALUES (?, ?)";
                        $stmt_image = $conn->prepare($image_insert_query);
                        if ($stmt_image) {
                            $stmt_image->bind_param("ss", $existing_ticket_id, $uploadPath);
                            if ($stmt_image->execute()) {
                                // Success message or further processing
                            } else {
                                echo "Error inserting image path: " . $stmt_image->error;
                            }
                            $stmt_image->close();
                        } else {
                            echo "Error preparing image insert statement: " . $conn->error;
                        }
                    } else {
                        echo "Error moving uploaded file: " . $file_name;
                    }
                } elseif (in_array($file_type, $allowedVideoTypes)) {
                    // Handle videos
                    $unique_name = uniqid() . '.' . $file_extension;
                    $uploadPath = $uploadDir . $unique_name;

                    // Move the uploaded file to the destination directory
                    if (move_uploaded_file($file_tmp, $uploadPath)) {
                        // Insert video path into tbl_ticket_videos
                        $video_insert_query = "INSERT INTO tbl_ticket_images (ticket_id, image_path) VALUES (?, ?)";
                        $stmt_video = $conn->prepare($video_insert_query);
                        if ($stmt_video) {
                            $stmt_video->bind_param("ss", $existing_ticket_id, $uploadPath);
                            if ($stmt_video->execute()) {
                                // Success message or further processing
                            } else {
                                echo "Error inserting video path: " . $stmt_video->error;
                            }
                            $stmt_video->close();
                        } else {
                            echo "Error preparing video insert statement: " . $conn->error;
                        }
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
        $_SESSION['error_message'] = "Ticket not found.";
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
        $ticket_id
    );

    if ($stmt_update->execute()) {
        $_SESSION['success_message'] = "Ticket updated successfully";
    } else {
        $_SESSION['error_message'] = "Error updating ticket: " . $stmt_update->error;
    }

    $stmt_update->close();
    //  header("Location: edit_ticket.php?id=$ticket_id");
    header("Location: ticket.php");
    exit();
}


// Fetch ticket details for display in the form
$ticket_id = $_GET['id'] ?? null; // Ensure to sanitize and validate $ticket_id
$ticket_query = "SELECT t.*, GROUP_CONCAT(ti.image_path SEPARATOR ',') AS image_paths 
                 FROM tbl_ticket t
                 LEFT JOIN tbl_ticket_images ti ON t.ticket_id = ti.ticket_id
                 WHERE t.id = ?
                 GROUP BY t.ticket_id";

$stmt_ticket = $conn->prepare($ticket_query);
$stmt_ticket->bind_param('s', $ticket_id);
$stmt_ticket->execute();
$ticket_result = $stmt_ticket->get_result();

if ($ticket_result->num_rows > 0) {
    $row = $ticket_result->fetch_assoc();
    // Extract image paths from the result
    $image_paths = !empty($row['image_paths']) ? explode(',', $row['image_paths']) : [];
} else {
    $_SESSION['error_message'] = "Ticket not found.";
    header("Location: 404.php");
    exit();
}


$stmt_ticket->close();
// $conn->close();
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
                        <div class="card-body p-0 card-primary">

                            <div class="card-header card-primary">
                                <h3 class="card-title">Ticket ID: <?= $row['ticket_id']; ?></h3>
                            </div>


                            <form method="POST" id="quickForm" novalidate="novalidate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $ticket_id; ?>" enctype="multipart/form-data">
                                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirect); ?>">
                                <div class="card-body col">
                                    <div class="row">
                                        <div class="form-group col-sm-3 ">
                                            <label for="station_id">Station ID <span class="text-danger">*</span></label>
                                            <input value="<?php echo $row['station_id'] ?>" class="form-control" type="text" name="station_id" id="station_id" autocomplete="off" onkeyup="showSuggestions(this.value)" raedonly>
                                            <div id="suggestion_dropdown" class="dropdown-content"></div>
                                        </div>

                                        <div class="form-group col-sm-3">
                                            <label for="station_name">Station Name</label>
                                            <input value="<?php echo $row['station_name'] ?>" type="text" name="station_name" class="form-control" id="station_name" placeholder="Station Name" readonly>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="station_type">Station Type</label>
                                            <input value="<?php echo $row['station_type'] ?>" type="text" name="station_type" class="form-control" id="station_type" placeholder="Station Type" readonly>
                                        </div>
                                        <div class="form-group col-sm-3">
                                            <label for="province">Province</label>
                                            <input value="<?php echo $row['province'] ?>" type="text" name="province" class="form-control" id="province" placeholder="Station Type" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-sm-8">
                                            <label for="issue_description">Issue Description</label>
                                            <textarea id="issue_description" name="issue_description" class="form-control" rows="3" placeholder="Issue Description"><?php echo htmlspecialchars($row['issue_description']); ?></textarea>
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
                                                        echo '<div class="image-container col-4 col-md-2" style="">';
                                                        echo '<img style="width:100%;" src="' . htmlspecialchars($image_path) . '" alt="Issue Image" class="issue-image">';
                                                        echo '<button type="button" class="close-button btn-sm delete-image" data-image="' . htmlspecialchars($image_path) . '">&times;</button>';
                                                        echo '</div>';
                                                    } elseif (in_array($file_extension, ['mp4', 'webm', 'ogg'])) {
                                                        // Video
                                                        echo '<div class="image-container col-4 col-md-2" style="">';
                                                        echo '<video style="width:100%;" src="' . htmlspecialchars($image_path) . '" controls class="issue-video"></video>';
                                                        echo '<button type="button" class="close-button btn-sm delete-image" data-image="' . htmlspecialchars($image_path) . '">&times;</button>';
                                                        echo '</div>';
                                                    }
                                                }
                                            }
                                            ?>


                                            <div class="col-12
                                             row mt-3" id="imagePreview">
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
                                            <select name="issue_type[]" class="form-control" id="issue_type" placeholder="-Select-" multiple>
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
                                            <label for="SLA_category">SLA Category</label>
                                            <select name="SLA_category" id="SLA_category" class="form-control select2bs4" style="width: 100%;">
                                                <option value="CAT Hardware" <?php echo ($row['SLA_category'] == 'CAT Hardware') ? 'selected' : ''; ?>>CAT Hardware</option>
                                                <option value="CAT 1*" <?php echo ($row['SLA_category'] == 'CAT 1*') ? 'selected' : ''; ?>>CAT 1*</option>
                                                <option value="CAT 2*" <?php echo ($row['SLA_category'] == 'CAT 2*') ? 'selected' : ''; ?>>CAT 2*</option>
                                                <option value="CAT 3*" <?php echo ($row['SLA_category'] == 'CAT 3*') ? 'selected' : ''; ?>>CAT 3*</option>
                                                <option value="CAT 4*" <?php echo ($row['SLA_category'] == 'CAT 4*') ? 'selected' : ''; ?>>CAT 4*</option>
                                                <option value="CAT 4 Report*" <?php echo ($row['SLA_category'] == 'CAT 4 Report*') ? 'selected' : ''; ?>>CAT 4 Report*</option>
                                                <option value="CAT 5*" <?php echo ($row['SLA_category'] == 'CAT 5*') ? 'selected' : ''; ?>>CAT 5*</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-sm-4">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control" style="width: 100%;">

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
    <!-- delete image -->
    <!-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-image').forEach(button => {
                button.addEventListener('click', function() {
                    const image = this.getAttribute('data-image');
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'delete_image.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            button.parentElement.remove(); // Remove the image container
                        }
                    };
                    xhr.send('image=' + encodeURIComponent(image) + '&ticket_id=<?php echo $ticket_id; ?>');
                });
            });
        });

        // Display newly added images immediately
        document.querySelector('input[type="file"]').addEventListener('change', function(event) {
            const files = event.target.files;
            const imageContainer = document.querySelector('.form-group img');
            if (files.length > 0) {
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const newImage = document.createElement('img');
                        newImage.src = e.target.result;
                        newImage.className = 'issue-image';
                        imageContainer.appendChild(newImage);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    </script> -->
    <!-- preview image -->
    <script src="../scripts/previewImages.js">

    </script>
    <style>
    </style>




</body>

</html>