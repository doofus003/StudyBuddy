<?php
session_start();
include('../db.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
    header('Location: ../account/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_study_group']) && isset($_POST['delete_study_group_id'])) {
        $group_id = intval($_POST['delete_study_group_id']);
        $stmt = $conn->prepare("DELETE FROM study_groups WHERE id = ?");
        $stmt->bind_param("i", $group_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $_SESSION['message'] = "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Study group deleted successfully.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Failed to delete study group.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>\n";
        }
        header('Location: study_groups.php');
        exit();
    }

    if (isset($_POST['edit_study_group']) && isset($_POST['id'])) {
        $group_id = intval($_POST['id']);
        $title = $_POST['title'];
        $description = $_POST['description'];
        $date = $_POST['date'];

        $stmt = $conn->prepare("UPDATE study_groups SET title = ?, description = ?, group_date = ? WHERE id = ?");
        $stmt->bind_param("sssi", $title, $description, $date, $group_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $_SESSION['message'] = "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Study group updated successfully.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Failed to update study group.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>\n";
        }
        header('Location: study_groups.php');
        exit();
    }
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

        <?php
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']);
        }
        ?>

        <!-- Study Groups Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date</th>
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
                    echo "<td>";
                    echo "<button class='btn btn-warning btn-sm' data-id='" . $row['id'] . "'>Edit</button> ";
                    echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this study group?\");'>";
                    echo "<input type='hidden' name='delete_study_group' value='1'>";
                    echo "<input type='hidden' name='delete_study_group_id' value='" . $row['id'] . "'>";
                    echo "<button type='submit' class='btn btn-danger btn-sm'>Delete</button>";
                    echo "</form>";
                    echo "</td>";
                    echo "</tr>";
                }

                $stmt->close();
                ?>
            </tbody>
        </table>

        <!-- Edit Study Group Modal -->
        <div class="modal fade" id="editStudyGroupModal" tabindex="-1" aria-labelledby="editStudyGroupModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editStudyGroupForm" method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editStudyGroupModalLabel">Edit Study Group</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="editGroupId" name="id">
                            <div class="mb-3">
                                <label for="editGroupTitle" class="form-label">Title</label>
                                <input type="text" class="form-control" id="editGroupTitle" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="editGroupDescription" class="form-label">Description</label>
                                <textarea class="form-control" id="editGroupDescription" name="description" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="editGroupDate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="editGroupDate" name="date" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="edit_study_group">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Open modal and populate fields with PHP values
    const editButtons = document.querySelectorAll('.btn-warning');
    editButtons.forEach(button => {
        button.addEventListener('click', () => {
            console.log('Edit button clicked'); // Debugging log
            const row = button.closest('tr');
            const id = row.children[0].textContent.trim();
            const title = row.children[1].textContent.trim();
            const description = row.children[2].textContent.trim();
            const date = row.children[3].textContent.trim();

            document.getElementById('editGroupId').value = id;
            document.getElementById('editGroupTitle').value = title;
            document.getElementById('editGroupDescription').value = description;
            document.getElementById('editGroupDate').value = date;

            const modal = new bootstrap.Modal(document.getElementById('editStudyGroupModal'));
            modal.show();
        });
    });
    </script>
</body>
</html>
