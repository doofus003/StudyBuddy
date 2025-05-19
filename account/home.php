<?php
session_start();
include('../db.php');

// Get current user data
$current_user = $_SESSION['username'] ?? null;
if (!$current_user) {
    header('Location: login.php');
    exit();
}
$user_query = "SELECT * FROM users WHERE Fname = '" . mysqli_real_escape_string($conn, $current_user) . "'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    $delete_query = "DELETE FROM users WHERE Fname = '" . mysqli_real_escape_string($conn, $current_user) . "'";
    if (mysqli_query($conn, $delete_query)) {
        session_destroy();
        header("Location: ../index.php?account_deleted=true");
        exit;
    } else {
        $error = "Error deleting account: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyBuddy - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../pretty/home.css">
</head>
<body style="background: #f4f7fa !important;">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <div class="navbar-brand-container">
                <a class="navbar-brand" href="../index.php">StudyBuddy</a>
            </div>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($current_user); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="achievements.php"><i class="bi bi-trophy"></i> My Achievements</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteAccountModal"><i class="bi bi-trash"></i> Delete Account</a></li>
                        <li><a class="dropdown-item" href="../index.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="text-center mb-4">
            <h5><?php echo htmlspecialchars($current_user); ?></h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="home.php">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <a class="nav-link" href="meetings.php">
                    <i class="bi bi-camera-video"></i> My Meetings
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <a class="nav-link" href="study_groups.php">
                    <i class="bi bi-people"></i> Study Groups
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <a class="nav-link" href="courses.php">
                    <i class="bi bi-journal-bookmark"></i> Courses
                </a>
            </li>
        </ul>
    </div>
    <!-- End Sidebar -->

    <!-- Main Content -->
    <div class="main-content" style="padding: 2rem 1rem;">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <!-- Welcome Section -->
                <div class="profile-card mb-4 p-4 text-center bg-gradient" style="background: linear-gradient(90deg, #6a82fb 0%, #fc5c7d 100%) !important; color: #fff !important; border-radius: 1rem;">
                    <h2 class="fw-bold mb-2">Welcome back, <?php echo htmlspecialchars($current_user); ?>!</h2>
                    <p class="lead mb-4">Ready to make the most of your study journey today?</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        <a href="meetings.php" class="btn btn-light btn-lg shadow-sm"><i class="bi bi-camera-video"></i> My Meetings</a>
                        <a href="study_groups.php" class="btn btn-outline-light btn-lg shadow-sm"><i class="bi bi-people"></i> Study Groups</a>
                        <a href="courses.php" class="btn btn-outline-light btn-lg shadow-sm"><i class="bi bi-journal-bookmark"></i> Courses</a>
                    </div>
                </div>
                <!-- Quick Stats Section -->
                <div class="row mb-4 g-3 justify-content-center">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card text-center shadow-sm h-100" style="border-radius: 1rem;">
                            <div class="card-body">
                                <i class="bi bi-camera-video fs-2 text-primary"></i>
                                <h5 class="card-title mt-2">Meetings</h5>
                                <p class="card-text fw-bold" style="font-size: 1.5rem;">
                                    <?php 
                                    $meetings_count = 0;
                                    $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM meetings WHERE user_id = '".$user_data['id']."'");
                                    if($row = mysqli_fetch_assoc($result)) $meetings_count = $row['cnt'];
                                    echo $meetings_count;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card text-center shadow-sm h-100" style="border-radius: 1rem;">
                            <div class="card-body">
                                <i class="bi bi-people fs-2 text-success"></i>
                                <h5 class="card-title mt-2">Groups</h5>
                                <p class="card-text fw-bold" style="font-size: 1.5rem;">
                                    <?php 
                                    $groups_count = 0;
                                    $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM study_groups");
                                    if($row = mysqli_fetch_assoc($result)) $groups_count = $row['cnt'];
                                    echo $groups_count;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="card text-center shadow-sm h-100" style="border-radius: 1rem;">
                            <div class="card-body">
                                <i class="bi bi-journal-bookmark fs-2 text-warning"></i>
                                <h5 class="card-title mt-2">Courses</h5>
                                <p class="card-text fw-bold" style="font-size: 1.5rem;">
                                    <?php 
                                    $courses_count = 0;
                                    $result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM courses WHERE user_id = '".$user_data['id']."'");
                                    if($row = mysqli_fetch_assoc($result)) $courses_count = $row['cnt'];
                                    echo $courses_count;
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- My Courses Section -->
                <div class="meet-section" style="background: #fff !important; border-radius: 1rem !important; box-shadow: 0 2px 8px rgba(44, 62, 80, 0.07); padding: 2rem 1.5rem;">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <h4 class="mb-0"><i class="bi bi-journal-bookmark"></i> My Courses</h4>
                        <button class="btn btn-primary d-flex align-items-center" onclick="location.href='courses.php'">
                            <i class="bi bi-plus me-2"></i> Add Course
                        </button>
                    </div>
                    <div class="row g-3">
                        <?php 
                        // Render courses as cards with title, download, and delete/edit if owner
                        if (function_exists('render_courses_content')) {
                            render_courses_content();
                        } else {
                            include('courses_content.php');
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Include Modals -->
    <?php include('modals.php'); ?>
    <?php if(function_exists('renderDeleteAccountModal')) renderDeleteAccountModal(); ?>
    <!-- JavaScript Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Delete Account Confirmation
        document.querySelectorAll('form[method="POST"]').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                if(e.submitter && e.submitter.name === 'delete_account') {
                    if(!confirm('Are you absolutely sure? All your data will be permanently deleted.')) {
                        e.preventDefault();
                    }
                }
            });
        });
    </script>
    <style>
        .main-content {
            padding: 2rem 1rem;
        }
        .profile-card, .card, .meet-section {
            background: #fff !important;
            border-radius: 1rem !important;
            box-shadow: 0 2px 8px rgba(44, 62, 80, 0.07);
        }
        .bg-gradient {
            background: linear-gradient(90deg, #6a82fb 0%, #fc5c7d 100%) !important;
            color: #fff !important;
        }
        .btn-primary, .btn-outline-primary {
            border-radius: 2rem !important;
        }
        .btn-primary-custom {
            background: #6a82fb !important;
            border: none !important;
        }
        .card-title, .card-text {
            color: #2a5a8a !important;
        }
        @media (max-width: 767px) {
            .main-content {
                padding: 1rem 0.2rem;
            }
            .sidebar {
                min-width: 100px;
                max-width: 120px;
            }
            .profile-card, .card, .meet-section {
                padding: 0.5rem !important;
            }
            .row.mb-4 > div {
                margin-bottom: 1rem !important;
            }
        }
        .meet-section .course-card {
            border-radius: 0.75rem;
            box-shadow: 0 1px 4px rgba(44,62,80,0.08);
            background: #f8fafc;
            transition: box-shadow 0.2s;
        }
        .meet-section .course-card:hover {
            box-shadow: 0 4px 16px rgba(44,62,80,0.13);
            background: #f1f5fa;
        }
        .meet-section .course-title {
            font-weight: 600;
            color: #2a5a8a;
            font-size: 1.1rem;
        }
        .meet-section .course-actions .btn {
            border-radius: 1.5rem;
            font-size: 0.95rem;
        }
    </style>
</body>
</html>