function showUploadedFile() {
    let dropArea = document.querySelector(".drop_box");
    let file_upload = document.getElementById('file-upload');
    let drop_box = document.getElementById('drop-box');

    if(file_upload.files.length > 0) {
        var fileName = file_upload.files[0].name;
        drop_box.textContent = `Successfully Uploaded file - ${fileName}`;
    } else {
        window.alert("Unsuccessfully!");
    }

}