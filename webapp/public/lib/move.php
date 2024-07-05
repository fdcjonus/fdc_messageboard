<?php
$result = false;

if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $targetDir = "../img/profile/"; // Directory where the file will be saved
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        // Check if file already exists
        if (!file_exists($targetFile)) {
            // Attempt to move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $result = true;
            } 
        } 
    }
}
return $result;
?>
