<?php
$page = "Image Stories";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $storyId = $_GET['delete'];

    // Check if the story ID is valid
    $getStoryStatement = $conn->prepare("SELECT * FROM stories_text_music_image WHERE id = ?");
    $getStoryStatement->execute([$storyId]);
    $storyData = $getStoryStatement->fetch(PDO::FETCH_ASSOC);

    if ($storyData) {
        // Delete the story image file from the storage
        $storyImagePath = '../storage/story/' . $storyData['image_file_name'];
        if (file_exists($storyImagePath)) {
            unlink($storyImagePath);
        }

        // Delete the record from the stories_text_music_image table
        $deleteStoryStatement = $conn->prepare("DELETE FROM stories_text_music_image WHERE id = ?");
        $deleteStoryStatement->execute([$storyId]);

        $_SESSION['success'] = 'Story has been deleted';
        header('location: story.php');
        exit(0);
    } else {
        // Invalid story ID, redirect or show an error message
        $_SESSION['error'] = 'Invalid Story ID';
        header('location: story.php');
        exit(0);
    }
}

// Handle form submission for music, text, and image story types
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Assuming you have a switch to handle different form submissions
    switch ($_POST['form_type']) {
        case 'music':
            // Handle the form for adding just music
            if (isset($_FILES['music_file'])) {
                $musicFileName = $_FILES['music_file']['name'];
                $musicFileTmpName = $_FILES['music_file']['tmp_name'];

                // Move the file to the storage folder
                $destination = '../storage/music/' . $musicFileName;
                move_uploaded_file($musicFileTmpName, $destination);

                // Insert the file details into the music table
                $insertMusicStatement = $conn->prepare("INSERT INTO music (file_name) VALUES (?)");
                $insertMusicStatement->execute([$musicFileName]);

                // Store the music file name in a session variable
                $_SESSION['added_music_file'] = $musicFileName;

                // Redirect after successful submission
                header('location: story.php');
                exit(0);
            }
            break;

        // Add cases for text and image story types if needed

        default:
            // Handle default case or show an error
            break;
    }
}
?>

<main class="content">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-md-6">
                <h1 class="h3 mb-3"><strong>All</strong> Image Stories</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="add-story.php" class="btn btn-pill btn-primary float-right">
                    <i class="align-middle" data-feather="plus"></i> Add Image Story
                </a>
            </div>
        </div>

        <div class="card-body">
            <h2>Image Stories</h2>
            <table class="table dataTable table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Story Image</th>
                        <th>BG Color</th>
                        <th>Text Color</th>
                        <th>Story Text</th>
                        <th>Music File Name</th>
                        <th>Views</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $statement = $conn->prepare('SELECT * FROM stories_text_music_image ORDER BY id DESC');
                    $statement->execute();
                    $imageStories = $statement->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($imageStories as $imageStory) {
                        // Fetch music data for the current image story
                        $musicStatement = $conn->prepare('SELECT file_name FROM music WHERE id = ?');
                        $musicStatement->execute([$imageStory['music_id']]);
                        $musicData = $musicStatement->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <tr>
                            <td><?php echo clean($imageStory['id']); ?></td>
                            <td>
                                <?php
                                if (!empty($imageStory['image_file_name'])) {
                                    echo '<img src="../storage/story/' . clean($imageStory['image_file_name']) . '" alt="Story Image" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">';
                                } else {
                                    echo '<img src="../storage/story/default.png" alt="Default Image" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">';
                                }
                                ?>
                            </td>
                            <td style="background-color: <?php echo clean($imageStory['bg_color']); ?>"><?php echo clean($imageStory['bg_color']); ?></td>
                            <td style="color: <?php echo clean($imageStory['text_color']); ?>"><?php echo clean($imageStory['text_color']); ?></td>
                            <td><?php echo clean($imageStory['story_text']); ?></td>
                            <td><?php echo clean($musicData['file_name']); ?></td>
                            <td><?php echo clean($imageStory['views']); ?></td>
                            <td><?php echo date("M d, Y", strtotime($imageStory['upload_time'])); ?></td>
                            <td>
                                <a href="edit-story.php?edit=<?php echo clean($imageStory['id']); ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="#" data-href="story.php?delete=<?php echo clean($imageStory['id']); ?>" data-bs-toggle="modal" data-bs-target="#confirm-delete" class="btn btn-sm btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>

            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="card-body">
                <?php
                if (isset($_SESSION['added_music_file'])) {
                    echo '<p class="alert alert-success">Added music file: ' . $_SESSION['added_music_file'] . '</p>';
                    unset($_SESSION['added_music_file']);
                }
                ?>
                <h2>Music Collection</h2>
                <table class="table dataTable table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Music File Name</th>
                            <th>Play Music</th>
                            <th>Date Added</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $musicStatement = $conn->prepare('SELECT * FROM music ORDER BY id DESC');
                        $musicStatement->execute();
                        $musicFiles = $musicStatement->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($musicFiles as $musicFile) {
                            ?>
                            <tr>
                                <td><?php echo clean($musicFile['id']); ?></td>
                                <td><?php echo clean($musicFile['file_name']); ?></td>
                                <td>
                                    <audio controls>
                                        <source src="../storage/music/<?php echo clean($musicFile['file_name']); ?>" type="audio/mpeg">
                                        Your browser does not support the audio element.
                                    </audio>
                                </td>
                                <td><?php echo date("M d, Y", strtotime($musicFile['upload_time'])); ?></td>
                                <td>
                                    <!-- You can add actions for music here if needed -->
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- BEGIN primary modal -->
<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Image Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body m-3">
                <p class="mb-0">Are you sure to delete this Image Story?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a class="btn btn-primary" href="#" id="delete-btn" title="Delete">Delete Image Story</a>
            </div>
        </div>
    </div>
</div>
<!-- END primary modal -->

<script>
    // Set the href attribute dynamically when modal is shown
    $('#confirm-delete').on('show.bs.modal', function (e) {
        var href = $(e.relatedTarget).data('href');
        $("#delete-btn").attr("href", href);
    });

    // Add event listener to delete button in the modal
    $('#delete-btn').on('click', function () {
        // Perform the delete action here if needed
        // For example, you can use AJAX to send a delete request to the server
        alert('Delete button clicked. Implement your delete logic here.');
        $('#confirm-delete').modal('hide'); // Hide the modal after the action
    });
</script>

<?php include_once('../template/admin/footer.php'); ?>
