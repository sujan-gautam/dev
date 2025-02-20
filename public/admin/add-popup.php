
<?php
$page = "Add Popup";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

if (isset($_POST['submit'])) {
    $valid = 1;
    $popup_type = clean($_POST['popup_type']);
    $popup_duration = clean($_POST['popup_duration']);

    $p_created = date('Y-m-d H:i:s');

    $statement = $conn->prepare('SELECT * FROM popups WHERE popup_type = ?');
    $statement->execute(array($popup_type));
    $total = $statement->rowCount();
    if ($total > 0) {
        $valid = 0;
        $errors[] = 'This Popup type is already registered.';
    }

    // Check if fields are empty
    if (empty($popup_type)) {
        $valid = 0;
        $errors[] = 'Please enter Popup Type';
    }

    // Check Popup Image
    $popup_image = $_FILES['popup_image']['name'];
    $popup_image_tmp = $_FILES['popup_image']['tmp_name'];

    if ($popup_image != '') {
        $popup_image_ext = pathinfo($popup_image, PATHINFO_EXTENSION);
        $file_name = basename($popup_image, '.' . $popup_image_ext);

        if ($popup_image_ext != 'jpg' && $popup_image_ext != 'png' && $popup_image_ext != 'jpeg' && $popup_image_ext != 'gif') {
            $valid = 0;
            $errors[] = 'You must upload a jpg, jpeg, gif, or png file<br>';
        }
    }

    // If everything is OK
    if ($valid == 1) {
        // Upload Popup Image if available
        if ($popup_image != '') {
            $popup_image_file = 'popup-image-' . time() . '.' . $popup_image_ext;
            move_uploaded_file($popup_image_tmp, '../storage/popups/' . $popup_image_file);
        } else {
            $popup_image_file = "default.png";
        }

        // Insert the data
        $insert = $conn->prepare("INSERT INTO popups (popup_type, popup_duration, popup_image, p_created) VALUES(?,?,?,?)");
        $insert->execute(array($popup_type, $popup_duration, $popup_image_file, $p_created));

        $_SESSION['success'] = 'Popup has been added successfully!';
        header('location: popups.php');
        exit(0);
    }
}
?>


<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Add</strong> Popup</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Popup info -->
                <div class="col-12 col-lg-4 d-flex">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Popup info</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label" for="popupType">Popup Type</label>
                                        <input type="text" class="form-control" id="popupType" placeholder="Enter Popup Type" name="popup_type">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="popupDuration">Popup Duration</label>
                                        <input type="text" class="form-control" id="popupDuration" placeholder="Enter Popup Duration" name="popup_duration">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Popup Image -->
                <div class="col-12 col-lg-4 d-flex">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Popup Image</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <img alt="Popup Image" src="../storage/popups/default.png" class="rounded mx-auto d-block" width="100" height="100" id="popupImg">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-primary">Choose Image
                                                <input type="file" class="file-upload" value="Upload" name="popup_image" onchange="previewFile(this);" accept="image/*">
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" name="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include_once('../template/admin/footer.php'); ?>
