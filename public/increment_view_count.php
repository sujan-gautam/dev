<?php
// increment_view_count.php

include("config/database.php");
// increment_view_count.php

if (isset($_GET['story_id'])) {
    $storyId = $_GET['story_id'];

    // Perform the increment and fetch the updated view count
    $conn->query("UPDATE stories_text_music_image SET views = views + 1 WHERE id = $storyId");
    $statement = $conn->query("SELECT views FROM stories_text_music_image WHERE id = $storyId");
    $views = $statement->fetch(PDO::FETCH_COLUMN);

    // Return the updated view count as JSON
    echo json_encode(['views' => $views]);
} else {
    // Handle the case where story_id is not provided
    echo json_encode(['error' => 'Story ID not provided']);
}
?>
