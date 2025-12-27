<?php 
session_start();
include("config.php");

// Redirect to login if admin is not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Get admin username
$admin_name = $_SESSION['admin'];
?>
<!-------------------HTML CODE------------------>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | OEARMS</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="home.css">
<!-- Favicon -->
<link rel="icon" href="log.jpg" type="image/x-icon">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm p-3 mb-4">
    <div class="container-fluid">
        <a class="navbar-brand fs-3" href="#">Admin Dashboard</a>
        <div class="d-flex">
            <span class="navbar-text me-3">
                Hello, <?= htmlspecialchars($admin_name) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container">
    <div class="row g-4">
<?php

// Get total number of users
$totalUsers = 0;
$result = mysqli_query($con, "SELECT COUNT(*) AS total FROM users");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $totalUsers = $row['total'];
}
?>

<div class="col-md-4">
    <div class="card dashboard-card shadow-lg text-center p-4">
        <i class="bi bi-people-fill fs-1 text-primary mb-3"></i>
        <h4>Total Users</h4>
        <h2 class="mb-2"><?= $totalUsers ?></h2> 
         <span  class="btn btn-success w-75">Total users</span>
    </div>
</div>


        <div class="col-md-4">
            <div class="card dashboard-card shadow-lg text-center p-4">
                <i class="bi bi-person-plus-fill fs-1 text-success mb-3"></i>
                <h4>Add User</h4>
                <p>Create a new user account</p>
                <a href="add_user.php" class="btn btn-success w-75">Add</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-card shadow-lg text-center p-4">
                <i class="bi bi-gear-fill fs-1 text-warning mb-3"></i>
                <h4>Manage Users</h4>
                <p>Edit or delete user accounts</p>
                <a href="view_users.php" class="btn btn-warning w-75">Open</a>
            </div>
        </div>

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
