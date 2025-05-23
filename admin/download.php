<?php
session_start();
include('../db.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
    header('Location: ../account/login.php');
    exit();
}

if (isset($_GET['file'])) {
    $file_path = realpath('../uploads/' . basename($_GET['file']));

    // Validate the file path
    if ($file_path && file_exists($file_path) && strpos($file_path, realpath('../uploads/')) === 0) {
        // Set headers to force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));

        // Clear output buffer and read the file
        ob_clean();
        flush();
        readfile($file_path);
        exit();
    } else {
        echo "<div class='alert alert-danger'>Invalid file path.</div>";
    }
} else {
    echo "<div class='alert alert-danger'>No file specified.</div>";
}
?>
