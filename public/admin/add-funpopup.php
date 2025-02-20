<?php
$page = "Add FunPopup";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"]) && isset($_POST["caption"]) && isset($_POST["link"])) {
    $image = $_FILES["image"];
    $caption = $_POST["caption"];
    $link = $_POST["link"];

    // Upload image
    $targetDirectory = "../storage/funpopups/";
    $targetFile = $targetDirectory . basename($image["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a valid image
    $check = getimagesize($image["tmp_name"]);
    if ($check !== false) {
        // Allow only certain image file formats
        if ($imageFileType == "jpg" || $imageFileType == "jpeg" || $imageFileType == "png") {
            if (move_uploaded_file($image["tmp_name"], $targetFile)) {
                // Insert entry into the database
                $sql = "INSERT INTO funpopups (image_filename, caption, link) VALUES ('$image[name]', '$caption', '$link')";
                if ($conn->query($sql) === TRUE) {
                    $_SESSION['success'] = 'FunPopup added successfully.';
                    header('location: funpopups.php');
                    exit;
                } else {
                    $errorInfo = $conn->errorInfo();
                    $_SESSION['error'] = 'Error adding FunPopup: ' . $errorInfo[2];
                }
            } else {
                $_SESSION['error'] = 'Error uploading image.';
            }
        } else {
            $_SESSION['error'] = 'Invalid image file format. Only JPG, JPEG, and PNG are allowed.';
        }
    } else {
        $_SESSION['error'] = 'Invalid image file.';
    }
}
?>

<main class="content">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-md-6">
                <h1 class="h3 mb-3"><strong>Add</strong> FunPopup</h1>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="image" class="form-label">Image:</label>
                        <input type="file" name="image" id="image" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="caption" class="form-label">Caption:</label>
                        <textarea name="caption" id="caption" rows="4" cols="50" class="form-control" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="link" class="form-label">Link:</label>
                        <input type="url" name="link" id="link" class="form-control">
                    </div>

                    <input type="submit" value="Add FunPopup" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</main>

<?php include_once('../template/admin/footer.php'); ?>
