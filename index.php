<?php
session_start();
include("config.php"); // Database connection

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = mysqli_prepare($con, "SELECT user_id, username, password, name FROM users WHERE username=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if ($row['password'] === $password) { // Plain password check
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['name'] = $row['name'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid username or password.";
            }
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!--html code-->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Login | OEARMS</titl>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="indexstyle.css">
<link rel="icon" type="image/png" href="log.jpg">

</head>
<body>

<div class="container d-flex flex-column justify-content-center align-items-center min-vh-100">



    <!-- Login Card -->
    <div class="card shadow-lg p-4 login-card text-center">
            <!-- Professional Lock Icon -->
        <div class="icon-logo mb-4">
              <i class="bi bi-shield-lock-fill"></i>
        </div>

        <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" class="mb-3">
            <div class="mb-3 input-group">
                <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                <input type="text" name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3 input-group position-relative">
                <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                <span class="position-absolute top-50 end-0 translate-middle-y me-2 cursor-pointer" onclick="togglePassword()">
                    <i class="bi bi-eye-fill" id="toggleIcon"></i>
                </span>
            </div>
            <button type="submit" class="btn btn-login w-100 mb-3">
                <i class="bi bi-in-right me-2"></i>Login
            </button>
        </form>

        <small class="text-muted">© <?= date("Y") ?> OEARMS. All rights reserved.</small>
    </div>
</div>

<script>
function togglePassword() {
    const password = document.getElementById('password');
    const icon = document.getElementById('toggleIcon');
    if(password.type === "password"){
        password.type = "text";
        icon.classList.replace("bi-eye-fill","bi-eye-slash-fill");
    } else {
        password.type = "password";
        icon.classList.replace("bi-eye-slash-fill","bi-eye-fill");
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


