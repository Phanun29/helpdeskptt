<?php
include "../inc/header_script.php";

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // User ID
$user_create_ticket = $fetch_info['users_id'];

// Ensure to sanitize $user_id to avoid SQL injection
$user_id_sanitized = $conn->real_escape_string($user_id);

// Fetch user details and permissions
$query_user = "
    SELECT u.*, r.list_ticket_status, r.add_ticket_status, r.edit_ticket_status, r.delete_ticket_status, r.list_ticket_assign
    FROM tbl_users u
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id
    WHERE u.users_id = '$user_id_sanitized'";
$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $listTicket = $user['list_ticket_status'];
    $AddTicket = $user['add_ticket_status'];
    $EditTicket = $user['edit_ticket_status'];
    $DeleteTicket = $user['delete_ticket_status'];
    $listTicketAssign = $user['list_ticket_assign'];

    // Build the ticket query based on permissions
    if ($listTicketAssign == 0) {
        $ticket_query = "
            SELECT t.*, 
                   REPLACE(GROUP_CONCAT(DISTINCT u.users_name SEPARATOR ','), ',',',') AS users_name,
                   GROUP_CONCAT(DISTINCT ti.image_path SEPARATOR ',') AS image_paths
            FROM tbl_ticket t
            LEFT JOIN tbl_users u ON FIND_IN_SET(u.users_id, t.users_id)
            LEFT JOIN tbl_ticket_images ti ON t.ticket_id = ti.ticket_id
            GROUP BY t.ticket_id
            ORDER BY t.ticket_id DESC";
    } else {
        $user_create_ticket_sanitized = $conn->real_escape_string($user_create_ticket);
        $ticket_query = "
            SELECT t.*, 
                   REPLACE(GROUP_CONCAT(DISTINCT u.users_name SEPARATOR ','),',',',') AS users_name,
                   GROUP_CONCAT(DISTINCT ti.image_path SEPARATOR ',') AS image_paths
            FROM tbl_ticket t
            LEFT JOIN tbl_users u ON FIND_IN_SET(u.users_id, t.users_id) OR FIND_IN_SET(u.users_id, t.user_create_ticket)
            LEFT JOIN tbl_ticket_images ti ON t.ticket_id = ti.ticket_id
            WHERE FIND_IN_SET('$user_id_sanitized', t.users_id) OR FIND_IN_SET('$user_create_ticket_sanitized', t.user_create_ticket)
            GROUP BY t.ticket_id
            ORDER BY t.ticket_id DESC";
    }

    // Execute the ticket query
    $ticket_result = $conn->query($ticket_query);

    // Check if user has permission to view tickets
    if (!$listTicket) {
        header("location: 404.php");
        exit();
    }
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
    header("location: 404.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <?php include "../inc/head.php"; ?>
    <style>
        @font-face {
            font-family: 'Khmer OS Battambang';
            src: url('KhmerOSbattambang.ttf') format('truetype');
        }

        /* <!-- style for show filter form --> */

        #filterForm1 {
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease-in-out;
        }

        #filterForm1.show {
            display: block;
            opacity: 1;
        }
    </style>
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
                            <h1 class="m-0">Tickets</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php
                                if (isset($_SESSION['success_message_ticket'])) {
                                    echo "<div class='alert alert-success alert-dismissible fade show mb-0' role='alert'>
                                    <strong>{$_SESSION['success_message_ticket']}</strong>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>";
                                    unset($_SESSION['success_message_ticket']); // Clear the message after displaying
                                }

                                if (isset($_SESSION['error_message_ticket'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show mb-0' role='alert'>
                                    <strong>{$_SESSION['error_message_ticket']}</strong>
                                    <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                        <span aria-hidden='true'>&times;</span>
                                    </button>
                                </div>";
                                    unset($_SESSION['error_message_ticket']); // Clear the message after displaying
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
                        <div class="card-body p-0" style="overflow: hidden;">

                            <div class="card-header">
                                <?php if (isset($AddTicket) && $AddTicket) : ?>
                                    <a id="add_ticket" href="add_ticket.php" class="btn btn-primary ">Add Ticket</a>
                                <?php endif; ?>
                                <button type="button" class="btn btn-secondary button-filter" id="toggleFilterBtn">Filter</button>
                                <!-- script dropdown filter -->
                                <script>
                                    document.getElementById('toggleFilterBtn').addEventListener('click', function() {
                                        var filterForm = document.getElementById('filterForm1');
                                        if (filterForm.classList.contains('show')) {
                                            filterForm.classList.remove('show');
                                            setTimeout(function() {
                                                filterForm.style.display = 'none';
                                            }, 500); // Match the transition duration
                                        } else {
                                            filterForm.style.display = 'block';
                                            setTimeout(function() {
                                                filterForm.classList.add('show');
                                            }, 10); // Slight delay to trigger transition
                                        }
                                    });
                                </script>
                                <div id="button_export" class="dt-buttons btn-group flex-wrap">
                                    <button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="tbl_ticket" onclick="exportToCSV()" type="button"><span>CSV</span></button>
                                    <button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0" aria-controls="tbl_ticket" onclick="exportToExcel()" type="button"><span>Excel</span></button>
                                    <button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0" aria-controls="tbl_ticket" onclick="exportToPDF()" type="button"><span>PDF</span></button>
                                </div>

                            </div>

                            <div class="card-header" id="filterForm1" style="display: none;">
                                <form id="filterForm" class="row">
                                    <div class="form-group col-6 col-md-3">
                                        <label for="station_id">Station ID</label>
                                        <input class="form-control" type="text" name="station_id" id="station_id" placeholder="All" autocomplete="off" onkeyup="showSuggestions(this.value)">
                                        <div id="suggestion_dropdown" class="dropdown-content"></div>
                                    </div>
                                    <div class="form-group col-6 col-md-3">
                                        <label for="province">Province</label>
                                        <select id="province" name="province" class="form-control" style="width: 100%;">
                                            <option value="">All</option>
                                            <option value="Phnom Penh">Phnom Penh</option>
                                            <option value="Siem Reap">Siem Reap </option>
                                            <option value="Banteay Meanchey">Banteay Meanchey </option>
                                            <option value="Kampong Speu">Kampong Speu </option>
                                            <option value="Kampong Thom">Kampong Thom </option>
                                            <option value="Prey Veng">Prey Veng </option>
                                            <option value="Kampot">Kampot </option>
                                            <option value="Battambang">Battambang </option>
                                            <option value="Preah Sihanouk">Preah Sihanouk </option>
                                            <option value="Svay Rieng">Svay Rieng </option>
                                            <option value="Kandal">Kandal </option>
                                            <option value="Kampong Chhnang">Kampong Chhnang </option>
                                            <option value="Tboung Khmum">Tboung Khmum </option>
                                            <option value="Kep">Kep </option>
                                            <option value="Pursat">Pursat </option>
                                            <option value="Koh Kong">Koh Kong </option>
                                            <option value="Kratie">Kratie </option>
                                            <option value="Preah Vihear">Preah Vihear </option>
                                            <option value="Mondul Kiri">Mondul Kiri </option>
                                            <option value="Kampong Cham">Kampong Cham </option>
                                            <option value="Pailin">Pailin </option>
                                            <option value="Stung Treng">Stung Treng </option>
                                            <option value="Oddar Meanchey">Oddar Meanchey </option>
                                            <option value="Ratanak Kiri">Ratanak Kiri </option>
                                            <option value="Takeo">Takeo </option>

                                        </select>
                                    </div>
                                    <div class="form-group col-6 col-md-3">
                                        <label for="issue_type">Issue Type</label>
                                        <select name="issue_type" id="issue_type" class="form-control" placeholder="-Select-">
                                            <option value="">All</option>
                                            <option value="Hardware">Hardware</option>
                                            <option value="Software">Software</option>
                                            <option value="Network">Network</option>
                                            <option value="Dispenser">Dispenser</option>
                                            <option value="ABA">ABA</option>
                                            <option value="FleetCard">FleetCard</option>
                                            <option value="ATG">ATG</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6 col-md-3">
                                        <label for="SLA_category">SLA Catego</label>
                                        <select name="SLA_category" id="SLA_category" class="form-control">
                                            <option value="">All</option>
                                            <option value="CAT Hardware">CAT Hardware</option>
                                            <option value="CAT 1">CAT 1</option>
                                            <option value="CAT 2">CAT 2</option>
                                            <option value="CAT 3">CAT 3</option>
                                            <option value="CAT 4">CAT 4</option>
                                            <option value="CAT 4 Report">CAT 4 Report</option>
                                            <option value="CAT 5">CAT 5</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6 col-md-3">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control" style="width: 100%;">
                                            <option value="">All</option>
                                            <option value="Open">Open</option>
                                            <option value="On Hold">On Hold</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Pending Vendor">Pending Vendor</option>
                                            <option value="Close">Close</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-6 col-md-3">
                                        <label for="users_id">Assign</label>
                                        <select name="users_id" id="users_id" class="form-control">
                                            <option value="">All</option>
                                            <?php
                                            $user_query1 = "SELECT users_id, users_name FROM tbl_users WHERE status = 1";
                                            $user_result1 = $conn->query($user_query1);
                                            $users = [];
                                            if ($user_result1 && $user_result1->num_rows > 0) {
                                                while ($row1 = $user_result1->fetch_assoc()) {
                                                    $users[] = $row1;
                                                }
                                            }
                                            if (!empty($users)) {
                                                for ($i = 0; $i < count($users); $i++) {
                                                    echo "<option value='" . $users[$i]['users_id'] . "'>" . $users[$i]['users_name'] . "</option>";
                                                }
                                            } else {
                                                echo "<option value=''>No users found with status 1</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-6 col-md-3">
                                        <label for="ticket_open_from">Ticket Open From</label>
                                        <input type="date" name="ticket_open_from" id="ticket_open_from" class="form-control">
                                    </div>
                                    <div class="form-group col-6 col-md-3">
                                        <label for="ticket_open_to">Ticket Open To</label>
                                        <input type="date" name="ticket_open_to" id="ticket_open_to" class="form-control">
                                    </div>
                                    <div class="form-group col-6 col-md-3">
                                        <label for="ticket_close_from">Ticket Close From</label>
                                        <input type="date" name="ticket_close_from" id="ticket_close_from" class="form-control">
                                    </div>
                                    <div class="form-group col-6 col-md-3">
                                        <label for="ticket_close_to">Ticket Close To</label>
                                        <input type="date" name="ticket_close_to" id="ticket_close_to" class="form-control" placeholder="select">
                                    </div>
                                    <div class="form-group col-md-12 mt-2">
                                        <button type="button" class="btn btn-primary">Filter <i class="fa-solid fa-filter"></i></button>
                                        <button type="reset" class="btn btn-danger" id="filterResetBtn">Clear</button>
                                    </div>
                                </form>
                            </div>

                            <br>
                            <table id="tableTicket" class="table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <?php if ($EditTicket == 0 & $DeleteTicket == 0) {

                                            echo "<th style='display:none;'></th>";
                                        } else {
                                            echo " <th class='export-ignore'>Action</th>";
                                        } ?>
                                        <th>Ticket ID</th>
                                        <th>Station ID</th>
                                        <th>Station Name</th>
                                        <th>Station Type</th>
                                        <th>Province</th>
                                        <th>Issue Description</th>
                                        <th class="export-ignore">Issue Image</th>
                                        <th>Issue Type</th>
                                        <th>SLA Category</th>
                                        <th>Status</th>
                                        <th>Assign</th>
                                        <th>Ticket Open</th>
                                        <th>Ticket on Hold</th>
                                        <th>Ticket In Progress</th>
                                        <th>Ticket Pending Vender</th>
                                        <th>Ticket Close</th>
                                        <th>Ticket Time</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                <tbody id="ticketTableBody">
                                    <?php
                                    $i = 1;
                                    if ($ticket_result->num_rows > 0) {
                                        while ($row = $ticket_result->fetch_assoc()) {
                                            echo "<tr id='ticket-" . $row['id'] . "'>";
                                            echo "<td class='py-2'>" . $i++ . "</td>";
                                            if ($EditTicket == 0 & $DeleteTicket == 0) {
                                                echo "<td style='display:none;'></td>";
                                            } else {
                                                echo "<td class='export-ignore py-1'>";

                                                // Your original ID
                                                $original_id = $row['id'];

                                                // Hash the ID to make it unique and consistent
                                                $hashed_id = hash('sha256', $original_id);

                                                // Encode the hash and take the first 10 characters
                                                $encoded_id = substr(base64_encode($hashed_id), 0, 20);
                                                if ($row['status'] != "Close") {
                                                    if ($EditTicket) {
                                                        echo "<a href='edit_ticket.php?id={$encoded_id}' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                    }
                                                    if ($DeleteTicket) {
                                                        echo "<button data-id='" . $row['id'] . "' class='btn btn-danger delete-btn'><i class='fa-solid fa-trash'></i></button>";
                                                    }
                                                } else if ($listTicketAssign == 0) {
                                                    echo "<a href='edit_ticket.php?id={$encoded_id}' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";
                                                }
                                                echo "</td>";
                                            }
                                            echo "<td class='py-1'><button class='btn btn-link' onclick='showTicketDetails(" . json_encode($row) . ")'>" . $row['ticket_id'] . "</button></td>";
                                            echo "<td class='py-1'>" . $row['station_id'] . "</td>";
                                            echo "<td class='py-1'>" . $row['station_name'] . "</td>";
                                            echo "<td class='py-1'>" . $row['station_type'] . "</td>";
                                            echo "<td class='py-1'>" . $row['province'] . "</td>";
                                            echo "<td class='py-1' style='font-family: Khmer, sans-serif;'><p>" . $row['issue_description'] . "</p></td>";
                                            $image_paths = explode(',', $row['image_paths']);
                                            if ($row['image_paths'] != null) {
                                                echo "<td class='export-ignore py-1'><button class='btn btn-link' onclick='showMedia(\"" . $row['image_paths'] . "\")'>Click to View</button></td>";
                                            } else {
                                                echo "<td class='export-ignore text-center text-warning'>none</td>";
                                            }
                                            echo "<td class='py-1'>" . $row['issue_type'] . "</td>";
                                            echo "<td class='py-1'>" . $row['SLA_category'] . "</td>";
                                            echo "<td class='py-1'>" . $row['status'] . "</td>";
                                            echo "<td class='py-1'>" . $row['users_name'] . "</td>";
                                            echo "<td class='py-1'>" . $row['ticket_open'] . "</td>";
                                            echo "<td class='py-1'>" . $row['ticket_on_hold'] . "</td>";
                                            echo "<td class='py-1'>" . $row['ticket_in_progress'] . "</td>";
                                            echo "<td class='py-1'>" . $row['ticket_pending_vendor'] . "</td>";
                                            echo "<td class='py-1'>" . $row['ticket_close'] . "</td>";
                                            if ($row['ticket_time'] != null) {
                                                echo "<td class='py-1'>" . $row['ticket_time'] . "</td>";
                                            } else {
                                                date_default_timezone_set('Asia/Bangkok');
                                                $ticketOpenTime = new DateTime($row['ticket_open']);
                                                $ticketCloseTime = new DateTime();
                                                // Calculate the difference
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
                                                $ticket_time .= $interval->s . 's ago';

                                                // Output the formatted time difference
                                                echo "<td class='py-1'>" . htmlspecialchars($ticket_time) . "</td>";
                                            }

                                            echo "<td class='py-1' style='font-family: Khmer, sans-serif; font-weight: 400; font-style: normal;'>" . $row['comment'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <!-- Ticket Details Modal -->
                            <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header py-1">
                                            <h5 class="modal-title" id="ticketModalLabel">Ticket Details</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body py-0">
                                            <table class="table p-0 modal-table" style='font-family: "Khmer", sans-serif;font-weight: 400;font-style: normal;'>
                                                <tr>
                                                    <td class="p-1">Ticket ID:</td>
                                                    <td class="p-1"><span id="modalTicketId" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Station ID:</td>
                                                    <td class="p-1"><span id="modalStationId" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Station Name:</td>
                                                    <td class="p-1"><span id="modalStationName" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Station Type:</td>
                                                    <td class="p-1"><span id="modalStationType" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Province:</td>
                                                    <td class="p-1"><span id="modalProvince" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Description:</td>
                                                    <td class="p-1" style="text-align: justify;"><span id="modalDescription" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Issue Type:</td>
                                                    <td class="p-1"><span id="modalIssueType" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">SLA_category:</td>
                                                    <td class="p-1"><span id="modalSLA_category" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Status:</td>
                                                    <td class="p-1"> <span id="modalStatus" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Assign:</td>
                                                    <td class="p-1"> <span id="modalAssign" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Open:</td>
                                                    <td class="p-1"> <span id="modalTicketOpen" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket On Hold:</td>
                                                    <td class="p-1"> <span id="modalTicketOnHold" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket In Progress:</td>
                                                    <td class="p-1"> <span id="modalTicketInProgress" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Pending Vendor:</td>
                                                    <td class="p-1"> <span id="modalTicketPendingVendor" class="word-wrap"></span></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Close:</td>

                                                    <td class="p-1"> <span id="modalTicketClose" class="word-wrap"></span></td>

                                                </tr>
                                                <tr>
                                                    <td class="p-1">Ticket Time:</td>

                                                    <td class="p-1"> <span id="modalTicketTime" class="word-wrap"></span></td>

                                                </tr>
                                                <tr>
                                                    <td class="p-1">Comment:</td>
                                                    <td class="p-1" style="text-align: justify;"> <span id="modalComment" class="word-wrap"></span></td>
                                                </tr>

                                            </table>
                                            <p class="p-0">Issue Media</p>
                                            <div id="modalIssueMedia" class="d-flex flex-wrap" onclick="showMedia(this.src)"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /model ticket details -->
                            <!-- Modal for displaying images -->
                            <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="imageModalLabel">Image Viewer</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="text-center" style="display: flex;justify-content:center;">
                                                <img id="modalImage" class="img-fluid" style="width: 600px; display: none;">
                                                <video id="modalVideo" class="img-fluid" style="width: 600px; display: none;" controls></video>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <span id="imageIndex"></span> / <span id="totalImages"></span> <!-- Display image index and total -->
                                            <button type="button" class="btn btn-secondary" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
                                            <button type="button" class="btn btn-secondary " id="nextBtn"><i class="fas fa-chevron-right"></i></button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
    <script src="../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#tableTicket").DataTable({
                "lengthChange": true,
                "autoWidth": true,

            }).buttons().container().appendTo('#tableTicket_wrapper .col-md-6:eq(0)');
            $('#tableTicket2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "responsive": true,
            });
        });
    </script>
    <!-- script pop up ticket details -->
    <script src="../scripts/pop_up_ticket_details.js"></script>
    <!-- script pop up image  -->
    <script src="../scripts/pop_up_images.js"></script>
    <!-- script delete ticket -->
    <script src="../scripts/delete_ticket.js"></script>
    <!-- auto fill station id -->
    <script src="../scripts/get_suggestions_auto_fill_stationID.js"></script>
    <!-- filter -->
    <script>
        $(document).ready(function() {
            // Handle filter button click
            $('#filterForm button[type="button"]').on('click', function() {
                var formData = $('#filterForm').serialize();
                $.ajax({
                    url: 'filter_ticket.php', // Replace with your PHP script handling filtering
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                        $('#ticketTableBody').html(response); // Update table body with filtered data
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });

            // Handle reset button click
            $('#filterResetBtn').on('click', function() {
                $('#filterForm')[0].reset();
                // Reset DataTable
                $('#tableTicket').DataTable().search('').draw();
            });
        });
    </script>
    <!-- export -->
    <!-- Include jsPDF and jsPDF AutoTable libraries -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Khmer&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script src="../scripts/export.js"></script>
    <!-- auto close alert -->
    <script src="../scripts/auto_close_alert.js"></script>

</body>

</html>