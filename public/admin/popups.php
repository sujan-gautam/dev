<?php
$page = "Popups";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // Check if the popup id is valid
    $statement = $conn->prepare("SELECT * FROM popups WHERE id = ?");
    $statement->execute(array($_GET['delete']));
    $total = $statement->rowCount();
    if ($total == 0 || $_GET['delete'] == 1) {
        header('location: popups.php');
        exit;
    } else {
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        if ($result['popup_image'] != '' && $result['popup_image'] != 'default.png') {
            unlink('../storage/popups/' . $result['popup_image']);
        }
        // Delete from popups Table
        $statement = $conn->prepare("DELETE FROM popups WHERE id = ?");
        $statement->execute(array($_GET['delete']));
        $_SESSION['success'] = 'Popup has been deleted';
        header('location: popups.php');
        exit(0);
    }
}
?>

<main class="content">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-md-6">
                <h1 class="h3 mb-3"><strong>All</strong> Popups</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="add-popup.php" class="btn btn-pill btn-primary float-right">
                    <i class="align-middle" data-feather="plus"></i> Add Popup
                </a>
                <a href="funpopups.php" class="btn btn-pill btn-primary float-right">
                    <i class="align-middle" data-feather="plus"></i> Add Fun Popup
                </a>
            </div> 
            
           
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table dataTable table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Popup Image</th>
                            <th>Popup Type</th>
                            <th>Popup Duration</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $statement = $conn->prepare('SELECT * FROM popups ORDER BY p_created DESC');
                        $statement->execute();
                        $popups = $statement->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($popups as $popup) {
                        ?>
                            <tr>
                                <td>
                                    <img src="../storage/popups/<?php echo clean($popup['popup_image']); ?>" width="100" height="100" class="rounded mx-auto d-block" alt="Popup Image">
                                </td>
                                <td><?php echo clean($popup['popup_type']); ?></td>
                                <td><?php echo clean($popup['popup_duration']); ?></td>
                                <td><?php echo date("M d, Y", strtotime($popup['p_created'])); ?></td>
                                <td>
                                    <a href="edit-popup.php?edit=<?php echo clean($popup['id']); ?>"><i class="align-middle" data-feather="edit-2"></i> </a>
                                    <?php if ($popup['id'] != 1) { ?>
                                        <a href="#" data-href="popups.php?delete=<?php echo clean($popup['id']); ?>" data-bs-toggle="modal" data-bs-target="#confirm-delete"><i class="align-middle" data-feather="trash"></i> </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- BEGIN primary modal -->
            <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Popup</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-3">
                            <p class="mb-0">Are you sure to delete this Popup?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <a class="btn btn-primary" href="#">Delete Popup</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END primary modal -->
        </div>
    </div>
</main>

<?php include_once('../template/admin/footer.php'); ?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
        // Handle delete popup
        $('#confirm-delete a.btn-primary').on('click', function () {
            var deleteUrl = $(this).attr('data-href');
            if (deleteUrl !== '#') {
                window.location.href = deleteUrl;
            }
        });

        // Show confirmation modal
        $('a[data-bs-toggle=modal][data-bs-target="#confirm-delete"]').on('click', function () {
            var deleteUrl = $(this).data('href');
            $('#confirm-delete a.btn-primary').attr('data-href', deleteUrl);
        });
    });
</script>
