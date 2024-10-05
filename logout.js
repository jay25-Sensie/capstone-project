
function confirmLogout(event) {
    // Prevent the default action of the link
    event.preventDefault();
    
    // Show confirmation dialog
    if (confirm("Are you sure you want to logout?")) {
        // If confirmed, redirect to logout.php
        window.location.href = "logout.php";
    }
}
