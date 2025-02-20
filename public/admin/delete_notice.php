<?php $page = "Notices"; ?>
<?php include_once('../template/admin/header.php'); ?>
<?php include_once('../template/admin/sidebar.php'); ?>
<?php include_once('../template/admin/navbar.php'); ?>

<?php
// Include your database configuration
// Adjust this include as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $notice_id = $_POST['notice_id'];
    
    // Function to delete an existing notification
    function deleteNotification($id) {
        global $conn;
        try {
            $sql = "DELETE FROM notifications WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
    
    if (deleteNotification($notice_id)) {
        header("Location: addnotice.php?status=success");
        exit();
    } else {
        header("Location: addnotice.php?status=error");
        exit();
    }
} else {
    // Handle other actions or redirect as needed
}
?>

<?php include_once('../template/admin/footer.php'); ?>