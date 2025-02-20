
function closePopup(element) {
    var popup = element.parentNode;
    popup.style.opacity = '0'; // fade out the popup
    setTimeout(function () {
        popup.parentNode.removeChild(popup);
    }, 0); // wait for the fade-out animation to complete

    var overlay = document.querySelector('.overlay');
    overlay.style.display = 'none';
    document.body.style.overflow = 'auto'; // Enable scrolling on the body when the popup is closed
}

function openPopup() {
    var overlay = document.querySelector('.overlay');
    overlay.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Disable scrolling on the body when the popup is open
    var popups = document.querySelectorAll('.popup');
    if (popups.length > 0) {
        popups.forEach(function (popup) {
            popup.style.opacity = '1'; // fade in the popup
        });
    }
}

window.addEventListener('load', openPopup);

// <!-- Main Popup Script -->

// JavaScript function to close the main popup
function closePopupNew(element) {
    var popUp = element.parentNode;
    popUp.style.display = 'none';
    document.getElementById('mainContent').classList.remove('blurryNew');
}

// Add blur effect to the main content when the main popup is displayed
document.querySelector('.pop-upNew').addEventListener('animationstart', function () {
    document.getElementById('mainContent').classList.add('blurryNew');
});

// Remove blur effect when the main popup is closed
document.querySelector('.pop-upNew').addEventListener('animationend', function () {
    document.getElementById('mainContent').classList.remove('blurryNew');
});
