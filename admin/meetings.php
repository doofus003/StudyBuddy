<?php
session_start();
include('../db.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || !isset($_SESSION['isadmin']) || $_SESSION['isadmin'] != 1) {
    header('Location: ../account/login.php');
    exit();
}
include('header_sidebar.php');
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
                echo "<button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editMeetingModal' data-id='" . $row['id'] . "'>Edit</button> ";
                echo "<button class='btn btn-danger btn-sm' onclick='deleteMeeting(" . $row['id'] . ")'>Delete</button>";
                echo "</td>";
                echo "</tr>";
            }

            $stmt->close();
            ?>
        </tbody>
    </table>

    <!-- Edit Meeting Modal (Placeholder) -->
    <div class="modal fade" id="editMeetingModal" tabindex="-1" aria-labelledby="editMeetingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMeetingModalLabel">Edit Meeting</h5>
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
function deleteMeeting(id) {
    if (confirm('Are you sure you want to delete this meeting?')) {
        window.location.href = 'delete_meeting.php?id=' + id;
    }
}
</script>
</body>
</html>
