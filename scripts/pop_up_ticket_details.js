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
    } if (ticket.ticket_time) {
        document.getElementById('modalTicketTime').textContent = ticket.ticket_time;
    } else {

    }

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

function showMedia(media) {
    var mediaArray = media.split(',');
    var imageModal = $('#imageModal');
    var modalImage = document.getElementById('modalImage');
    var modalVideo = document.getElementById('modalVideo');
    var currentMediaIndex = 0;

    function showCurrentMedia() {
        var currentMedia = mediaArray[currentMediaIndex];
        if (currentMedia.match(/\.(jpeg|jpg|gif|png)$/i)) {
            modalImage.src = currentMedia;
            modalImage.style.display = 'block';
            modalVideo.style.display = 'none';
        } else if (currentMedia.match(/\.(mp4|webm|ogg)$/i)) {
            modalVideo.src = currentMedia;
            modalVideo.style.display = 'block';
            modalImage.style.display = 'none';
        }
        $('#imageIndex').text(currentMediaIndex + 1); // Display current media index
        $('#totalImages').text(mediaArray.length); // Display total number of media
    }

    // Initial media display
    showCurrentMedia();

    // Navigation buttons
    $('#nextBtn').click(function () {
        currentMediaIndex = (currentMediaIndex + 1) % mediaArray.length;
        showCurrentMedia();
    });

    $('#prevBtn').click(function () {
        currentMediaIndex = (currentMediaIndex - 1 + mediaArray.length) % mediaArray.length;
        showCurrentMedia();
    });

    imageModal.modal('show');
}
