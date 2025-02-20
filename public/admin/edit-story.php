<?php
$page = "Edit Image Story";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');

// Function to get the list of uploaded music files
function getUploadedMusicFiles()
{
    $musicFiles = [];
    $musicDirectory = '../storage/music/';

    if ($handle = opendir($musicDirectory)) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != "..") {
                $musicFiles[] = $entry;
            }
        }
        closedir($handle);
    }

    return $musicFiles;
}

$uploadedMusicFiles = getUploadedMusicFiles();

// Initialize variables
$musicStoryId = $_GET['edit'] ?? null;
$existingMusicStory = [];

// Fetch existing data for the music story if editing
if ($musicStoryId) {
    $fetchMusicStoryStatement = $conn->prepare("SELECT * FROM stories_text_music_image WHERE id = ?");
    $fetchMusicStoryStatement->execute([$musicStoryId]);

    // Check if the query was successful
    if ($fetchMusicStoryStatement) {
        $existingMusicStory = $fetchMusicStoryStatement->fetch(PDO::FETCH_ASSOC);

        if (!$existingMusicStory) {
            // Handle the case where the music story with the given ID doesn't exist
            // You may want to show an error message or redirect to an error page
            echo "Music story not found.";
            exit;
        }
    } else {
        // Handle the case where the query was not successful
        // You may want to show an error message or redirect to an error page
        echo "Error fetching music story data.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Handle form submission, process data, and update the database

    // Assuming you have a switch to handle different form submissions
    switch ($_POST['form_type']) {
        case 'music_text_image':
            // Handle the form for editing music + text + image
            handleMusicTextImageFormSubmission($conn, $uploadedMusicFiles, $existingMusicStory, $musicStoryId);
            break;

        case 'edit_views':
            // Handle the form for editing views
            handleEditViewsFormSubmission($conn, $existingMusicStory, $musicStoryId);
            break;

        default:
            // Handle the default case or show an error
            break;
    }

    // Redirect after successful submission
    header('location: imagestory.php');
    exit(0);
}

function handleMusicTextImageFormSubmission($conn, $uploadedMusicFiles, $existingMusicStory, $musicStoryId)
{
    // Your existing code for handling music + text + image form submission
    // ...
    if (
        isset($_FILES['story_image']) &&
        isset($_POST['selected_music']) &&
        isset($_POST['bg_color']) &&
        isset($_POST['text_color']) &&
        isset($_POST['story_text'])
    ) {
        // Process form data and update the database
        $storyImageName = $_FILES['story_image']['name'];
        $storyImageTmpName = $_FILES['story_image']['tmp_name'];
        $selectedMusic = $_POST['selected_music'];
        $bg_color = $_POST['bg_color'];
        $text_color = $_POST['text_color'];
        $story_text = $_POST['story_text'];

        // Move the story image file to the storage folder
        if (!empty($storyImageName)) {
            $destination = '../storage/story/' . $storyImageName;
            move_uploaded_file($storyImageTmpName, $destination);
        } else {
            // If image is not edited, use the existing image name
            $storyImageName = $existingMusicStory['image_file_name'];
        }

        // Get music ID based on the selected music file
        $getMusicIdStatement = $conn->prepare("SELECT id FROM music WHERE file_name = ?");
        $getMusicIdStatement->execute([$selectedMusic]);
        $musicIdResult = $getMusicIdStatement->fetch(PDO::FETCH_ASSOC);

        if ($musicIdResult) {
            $musicId = $musicIdResult['id'];

            // Update data in the stories_text_music_image table
            $updateStatement = $conn->prepare("
                UPDATE stories_text_music_image
                SET music_id = ?, bg_color = ?, text_color = ?, story_text = ?, image_file_name = ?
                WHERE id = ?
            ");
            $updateStatement->execute([$musicId, $bg_color, $text_color, $story_text, $storyImageName, $musicStoryId]);

            // Redirect after successful submission
            header('location: imagestory.php');
            exit(0);
        } else {
            // Handle the case where the music file ID couldn't be found
            // You may want to show an error message or redirect to an error page
        }
    }

}

function handleEditViewsFormSubmission($conn, $existingMusicStory, $musicStoryId)
{
    if (isset($_POST['views'])) {
        $newViews = $_POST['views'];

        // Update the views count for the story
        $updateViewsStatement = $conn->prepare("UPDATE stories_text_music_image SET views = ? WHERE id = ?");
        $updateViewsStatement->execute([$newViews, $musicStoryId]);
          // Redirect after successful submission
          header('location: imagestory.php');
          exit(0);
      } else {
          // Handle the case where the music file ID couldn't be found
          // You may want to show an error message or redirect to an error page
      
    }
}
?>
<!-- Add dino-color-picker script -->
<script type="module" src="https://unpkg.com/dino-color-picker"></script>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Edit</strong> Image Story</h1>

        <!-- Section: Edit Music + Text + Image -->
        <div class="mb-5">
            <h2>Edit Music + Text + Image</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="music_text_image">
                <!-- ... (Existing form fields for music + text + image) ... -->
                <div class="mb-3">
                    <label class="form-label" for="selected_music_image">Select Music</label>
                    <select class="form-control" id="selected_music_image" name="selected_music" >
                        <?php foreach ($uploadedMusicFiles as $musicFile): ?>
                            <option value="<?php echo $musicFile; ?>" <?php echo ($existingMusicStory['music_id'] == $musicFile) ? 'selected' : ''; ?>><?php echo $musicFile; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="bg_color_image">Background Color</label>
                    <!-- Use dino-color-picker for background color -->
                    <input type="color" class="form-control" id="bg_color_image" name="bg_color" value="<?php echo $existingMusicStory['bg_color']; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="text_color_image">Text Color</label>
                    <!-- Use dino-color-picker for text color -->
                    <input type="color" class="form-control" id="text_color_image" name="text_color" value="<?php echo $existingMusicStory['text_color']; ?>" >
                </div>
                <div class="mb-3">
                    <label class="form-label" for="story_text_image">Story Text</label>
                    <textarea class="form-control" id="story_text_image" name="story_text" rows="5" ><?php echo $existingMusicStory['story_text']; ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="story_image">Update Image</label>
                    <input type="file" class="form-control" id="story_image" name="story_image" accept="image/*">
                </div>
                <div class="mb-3 current-image-box">
                    <label class="form-label">Current Image</label>
                    <?php if (!empty($existingMusicStory['image_file_name'])): ?>
                        <img src="../storage/story/<?php echo $existingMusicStory['image_file_name']; ?>" alt="Current Image" style="max-width: 100%;">
                    <?php else: ?>
                        <p>No image uploaded for this story.</p>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Selected Music File</label>
                    <?php echo $existingMusicStory['music_id']; ?>
                </div>

                <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>

        <!-- Section: Edit Views -->
        <div class="mb-5">
            <h2>Edit Views</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="edit_views">
                <div class="mb-3">
                    <label class="form-label" for="views">Enter New Views Count</label>
                    <input type="number" class="form-control" id="views" name="views" value="<?php echo $existingMusicStory['views']; ?>" >
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Save Views</button>
            </form>
        </div>
    </div>
</main>
<style>
    .current-image-box {
        max-width: 300px; /* Adjust the max-width as needed */
        margin-top: 10px; /* Add some margin for spacing */
    }

    .current-image-box img {
        max-width: 100%;
        height: auto;
    }
</style>

<?php include_once('../template/admin/footer.php'); ?>
