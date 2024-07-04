function filterTickets() {
    // Retrieve filter criteria from form inputs
    var stationId = document.getElementById('station_id').value;
    var issueType = document.getElementById('issue_type').value;
    var priority = document.getElementById('priority').value;
    var status = document.getElementById('status').value;
    var assign = document.getElementById('users_id').value;
    var ticketOpenFrom = document.getElementById('ticket_open_from').value;
    var ticketOpenTo = document.getElementById('ticket_open_to').value;
    var ticketCloseFrom = document.getElementById('ticket_close_from').value;
    var ticketCloseTo = document.getElementById('ticket_close_to').value;

    // Perform filtering logic or send AJAX request to server
    // Example: perform AJAX request to fetch filtered tickets
    $.ajax({
        url: 'filter_tickets.php',
        type: 'GET',
        data: {
            station_id: stationId,
            issue_type: issueType,
            priority: priority,
            status: status,
            users_id: assign,
            ticket_open_from: ticketOpenFrom,
            ticket_open_to: ticketOpenTo,
            ticket_close_from: ticketCloseFrom,
            ticket_close_to: ticketCloseTo
        },
        success: function (response) {
            // Handle success response - update table or display results
            $('#ticketTableBody').html(response);
            $('#ticketTable').DataTable().ajax.reload(); // Reload DataTable if using AJAX
        },
        error: function (xhr, status, error) {
            // Handle error
            console.error('Error filtering tickets:', error);
        }
    });
}
