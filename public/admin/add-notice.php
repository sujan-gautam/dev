<?php
// Include your database configuration
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php'); // Adjust this include as needed

// Function to add a new notification
function addNotification($message) {
    global $conn;
    try {
        $sql = "INSERT INTO notifications (message) VALUES (:message)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);

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

// Function to edit an existing notification
function editNotification($id, $message) {
    global $conn;
    try {
        $sql = "UPDATE notifications SET message = :message WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        // Editing an existing notification
        $id = $_POST['notice_id'];
        $new_message = $_POST['new_message'];
        if (editNotification($id, $new_message)) {
            header("Location: add-notice.php?status=success");
            exit();
        } else {
            header("Location: add-notice.php?status=error");
            exit();
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        // Deleting an existing notification
        $id = $_POST['notice_id'];
        if (deleteNotification($id)) {
            header("Location: add-notice.php?status=success");
            exit();
        } else {
            header("Location: add-notice.php?status=error");
            exit();
        }
    } else {
        // Adding a new notification
        $message = $_POST['notification'];
        if (addNotification($message)) {
            header("Location: add-notice.php?status=success");
            exit();
        } else {
            header("Location: add-notice.php?status=error");
            exit();
        }
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
            echo '<form method="POST" action="add-notice.php">';
            echo '<input type="hidden" name="notice_id" value="' . $notice['id'] . '">';
            echo '<input type="text" name="new_message" value="' . htmlspecialchars($notice['message']) . '">';
            echo '<button type="submit" name="action" value="edit">Edit</button>';
            echo '</form>';
            echo '</td>';
            echo '<td>';
            echo '<form method="POST" action="add-notice.php">';
            echo '<input type="hidden" name="notice_id" value="' . $notice['id'] . '">';
            echo '<button type="submit" name="action" value="delete" onclick="return confirm(\'Are you sure you want to delete this notice?\');">Delete</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <!-- Include your HTML head content here -->
</head>
<body>
    <?php include_once('../template/admin/header.php'); ?>
    <?php include_once('../template/admin/sidebar.php'); ?>
    <?php include_once('../template/admin/navbar.php'); ?>

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
            <form method="POST" action="add-notice.php">
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
</body>
</html>
