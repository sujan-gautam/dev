<?php
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');
// Include your database connection file here
// include_once('../path/to/your/database/connection.php');

// Make sure $conn is a valid PDO connection
if (!isset($conn)) {
    die("Database connection is not established.");
}

// Check if the request is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the visitor ID from the POST data
    $visitorId = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($visitorId > 0) {
        // Fetch trace details from the database based on the visitor ID
        $statement = $conn->prepare('SELECT * FROM visitor_data WHERE id = :id');
        $statement->bindParam(':id', $visitorId, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        // Check if a result is found
        if ($result) {
            // Return trace details as JSON
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        } else {
            // If no result is found, return an error
            header('HTTP/1.1 404 Not Found');
            echo json_encode(array('error' => 'Visitor not found'));
            exit;
        }
    } else {
        // If the visitor ID is not valid, return an error
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(array('error' => 'Invalid visitor ID'));
        exit;
    }
} else {
    // If the request method is not POST, return an error
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(array('error' => 'Method not allowed'));
    exit;
}
