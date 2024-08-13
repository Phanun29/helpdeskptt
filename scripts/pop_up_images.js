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
