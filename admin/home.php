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

<div class="content text-center" style="padding: 20px;margin-left: -20%;">
    <h1 class="mb-4">Welcome, Admin</h1>
    <p class="mb-4">Use the menu on the left to manage users, study groups, courses, and meetings.</p>

    <div class="content">
        <h2 class="mb-4">Admin Dashboard</h2>
        <div class="row justify-content-center g-4">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-4">
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            $stmt->close();
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card text-white bg-success h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Total Study Groups</h5>
                        <p class="card-text display-4">
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM study_groups");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            $stmt->close();
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Total Courses</h5>
                        <p class="card-text display-4">
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM courses");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            $stmt->close();
                            ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="card text-white bg-danger h-100">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h5 class="card-title">Total Meetings</h5>
                        <p class="card-text display-4">
                            <?php
                            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM meetings");
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $row = $result->fetch_assoc();
                            echo $row['total'];
                            $stmt->close();
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>