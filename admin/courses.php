<?php
session_start();
include('../db.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
    header('Location: ../account/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_course']) && isset($_POST['delete_course_id'])) {
        $course_id = intval($_POST['delete_course_id']);
        $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $course_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $_SESSION['message'] = "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Course deleted successfully.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Failed to delete course.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>\n";
        }
        header('Location: courses.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('header_sidebar.php'); ?>
    <?php
    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
    ?>
    <!-- Main Content -->
    <div class="p-4" style="flex-grow: 1;">
        <h1>Manage Courses</h1>

        <!-- Courses Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Subject</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT * FROM courses");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    echo "<td>" . $row['subject'] . "</td>";
                    echo "<td>";
                    echo "<a href='download.php?file=" . urlencode($row['file_path']) . "' class='btn btn-primary btn-sm'>Download PDF</a> ";
                    echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this course?\");'>";
                    echo "<input type='hidden' name='delete_course' value='1'>";
                    echo "<input type='hidden' name='delete_course_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='btn btn-danger btn-sm'>Delete</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }

                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
