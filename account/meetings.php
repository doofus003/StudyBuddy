<?php
session_start();
include '../db.php';
$current_user = $_SESSION['username'] ?? null;
if (!$current_user) {
    header('Location: login.php');
    exit();
}
$user_query = "SELECT * FROM users WHERE Fname = '" . mysqli_real_escape_string($conn, $current_user) . "'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

if (isset($_POST['create_meeting'])) {
    $title = trim($_POST['meeting_title']);
    $description = trim($_POST['meeting_description']);
    $meeting_date = $_POST['meeting_date'];
    $meeting_time = $_POST['meeting_time'];
    $meeting_link = trim($_POST['meeting_link']);
    $meeting_link = preg_replace('/\s+/', '', $meeting_link);
    $valid_link = false;
    if (preg_match('/^(https?:\/\/)?(meet\.google\.com|zoom\.us|jitsi\.org|meet\.jit\.si)/i', $meeting_link)) {
        $valid_link = true;
    }
    if (!$valid_link) {
        echo "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Please enter a valid Google Meet, Zoom, or Jitsi link.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
        echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
    } else {
        if (!preg_match('/^https?:\/\//i', $meeting_link)) {
            $meeting_link = 'https://' . $meeting_link;
        }
        $stmt = $conn->prepare("INSERT INTO meetings (user_id, title, description, meeting_date, meeting_link, participants) VALUES (?, ?, ?, ?, ?, ?)");
        $date_time = $meeting_date . ' ' . $meeting_time;
        $participants = $current_user;
        $stmt->bind_param("isssss", $user_data['id'], $title, $description, $date_time, $meeting_link, $participants);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Meeting created! Share the link: <a href='".htmlspecialchars($meeting_link,ENT_QUOTES)."' target='_blank'>Join Link</a><button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
            echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
        } else {
            echo "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Error creating meeting.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
            echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
        }
    }
}
if (isset($_POST['join_meeting']) && isset($_POST['meeting_code'])) {
    $meeting_code = trim($_POST['meeting_code']);
    $meeting_code = preg_replace('/\s+/', '', strtolower($meeting_code)); // Normalize input
    $meeting_link = '';
    $stmt = $conn->prepare("SELECT meeting_link, participants, id FROM meetings WHERE REPLACE(LOWER(meeting_link), ' ', '') LIKE ? OR LOWER(title) LIKE ?");
    $like = '%' . $meeting_code . '%';
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $stmt->bind_result($meeting_link, $participants, $meeting_id);
    if ($stmt->fetch()) {
        if (strpos($participants, $current_user) === false) {
            $participants .= ',' . $current_user;
            $update = $conn->prepare("UPDATE meetings SET participants=? WHERE id=?");
            $update->bind_param("si", $participants, $meeting_id);
            $update->execute();
        }
        echo "<script>window.open('" . htmlspecialchars($meeting_link, ENT_QUOTES) . "', '_blank');</script>";
    } else {
        echo "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>No meeting found. You can join using any part of the meeting link or title.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
        echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
    }
}
if (isset($_POST['delete_meeting']) && isset($_POST['delete_meeting_id'])) {
    $meeting_id = intval($_POST['delete_meeting_id']);
    $stmt = $conn->prepare("DELETE FROM meetings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $meeting_id, $user_data['id']);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        echo "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Meeting deleted.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
        echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
    } else {
        echo "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Failed to delete meeting.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
        echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Meetings - StudyBuddy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../pretty/home.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <div class="navbar-brand-container">
                <a class="navbar-brand" href="../index.php">StudyBuddy</a>
            </div>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user_data['Fname'] ?? $current_user); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="achievements.php"><i class="bi bi-trophy"></i> My Achievements</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../index.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="sidebar">
        <div class="text-center mb-4">
            <h5><?php echo htmlspecialchars($user_data['Fname'] ?? $current_user); ?></h5>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="home.php">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <a class="nav-link active" href="meetings.php">
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
    <div class="main-content">
        <h2>My Meetings</h2>
        <!-- Create Meeting Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="POST" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <input type="text" name="meeting_title" class="form-control" placeholder="Title" required>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="meeting_date" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <input type="time" name="meeting_time" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="meeting_description" class="form-control" placeholder="Description">
                    </div>
                    <div class="col-md-3">
                        <input type="url" name="meeting_link" class="form-control" placeholder="Paste Google Meet, Zoom, or Jitsi link" required>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" name="create_meeting" class="btn btn-primary btn-sm">Create</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Join Meeting Form -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="POST" class="row g-2 align-items-end">
                    <div class="col-md-10">
                        <input type="text" name="meeting_code" class="form-control" placeholder="Enter Meeting Code to Join" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="join_meeting" class="btn btn-outline-primary btn-sm">Join</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- List My Meetings -->
        <div class="list-group mt-2">
        <?php
        $my_meetings = $conn->prepare("SELECT * FROM meetings WHERE FIND_IN_SET(?, participants) ORDER BY meeting_date DESC LIMIT 10");
        $my_meetings->bind_param("s", $current_user);
        $my_meetings->execute();
        $result = $my_meetings->get_result();
        if ($result->num_rows > 0):
            while($meeting = $result->fetch_assoc()): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?= htmlspecialchars($meeting['title']) ?></strong>
                    <span class="text-muted small ms-2"><?= htmlspecialchars($meeting['meeting_date']) ?></span>
                    <span class="text-muted small ms-2">Link: <a href="<?= htmlspecialchars($meeting['meeting_link']) ?>" target="_blank"><?= htmlspecialchars($meeting['meeting_link']) ?></a></span>
                    <br>
                    <span class="small">Description: <?= htmlspecialchars($meeting['description']) ?></span>
                </div>
                <div>
                    <a href="<?= htmlspecialchars($meeting['meeting_link']) ?>" target="_blank" class="btn btn-outline-success btn-sm">Join</a>
                    <?php if ($meeting['user_id'] == $user_data['id']): ?>
                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete this meeting?');">
                        <input type="hidden" name="delete_meeting_id" value="<?= $meeting['id'] ?>">
                        <button type="submit" name="delete_meeting" class="btn btn-outline-danger btn-sm">Delete</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; else: ?>
            <div class="text-muted">No meetings found.</div>
        <?php endif; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>