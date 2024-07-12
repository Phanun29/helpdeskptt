
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
                    data: {
                        id: ticketId
                    },
                    success: function (response) {
                        if (response === 'success') {
                            $('#ticket-' + ticketId).remove();
                            Swal.fire(
                                'Deleted!',
                                'Your ticket has been deleted.',
                                'success'
                            );
                        } else if (response === 'closed') {
                            Swal.fire(
                                'Error!',
                                'Closed tickets cannot be deleted.',
                                'error'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                'Failed to delete ticket.',
                                'error'
                            );
                        }
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
        // alert('Edit button clicked for ticket ID: ' + ticketId);
    });
});
