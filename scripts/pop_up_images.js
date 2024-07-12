//<!--script pop up show image -->

    function showImage(images) {
        var imageArray = images.split(',');
        var imageModal = $('#imageModal');
        var modalImage = document.getElementById('modalImage');
        var currentImageIndex = 0;

        function showCurrentImage() {
            modalImage.src = imageArray[currentImageIndex];
            $('#imageIndex').text(currentImageIndex + 1); // Display current image index
            $('#totalImages').text(imageArray.length); // Display total number of images
        }

        // Initial image display
        showCurrentImage();

        // Navigation buttons
        $('#nextBtn').click(function() {
            currentImageIndex = (currentImageIndex + 1) % imageArray.length;
            showCurrentImage();
        });

        $('#prevBtn').click(function() {
            currentImageIndex = (currentImageIndex - 1 + imageArray.length) % imageArray.length;
            showCurrentImage();
        });

        imageModal.modal('show');
    }
