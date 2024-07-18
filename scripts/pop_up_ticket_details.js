function formatDateTime(dateString) {
    const date = new Date(dateString);
    const options = {
        year: 'numeric', month: 'short', day: '2-digit',
        hour: '2-digit', minute: '2-digit', second: '2-digit',
        hour12: true
    };
    return date.toLocaleDateString('en-GB', options).replace(/,/g, '');
}

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

    // Format the dates
    if (ticket.ticket_open) {
        document.getElementById('modalTicketOpen').textContent = formatDateTime(ticket.ticket_open);
    }
    if (ticket.ticket_on_hold) {
        document.getElementById('modalTicketOnHold').textContent = formatDateTime(ticket.ticket_on_hold);
    }
    if (ticket.ticket_in_progress) {
        document.getElementById('modalTicketInProgress').textContent = formatDateTime(ticket.ticket_in_progress);
    }
    if (ticket.ticket_pending_vendor) {
        document.getElementById('modalTicketPendingVendor').textContent = formatDateTime(ticket.ticket_pending_vendor);
    }
    if (ticket.ticket_close) {
        document.getElementById('modalTicketClose').textContent = formatDateTime(ticket.ticket_close);
    }

    document.getElementById('modalComment').textContent = ticket.comment;

    // Set image source for main image
    document.getElementById('modalIssueImages').src = ticket.image_paths || '';

    // Clear previous images
    const modalIssueImages = document.getElementById('modalIssueImages');
    modalIssueImages.innerHTML = '';

    // Display multiple images if available
    if (ticket.image_paths) {
        const images = ticket.image_paths.split(',');
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
