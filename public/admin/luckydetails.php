<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

    $page = "Lucky Details";
// Include your HTML header here
?>

<?php include_once('../template/admin/header.php'); ?>
<?php include_once('../template/admin/sidebar.php'); ?>
<?php include_once('../template/admin/navbar.php'); ?>

<?php
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    // Check if the id is valid
    $statement = $conn->prepare("SELECT * FROM discount_wheel WHERE id=?");
    $statement->execute(array($_GET['delete']));
    $total = $statement->rowCount();

    if ($total == 0 || $_GET['delete'] == 1) {
        header('location: luckydetails.php');
        exit;
    } else {
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Perform delete from discount_wheel table
        $statement = $conn->prepare("DELETE FROM discount_wheel WHERE id=?");
        $statement->execute(array($_GET['delete']));

        $_SESSION['success'] = 'Discount Wheel detail has been deleted';
        header('location: luckydetails.php');
        exit(0);
    }
}
?>

<main class="content">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-md-6">
                <h1 class="h3 mb-3"><strong>All</strong> Lucky Wheel Details</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <!-- Adjust link accordingly -->
                <a href="luckywheel.php" class="btn btn-pill btn-primary float-right">
                    <i class="align-middle" data-feather="eye"></i> See Discount Details
                </a>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table dataTable table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Discount Rate</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $statement = $conn->prepare('SELECT * FROM discount_wheel ORDER BY id DESC');
                        $statement->execute();
                        $discountWheels = $statement->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($discountWheels as $discountWheel) {
                            ?>
                            <tr>
                                <td><?php echo clean($discountWheel['name']); ?></td>
                                <td><?php echo clean($discountWheel['address']); ?></td>
                                <td><?php echo clean($discountWheel['phone']); ?></td>
                                <td><?php echo clean($discountWheel['discount_rate']); ?>%</td>
                                <td><?php echo date("M d, Y", strtotime($discountWheel['created_at'])); ?></td>
                                <td>
                                    <!-- <a href="#">Edit</a> -->
                                    <!-- Adjust link accordingly -->
                                    <a href="#" data-href="luckydetails.php?delete=<?php echo clean($discountWheel['id']); ?>"
                                       data-bs-toggle="modal" data-bs-target="#confirm-delete">Delete</a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- BEGIN primary modal -->
            <div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Delete Lucky Wheel Detail</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <div class="modal-body m-3">
                            <p class="mb-0">Are you sure to delete this Lucky Wheel Detail?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <a class="btn btn-primary">Delete Lucky Wheel Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END primary modal -->
        </div>
    </div>
</main>

<?php include_once('../template/admin/footer.php'); ?>
