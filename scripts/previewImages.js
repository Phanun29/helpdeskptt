var selectedFiles = []; // Array to store selected files

// Function to create preview for each selected file
function previewImages(event) {
    var previewContainer = document.getElementById('imagePreview');
    var files = event.target.files;
    var newFiles = Array.from(files); // Convert FileList to array

    // Add newly selected files to the selectedFiles array
    newFiles.forEach(function (file) {
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

// Bind previewImages function to file input change event
document.getElementById('issue_image').addEventListener('change', previewImages);

// Handle form submission to save images
document.getElementById('imageForm').addEventListener('submit', function (event) {
    event.preventDefault();

    // Simulate saving all selected files (for demonstration purposes)
    console.log("Selected Files:", selectedFiles);
    // Here you would typically submit the form using AJAX or other methods
    // to save the selected files on the server.

    // Reset selectedFiles array for next submission
    selectedFiles = [];

    // Clear previews after saving (optional)
    document.getElementById('imagePreview').innerHTML = '';
    // Clear file input
    document.getElementById('issue_image').value = '';
});
