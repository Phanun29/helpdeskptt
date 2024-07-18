$(document).ready(function () {
    // Handle delete button click
    $(document).on('click', '.delete-btn', function () {
        var ticketId = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this ticket!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'delete_ticket.php',
                    type: 'POST',
                    data: { id: ticketId },
                    success: function (response) {
                        console.log('Response:', response); // Debugging: Log the response
                        if (response === 'success') {
                            console.log('Removing row with ID: #ticket-' + ticketId); // Log the row being removed
                            $('#ticket-' + ticketId).remove(); // Ensure the ID selector matches your HTML
                            Swal.fire('Deleted!', 'Your ticket has been deleted.', 'success');
                        } else if (response === 'closed') {
                            Swal.fire('Error!', 'Closed tickets cannot be deleted.', 'error');
                        } else {
                            Swal.fire('Error!', 'Failed to delete ticket.', 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error); // Debugging: Log AJAX errors
                        Swal.fire('Error!', 'An error occurred while deleting the ticket.', 'error');
                    }
                });
            }
        });
    });

    // Handle edit button click
    $(document).on('click', '.edit-btn', function () {
        var ticketId = $(this).data('id');
        // Redirect or load edit form page, passing ticketId
        window.location.href = 'edit_ticket.php?id=' + ticketId;
    });
});