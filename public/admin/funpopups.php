<?php
$page = "FunPopups";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // Check if the popup id is valid or not
    $statement = $conn->prepare("SELECT * FROM funpopups WHERE id=?");
    $statement->execute(array($_GET['delete']));
    $total = $statement->rowCount();

    if ($total == 0) {
        header('location: funpopups.php');
        exit;
    } else {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        // You may need to add code here to handle deleting files associated with the popup
        // (e.g., unlink('uploads/'.$result['image_filename']);)
        
        // Delete from funpopups Table
        $statement = $conn->prepare("DELETE FROM funpopups WHERE id=?");
        $statement->execute(array($_GET['delete']));
        $_SESSION['success'] = 'FunPopup has been deleted';
        header('location: funpopups.php');
        exit(0);
    }
}

?>

<main class="content">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-md-6">
                <h1 class="h3 mb-3"><strong>All</strong> Fun Popups</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <!-- Add Popup button link -->
                <a href="add-funpopup.php" class="btn btn-pill btn-primary float-right">
                    <i class="align-middle" data-feather="plus"></i> Add FunPopup
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table dataTable table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Popup Image</th>
                            <th>Popup Caption</th>
                            <th>Popup Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $statement = $conn->prepare('SELECT * FROM funpopups ORDER BY id DESC');
                        $statement->execute();
                        $funpopups = $statement->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($funpopups as $funpopup) {
                        ?>
                            <tr>
                                <td>
                                    <img src="../storage/funpopups/<?php echo clean($funpopup['image_filename']); ?>" width="100" height="100" class="rounded mx-auto d-block" alt="Popup Image">
                                </td>
                                <td><?php echo clean($funpopup['caption']); ?></td>
                                <td><a class="link" href="<?php echo clean($funpopup['link']); ?>" target="_blank">Go to Link</a></td>
                                <td>
                                    <!-- Edit Popup link -->
                                    <a href="edit-funpopup.php?edit=<?php echo clean($funpopup['id']); ?>">
                                        <i class="align-middle" data-feather="edit-2"></i>
                                    </a>
                                    <?php if ($funpopup['id'] != 1) { ?>
                                        <!-- Delete Popup link -->
                                        <a href="#" data-href="funpopups.php?delete=<?php echo clean($funpopup['id']); ?>" data-bs-toggle="modal" data-bs-target="#confirm-delete">
                                            <i class="align-middle" data-feather="trash"></i>
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Popup Confirmation Modal -->
        <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete FunPopup</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body m-3">
                        <p class="mb-0">Are you sure to delete this FunPopup?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a class="btn btn-primary" href="#">Delete FunPopup</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include_once('../template/admin/footer.php'); ?>
