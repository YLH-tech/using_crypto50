function showUploadedFile() {
    const fileInput = document.getElementById('file-upload');
    const previewImage = document.getElementById('imagePreview');
    const instructions = document.getElementById('instructions');
    const uploadButton = document.getElementById('upload-btn');

    // Check if a file was selected
    if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Set image source to selected file's data URL
            previewImage.src = e.target.result;
            previewImage.style.display = 'block';

            // Hide instructions and button
            instructions.style.display = 'none';
            uploadButton.style.display = 'none';
        }
        reader.readAsDataURL(fileInput.files[0]);
    }
}