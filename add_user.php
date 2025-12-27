<?php
session_start();
include("config.php");

/* ================= ADMIN LOGIN CHECK ================= */
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

$error = "";
$success = "";

/* ================= INSERT USER ================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name     = trim($_POST['name']);
    $agency   = trim($_POST['agency']);
    $phone    = trim($_POST['phone']);
    $email    = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // PLAIN PASSWORD

    if (empty($name) || empty($agency) || empty($email) || empty($username) || empty($password)) {
        $error = "All required fields must be filled.";
    } else {

        /* ================= CHECK DUPLICATE ================= */
        $check = $con->prepare(
            "SELECT user_id FROM users WHERE email = ? OR username = ?"
        );
        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email or Username already exists.";
        } else {

            /* ================= INSERT USER (PLAIN PASSWORD) ================= */
            $stmt = $con->prepare(
                "INSERT INTO users (name, agency, phone, email, username, password)
                 VALUES (?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "ssssss",
                $name,
                $agency,
                $phone,
                $email,
                $username,
                $password   // STORED AS PLAIN TEXT
            );

            if ($stmt->execute()) {
                $success = "User added successfully.";
            } else {
                $error = "Database error. Please try again.";
            }
        }
    }
}
?>

<!---html code--->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add User | OEARMS</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="add_userstyle.css">
<!-- Favicon -->
<link rel="icon" type="image/png" href="log.jpg">
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-lg rounded-4">
                <div class="card-header text-center bg-primary text-white rounded-top-4">
                    <h4 class="mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i> Add New User
                    </h4>
                </div>

                <div class="card-body">

                    <!-- Alerts -->
                    <?php if ($error): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i> <?= $error; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success d-flex align-items-center">
                            <i class="bi bi-check-circle-fill me-2 text-success"></i> <?= $success; ?>
                        </div>
                    <?php endif; ?>

                    <!-- FORM -->
                    <form method="POST">

                        <!-- Full Name -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-person-fill"></i></span>
                            <input type="text" name="name" class="form-control" placeholder="Full Name *" required>
                        </div>

                        <!-- Agency -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light text-info"><i class="bi bi-building"></i></span>
                            <input type="text" name="agency" class="form-control" placeholder="Agency *" required>
                        </div>

                        <!-- Phone -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light text-success"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" name="phone" class="form-control" placeholder="Phone">
                        </div>

                        <!-- Email -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light text-danger"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" name="email" class="form-control" placeholder="Email *" required>
                        </div>

                        <!-- Username -->
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-light text-warning"><i class="bi bi-person-badge-fill"></i></span>
                            <input type="text" name="username" class="form-control" placeholder="Username *" required>
                        </div>

                        <!-- Password -->
                        <div class="input-group mb-3 position-relative">
                            <span class="input-group-text bg-light text-secondary"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="Password *" id="password" required>
                            <span class="input-group-text position-absolute end-0 top-50 translate-middle-y me-1 password-toggle" style="cursor:pointer;" onclick="togglePassword()">
                                <i class="bi bi-eye-fill text-dark"></i>
                            </span>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success rounded-pill">
                                <i class="bi bi-save me-1"></i> Save User
                            </button>
                        </div>

                    </form>

                </div>

                <div class="card-footer text-center">
                    <a href="view_users.php" class="text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i> Back to Users List
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.password-toggle i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('bi-eye-fill');
        toggleIcon.classList.add('bi-eye-slash-fill');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash-fill');
        toggleIcon.classList.add('bi-eye-fill');
    }
}
</script>

</body>
</html>
