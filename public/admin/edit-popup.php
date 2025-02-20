<?php
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

// Check if the 'edit' parameter is set and is numeric
if (!isset($_GET['edit']) || !is_numeric($_GET['edit'])) {
    header('location: edit-popup.php');
    exit;
} else {
    $statement = $conn->prepare("SELECT * FROM popups WHERE id = ?");
    $statement->execute(array($_GET['edit']));
    $total = $statement->rowCount();

    if ($total == 0) {
        header('location: edit-popup.php');
        exit;
    } else {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        extract($result, EXTR_PREFIX_ALL, "edit");
    }
}

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $valid = 1;
    $popup_type = clean($_POST['popup_type']);
    $popup_duration = clean($_POST['popup_duration']);

    // if (isset($_POST['popup_status'])) {
    //     $popup_status = 1;
    // } else {
    //     $popup_status = 0;
    // }

    // Check if fields are empty
    if (empty($popup_type)) {
        $valid = 0;
        $errors[] = 'Please Enter Popup Type';
    }
    if (empty($popup_duration)) {
        $valid = 0;
        $errors[] = 'Please Enter Popup Duration';
    }

    // Check if an image is uploaded
    if (!empty($_FILES['popup_image']['name'])) {
        $popup_image = $_FILES['popup_image']['name'];
        $popup_image_tmp = $_FILES['popup_image']['tmp_name'];

        $popup_image_ext = pathinfo($popup_image, PATHINFO_EXTENSION);

        if (!in_array($popup_image_ext, array('jpg', 'jpeg', 'png', 'gif'))) {
            $valid = 0;
            $errors[] = 'You must upload a jpg, jpeg, gif, or png file.';
        }
    } else {
        $popup_image = $edit_popup_image;
    }

    if ($valid == 1) {
        // Upload the image if it's not empty
        if (!empty($_FILES['popup_image']['name'])) {
            $popup_image_file = '../popup-image-' . time() . '.' . $popup_image_ext;
            move_uploaded_file($popup_image_tmp, '../storage/popups/' . $popup_image_file);
        } else {
            $popup_image_file = $edit_popup_image;
        }

        // Update the popup data in the database
        $update = $conn->prepare("UPDATE popups SET popup_type = ?, popup_duration = ?, popup_image = ? WHERE id = ?");
        $update->execute(array($popup_type, $popup_duration, $popup_image_file,  $edit_id));

        // Redirect to the popups page after updating
        $_SESSION['success'] = 'Popup has been updated successfully!';
        header('location: popups.php');
        exit(0);
    }
}
?>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Edit</strong> Popup</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-12 col-lg-6 d-flex">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Popup Info</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label" for="inputType">Popup Type</label>
                                <input type="text" class="form-control" id="inputType" placeholder="Enter Popup Type" name="popup_type" value="<?php echo clean($edit_popup_type); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="inputDuration">Popup Duration</label>
                                <input type="text" class="form-control" id="inputDuration" placeholder="Enter Popup Duration" name="popup_duration" value="<?php echo clean($edit_popup_duration); ?>">
                            </div>
                            <!-- <div class="mb-3">
                                <label class="form-label" for="popupStatus">Popup Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="popupStatus" <?php if($edit_popup_status == 1){echo 'checked=""';} ?> name="popup_status">
                                </div> -->
                            <!-- </div> -->
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 d-flex">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Popup Image</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center">
                                <img alt="Popup Image" src="../storage/popups/<?php echo clean($edit_popup_image); ?>" class="rounded mx-auto d-block" width="100" height="100" id="popupImg">
                                <div class="mt-2">
                                    <button type="button" class="btn btn-primary">Choose Image
                                        <input type="file" class="file-upload edit-file" value="Upload" name="popup_image" onchange="previewFile(this);" accept="image/*">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <button type="submit" name="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<?php include_once('../template/admin/footer.php'); ?>
