<?php
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if($result) {
        $count = mysqli_num_rows($result);
        if($count == 0) {
            if($password == $confirm_password) {

                $sql = "INSERT INTO users (email, passwd, Fname) VALUES ('$email', '$password', '$username')";
                $insert_result = mysqli_query($conn, $sql);

                if ($insert_result) {
                    $success = "Account created successfully. <a href='login.php' class='alert-link'>Login here</a>";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            } else { 
                $error = "Passwords do not match";
            }      
        } else {
            $error = "Email already used, <a href='login.php' class='alert-link'>login here</a>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyBuddy - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../pretty/signup.css">
</head>
<body>
    <div class="container">
        <div class="signup-card">
            <h2 class="signup-header">Create Your Account</h2>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form name="f2" onsubmit="return validation()" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Choose a username" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Create a password" required>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-signup">Sign Up</button>
                
                <a href="../index.php" class="cancel-btn">Cancel and return home</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function validation() {
        var email = document.f2.email.value;
        var password = document.f2.password.value;
        var username = document.f2.username.value;
        var confirmPassword = document.f2.confirm_password.value;

        if (email === "" || password === "" || username === "" || confirmPassword === "") {
            alert("Please fill in all fields");
            return false;
        }
        
        if (password !== confirmPassword) {
            alert("Passwords do not match");
            return false;
        }
        
        return true;
    }
    </script>
</body>
</html>