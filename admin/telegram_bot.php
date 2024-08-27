<?php
include "../inc/header_script.php";
// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; //  user ID

$query_user = " SELECT u.*, r.list_telegram_bot
                FROM tbl_users u 
                JOIN tbl_users_rules r 
                ON u.rules_id = r.rules_id 
                WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    if (!$user['list_telegram_bot']) {
        header("Location: 404.php");
        exit();
    }
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
        <?php
        include "../inc/top_nav_bar.php";
        include "../inc/sidebar.php"
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Telegram Bot</h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <?php
                                if (isset($_SESSION['success_message_telegram_bot'])) {
                                    echo "<div class='alert alert-success alert-dismissible fade show  mb-0' role='alert'>
                                        <strong>{$_SESSION['success_message_telegram_bot']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['success_message_telegram_bot']); // Clear the message after displaying
                                }

                                if (isset($_SESSION['error_message_telegram_bot'])) {
                                    echo "<div class='alert alert-danger alert-dismissible fade show  mb-0' role='alert'>
                                        <strong>{$_SESSION['error_message_telegram_bot']}</strong>
                                        <button type='button' class='close' data-dismiss='modal' aria-label='Close' onclick='this.parentElement.style.display=\"none\";'>
                                            <span aria-hidden='true'>&times;</span>
                                        </button>
                                    </div>";
                                    unset($_SESSION['error_message_telegram_bot']); // Clear the message after displaying
                                }
                                ?>
                            </ol>
                        </div>
                        <!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="card">
                        <!-- /.card-header -->
                        <div class="card-body p-0" style="overflow: hidden;">

                            <div class="card-header">
                                <a href="add_telegram_bot.php" id="add_ticket" class="btn btn-primary ">Add telegra bot</a>
                            </div>

                            <br>

                            <table id="tableUserRules" class="table_users_rules table table-bordered table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Action</th>
                                        <th>Bot Name</th>
                                        <th>Token</th>
                                        <th>station type</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $telegram_bot_query = "SELECT * FROM tbl_telegram_bot ORDER BY id DESC ";
                                    $telegram_bot_result = $conn->query($telegram_bot_query);
                                    $i =  1;
                                    if ($telegram_bot_result->num_rows > 0) {
                                        while ($telegram_bot = $telegram_bot_result->fetch_assoc()) {
                                            echo "<tr id='telegram_bot-{$telegram_bot['id']}'>";
                                            echo "<td  class='py-1'>" . $i++ . "</td>";

                                            echo "<td  class='py-1'>";
                                            // Encrypt id
                                            //  original ID
                                            $original_id = $telegram_bot['id'];

                                            // Hash the ID to make it unique and consistent
                                            $hashed_id = hash('sha256', $original_id);

                                            // Encode the hash and take the first 10 characters
                                            $encoded_id = substr(base64_encode($hashed_id), 0, 20);
                                            echo "<a href='edit_telegram_bot.php?id={$encoded_id}' class='btn btn-primary'><i class='fa-solid fa-pen-to-square'></i></a> ";

                                            echo "<button data-id='" . $telegram_bot['id'] . "' class='btn btn-danger delete-btn'><i class='fa-solid fa-trash'></i></button>";

                                            echo "</td>";
                                            echo "<td class='py-1'>" . $telegram_bot['bot_name'] . "</td>";
                                            echo "<td class='py-1'>" . $telegram_bot['token'] . "</td>";
                                            echo "<td class='py-1'>" . $telegram_bot['station_type'] . "</td>";



                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td class='text-center' colspan='6'>No users found!</td></tr>";
                                    }
                                    ?>
                                </tbody>

                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include "../inc/footer.php" ?>
    </div>
    <!-- ./wrapper -->

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
    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <!-- AdminLTE App -->
    <script src="../dist/js/adminlte.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../dist/js/demo.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $("#tableUserRules").DataTable({
                "responsive": false,
                "lengthChange": true,
                "autoWidth": false,
                "buttons": [, "csv", "excel", "pdf"]
            }).buttons().container().appendTo('#tableUserRules_wrapper .col-md-6:eq(0)');
            $('#tableUserRules2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": false,
            });
        });
    </script>
    <!-- auto close alert -->
    <script src="../scripts/auto_close_alert.js"></script>
    <!-- delete user rules-->
    <script>
        $(document).ready(function() {
            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                var telegram_bot = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this telegram bot!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_telegram_bot.php', // Adjust URL to your delete script
                            type: 'POST',
                            data: {
                                id: telegram_bot
                            },
                            success: function(response) {
                                console.log('Response:', response); // Debugging: Log the response
                                if (response === 'success') {
                                    console.log('Removing row with ID: #telegram_bot-' + telegram_bot); // Log the row being removed
                                    $('#telegram_bot-' + telegram_bot).remove(); // Remove the row from the table
                                    Swal.fire('Deleted!', 'Your telegram bot has been deleted.', 'success');
                                } else {
                                    Swal.fire('Error!', 'Failed to deletetelegram bot.', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', status, error); // Debugging: Log AJAX errors
                                Swal.fire('Error!', 'An error occurred while deleting the telegram bot.', 'error');
                            }
                        });
                    }
                });
            });


        });
    </script>
</body>

</html>