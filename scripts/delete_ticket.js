$(document).ready(function () {
    // Handle delete button click
    $(document).on('click', '.delete-btn', function () {
        var ticketId = $(this).data('id'); // Get the ID from data attribute

        // Display confirmation dialog using SweetAlert2
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
                // Make AJAX request to delete_ticket.php
                $.ajax({
                    url: 'delete_ticket.php',
                    type: 'POST',
                    data: { id: ticketId },
                    success: function (response) {
                        console.log('Response:', response); // Debugging: Log the response

                        if (response === 'success') {
                            // Remove the row from the table
                            var rowSelector = '#ticket-' + ticketId;
                            console.log('Removing row with selector: ' + rowSelector); // Log the row being removed
                            $(rowSelector).remove();
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
});
