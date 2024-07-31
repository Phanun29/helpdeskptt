<?php
include "../inc/header_script.php";

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; // Example user ID
$user_create_ticket = $fetch_info['users_id'];
$query_user = "SELECT u.*, r.list_ticket_status, r.add_ticket_status, r.edit_ticket_status, r.delete_ticket_status, r.list_ticket_assign
    FROM tbl_users u 
    JOIN tbl_users_rules r ON u.rules_id = r.rules_id 
    WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
  $user = $result_user->fetch_assoc();

  $listTicketAssign = $user['list_ticket_assign'];
  $user_id = $fetch_info['users_id']; // Assuming you have stored user ID in session

  // Set common parts of the query
  $ticket_select =
    "SELECT 
            t.*, 
            REPLACE(GROUP_CONCAT(u.users_name SEPARATOR ', '), ', ', ',') as users_name
        FROM 
            tbl_ticket t
        LEFT JOIN 
            tbl_users u ON FIND_IN_SET(u.users_id, t.users_id) OR FIND_IN_SET(u.users_id, t.user_create_ticket)";
  $group_by = "GROUP BY t.ticket_id DESC LIMIT 20";

  // Set query based on user type
  if ($listTicketAssign == 0) {
    $ticket_query = "$ticket_select $group_by";
    $status_query = "SELECT status, COUNT(*) as count FROM tbl_ticket GROUP BY status ";
    $SLA_category_query = "SELECT SLA_category, COUNT(*) as count FROM tbl_ticket WHERE SLA_category IS NOT NULL GROUP BY SLA_category";
    $issue_query = "SELECT issue_type FROM tbl_ticket";
  } else {
    $ticket_query = "$ticket_select WHERE FIND_IN_SET($user_id, t.users_id) OR FIND_IN_SET($user_create_ticket, t.user_create_ticket) $group_by";

    $status_query = " SELECT status, COUNT(*) as count 
                      FROM tbl_ticket 
                      WHERE FIND_IN_SET($user_id, users_id) OR FIND_IN_SET($user_id, user_create_ticket) 
                      GROUP BY status";

    $SLA_category_query = " SELECT SLA_category, COUNT(*) as count 
                            FROM tbl_ticket 
                            WHERE SLA_category IS NOT NULL AND (FIND_IN_SET($user_id, users_id) OR FIND_IN_SET($user_id, user_create_ticket) ) 
                            GROUP BY SLA_category";

    $issue_query = "SELECT issue_type 
                    FROM tbl_ticket 
                    WHERE FIND_IN_SET($user_id, users_id) OR FIND_IN_SET($user_id, user_create_ticket)  ";
  }

  // Execute ticket query
  $ticket_result = $conn->query($ticket_query);

  // Fetch status counts
  $status_counts = [
    'Open' => 0,
    'On Hold' => 0,
    'In Progress' => 0,
    'Pending Vendor' => 0,
    'Close' => 0
  ];

  $result_status = $conn->query($status_query);
  while ($row = $result_status->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
  }

  // Fetch SLA_category counts and calculate percentages
  $SLA_category_counts = [
    'CAT Hardware' => 0,
    'CAT 1' => 0,
    'CAT 2' => 0,
    'CAT 3' => 0,
    'CAT 4' => 0,
    'CAT 4 Report' => 0,
    'CAT 5' => 0,
    'Other' => 0
  ];

  $total_tickets = 0;
  $result_SLA_category = $conn->query($SLA_category_query);
  while ($row = $result_SLA_category->fetch_assoc()) {
    $SLA_category_counts[$row['SLA_category']] = $row['count'];
    $total_tickets += $row['count'];
  }

  $SLA_category_percentages = array_map(function ($count) use ($total_tickets) {
    return $total_tickets > 0 ? round(($count / $total_tickets) * 100) : 0;
  }, $SLA_category_counts);

  // Fetch issue counts
  $issue_counts = [
    'Hardware' => 0,
    'Software' => 0,
    'Network' => 0,
    'Dispenser' => 0,
    'ABA' => 0,
    'FleetCard' => 0,
    'ATG' => 0
  ];

  $result_issue = $conn->query($issue_query);
  while ($row = $result_issue->fetch_assoc()) {
    $issue_types = explode(', ', $row['issue_type']);
    foreach ($issue_types as $issue_type) {
      $type = trim($issue_type);
      if (isset($issue_counts[$type])) {
        $issue_counts[$type]++;
      }
    }
  }

  // Prepare labels and data for Chart.js
  $labels = array_keys($issue_counts);
  $data = array_values($issue_counts);
} else {
  $_SESSION['error_message'] = "User not found or permission check failed.";
}
?>




<!DOCTYPE html>
<html lang="en">

<head>

  <?php include "../inc/head.php" ?>


