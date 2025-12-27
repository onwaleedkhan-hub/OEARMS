 <?php
session_start();
include("config.php");

/* ==========================
   AUTH CHECK
========================== */
if (!isset($_SESSION['user_id'])) {
    die("<h4 class='text-center text-danger mt-5'>Access denied. Please login first.</h4>");
}
$user_id = $_SESSION['user_id'];

/* ==========================
   SEARCH VALUE
========================== */
$search = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
}

/* ==========================
   SELECT APPROVED CUSTOMERS
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
          AND (
                c.Customers_ID LIKE ?
                OR c.Name LIKE ?
                OR c.Passport_No LIKE ?
              )
        ORDER BY c.Customers_ID DESC";

$stmt = mysqli_prepare($con, $sql);
$status = "Approved";
$searchTerm = "%$search%";

mysqli_stmt_bind_param(
    $stmt,
    "sisss",
    $status,
    $user_id,
    $searchTerm,
    $searchTerm,
    $searchTerm
);

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Approved Customers</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" type="image/png" href="log.jpg">

<style>
:root{
    --primary:#1e40af;
    --secondary:#3b82f6;
    --light:#f8fafc;
    --dark:#0f172a;
}

/* Body */
body{
    background: linear-gradient(135deg,#f1f5f9,#e2e8f0);
    font-family: 'Segoe UI', sans-serif;
}

/* Navbar */
.navbar{
    background: linear-gradient(90deg,var(--dark),var(--primary));
}
.navbar-brand{
    font-size:1rem;
}

/* Search Box */
.search-box{
    background:#fff;
    border-radius:30px;
    padding:3px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
.search-box input{
    border:none;
    box-shadow:none;
}
.search-box input:focus{
    box-shadow:none;
}
.search-box .btn{
    border-radius:30px;
    background:linear-gradient(90deg,var(--primary),var(--secondary));
    color:#fff;
    border:none;
}
.search-box .btn:hover{
    opacity:.9;
}

/* Card */
.card{
    border:none;
    border-radius:16px;
    background:#fff;
}

/* Table */
.table th{
    background:var(--primary);
    color:#fff;
    text-align:center;
    font-weight:500;
}
.table td{
    vertical-align:middle;
    font-size:.9rem;
}
.table tbody tr:hover{
    background:#f1f5ff;
}

/* Badge */
.badge-approved{
    background:#16a34a;
    padding:6px 14px;
    font-size:.75rem;
    border-radius:20px;
}

/* Buttons */
.btn-view{
    background:linear-gradient(90deg,#0ea5e9,#2563eb);
    border:none;
    color:#fff;
    border-radius:20px;
    padding:5px 14px;
}
.btn-view:hover{
    opacity:.9;
}

/* Responsive */
@media (max-width: 768px){
    .search-box {
        margin-top: 10px;
    }
    .table th, .table td {
        font-size: 0.8rem;
    }
    .btn-view {
        padding: 4px 10px;
        font-size: 0.8rem;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm py-2">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center fw-semibold" href="#">
            <i class="bi bi-patch-check-fill me-2"></i>
            Approved Customers
        </a>

        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link text-white" href="dashboard.php">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
            </li>
        </ul>

        <!-- Search -->
        <form method="GET" class="d-flex flex-wrap">
            <div class="input-group input-group-sm search-box">
                <span class="input-group-text bg-transparent border-0">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search....."
                       value="<?= htmlspecialchars($search); ?>">
                <button class="btn px-3">
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
                                    <span class="badge badge-approved">Approved</span>
                                </td>
                                <td class="text-center">
                                    <a href="approved_customer.php?id=<?= $row['Customers_ID']; ?>"
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
                                No records found
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
