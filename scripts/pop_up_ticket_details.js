function showTicketDetails(ticket) {
    // Check if ticket_time is null and calculate it if needed
    if (!ticket.ticket_time) {
        const ticketOpenTime = new Date(ticket.ticket_open);
        const ticketCloseTime = new Date();
        const interval = new Date(ticketCloseTime - ticketOpenTime);

        const days = interval.getUTCDate() - 1;
        const hours = interval.getUTCHours();
        const minutes = interval.getUTCMinutes();
        const seconds = interval.getUTCSeconds();

        let ticket_time = '';
        if (days > 0) {
            ticket_time += days + 'd, ';
        }
        if (hours > 0 || days > 0) {
            ticket_time += hours + 'h, ';
        }
        if (minutes > 0 || hours > 0 || days > 0) {
            ticket_time += minutes + 'm, ';
        }
        ticket_time += seconds + 's ago';

        ticket.ticket_time = ticket_time;
    }
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
    document.getElementById('modalTicketTime').textContent = ticket.ticket_time;
    document.getElementById('modalComment').textContent = ticket.comment;

    // Clear previous media
    const modalIssueMedia = document.getElementById('modalIssueMedia');
    modalIssueMedia.innerHTML = '';

    // Display multiple images and videos if available
    if (ticket.image_paths) {
        const media = ticket.image_paths.split(',');
        media.forEach(item => {
            const trimmedItem = item.trim();
            if (trimmedItem.match(/\.(jpeg|jpg|gif|png)$/i)) {
                const imgElement = document.createElement('img');
                imgElement.src = trimmedItem;
                imgElement.style.width = '50px';
                imgElement.style.cursor = 'pointer';
                imgElement.onclick = () => showMedia(trimmedItem);
                modalIssueMedia.appendChild(imgElement);
            } else if (trimmedItem.match(/\.(mp4|webm|ogg)$/i)) {
                const videoElement = document.createElement('video');
                videoElement.src = trimmedItem;
                videoElement.style.width = '50px';
                videoElement.style.cursor = 'pointer';
                videoElement.controls = true;
                videoElement.onclick = () => showMedia(trimmedItem);
                modalIssueMedia.appendChild(videoElement);
            }
        });
    }

    $('#ticketModal').modal('show');

}

function showMedia(imageUrl) {
    $('#imageToShow').attr('src', imageUrl);
    $('#imageModal').modal('show');
}
