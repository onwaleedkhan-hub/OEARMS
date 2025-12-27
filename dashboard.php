<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    die("<h2 class='text-center text-danger mt-5'>Access Denied. Please login first</h2>");
}

$user_id = $_SESSION['user_id'];

// Function to get count of customers by status
function getCustomerCount($con, $user_id, $status_name = null) {
    if ($status_name) {
        $sql = "SELECT COUNT(*) AS total 
                FROM Customers c
                INNER JOIN Status s ON c.Status_ID = s.Status_ID
                WHERE c.user_id = ? AND s.Status_Name = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "is", $user_id, $status_name);
    } else {
        $sql = "SELECT COUNT(*) AS total FROM Customers WHERE user_id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $count = 0;
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['total'];
    }
    return $count;
}

$total_customers = getCustomerCount($con, $user_id);
$total_approved = getCustomerCount($con, $user_id, 'Approved');
$total_pending = getCustomerCount($con, $user_id, 'Pending');
?>

<!-------------------HTML CODE BELOW-------------------->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | OEARMS</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<!-- Custom CSS -->
<link href="dashboardstyle.css" rel="stylesheet">
<!-- Favicon -->
<link rel="icon" type="image/png" href="log.jpg">

</head>

<body class="d-flex flex-column min-vh-100">

<!-- ================= HEADER ================= -->
<header class="text-white py-3 px-4 d-flex justify-content-between align-items-center shadow-sm position-sticky top-0 ">
    <div class="fw-semibold d-flex align-items-center">
        <i class="bi bi-globe2 me-2 fs-5 text-warning"></i>
        Overseas Employment Agency Record Management System
    </div>
    <a href="logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
        <i class="bi bi-box-arrow-right me-1"></i> Logout
    </a>
</header>

<div class="d-flex flex-grow-1">

    <!-- ================= SIDEBAR ================= -->
    <nav class="sidebar shadow-sm">
        <a href="dashboard.php" class="active mb-3">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
        </a>
        <a href="add_record.php">
            <i class="bi bi-person-plus-fill"></i>
            <span>Add Customer</span>
        </a>
        <a href="view_records.php">
            <i class="bi bi-people-fill"></i>
            <span>View Customers</span>
        </a>
        <a href="agent.php">
            <i class="bi bi-briefcase-fill"></i>
            <span>Agents</span>
        </a>
    </nav>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="flex-grow-1 p-4">
        <div class="container-fluid">
            <div class="row g-4">

                <!-- Total Customers -->
                <div class="col-lg-4 col-md-6">
                    <div class="card-dashboard bg-primary text-white">
                        <i class="bi bi-people-fill"></i>
                        <h6>Total Customers</h6>
                        <p><?= $total_customers; ?></p>
                        <a href="view_records.php" class="btn btn-light btn-sm rounded-pill">
                            View All Customers
                        </a>
                    </div>
                </div>

                <!-- Approved Customers -->
                <div class="col-lg-4 col-md-6">
                    <div class="card-dashboard bg-success text-white">
                        <i class="bi bi-check-circle-fill"></i>
                        <h6>Approved Customers</h6>
                        <p><?= $total_approved; ?></p>
                        <a href="approved.php" class="btn btn-light btn-sm rounded-pill">
                            View Approved Customers
                        </a>
                    </div>
                </div>

                <!-- Pending Customers -->
                <div class="col-lg-4 col-md-6">
                    <div class="card-dashboard bg-warning text-dark">
                        <i class="bi bi-hourglass-split"></i>
                        <h6>Pending Customers</h6>
                        <p><?= $total_pending; ?></p>
                        <a href="pending.php" class="btn btn-dark btn-sm rounded-pill">
                            View Pending Customers
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


