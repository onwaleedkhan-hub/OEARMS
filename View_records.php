<?php
session_start();
include("config.php");

/* =======================
   CHECK LOGIN
======================= */
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please login first.");
}
$user_id = $_SESSION['user_id'];

/* =======================
   BASE SQL QUERY
======================= */
$sql = "SELECT 
            c.Customers_ID,
            c.Name,
            c.Passport_No,
            v.Visa_No,
            s.Sponsor_Number,
            a.Agent_Name,
            st.Status_Name
        FROM Customers c
        LEFT JOIN Visa v     ON c.Visa_ID = v.Visa_ID
        LEFT JOIN Sponsor s ON v.Sponsor_ID = s.Sponsor_ID
        LEFT JOIN Agent a   ON c.Agent_ID = a.Agent_ID
        LEFT JOIN Status st ON c.Status_ID = st.Status_ID
        WHERE c.user_id = ?";

$params = [$user_id];
$types  = "i";

/* =======================
   SEARCH FUNCTIONALITY
======================= */
if (!empty($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";
    $sql .= " AND (
                c.Name LIKE ? OR
                c.Passport_No LIKE ? OR
                v.Visa_No LIKE ? OR
                s.Sponsor_Number LIKE ? OR
                a.Agent_Name LIKE ? OR
                st.Status_Name LIKE ?
            )";

    $types .= "ssssss";
    $params = array_merge($params, array_fill(0, 6, $search));
}

$sql .= " ORDER BY c.Customers_ID ASC";

/* =======================
   PREPARE & EXECUTE
======================= */
$stmt = mysqli_prepare($con, $sql) or die(mysqli_error($con));
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!--- HTML STARTS HERE -->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Records</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<!-- Custom CSS -->
<link  rel="stylesheet" href="view_recordstyle.css">
<!-- Favicon -->
<link rel="icon" href="log.jpg">
</head>

<body class="bg-light">

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="dashboard.php">
            <i class="bi bi-people-fill me-2 text-info"></i>OEARMS
        </a>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link text-light" href="dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-light" href="add_record.php">
                        <i class="bi bi-person-plus-fill me-1"></i>New Customer
                    </a>
                </li>
            </ul>

            <!-- Search -->
            <form method="GET" class="d-flex align-items-center gap-2">
                <div class="position-relative" style="width:220px;">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="search"
                           name="query"
                           class="form-control ps-5 rounded-pill"
                           placeholder="Search records..."
                           value="<?= isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '' ?>">
                </div>
                <button class="btn btn-outline-info rounded-pill px-4" type="submit">
                    Search
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- ================= CONTENT ================= -->
<div class="container my-5">
    <h3 class="text-center fw-bold text-dark mb-4">
        <i class="bi bi-card-list text-info me-2"></i>Customer Records
    </h3>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">

            <div class="table-responsive">
                <table class="table table-hover align-middle text-nowrap">
                    <thead class="bg-secondary text-white text-center">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Passport No</th>
                            <th>Visa No</th>
                            <th>Sponsor No</th>
                            <th>Agent</th>
                            <th>Status</th>
                            <th>Action</th>
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
                                <td class="text-center"><?= htmlspecialchars($row['Sponsor_Number']); ?></td>
                                <td><?= htmlspecialchars($row['Agent_Name']); ?></td>
                                <td class="text-center">
                                    <span class="badge px-3 py-2
                                        <?= $row['Status_Name'] === 'Approved' ? 'bg-success' :
                                           ($row['Status_Name'] === 'Pending' ? 'bg-warning text-dark' : 'bg-secondary'); ?>">
                                        <?= $row['Status_Name']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="view.php?id=<?= $row['Customers_ID']; ?>"
                                       class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                        <i class="bi bi-eye-fill me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
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
