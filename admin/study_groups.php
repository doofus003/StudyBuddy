<?php
session_start();
include('../db.php');

// Check if the user is logged in and is an admin
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
    <title>Manage Study Groups</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('header_sidebar.php'); ?>
    <!-- Main Content -->
    <div class="p-4" style="flex-grow: 1;">
        <h1>Manage Study Groups</h1>

        <!-- Study Groups Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $conn->prepare("SELECT * FROM study_groups");
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['title'] . "</td>";
                    echo "<td>" . $row['description'] . "</td>";
                    echo "<td>" . $row['group_date'] . "</td>";
                    echo "<td>" . $row['group_time'] . "</td>";
                    echo "<td>";
                    echo "<button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editStudyGroupModal' data-id='" . $row['id'] . "'>Edit</button> ";
                    echo "<button class='btn btn-danger btn-sm' onclick='deleteStudyGroup(" . $row['id'] . ")'>Delete</button>";
                    echo "</td>";
                    echo "</tr>";
                }

                $stmt->close();
                ?>
            </tbody>
        </table>

        <!-- Edit Study Group Modal (Placeholder) -->
        <div class="modal fade" id="editStudyGroupModal" tabindex="-1" aria-labelledby="editStudyGroupModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editStudyGroupModalLabel">Edit Study Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Feature under construction.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function deleteStudyGroup(id) {
        if (confirm('Are you sure you want to delete this study group?')) {
            window.location.href = 'delete_study_group.php?id=' + id;
        }
    }
    </script>
</body>
</html>
