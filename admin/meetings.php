<?php
session_start();
include('../db.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
    header('Location: ../account/login.php');
    exit();
}
include('header_sidebar.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_meeting']) && isset($_POST['delete_meeting_id'])) {
        $meeting_id = intval($_POST['delete_meeting_id']);
        $stmt = $conn->prepare("DELETE FROM meetings WHERE id = ?");
        $stmt->bind_param("i", $meeting_id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Meeting deleted successfully.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
            echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
        } else {
            echo "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Failed to delete meeting.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>\n";
            echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
        }
    }

    if (isset($_POST['edit_meeting']) && isset($_POST['id']) && isset($_POST['title']) && isset($_POST['date']) && isset($_POST['time'])) {
        $id = intval($_POST['id']);
        $title = $_POST['title'];
        $date = $_POST['date'];
        $time = $_POST['time'];

        $datetime = $date . ' ' . $time;
        $stmt = $conn->prepare("UPDATE meetings SET title = ?, meeting_date = ? WHERE id = ?");
        $stmt->bind_param('ssi', $title, $datetime, $id);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Meeting updated successfully.<button type='button' class='btn-close float-end' data-bs-dismiss='alert'></button></div>\n";
            echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
        } else {
            echo "<div class='alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3' style='z-index:2000; min-width:300px;'>Failed to update meeting.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>\n";
            echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
        }
    }
}
?>
<!-- Main Content -->
<div class="p-4" style="flex-grow: 1;">
    <h1>Manage Meetings</h1>

    <!-- Meetings Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $conn->prepare("SELECT * FROM meetings");
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $date = date('Y-m-d', strtotime($row['meeting_date']));
                $time = date('H:i:s', strtotime($row['meeting_date']));

                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['title'] . "</td>";
                echo "<td>" . $date . "</td>";
                echo "<td>" . $time . "</td>";
                echo "<td>";
                echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Are you sure you want to delete this meeting?\");'>";
                echo "<input type='hidden' name='delete_meeting' value='1'>";
                echo "<input type='hidden' name='delete_meeting_id' value='" . $row['id'] . "'>";
                echo "<button type='submit' class='btn btn-danger btn-sm'>Delete</button>";
                echo "</form>";
                echo "</td>";
                echo "</tr>";
            }

            $stmt->close();
            ?>
        </tbody>
    </table>

    <!-- Edit Meeting Modal -->
    <div class="modal fade" id="editMeetingModal" tabindex="-1" aria-labelledby="editMeetingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editMeetingForm" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editMeetingModalLabel">Edit Meeting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="editMeetingId" name="id">
                        <input type="hidden" name="edit_meeting" value="1">
                        <div class="mb-3">
                            <label for="editMeetingTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editMeetingTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editMeetingDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="editMeetingDate" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="editMeetingTime" class="form-label">Time</label>
                            <input type="time" class="form-control" id="editMeetingTime" name="time" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
// Populate the edit modal with meeting data
const editMeetingModal = document.getElementById('editMeetingModal');
editMeetingModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget; // Button that triggered the modal
    const meetingId = button.getAttribute('data-id');

    fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'get', id: meetingId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('editMeetingTitle').value = data.meeting.title;
            document.getElementById('editMeetingDate').value = data.meeting.date;
            document.getElementById('editMeetingTime').value = data.meeting.time;
            document.getElementById('editMeetingId').value = data.meeting.id;
        } else {
            alert('Failed to fetch meeting data: ' + data.error);
        }
    });
});
</script>
</body>
</html>
