var selectedFiles = []; // Array to store selected files

// Function to create preview for each selected or pasted file
function previewFiles(files) {
    var previewContainer = document.getElementById('imagePreview');

    files.forEach(function (file) {
        selectedFiles.push(file);

        var reader = new FileReader();

        reader.onload = function (e) {
            var fileContainer = document.createElement('div');
            fileContainer.className = 'image-container col-4 col-md-1';

            if (file.type.startsWith('image/')) {
                var image = document.createElement('img');
                image.className = 'preview-image';
                image.src = e.target.result;
                fileContainer.appendChild(image);
            } else if (file.type.startsWith('video/')) {
                var video = document.createElement('video');
                video.className = 'preview-video';
                video.src = e.target.result;
                video.controls = true;
                fileContainer.appendChild(video);
            }

            var closeButton = document.createElement('button');
            closeButton.className = 'close-button';
            closeButton.innerHTML = '&times;';
            closeButton.addEventListener('click', function () {
                // Remove the file container when the button is clicked
                fileContainer.remove();
                // Remove the corresponding file from the selectedFiles array
                var index = selectedFiles.indexOf(file);
                if (index !== -1) selectedFiles.splice(index, 1);
                // Update the file input element with the updated selected files
                updateFileInput();
            });

            fileContainer.appendChild(closeButton);
            previewContainer.appendChild(fileContainer);
        };

        reader.readAsDataURL(file);
    });

    updateFileInput();
}

// Function to update the file input element with the selected files
function updateFileInput() {
    var newFileList = new DataTransfer();
    selectedFiles.forEach(function (file) {
        newFileList.items.add(file);
    });
    document.getElementById('issue_image').files = newFileList.files;
}

// Handle file selection through file input
document.getElementById('issue_image').addEventListener('change', function (event) {
    var files = Array.from(event.target.files);
    previewFiles(files);
});

// Handle pasting of images
document.addEventListener('paste', function (event) {
    var items = event.clipboardData.items;
    var newFiles = [];

    for (var i = 0; i < items.length; i++) {
        if (items[i].type.startsWith('image/')) {
            var blob = items[i].getAsFile();
            var file = new File([blob], `pasted_${Date.now()}.jpg`, { type: blob.type });
            newFiles.push(file);
        }
    }

    if (newFiles.length > 0) {
        previewFiles(newFiles);
    }
});