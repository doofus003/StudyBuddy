<?php
// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../db.php';
$current_user = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - StudyBuddy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../pretty/home.css">
</head>
<body>
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
                        <li><a class="dropdown-item text-danger" href="../index.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
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
                <a class="nav-link" href="home.php">
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
                <a class="nav-link active" href="courses.php">
                    <i class="bi bi-journal-bookmark"></i> Courses
                </a>
            </li>
        
        </ul>
    </div>
    <!-- End Sidebar -->
    <!-- Main Content -->
    <div class="main-content">
        <?php
        // Main Content
        if ($user_id) {
            $courses_query = "SELECT * FROM courses WHERE user_id = '$user_id' LIMIT 3";
            $courses_result = mysqli_query($conn, $courses_query);
            
            if(mysqli_num_rows($courses_result) > 0): ?>
                <div class="row mt-3">
                    <?php while($course = mysqli_fetch_assoc($courses_result)): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($course['title']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($course['subject']) ?></p>
                                    <a href="<?= htmlspecialchars($course['file_path']) ?>" class="btn btn-sm btn-primary-custom">
                                        View Materials
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No courses found. Add some to get started!</p>
            <?php endif;
        } else {
            echo "<p class='text-muted'>Please login to view your courses</p>";
        }

        // Handle course upload
        if (isset($_POST['upload_course']) && isset($_FILES['course_pdf']) && $user_id) {
            $title = trim($_POST['title']);
            $subject = trim($_POST['subject']);
            $description = trim($_POST['description']);
            $visibility = 1; // public for now
            $file = $_FILES['course_pdf'];
            $upload_dir = '../uploads/';
            $file_name = uniqid('course_', true) . '_' . basename($file['name']);
            $target_path = $upload_dir . $file_name;
            $db_path = 'uploads/' . $file_name;
            $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($file_type === 'pdf' && move_uploaded_file($file['tmp_name'], $target_path)) {
                $stmt = $conn->prepare("INSERT INTO courses (user_id, title, description, file_path, subject, visibility) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param('issssi', $user_id, $title, $description, $db_path, $subject, $visibility);
                $stmt->execute();
                echo '<div class="alert alert-success">Course uploaded successfully!</div>';
            } else {
                echo '<div class="alert alert-danger">Upload failed. Only PDF files are allowed.</div>';
            }
        }
        ?>
        <!-- Upload Course Form -->
        <?php if ($user_id): ?>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Upload a New Course</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-2">
                        <input type="text" name="title" class="form-control" placeholder="Course Title" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                    </div>
                    <div class="mb-2">
                        <textarea name="description" class="form-control" placeholder="Description" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <input type="file" name="course_pdf" accept="application/pdf" class="form-control" required>
                    </div>
                    <button type="submit" name="upload_course" class="btn btn-primary btn-sm">Upload Course</button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- List All Courses -->
        <div class="mb-4">
            <h5>All Courses</h5>
            <div class="row">
            <?php
            $all_courses = mysqli_query($conn, "SELECT c.*, u.Fname FROM courses c JOIN users u ON c.user_id = u.id ORDER BY c.created_at DESC");
            if(mysqli_num_rows($all_courses) > 0):
                while($course = mysqli_fetch_assoc($all_courses)):
            ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($course['title']) ?></h6>
                            <div class="small text-muted mb-1">By <?= htmlspecialchars($course['Fname']) ?> | <?= htmlspecialchars($course['subject']) ?></div>
                            <p class="card-text small mb-2"><?= htmlspecialchars($course['description']) ?></p>
                            <a href="../<?= htmlspecialchars($course['file_path']) ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                                <i class="bi bi-file-earmark-pdf"></i> Download PDF
                            </a>
                            <?php if ($user_id == $course['user_id']): ?>
                                <!-- Edit button triggers modal -->
                                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editCourseModal<?= $course['id'] ?>">Edit</button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                    <input type="hidden" name="delete_course_id" value="<?= $course['id'] ?>">
                                    <button type="submit" name="delete_course" class="btn btn-outline-danger btn-sm">Delete</button>
                                </form>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editCourseModal<?= $course['id'] ?>" tabindex="-1" aria-labelledby="editCourseModalLabel<?= $course['id'] ?>" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h5 class="modal-title" id="editCourseModalLabel<?= $course['id'] ?>">Edit Course</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                      </div>
                                      <form method="POST">
                                        <div class="modal-body">
                                          <input type="hidden" name="edit_course_id" value="<?= $course['id'] ?>">
                                          <div class="mb-2">
                                            <input type="text" name="edit_title" class="form-control" value="<?= htmlspecialchars($course['title']) ?>" required>
                                          </div>
                                          <div class="mb-2">
                                            <input type="text" name="edit_subject" class="form-control" value="<?= htmlspecialchars($course['subject']) ?>" required>
                                          </div>
                                          <div class="mb-2">
                                            <textarea name="edit_description" class="form-control" rows="2" required><?= htmlspecialchars($course['description']) ?></textarea>
                                          </div>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="submit" name="update_course" class="btn btn-primary">Save Changes</button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; else: ?>
                <p class="text-muted">No courses have been posted yet.</p>
            <?php endif; ?>
            </div>
        </div>
        <?php
        // Handle delete course
        if (isset($_POST['delete_course']) && isset($_POST['delete_course_id'])) {
            $course_id = intval($_POST['delete_course_id']);
            $stmt = $conn->prepare("DELETE FROM courses WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $course_id, $user_id);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                echo "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Course deleted successfully.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
                echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
            } else {
                echo "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Failed to delete course or you are not the owner.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>\n";
                echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
            }
        }
        // Handle update course
        if (isset($_POST['update_course']) && isset($_POST['edit_course_id'])) {
            $course_id = intval($_POST['edit_course_id']);
            $title = trim($_POST['edit_title']);
            $subject = trim($_POST['edit_subject']);
            $description = trim($_POST['edit_description']);
            $stmt = $conn->prepare("UPDATE courses SET title=?, subject=?, description=? WHERE id=? AND user_id=?");
            $stmt->bind_param("ssssi", $title, $subject, $description, $course_id, $user_id);
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                echo "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Course updated successfully.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
                echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
            } else {
                echo "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Failed to update course or you are not the owner.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>\n";
                echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
            }
        }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>