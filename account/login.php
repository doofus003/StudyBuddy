<?php 
session_start();
include('../db.php');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['user'];
    $password = $_POST['pass'];

    // Debugging: Check if session is active
    error_log("Session status: " . session_status());

    // Use prepared statements for security
    $stmt = $conn->prepare("SELECT * FROM users WHERE Fname = ? AND passwd = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result) {
        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION["username"] = $username;
            $_SESSION["isadmin"] = $user['isadmin']; // Store isadmin in session

            // Debugging: Log the isadmin value and redirection
            error_log("isadmin value: " . $user['isadmin']);
            error_log("Redirecting to: " . ($user['isadmin'] == 1 ? "../admin/home.php" : "home.php"));

            // Redirect based on isadmin attribute
            if($user['isadmin'] == 1) {
                header("Location: ../admin/home.php");
            } else {
                header("Location: home.php");
            }
            exit;
        } else {
            $error = "Invalid username or password";
            error_log($error); // Debugging: Log invalid login
        }
    } else {
        error_log("Database query failed"); // Debugging: Log query failure
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" width="device-width, initial-scale=1.0">
    <title>StudyBuddy - Login</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
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