</head>

<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <?php include "../inc/nav.php" ?>
    <?php include "../inc/sidebar.php" ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">

              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">

            <!-- section start Summary -->
            <style>
              .inner p,
              h3 {
                margin: 0;
              }
            </style>
            <!-- <div class="row col-12 "> -->
            <div class="card col-12 col-md-6">
              <div class="card-header">
                <h3 class="card-title"> Status</h3>
              </div>
              <div class="row">
                <div class="col-lg-4 col-4">
                  <div class="small-box bg-info">
                    <div class="inner">
                      <h3><?php echo $status_counts['Open']; ?></h3>
                      <p>Open</p>
                    </div>
                    <div class="icon">
                    </div>
                    <a href="moreinfo.php?status=Open" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                <div class="col-lg-4 col-4">
                  <div class="small-box bg-danger">
                    <div class="inner">
                      <h3><?php echo $status_counts['On Hold']; ?></h3>
                      <p>On Hold</p>
                    </div>
                    <div class="icon">

                    </div>
                    <a href="moreinfo.php?status=On Hold" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                <div class="col-lg-4 col-4">
                  <div class="small-box bg-warning">
                    <div class="inner">
                      <h3><?php echo $status_counts['In Progress']; ?></h3>
                      <p>In Progress</p>
                    </div>
                    <div class="icon">

                    </div>
                    <a href="moreinfo.php?status=In Progress" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-lg-6 col-6">
                  <div class="small-box bg-primary">
                    <div class="inner">
                      <h3><?php echo $status_counts['Pending Vendor']; ?></h3>
                      <p>Pending Vendor</p>
                    </div>
                    <div class="icon">

                    </div>
                    <a href="moreinfo.php?status=Pending Vendor" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>

                <div class="col-lg-6 col-6">
                  <div class="small-box bg-success">
                    <div class="inner">
                      <h3><?php echo $status_counts['Close']; ?></h3>
                      <p>Close</p>
                    </div>
                    <div class="icon">

                    </div>
                    <a href="moreinfo.php?status=Close" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                  </div>
                </div>
              </div>
            </div>
            <div class="card col-12 col-md-6">
              <div class="chartMenu">
              </div>
              <div class="chartCard">
                <div class="chartBox">
                  <canvas id="issueTypeChart"></canvas>
                </div>
              </div>
            </div>
            <!-- /.section start Summary -->

            <!-- Section Start SLA_category -->
            <div class="card col-12" id="SLA_category">
              <div class="card-header">
                <h3 class="card-title"><b>SLA Category %</b></h3>
              </div>
              <div class="col-12">
                <div class="card-body">
                  <div class="row">
                    <div class="col-6 col-md-3 text-center">
                      <div style="display:inline;width:90px;height:90px;">
                        <input type="text" class="knob" value="<?php echo $SLA_category_percentages['CAT Hardware']; ?>" data-width="90" data-height="90" data-fgcolor="#3c8dbc" data-readOnly="true">
                      </div>
                      <div class="knob-label">CAT Hardware</div>
                    </div>

                    <div class="col-6 col-md-3 text-center">
                      <div style="display:inline;width:90px;height:90px;">
                        <input type="text" class="knob" value="<?php echo $SLA_category_percentages['CAT 1']; ?>" data-width="90" data-height="90" data-fgcolor="#f56954" data-readOnly="true">
                      </div>
                      <div class="knob-label">CAT 1</div>
                    </div>

                    <div class="col-6 col-md-3 text-center">
                      <div style="display:inline;width:90px;height:90px;">
                        <input type="text" class="knob" value="<?php echo $SLA_category_percentages['CAT 2']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                      </div>
                      <div class="knob-label">CAT 2</div>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                      <div style="display:inline;width:90px;height:90px;">
                        <input type="text" class="knob" value="<?php echo $SLA_category_percentages['CAT 3']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                      </div>
                      <div class="knob-label">CAT 3</div>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                      <div style="display:inline;width:90px;height:90px;">
                        <input type="text" class="knob" value="<?php echo $SLA_category_percentages['CAT 4']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                      </div>
                      <div class="knob-label">CAT 4</div>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                      <div style="display:inline;width:90px;height:90px;">
                        <input type="text" class="knob" value="<?php echo $SLA_category_percentages['CAT 4 Report']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                      </div>
                      <div class="knob-label">CAT 4 Report</div>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                      <div style="display:inline;width:90px;height:90px;">
                        <input type="text" class="knob" value="<?php echo $SLA_category_percentages['CAT 5']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                      </div>
                      <div class="knob-label">CAT 5</div>
                    </div>
                    <div class="col-6 col-md-3 text-center">
                      <div style="display:inline;width:90px;height:90px;">
                        <input type="text" class="knob" value="<?php echo $SLA_category_percentages['Other']; ?>" data-width="90" data-height="90" data-fgcolor="#932ab6" data-readOnly="true">
                      </div>
                      <div class="knob-label">Other</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- /.Section Start SLA_category -->

            <!-- </div> -->
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Ticket</h3>
              </div>
              <div class="card-body table-responsive p-0">
                <table id="tableTicket" class="table table-bordered table-head-fixed text-nowrap">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Ticket ID</th>
                      <th>Station ID</th>
                      <th>Station Name</th>
                      <th>Station Type</th>
                      <th>Province</th>
                      <th>Issue Description</th>
                      <th>Issue Type</th>
                      <th>SLA Category </th>
                      <th>Status</th>
                      <th>Assign</th>
                      <th>Ticket Open</th>
                      <th>Ticket On Hold</th>
                      <th>Ticket in Progress</th>
                      <th>Ticket Pendign Vendor</th>
                      <th>Ticket Close</th>
                      <th>Ticket Time</th>
                      <th>Comment</th>

                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $i = 1;
                    if ($ticket_result->num_rows > 0) {
                      while ($row = $ticket_result->fetch_assoc()) {

                        echo "<tr>";
                        echo "<td>" . $i++ . "</td>";
                        echo "<td>" . $row['ticket_id'] . "</td>";
                        echo "<td>" . $row['station_id'] . "</td>";
                        echo "<td>" . $row['station_name'] . "</td>";
                        echo "<td>" . $row['station_type'] . "</td>";
                        echo "<td>" . $row['province'] . "</td>";
                        echo "<td>" . $row['issue_description'] . "</td>";
                        echo "<td>" . $row['issue_type'] . "</td>";
                        echo "<td>" . $row['SLA_category'] . "</td>";
                        echo "<td>" . $row['status'] . "</td>";
                        echo "<td>" . $row['users_name'] . "</td>";
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
                        echo "<td>" . $row['comment'] . "</td>";
                        echo "</tr>";
                      }
                    } else {
                      echo "<tr><td colspan='15' class='text-center'>No tickets found</td></tr>";
                    }

                    ?>
                  </tbody>
                </table>
              </div>

            </div>


          </div>

        </div>
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->


    <?php include "../inc/footer.php" ?>

  </div>
  <!-- ./wrapper -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Data from PHP
    const labels = <?php echo json_encode($labels); ?>;
    const data = <?php echo json_encode($data); ?>;
    // Chart.js setup
    const config = {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Issue Type',
          data: data,
          backgroundColor: [
            // 'red', // Hardware
            // 'blue', // Software
            // 'yellow', // Network
            // 'white', // Dispenser
            // '#00415f', // aba
            // '#a9cf38', //fleet card
            // 'rgba(111, 18, 13, 1)' //atg

            'rgba(54, 162, 235, 0.2)',
          ],
          // borderColor: [
          //   'rgba(0, 166, 158, 1)', // Hardware
          //   'rgba(255, 184, 34, 1)', // Software
          //   'rgba(255, 182, 94, 1)', // Network
          //   'rgba(141, 68, 173, 1)', // Dispenser
          //   'rgba(107, 210, 190, 1)', //aba
          //   'rgba(141, 648, 173, 1)', //fleet card
          //   'rgba(111, 18, 13, 1)' //atg
          // ],
          borderColor: '#146ca4',
          borderWidth: 1
        }]
      },
      options: {
        // indexAxis: 'y',
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    };

    // Render the chart
    const myChart = new Chart(document.getElementById('issueTypeChart'), config);
  </script>

  <!-- jQuery -->
  <script src="../plugins/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="../plugins/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button)
  </script>
  <!-- Bootstrap 4 -->
  <script src="../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- ChartJS -->
  <script src="../plugins/chart.js/Chart.min.js"></script>
  <!-- Sparkline -->
  <script src="../plugins/sparklines/sparkline.js"></script>
  <!-- JQVMap -->
  <script src="../plugins/jqvmap/jquery.vmap.min.js"></script>
  <script src="../plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
  <!-- jQuery Knob Chart -->
  <script src="../plugins/jquery-knob/jquery.knob.min.js"></script>
  <!-- daterangepicker -->
  <script src="../plugins/moment/moment.min.js"></script>
  <script src="../plugins/daterangepicker/daterangepicker.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="../plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
  <!-- Summernote -->
  <script src="../plugins/summernote/summernote-bs4.min.js"></script>
  <!-- overlayScrollbars -->
  <script src="../plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
  <!-- AdminLTE App -->
  <script src="../dist/js/adminlte.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../dist/js/demo.js"></script>
  <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
  <script src="../dist/js/pages/dashboard.js"></script>
  <script src="scripts/app.js"></script>
</body>

</html>