<?php
session_start();
include("config.php");

/* ==========================
   CHECK LOGIN
========================== */
if (!isset($_SESSION['user_id'])) {
    die("<h4 class='text-center text-danger mt-5'>Access denied. Please login first.</h4>");
}
$user_id = $_SESSION['user_id'];

/* ==========================
   SEARCH VALUE
========================== */
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

/* ==========================
   SELECT PENDING CUSTOMERS
========================== */
$sql = "SELECT 
            c.Customers_ID,
            c.Name,
            c.Passport_No,
            v.Visa_No,
            v.Visa_Type,
            s.Sponsor_Number,
            s.Sponsor_Name,
            a.Agent_Name
        FROM Customers c
        INNER JOIN Status st  ON c.Status_ID = st.Status_ID
        INNER JOIN Visa v    ON c.Visa_ID = v.Visa_ID
        INNER JOIN Sponsor s ON v.Sponsor_ID = s.Sponsor_ID
        INNER JOIN Agent a   ON c.Agent_ID = a.Agent_ID
        WHERE st.Status_Name = ?
          AND c.user_id = ?
        ORDER BY c.Customers_ID DESC";

$stmt = mysqli_prepare($con, $sql);
$status = "Pending";
mysqli_stmt_bind_param($stmt, "si", $status, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!---------HTML STARTS HERE--------->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pending Customers</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" type="image/png" href="log.jpg">

<style>
:root{
    --theme-dark:#065f46;
    --theme-main:#10b981;
    --theme-light:#ecfdf5;
}

/* Global */
body{
    background:linear-gradient(135deg,#f0fdfa,#ecfeff);
    font-family:'Segoe UI',sans-serif;
}

/* Navbar */
.navbar{
    background:linear-gradient(90deg,var(--theme-dark),var(--theme-main));
}

/* Search Bar */
.search-box{
    background:#ffffff;
    border-radius:30px;
    padding:4px;
    box-shadow:0 6px 16px rgba(0,0,0,0.12);
}

.search-box input{
    border:none;
    box-shadow:none;
}

.search-box input:focus{
    box-shadow:none;
}

.search-box .btn{
    background:linear-gradient(90deg,var(--theme-main),#059669);
    border:none;
    color:#fff;
    border-radius:30px;
    padding:6px 18px;
    font-weight:500;
}

.search-box .btn:hover{
    opacity:.9;
}

/* Card */
.card{
    border:none;
    border-radius:16px;
    background:#ffffff;
}

/* Table */
.table th{
    background:var(--theme-main);
    color:#ffffff;
    text-align:center;
    font-weight:500;
}

.table td{
    vertical-align:middle;
    font-size:.9rem;
}

.table tbody tr:hover{
    background:var(--theme-light);
}

/* Badge */
.badge-pending{
    background:#10b981;
    color:#ffffff;
    padding:6px 14px;
    font-size:.75rem;
    border-radius:20px;
}

/* Buttons */
.btn-view{
    background:linear-gradient(90deg,#10b981,#059669);
    border:none;
    color:#ffffff;
    border-radius:20px;
    padding:5px 14px;
}

.btn-view:hover{
    opacity:.9;
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-2">
    <div class="container-fluid px-4">

        <a class="navbar-brand d-flex align-items-center fw-semibold" href="#">
            <i class="bi bi-hourglass-split me-2"></i>
            Pending Customers
        </a>

        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link text-white" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
            </li>
        </ul>

        <!-- Search -->
        <form method="GET">
            <div class="input-group input-group-sm search-box">
                <span class="input-group-text bg-transparent border-0">
                    <i class="bi bi-search text-success"></i>
                </span>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search..."
                       value="<?= htmlspecialchars($search); ?>">
                <button class="btn">
                    Search
                </button>
            </div>
        </form>

    </div>
</nav>

<!-- CONTENT -->
<div class="container my-5">

    <div class="card shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Passport</th>
                            <th>Visa No</th>
                            <th>Visa Type</th>
                            <th>Sponsor</th>
                            <th>Agent</th>
                            <th>Status</th>
                            <th width="120">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="text-center fw-semibold"><?= $row['Customers_ID']; ?></td>
                                <td><?= htmlspecialchars($row['Name']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['Passport_No']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['Visa_No']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['Visa_Type']); ?></td>
                                <td class="text-center"><?= htmlspecialchars($row['Sponsor_Number']); ?></td>
                                <td><?= htmlspecialchars($row['Agent_Name']); ?></td>
                                <td class="text-center">
                                    <span class="badge badge-pending">
                                        <i class="bi bi-hourglass-split me-1"></i> Pending
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="pending_customer.php?id=<?= $row['Customers_ID']; ?>"
                                       class="btn btn-sm btn-view">
                                        <i class="bi bi-eye-fill"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-database-x fs-3 d-block mb-2"></i>
                                No pending customers found
                            </td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
