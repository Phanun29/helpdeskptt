<?php
include "../inc/header_script.php";
// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; //  user ID

$query_user = " SELECT u.*  , r.list_ticket_track
                FROM tbl_users u 
                JOIN tbl_users_rules r 
                ON u.rules_id = r.rules_id 
                WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    if (!$user['list_ticket_track']) {
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
    <?php include "../inc/head.php"; ?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php
        include "../inc/nav.php";
        include "../inc/sidebar.php";
        ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">Tracking Ticket</h1>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <!-- Small boxes (Stat box) -->
                    <div class="card">
                        <div class="card-body">
                            <div class="">
                                <form action="" id="track_ticket" method="GET">
                                    <input type="search" name="ticket_id" class="form-control form-control-sm col-12 col-md-6" placeholder="Enter Ticket ID " required>
                                    <button class="btn btn-primary mt-2" id="filter_button" type="button">FILTER <i class="fa fa-filter" aria-hidden="true"></i></button>
                                    <button class="btn btn-secondary buttons-pdf buttons-html5 mt-2" tabindex="0" aria-controls="tbl_ticket" onclick="exportToPDF()" type="button"><span>PDF</span></button>
                                    <button class="btn btn-secondary buttons-csv buttons-html5 mt-2" tabindex="0" aria-controls="tbl_ticket" onclick="exportToExcel()" type="button"><span>Excel</span></button>
                                </form>
                            </div>
                            <div class="mt-4">
                                <div>
                                    <h4>RESULT</h4>
                                </div>
                                <div id="ticketResults" style="overflow: auto;">
                                    <!-- FILTER results will be injected here -->
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div><!-- /.container-fluid -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php include "../inc/footer.php"; ?>
    </div>
    <!-- ./wrapper -->
    <!-- export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script>
        function exportToExcel() {
            var exportTable = document.createElement('table');
            var exportTableBody = document.createElement('tbody');
            var headerRow = document.querySelector('#ticketResults thead tr');
            var exportHeaderRow = document.createElement('tr');

            headerRow.querySelectorAll('th').forEach(function(cell) {
                if (!cell.classList.contains('export-ignore')) {
                    var exportCell = document.createElement('td');
                    exportCell.textContent = cell.textContent;
                    exportCell.style.border = '1px solid #000';
                    exportCell.style.padding = '4px';
                    exportHeaderRow.appendChild(exportCell);
                }
            });
            exportTableBody.appendChild(exportHeaderRow);

            var tableRows = document.querySelectorAll('#ticketResults tbody tr');
            tableRows.forEach(function(row) {
                var exportRow = document.createElement('tr');
                row.querySelectorAll('td').forEach(function(cell, index) {
                    if (!cell.classList.contains('export-ignore')) {
                        var exportCell = document.createElement('td');
                        exportCell.textContent = cell.textContent;
                        exportCell.style.border = '1px solid #000';
                        exportCell.style.padding = '4px';
                        exportRow.appendChild(exportCell);
                    }
                });
                exportTableBody.appendChild(exportRow);
            });

            exportTable.appendChild(exportTableBody);

            var blob = new Blob(['\ufeff', exportTable.outerHTML], {
                type: 'application/vnd.ms-excel'
            });

            var url = URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.href = url;
            a.download = "ticket.xls";
            document.body.appendChild(a);
            a.click();

            setTimeout(function() {
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            }, 0);
        }
        async function exportToPDF() {
            const {
                jsPDF
            } = window.jspdf;
            const response = await fetch('fontBase64.txt');
            const fontBase64 = await response.text();

            const doc = new jsPDF('landscape');
            doc.addFileToVFS('NotoSerifKhmer-Regular.ttf', fontBase64);
            doc.addFont('NotoSerifKhmer-Regular.ttf', 'Noto Serif Khmer', 'normal');
            doc.setFont('Noto Serif Khmer');
            const filteredHtml = $('#tableTicket').clone().find('.export-ignore').remove().end()[0];
            doc.autoTable({
                html: filteredHtml,
                styles: {
                    font: 'Noto Serif Khmer',
                    fontSize: 6,
                    cellPadding: 2
                },
                startY: 0,
                headStyles: {
                    fillColor: [22, 160, 133],
                    textColor: [255, 255, 255],
                    fontStyle: 'bold',
                    fontSize: 8,
                    halign: 'center'
                },
                bodyStyles: {
                    fillColor: [238, 238, 238],
                    textColor: [0, 0, 0],
                    fontSize: 8,
                    halign: 'center'
                },
                alternateRowStyles: {
                    fillColor: [255, 255, 255]
                },
                margin: {
                    top: 0,
                    bottom: 0,
                    left: 0,
                    right: 0
                },
                theme: 'grid'
            });

            const pdfData = doc.output('blob');
            const pdfUrl = URL.createObjectURL(pdfData);

            const newWindow = window.open();
            newWindow.document.write(`<iframe width="100%" height="100%" src="${pdfUrl}"></iframe>`);
            newWindow.document.write(`<a href="${pdfUrl}" download="table.pdf">Download PDF</a>`);
        }
    </script>
    <!-- filter -->
    <script>
        $(document).ready(function() {
            $('#track_ticket button[type="button"]').on('click', function() {
                var formData = $('#track_ticket').serialize();
                var ticketId = $('input[name="ticket_id"]').val().trim();

                if (ticketId === '') {
                    // Clear the results if the input is empty
                    $('#ticketResults').empty();
                } else {
                    $.ajax({
                        url: 'filter_ticket_track.php', // Replace with your PHP script handling filtering
                        type: 'GET',
                        data: formData,
                        success: function(response) {
                            $('#ticketResults').html(response); // Update the result container with filtered data
                        },
                        error: function(xhr, status, error) {
                            console.error('Error:', error);
                        }
                    });
                }
            });
        });
    </script>

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