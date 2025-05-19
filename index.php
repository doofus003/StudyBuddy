<?php 
include "db.php";
session_start();
?>  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyBuddy - Find Your Perfect Study Partner</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="pretty/index.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <div class="navbar-container">
                <div class="navbar-brand-container">
                    <a class="navbar-brand" href="#">StudyBuddy</a>
                </div>
                <i class="bi bi-emoji-smile smiley-icon"></i>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </div>
    </nav>

    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Find Your Perfect Study Partner</h1>
            <p class="lead mb-5">Connect with like-minded students, share resources, and achieve academic success together.</p>
            <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                <a href="account/signup.php" class="btn btn-primary btn-lg btn-primary-custom px-4 gap-3">Get Started</a>
                <a href="account/login.php" class="btn btn-outline-light btn-lg px-4">Login</a>
            </div>
        </div>
    </section>

    <section class="container mb-5">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <div class="feature-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3>Connect</h3>
                <p>Find study partners who share your academic goals and learning style.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon">
                    <i class="bi bi-book-half"></i>
                </div>
                <h3>Collaborate</h3>
                <p>Share notes, resources, and study techniques with your study group.</p>
            </div>
            <div class="col-md-4 mb-4">
                <div class="feature-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h3>Succeed</h3>
                <p>Improve your grades and understanding through collaborative learning.</p>
            </div>
        </div>
    </section>

    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p class="mb-0">© 2023 StudyBuddy. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>