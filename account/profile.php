<?php  
include "../db.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['username'];

$query = $conn->prepare("SELECT Fname, email, grade, major, city, status FROM users WHERE Fname = ?");
$query->bind_param("s", $user_name);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = trim($_POST['fname']);
    $email = trim($_POST['email']);
    $grade = trim($_POST['grade']);
    $major = trim($_POST['major']);
    $city  = trim($_POST['city']);

 
    $update = $conn->prepare("UPDATE users SET Fname=?, email=?, grade=?, major=?, city=? WHERE Fname=?");
    $update->bind_param("ssssss", $fname, $email, $grade, $major, $city, $user_name);

    if ($update->execute()) {
        echo "<script>alert('Profile updated successfully.'); window.location.href='profile.php';</script>";
    } else {
        echo "<script>alert('Failed to update profile.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - StudyBuddy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../pretty/home.css">
    <style>
        .profile-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(37,99,235,0.10), 0 1.5px 6px rgba(0,0,0,0.04);
            padding: 2.5rem 2.5rem 2rem 2.5rem;
            margin-top: 3rem;
            max-width: 520px;
            margin-left: auto;
            margin-right: auto;
            border: 1.5px solid #e0e7ef;
            transition: box-shadow 0.2s;
        }
        .profile-card:hover {
            box-shadow: 0 8px 32px rgba(37,99,235,0.18), 0 2px 8px rgba(0,0,0,0.06);
        }
        .profile-title {
            text-align: center;
            margin-bottom: 2rem;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: 1px;
            font-size: 2rem;
        }
        .status {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #16a34a;
            font-weight: 600;
            font-size: 1.1rem;
        }
        form label {
            font-weight: 600;
            margin-top: 0.5rem;
            color: #2563eb;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0 18px 0;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            background: #f1f5f9;
            font-size: 1rem;
            transition: border 0.2s, background 0.2s;
        }
        input[type="text"]:focus, input[type="email"]:focus {
            border: 1.5px solid #2563eb;
            outline: none;
            background: #fff;
        }
        input[readonly] {
            background: #e5e7eb;
            color: #6b7280;
        }
        .d-flex.justify-content-center.mt-4 {
            gap: 0.75rem;
        }
        .btn-primary-custom {
            background: linear-gradient(90deg, #2563eb 60%, #60a5fa 100%);
            border: none;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(37,99,235,0.08);
        }
        .btn-primary-custom:hover {
            background: linear-gradient(90deg, #1d4ed8 60%, #2563eb 100%);
        }
        .btn-outline-primary {
            border-color: #2563eb;
            color: #2563eb;
            font-weight: 600;
        }
        .btn-outline-primary:hover {
            background: #2563eb;
            color: #fff;
        }
        .btn-outline-secondary {
            border-color: #64748b;
            color: #64748b;
            font-weight: 600;
        }
        .btn-outline-secondary:hover {
            background: #64748b;
            color: #fff;
        }
    </style>
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
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($user['Fname']); ?>
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
            <h5><?php echo htmlspecialchars($user['Fname']); ?></h5>
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
                <a class="nav-link" href="courses.php">
                    <i class="bi bi-journal-bookmark"></i> Courses
                </a>
            </li>
            
        </ul>
    </div>

    <div class="main-content">
        <div class="profile-card mt-5">
            <h2 class="profile-title">My Profile</h2>
            <div class="status">
                Status: <strong><?= htmlspecialchars($user['status'] === 'on' ? 'Online' : 'Offline') ?></strong>
            </div>
            <form method="POST" id="profileForm" onsubmit="return validateForm()">
                <label>Full Name:</label>
                <input type="text" name="fname" value="<?= htmlspecialchars($user['Fname']) ?>" required readonly>

                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required readonly>

                <label>Grade:</label>
                <input type="text" name="grade" value="<?= htmlspecialchars($user['grade']) ?>" required readonly>

                <label>Major:</label>
                <input type="text" name="major" value="<?= htmlspecialchars($user['major']) ?>" required readonly>

                <label>City:</label>
                <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>" required readonly>

                <div class="d-flex justify-content-center mt-4">
                    <button type="button" class="btn btn-outline-primary me-2" id="editBtn"><i class="bi bi-pencil"></i> Edit</button>
                    <input type="submit" class="btn btn-primary-custom me-2" value="Save" id="saveBtn" style="display:none;">
                    <button type="button" class="btn btn-outline-secondary" id="cancelBtn" style="display:none;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function validateForm() {
        const email = document.querySelector('input[name="email"]').value;
        if (!email.includes('@')) {
            alert("Please enter a valid email.");
            return false;
        }
        return true;
    }

    const editBtn = document.getElementById('editBtn');
    const saveBtn = document.getElementById('saveBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input[type="text"], input[type="email"]');

    editBtn.onclick = function() {
        inputs.forEach(input => input.removeAttribute('readonly'));
        editBtn.style.display = 'none';
        saveBtn.style.display = 'inline-block';
        cancelBtn.style.display = 'inline-block';
    };

    cancelBtn.onclick = function() {
        window.location.reload();
    };
    </script>
</body>
</html>
