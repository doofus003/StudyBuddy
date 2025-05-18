<?php 
session_start();
include('../db.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    $sql = "select * from users where Fname = '$username' and passwd = '$password'";
    $result = mysqli_query($conn, $sql);
    if($result) {
        $count = mysqli_num_rows($result);
        if(mysqli_num_rows($result) > 0) {
            $_SESSION["username"] = $username;
            header("Location: home.php");
            exit;
        } else {
            $error = "Invalid username or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyBuddy - Login</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../pretty/login.css">
</head>
<body>
    <div class="container">
        <div class="login-card">
            <h2 class="login-header">Welcome Back</h2>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form name="f1" onsubmit="return validation()" method="POST">
                <div class="mb-3">
                    <label for="user" class="form-label">Username</label>
                    <input type="text" class="form-control" id="user" name="user" placeholder="Enter your username">
                </div>
                
                <div class="mb-3">
                    <label for="pass" class="form-label">Password</label>
                    <input type="password" class="form-control" id="pass" name="pass" placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-primary btn-login">Login</button>
                
                <a href="../index.php" class="cancel-btn">Cancel and return home</a>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function validation() {
        var id = document.f1.user.value;
        var ps = document.f1.pass.value;
        
        if(id.length == "" && ps.length == "") {
            alert("Username and Password fields are empty");
            return false;
        }
        else {
            if(id.length == "") {
                alert("Username is empty");
                return false;
            }
            if(ps.length == "") {
                alert("Password field is empty");
                return false;
            }
        }
    }
    </script>
</body>
</html>