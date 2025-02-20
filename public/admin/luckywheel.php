<?php
    $page = "Lucky Wheel";
    include_once('../template/admin/header.php');
    include_once('../template/admin/sidebar.php');
    include_once('../template/admin/navbar.php');

    if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
        // Check if the user id is valid
        $statement = $conn->prepare("SELECT * FROM discount_results WHERE id=?");
        $statement->execute(array($_GET['delete']));
        $total = $statement->rowCount();

        if ($total > 0) {
            // Delete from discount_results Table
            $statement = $conn->prepare("DELETE FROM discount_results WHERE id=?");
            $statement->execute(array($_GET['delete']));
            $_SESSION['success'] = 'User has been deleted';
        }
    }

    // Fetch user data from the database
    $statement = $conn->prepare('SELECT * FROM discount_results ORDER BY id DESC');
    $statement->execute();
    $users = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="content">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-md-12">
                <h1 class="h3 mb-3"><strong>All</strong> Users</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <!-- Adjust link accordingly -->
                <a href="luckydetails.php" class="btn btn-pill btn-primary float-right">
                    <i class="align-middle" data-feather="eye"></i> See User Details
                </a>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <table class="table dataTable table-striped table-hover">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>User Name</th>
                            <th>Date Created</th>
                            <th>Discount Percentage</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user) { ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['user_name']; ?></td>
                                <td><?php echo date("M d, Y", strtotime($user['created_at'])); ?></td>
                                <td><?php echo $user['discount_percentage']; ?>%</td>
                                <td>
                                    <!-- Add delete icon with a link to delete action -->
                                    <a href="luckywheel.php?delete=<?php echo $user['id']; ?>">
                                        <i class="align-middle" data-feather="trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include_once('../template/admin/footer.php'); ?>
