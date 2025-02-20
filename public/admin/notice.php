<?php include_once('../template/admin/header.php'); ?>
<?php include_once('../template/admin/sidebar.php'); ?>
<?php include_once('../template/admin/navbar.php'); ?>
<?php
// Database connection and other includes

// Function to get a notification by ID
function getNotificationById($id) {
    global $conn;
    $sql = "SELECT * FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Function to update a notification by ID
function updateNotification($id, $message) {
    global $conn;
    $sql = "UPDATE notifications SET message = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $message, $id);

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Function to fetch and display notices
function displayNotices() {
    global $conn; // Make sure $conn is available in the function
    try {
        $sql = "SELECT * FROM notifications";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $notices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($notices as $notice) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($notice['message']) . '</td>';
            echo '<td>';
            echo '<a href="edit-notice.php?id=' . $notice['id'] . '"><i class="align-middle" data-feather="edit-2"></i></a>';
            echo '</td>';
            echo '<td>';
            echo '<form method="POST" action="addnotice.php">';
            echo '<input type="hidden" name="notice_id" value="' . $notice['id'] . '">';
            echo '<button type="submit" name="action" value="delete">Delete</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Add</strong> Notices</h1>

        <!-- Display existing notices -->
        <table class="table dataTable table-striped table-hover">
            <thead>
                <tr>
                    <th>Message</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php displayNotices(); ?>
            </tbody>
        </table>

        <!-- Notification form -->
        <form method="POST" action="addnotice.php">
            <label for="notification">Notification Message:</label>
            <input type="text" id="notification" name="notification" required>
            <button type="submit" class="btn btn-primary">Add Notification</button>
        </form>

        <?php
        // Display status messages, if any
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'success') {
                echo '<p style="color: green;">Notification added successfully!</p>';
            } elseif ($_GET['status'] === 'error') {
                echo '<p style="color: red;">Error adding notification.</p>';
            }
        }
        ?>
    </div>
</main>

<?php include_once('../template/admin/footer.php'); ?>
