<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Management</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="mb-4">Ticket Management</h1>
        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form id="filterForm">
                    <div class="form-row">
                        <div class="form-group col-md-2">
                            <label for="station_id">Station ID</label>
                            <input type="text" class="form-control" id="station_id" name="station_id">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="issue_type">Issue Type</label>
                            <select class="form-control" id="issue_type" name="issue_type">
                                <option value="">All</option>
                                <option value="Hardware">Hardware</option>
                                <option value="Software">Software</option>
                                <option value="Network">Network</option>
                                <option value="Dispenser">Dispenser</option>
                                <option value="Unassigned">Unassigned</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="SLA_category">SLA Category</label>
                            <select class="form-control" id="SLA_category" name="SLA_category">
                                <option value="">All</option>
                                <option value="CAT Hardware">CAT Hardware</option>
                                <option value="CAT 1*">CAT 1*</option>
                                <option value="CAT 2*">CAT 2*</option>
                                <option value="CAT 3*">CAT 3*</option>
                                <option value="CAT 4*">CAT 4*</option>
                                <option value="CAT 4 Report*">CAT 4 Report*</option>
                                <option value="CAT 5*">CAT 5*</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">All</option>
                                <option value="Open">Open</option>
                                <option value="On Hold">On Hold</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Pending Vendor">Pending Vendor</option>
                                <option value="Close">Close</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="users_id">Assign</label>
                            <select class="form-control" id="users_id" name="users_id">
                                <option value="">All</option>
                                <!-- Populate with users from database if needed -->
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ticket_open_from">Ticket Open From</label>
                            <input type="date" class="form-control" id="ticket_open_from" name="ticket_open_from">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ticket_open_to">Ticket Open To</label>
                            <input type="date" class="form-control" id="ticket_open_to" name="ticket_open_to">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ticket_close_from">Ticket Close From</label>
                            <input type="date" class="form-control" id="ticket_close_from" name="ticket_close_from">
                        </div>
                        <div class="form-group col-md-2">
                            <label for="ticket_close_to">Ticket Close To</label>
                            <input type="date" class="form-control" id="ticket_close_to" name="ticket_close_to">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">Filter</button>
                    <button type="button" class="btn btn-secondary" id="clearFilters">Clear Filters</button>
                </form>
            </div>
        </div>
        <!-- Tickets Table -->
        <div class="card">
            <div class="card-body">
                <table id="ticketTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>Station ID</th>
                            <th>Issue Type</th>
                            <th>SLA Category</th>
                            <th>Status</th>
                            <th>Assign</th>
                            <th>Ticket Open</th>
                            <th>Ticket Close</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.2/js/jquery.dataTables.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- <script>
        $(document).ready(function () {
            var table = $('#ticketTable').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url": "process_filter.php", // Path to process_filter.php
                    "type": "POST"
                },
                "columns": [
                    { "data": "ticket_id" },
                    { "data": "station_id" },
                    { "data": "issue_type" },
                    { "data": "SLA_category" },
                    { "data": "status" },
                    { "data": "assign" }, // Change 'assign' to match your column name
                    { "data": "ticket_open" },
                    { "data": "ticket_close" }
                ]
            });

            $('#filterForm').on('submit', function (e) {
                e.preventDefault();
                table.ajax.reload(); // Reload DataTable on form submission
            });

            $('#clearFilters').on('click', function () {
                $('#filterForm')[0].reset();
                table.ajax.reload(); // Reload DataTable on clear filters
            });
        });
    </script> -->
</body>
</html>
