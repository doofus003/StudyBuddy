<?php
session_start();
include '../db.php';
$user_id = $_SESSION['user_id'] ?? null;
$current_user = $_SESSION['username'] ?? null;

function show_alert($msg, $type = 'success') {
    echo "<div class='alert alert-$type alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3' role='alert' style='z-index:2000; min-width:300px;'>"
        . $msg .
        "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>\n";
    echo "<script>window.scrollTo({top:0,behavior:'smooth'});</script>";
}

if ($current_user) {
    $user_query = $conn->prepare("SELECT * FROM users WHERE Fname = ?");
    $user_query->bind_param("s", $current_user);
    $user_query->execute();
    $user_data = $user_query->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $group_date = $_POST['group_date'];
    $group_time = $_POST['group_time'];
    $stmt = $conn->prepare("INSERT INTO study_groups (creator_id, title, description, group_date, group_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $title, $description, $group_date, $group_time);
    if ($stmt->execute()) {
        show_alert('Study group created!');
    } else {
        show_alert('Error: ' . $stmt->error, 'danger');
    }
}

if (isset($_POST['join_group']) && isset($_POST['group_id'])) {
    $group_id = intval($_POST['group_id']);

    $check = $conn->prepare("SELECT id FROM study_group_members WHERE group_id = ? AND user_id = ?");
    $check->bind_param("ii", $group_id, $user_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO study_group_members (group_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $group_id, $user_id);
        if ($stmt->execute()) {
            show_alert('Joined group!');
        } else {
            show_alert('Error: ' . $stmt->error, 'danger');
        }
    } else {
        show_alert('You already joined this group.', 'warning');
    }
}

if (isset($_POST['delete_group']) && isset($_POST['group_id'])) {
    $group_id = intval($_POST['group_id']);
    $stmt = $conn->prepare("DELETE FROM study_groups WHERE id = ? AND creator_id = ?");
    $stmt->bind_param("ii", $group_id, $user_id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        show_alert('Group deleted successfully.');
    } else {
        show_alert('Failed to delete group or you are not the owner.', 'danger');
    }
}

if (isset($_POST['update_group']) && isset($_POST['group_id'])) {
    $group_id = intval($_POST['group_id']);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $group_date = $_POST['group_date'];
    $group_time = $_POST['group_time'];

    $stmt = $conn->prepare("UPDATE study_groups SET title=?, description=?, group_date=?, group_time=? WHERE id=? AND creator_id=?");
    $stmt->bind_param("ssssii", $title, $description, $group_date, $group_time, $group_id, $user_id);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        show_alert('Group updated successfully.');
    } else {
        show_alert('Failed to update group or you are not the owner.', 'danger');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Groups - StudyBuddy</title>
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
                <a class="nav-link" href="meetings.php">
                    <i class="bi bi-camera-video"></i> My Meetings
                </a>
            </li>
            <li class="nav-divider"></li>
            <li class="nav-item">
                <a class="nav-link active" href="study_groups.php">
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
    
    <div class="main-content">
        <h2>Study Groups</h2>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Create a Study Group</h5>
                <form method="POST">
                    <div class="mb-2">
                        <input type="text" name="title" class="form-control" placeholder="Group Title" required>
                    </div>
                    <div class="mb-2">
                        <textarea name="description" class="form-control" placeholder="Description" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <input type="date" name="group_date" class="form-control" required>
                    </div>
                    <div class="mb-2">
                        <input type="time" name="group_time" class="form-control" required>
                    </div>
                    <button type="submit" name="create_group" class="btn btn-primary btn-sm">Create Group</button>
                </form>
            </div>
        </div>
        
        <h5>All Study Groups</h5>
        <?php
        $query = "SELECT g.*, COUNT(m.id) AS member_count FROM study_groups g LEFT JOIN study_group_members m ON g.id = m.group_id GROUP BY g.id ORDER BY g.group_date, g.group_time";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()): ?>
            <div class="card mb-2">
                <div class="card-body">
                    <h6><?= htmlspecialchars($row['title']) ?></h6>
                    <div class="small text-muted mb-1">Date: <?= htmlspecialchars($row['group_date']) ?> Time: <?= htmlspecialchars($row['group_time']) ?></div>
                    <p class="mb-1"><?= htmlspecialchars($row['description']) ?></p>
                    <p class="mb-1">Members: <?= $row['member_count'] ?></p>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="group_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="join_group" class="btn btn-outline-primary btn-sm">Join</button>
                    </form>
                    <?php if ($row['creator_id'] == $user_id): ?>
                        
                        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#editGroupModal<?= $row['id'] ?>">Edit</button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this group?');">
                            <input type="hidden" name="group_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete_group" class="btn btn-outline-danger btn-sm">Delete</button>
                        </form>
                       
                        <div class="modal fade" id="editGroupModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="editGroupModalLabel<?= $row['id'] ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="editGroupModalLabel<?= $row['id'] ?>">Edit Group</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <form method="POST">
                                <div class="modal-body">
                                  <input type="hidden" name="group_id" value="<?= $row['id'] ?>">
                                  <div class="mb-2">
                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($row['title']) ?>" required>
                                  </div>
                                  <div class="mb-2">
                                    <textarea name="description" class="form-control" rows="2" required><?= htmlspecialchars($row['description']) ?></textarea>
                                  </div>
                                  <div class="mb-2">
                                    <input type="date" name="group_date" class="form-control" value="<?= htmlspecialchars($row['group_date']) ?>" required>
                                  </div>
                                  <div class="mb-2">
                                    <input type="time" name="group_time" class="form-control" value="<?= htmlspecialchars($row['group_time']) ?>" required>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                  <button type="submit" name="update_group" class="btn btn-primary">Save Changes</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>

        <h5 class="mt-4">Groups You Joined</h5>
        <?php
        $stmt = $conn->prepare("SELECT g.* FROM study_groups g INNER JOIN study_group_members m ON g.id = m.group_id WHERE m.user_id = ? ORDER BY g.group_date, g.group_time");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $joined = $stmt->get_result();
        while ($row = $joined->fetch_assoc()): ?>
            <div class="card mb-2 border-success">
                <div class="card-body">
                    <h6><?= htmlspecialchars($row['title']) ?></h6>
                    <div class="small text-muted mb-1">Date: <?= htmlspecialchars($row['group_date']) ?> Time: <?= htmlspecialchars($row['group_time']) ?></div>
                    <p class="mb-1"><?= htmlspecialchars($row['description']) ?></p>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
