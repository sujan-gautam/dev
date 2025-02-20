
function showVisitorInfo() {
    var eyeIcon = document.getElementById("eyeIcon");
    eyeIcon.className = "fa-regular fa-eye";

    var screenWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;

    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                var info = JSON.parse(xhr.responseText);
                info.deviceType = getDeviceType(screenWidth); // Update device type
                displayPopup(info);
            } catch (error) {
                console.error("Error parsing JSON:", error);
            }
        }
    };
    xhr.open('GET', 'get_visitor_info.php?width=' + screenWidth, true);
    xhr.send();
}

function getDeviceType(screenWidth) {
    if (screenWidth < 768) {
        return 'Mobile';
    } else if (screenWidth < 1024) {
        return 'Tablet';
    } else {
        return 'Desktop';
    }
}

function displayPopup(info) {
    var popup = document.getElementById('popupTracker');
    var popupContent = document.getElementById('popupContentTracker');

    popupContent.innerHTML = `
        <div class="info-containerTracker">
            <strongTracker>IP:</strongTracker> ${info.ip}<br>
            <strongTracker>Device Type:</strongTracker> ${info.deviceType}<br>
            <strongTracker>Browser:</strongTracker> ${info.browser}<br>
        </div>
    `;
    popup.style.display = 'none';
}

function closeTrackerPopup() {
    var eyeIcon = document.getElementById("eyeIcon");
    eyeIcon.className = "fa-regular fa-eye-slash";

    var popup = document.getElementById('popupTracker');
    popup.style.display = 'none';
}

// Automatically trigger showVisitorInfo when the page loads
window.onload = function () {
    showVisitorInfo();
};