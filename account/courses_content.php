<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../db.php';
$current_user = $_SESSION['username'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if (!isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    $user_query = "SELECT id FROM users WHERE Fname = '" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'";
    $user_result = mysqli_query($conn, $user_query);
    if ($user_result && $row = mysqli_fetch_assoc($user_result)) {
        $_SESSION['user_id'] = $row['id'];
        $user_id = $row['id'];
    }
}

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
    if (isset($_SESSION['username'])) {
        $user_query = "SELECT id FROM users WHERE Fname = '" . mysqli_real_escape_string($conn, $_SESSION['username']) . "'";
        $user_result = mysqli_query($conn, $user_query);
        if ($user_result && $row = mysqli_fetch_assoc($user_result)) {
            $_SESSION['user_id'] = $row['id'];
            $user_id = $row['id'];
            echo "<script>location.reload();</script>";
            exit;
        }
    }
    echo "<p class='text-muted'>Please login to view your courses</p>";
}
?>
