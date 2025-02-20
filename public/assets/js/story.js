
    document.addEventListener('DOMContentLoaded', function () {
        const stories = document.querySelectorAll('.story');

        stories.forEach(story => {
            const imageExists = story.getAttribute('data-image-exists') === 'true';
            const imageFileName = story.getAttribute('data-image-file');
            const bgColor = story.getAttribute('data-bg-color');
            const outerLayer = story.querySelector('.outer-layer');

            if (imageExists && imageFileName !== 'default.png') {
                outerLayer.style.backgroundColor = '#0d0d0d';
            } else {
                outerLayer.style.backgroundColor = bgColor || 'transparent';
            }
        });
    });
document.addEventListener('DOMContentLoaded', function () {
    let backgroundAudio;
    let currentStoryIndex = 0;

    const closeBtn = document.getElementById('close-btn');
    const selectedStoryContainer = document.getElementById('selected-story-container');
    const selectedStoryText = document.getElementById('selected-story-text');
    const stories = document.querySelectorAll('.story');
    const swipeLeftBtn = document.getElementById('swipe-left');
    const swipeRightBtn = document.getElementById('swipe-right');
    const selectedViewCount = document.getElementById('selected-view-count');

    stories.forEach((story, index) => {
        story.addEventListener('click', function () {
            const bgColor = story.getAttribute('data-bg-color');
            const storyId = story.getAttribute('data-story-id');
            const viewCountSpan = story.querySelector('.view-count');

            // Fetch view count from the server (assuming you have a PHP script to retrieve it)
            fetch(`increment_view_count.php?story_id=${storyId}`)
                .then(response => response.json())
                .then(data => {
                    // Set the view count in the selected story container
                    selectedViewCount.textContent = story.querySelector('.view-count').textContent;
                })
                .catch(error => console.error('Error fetching view count:', error));

            const imageExists = story.getAttribute('data-image-exists') === 'true';
            const imageFile = story.getAttribute('data-image-file');
            const musicUrl = story.getAttribute('data-music-url');
            const storyText = story.querySelector('.story-text').textContent;
            const textColor = story.getAttribute('data-text-color');

            // Set styles for selected-story-text based on the image
            if (imageExists && imageFile !== 'default.png') {
                setCustomTextStyle(selectedStoryText);
            } else {
                setDefaultImageStyle(selectedStoryText);
            }

            // Set the selected story text
            selectedStoryText.textContent = storyText;
            selectedStoryText.style.backgroundColor = bgColor;
            selectedStoryText.style.color = textColor;

            // Show the selected story container
            selectedStoryContainer.style.visibility = 'visible';

            // Remove existing audio element
            removeBackgroundAudio();

            // Create a new audio element
            backgroundAudio = document.createElement('audio');
            backgroundAudio.src = musicUrl;
            backgroundAudio.autoplay = true;
            backgroundAudio.loop = true;

            // Append the new audio element to the story overlay
            document.querySelector('.story-overlay').appendChild(backgroundAudio);

            // Remove existing image element
            removeSelectedImage();

            // Add logic to handle image display
            if (imageExists && imageFile !== 'default.png') {
                // If it's not 'default.png', create and append the selected image
                createSelectedImage(`storage/story/${imageFile}`);
            } else {
                // If the image file name is 'default.png', create a colored box
                createImageBox(bgColor);
            }

            // Add logic to handle swipe and close buttons
            closeBtn.addEventListener('click', handleCloseButtonClick);
            swipeLeftBtn.addEventListener('click', handleSwipeLeftButtonClick);
            swipeRightBtn.addEventListener('click', handleSwipeRightButtonClick);

            currentStoryIndex = index;
            // Hide or show swipe buttons based on the current index
            handleSwipeButtonsVisibility();
        });
    });


    function handleCloseButtonClick() {
        // Pause and remove the background music when the story is closed
        removeBackgroundAudio();

        // Remove the selected image element, if it exists
        removeSelectedImage();

        // Clear the story text
        selectedStoryText.textContent = '';

        hideSelectedStory();
    }

    function hideSelectedStory() {
        // Clear the content of the selected story container
        selectedStoryContainer.style.visibility = 'hidden';

        // Remove event listeners to prevent potential memory leaks
        closeBtn.removeEventListener('click', handleCloseButtonClick);
        swipeLeftBtn.removeEventListener('click', handleSwipeLeftButtonClick);
        swipeRightBtn.removeEventListener('click', handleSwipeRightButtonClick);
    }

    function handleSwipeLeftButtonClick() {
        if (currentStoryIndex > 0) {
            currentStoryIndex--;
             // Update the view count in the overlay
             selectedViewCount.textContent = stories[currentStoryIndex].querySelector('.view-count').textContent;
            updateStoryContent();
        }
    }

    function handleSwipeRightButtonClick() {
        if (currentStoryIndex < stories.length - 1) {
            currentStoryIndex++;
             // Update the view count in the overlay
             selectedViewCount.textContent = stories[currentStoryIndex].querySelector('.view-count').textContent;
            updateStoryContent();
        }
    }

    function updateStoryContent() {
        const currentStory = stories[currentStoryIndex];

        // Update the story text, background color, and text color
        selectedStoryText.textContent = currentStory.querySelector('.story-text').textContent;
        selectedStoryText.style.backgroundColor = currentStory.getAttribute('data-bg-color');
        selectedStoryText.style.color = currentStory.getAttribute('data-text-color');

        // Set styles for selected-story-text based on the image
        const imageExists = currentStory.getAttribute('data-image-exists') === 'true';
        const imageFile = currentStory.getAttribute('data-image-file');

        if (imageExists && imageFile !== 'default.png') {
            setCustomTextStyle(selectedStoryText);
        } else {
            // For default.png, set width and height to 250px
            setDefaultImageStyle(selectedStoryText);
        }

        // Change the background music based on the current story
        const newMusicUrl = currentStory.getAttribute('data-music-url');
        backgroundAudio.src = newMusicUrl;

        // Remove existing image element
        removeSelectedImage();

        // Add logic to handle image display
        if (imageExists && imageFile !== 'default.png') {
            // If it's not 'default.png', create and append the selected image
            createSelectedImage(`storage/story/${imageFile}`);
        } else {
            // If the image file name is 'default.png', create a colored box
            createImageBox(currentStory.getAttribute('data-bg-color') || 'transparent');
        }

        // Always show text and background color
        selectedStoryContainer.style.visibility = 'visible';

        // Hide or show swipe buttons based on the current index
        handleSwipeButtonsVisibility();
    }

    function handleSwipeButtonsVisibility() {
        swipeLeftBtn.style.display = currentStoryIndex > 0 ? 'block' : 'none';
        swipeRightBtn.style.display = currentStoryIndex < stories.length - 1 ? 'block' : 'none';
    }

    function removeBackgroundAudio() {
        if (backgroundAudio) {
            backgroundAudio.pause();
            backgroundAudio.remove();
        }
    }

    function removeSelectedImage() {
        const selectedImage = selectedStoryContainer.querySelector('img');
        if (selectedImage) {
            selectedImage.remove();
        }
    }

    function createSelectedImage(src) {
        const selectedImage = document.createElement('img');
        selectedImage.src = src;
        selectedImage.alt = 'Selected Story Image';
        selectedImage.style.width = '250px';
        selectedImage.style.height = 'auto';
        selectedImage.style.objectFit = 'cover';
        selectedStoryContainer.appendChild(selectedImage);
        selectedStoryText.classList.add('custom-text-style');
    }

    function createImageBox(bgColor) {
        const imageBox = document.createElement('div');
        imageBox.style.backgroundColor = bgColor;
        imageBox.style.width = '100%';
        imageBox.style.height = '100%';
        imageBox.style.borderRadius = '10px';
        selectedStoryContainer.appendChild(imageBox);
        selectedStoryText.classList.remove('custom-text-style');
    }

    function setCustomTextStyle(element) {
        element.style.height = 'fit-content';
        element.style.minWidth = '250px';
        element.style.maxWidth = '250px';
        element.style.minHeight = 'fit-content';
    }

    function setDefaultImageStyle(element) {
        element.style.height = '250px';
        element.style.width = '250px';
        element.style.minWidth = '250px';
        element.style.maxWidth = '250px';
        element.style.minHeight = '250px';
    }
});