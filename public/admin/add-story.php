<?php
// Include necessary files and configurations
$page = "Add Music Story";
include_once('../template/admin/header.php');
include_once('../template/admin/sidebar.php');
include_once('../template/admin/navbar.php');
include_once('../config/database.php');

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Handle form submission, process data, and save to the database

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

                // Redirect after successful submission
                header('location: story.php');
                exit(0);
            }
            break;

        case 'music_text_image':
            // Handle the form for adding music + text + image
            handleMusicTextImageFormSubmission($conn, $uploadedMusicFiles);
            break;

        default:
            // Handle default case or show an error
            break;
    }
}

function handleMusicTextImageFormSubmission($conn, $uploadedMusicFiles)
{
    if (
        isset($_POST['selected_music']) &&
        isset($_POST['bg_color']) &&
        isset($_POST['text_color']) &&
        isset($_POST['story_text'])
    ) {
        // Process form data and insert into the database
        $selectedMusic = $_POST['selected_music'];
        $bg_color = $_POST['bg_color'];
        $text_color = $_POST['text_color'];
        $story_text = $_POST['story_text'];

        // Set default image file name
        $storyImageName = 'default.png';

        // Check if an image is uploaded
        if (isset($_FILES['story_image']) && !empty($_FILES['story_image']['name'])) {
            // Move the story image file to the storage folder
            $storyImageName = $_FILES['story_image']['name'];
            $storyImageTmpName = $_FILES['story_image']['tmp_name'];
            $destination = '../storage/story/' . $storyImageName;
            move_uploaded_file($storyImageTmpName, $destination);
        }

        // Get music ID based on the selected music file
        $getMusicIdStatement = $conn->prepare("SELECT id FROM music WHERE file_name = ?");
        $getMusicIdStatement->execute([$selectedMusic]);
        $musicIdResult = $getMusicIdStatement->fetch(PDO::FETCH_ASSOC);

        if ($musicIdResult) {
            $musicId = $musicIdResult['id'];

            // Insert data into stories_text_music_image table
            $insertStatement = $conn->prepare("
                INSERT INTO stories_text_music_image (music_id, text_content, bg_color, text_color, story_text, image_file_name, upload_time)
                VALUES (?, '', ?, ?, ?, ?, current_timestamp())
            ");
            $insertStatement->execute([$musicId, $bg_color, $text_color, $story_text, $storyImageName]);

            // Redirect after successful submission
            header('location: story.php');
            exit(0);
        } else {
            // Handle the case where music file ID couldn't be found
            // You may want to show an error message or redirect to an error page
        }
    }
}
?>

<!-- Add dino-color-picker script -->
<script type="module" src="https://unpkg.com/dino-color-picker"></script>

<main class="content">
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3"><strong>Add</strong> Music Story</h1>

        <!-- Section: Add Just Music -->
        <div class="mb-5">
            <h2>Add Just Music</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="music">
                <div class="mb-3">
                    <label class="form-label" for="music_file">Upload Music File</label>
                    <input type="file" class="form-control" id="music_file" name="music_file" accept="audio/*" required>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Add Music</button>
            </form>
        </div>

        <!-- Section: Add Music + Text + Image -->
        <div class="mb-5">
            <h2>Add Music + Text + Image</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="music_text_image">
                <div class="mb-3">
                    <label class="form-label" for="selected_music_image">Select Music</label>
                    <select class="form-control" id="selected_music_image" name="selected_music" required>
                        <?php foreach ($uploadedMusicFiles as $musicFile): ?>
                            <option value="<?php echo $musicFile; ?>"><?php echo $musicFile; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="bg_color_image">Background Color</label>
                    <!-- Use dino-color-picker for background color -->
                    <input type="color" class="form-control" id="bg_color_image" name="bg_color" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="text_color_image">Text Color</label>
                    <!-- Use dino-color-picker for text color -->
                    <input type="color" class="form-control" id="text_color_image" name="text_color" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="story_text_image">Story Text</label>
                    <textarea class="form-control" id="story_text_image" name="story_text" rows="5" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="story_image">Upload Image</label>
                    <input type="file" class="form-control" id="story_image" name="story_image" accept="image/*">
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Add Music + Text + Image</button>
            </form>
        </div>
    </div>
</main>

<?php
include_once('../template/admin/footer.php');
?>
