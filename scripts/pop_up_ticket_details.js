//<!--script pop up details ticket -->

    function showTicketDetails(ticket) {
        // Set text content for single fields
        document.getElementById('modalTicketId').textContent = ticket.ticket_id;
        document.getElementById('modalStationId').textContent = ticket.station_id;
        document.getElementById('modalStationName').textContent = ticket.station_name;
        document.getElementById('modalStationType').textContent = ticket.station_type;
        document.getElementById('modalProvince').textContent = ticket.province;
        document.getElementById('modalDescription').textContent = ticket.issue_description;
        document.getElementById('modalIssueType').textContent = ticket.issue_type;
        document.getElementById('modalSLA_category').textContent = ticket.SLA_category;
        document.getElementById('modalStatus').textContent = ticket.status;
        document.getElementById('modalAssign').textContent = ticket.users_name;
        document.getElementById('modalTicketOpen').textContent = ticket.ticket_open;
        document.getElementById('modalTicketOnHold').textContent = ticket.ticket_on_hold;
        document.getElementById('modalTicketInProgress').textContent = ticket.ticket_in_progress;
        document.getElementById('modalTicketPendingVendor').textContent = ticket.ticket_pending_vendor;
        document.getElementById('modalTicketClose').textContent = ticket.ticket_close;
        document.getElementById('modalComment').textContent = ticket.comment;

        // Set image source for main image
        document.getElementById('modalIssueImages').src = ticket.issue_image || '';

        // Clear previous images
        const modalIssueImages = document.getElementById('modalIssueImages');
        modalIssueImages.innerHTML = '';

        // Display multiple images if available
        if (ticket.issue_image) {
            const images = ticket.issue_image.split(',');
            images.forEach(image => {
                const imgElement = document.createElement('img');
                imgElement.src = image.trim();
                imgElement.style.width = '50px';
                imgElement.style.cursor = 'pointer';
                imgElement.onclick = () => showImage(image.trim());
                modalIssueImages.appendChild(imgElement);
            });
        }

        $('#ticketModal').modal('show');
    }

    function showImage(imageUrl) {
        $('#imageToShow').attr('src', imageUrl);
        $('#imageModal').modal('show');
    }
