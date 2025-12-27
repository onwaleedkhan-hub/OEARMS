<?php
session_start();
include("config.php");

// Redirect if admin not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Validate user_id in query string
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header("Location: view_users.php");
    exit();
}

$user_id = intval($_GET['user_id']);
$error = "";
$success = "";

// Fetch existing user data
$stmt = mysqli_prepare($con, "SELECT * FROM users WHERE user_id=?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    header("Location: view_users.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $agency = trim($_POST['agency']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // plain password (consider hashing in production)

    // Basic validation
    if (empty($name) || empty($agency) || empty($email) || empty($username) || empty($password)) {
        $error = "Please fill all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!empty($phone) && !preg_match('/^\+?[0-9]{7,15}$/', $phone)) {
        $error = "Invalid phone number.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check for duplicate email or username excluding current user
        $stmt_check = mysqli_prepare($con, "SELECT user_id FROM users WHERE (email=? OR username=?) AND user_id<>?");
        mysqli_stmt_bind_param($stmt_check, "ssi", $email, $username, $user_id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);

        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $error = "Email or Username already exists.";
        } else {
            // Update user record
            $stmt_update = mysqli_prepare(
                $con,
                "UPDATE users SET name=?, agency=?, phone=?, email=?, username=?, password=? WHERE user_id=?"
            );
            mysqli_stmt_bind_param($stmt_update, "ssssssi", $name, $agency, $phone, $email, $username, $password, $user_id);

            if (mysqli_stmt_execute($stmt_update)) {
                $success = "User updated successfully!";
                // Refresh user data
                $stmt->execute();
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit User | OEARMS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="updatestyle.css">
<link rel="icon" href="log.jpg" type="image/x-icon">
</head>
<body class="bg-light">

<div class="container py-5">
    <a href="view_users.php" class="btn btn-outline-secondary mb-4">
        <i class="bi bi-arrow-left-circle me-2"></i>Back to Users
    </a>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h4 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit User</h4>
        </div>
        <div class="card-body p-4">

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Agency</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                        <input type="text" name="agency" class="form-control" value="<?= htmlspecialchars($user['agency']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Phone</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-telephone-fill"></i></span>
                        <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-person-circle"></i></span>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" class="form-control" value="<?= htmlspecialchars($user['password']) ?>" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill">
                    <i class="bi bi-save-fill me-2"></i>Update User
                </button>
            </form>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
