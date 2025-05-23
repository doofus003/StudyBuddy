<?php
// Check if a session is already active before starting a new one
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['username']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
    header('Location: ../account/login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../pretty/home.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .sidebar {
            background-color: #343a40;
            color: #fff;
            height: 100%;
            padding: 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
            flex-grow: 1;
        }
        .sidebar h5 {
            color: #ffc107;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            margin-bottom: 10px;
            transition: color 0.3s;
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .sidebar .nav-link.active {
            color: #fff;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="home.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../account/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <h5>Admin Menu</h5>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="home.php">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users.php">Manage Users</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="study_groups.php">Manage Study Groups</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="courses.php">Manage Courses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="meetings.php">Manage Meetings</a>
            </li>
        </ul>
    </div>
    <div class="content">
