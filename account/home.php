<?php
session_start();
include('../db.php');

// Get current user data
$current_user = $_SESSION['user'] ?? 'Guest';
$user_query = "SELECT * FROM users WHERE Fname = '$current_user'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Handle account deletion
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    $delete_query = "DELETE FROM users WHERE Fname = '$current_user'";
    if(mysqli_query($conn, $delete_query)) {
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
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <div class="d-flex mx-auto w-50">
                <form class="d-flex w-100">
                    <input class="form-control me-2" type="search" placeholder="Search for people or courses..." aria-label="Search">
                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>
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
            <img src="https://via.placeholder.com/100" class="rounded-circle mb-2" alt="Profile Image">
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
            <li class="nav-divider"></li>
            <li class="nav-item">
                <a class="nav-link" href="schedule.php">
                    <i class="bi bi-calendar-check"></i> Schedule
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-8">
                <!-- Welcome Section -->
                <div class="profile-card">
                    <h3>Welcome back, <?php echo htmlspecialchars($current_user); ?>!</h3>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-start mt-4">
                        <button class="btn btn-primary btn-primary-custom me-md-2" onclick="location.href='start_meet.php'">
                            <i class="bi bi-camera-video"></i> Start a Meet
                        </button>
                        <button class="btn btn-outline-primary" onclick="location.href='join_meet.php'">
                            <i class="bi bi-people"></i> Join with Code
                        </button>
                    </div>
                </div>
                
                <!-- Upcoming Meetings Section -->
                <div class="meet-section">
                    <h4><i class="bi bi-calendar-event"></i> Upcoming Meetings</h4>
                    <?php include('meetings.php'); ?>
                </div>
                
                <!-- My Courses Section -->
                <div class="meet-section">
                    <h4><i class="bi bi-journal-bookmark"></i> My Courses</h4>
                    <?php include('courses.php'); ?>
                    <button class="btn btn-outline-primary mt-2" onclick="location.href='courses.php'">
                        <i class="bi bi-plus"></i> Add More Courses
                    </button>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Calendar Section -->
                <div class="mb-4">
                    <h4 class="mb-3"><i class="bi bi-calendar-week"></i> Study Calendar</h4>
                    <div id="calendar" class="shadow-sm p-3 bg-white rounded"></div>
                </div>

                
                <!-- Quick Actions -->
                <div class="profile-card">
                    <h5>Quick Actions</h5>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="location.href='schedule_exam.php'">
                            <i class="bi bi-journal-plus"></i> Schedule Exam
                        </button>
                        <button class="btn btn-outline-primary" onclick="location.href='find_partner.php'">
                            <i class="bi bi-search"></i> Find Study Partner
                        </button>
                        <button class="btn btn-outline-primary" onclick="location.href='upload_material.php'">
                            <i class="bi bi-upload"></i> Upload Study Material
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modals -->
    <?php include('modals.php'); ?>
    <?php renderDeleteAccountModal(); ?>

    <!-- JavaScript Files -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script>
        // Initialize Calendar
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: [
                    {
                        title: 'Study Session',
                        start: new Date(),
                        end: new Date(new Date().setHours(new Date().getHours() + 2)),
                        color: '#4a89dc'
                    },
                    {
                        title: 'Group Meeting',
                        start: new Date(new Date().setDate(new Date().getDate() + 2)),
                        end: new Date(new Date().setDate(new Date().getDate() + 2)),
                        color: '#2a5a8a'
                    }
                ],
                eventClick: function(info) {
                    alert('Event: ' + info.event.title);
                },
                height: 'auto'
            });
            calendar.render();
        });

        // Delete Account Confirmation
        document.querySelector('form[method="POST"]').addEventListener('submit', function(e) {
            if(e.submitter.name === 'delete_account') {
                if(!confirm('Are you absolutely sure? All your data will be permanently deleted.')) {
                    e.preventDefault();
                }
            }
        });
    </script>
</body>
</html>