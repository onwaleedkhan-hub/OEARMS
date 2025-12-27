<?php
session_start();
include("config.php");

/* =========================
   AUTH CHECK
========================= */
if (!isset($_SESSION['user_id'])) {
    die("<h4 class='text-center text-danger mt-5'>Access Denied. Please login.</h4>");
}

$user_id = $_SESSION['user_id'];

/* =========================
   FETCH PENDING CUSTOMERS
========================= */
$sql = "
SELECT 
    c.Name, c.Father_Name, c.Passport_No, c.Phone,
    c.Medical_Expiry, c.Send_Date, c.E_Number, c.Permission,
    v.Visa_No, v.Visa_Type,
    s.Sponsor_Number, s.Sponsor_Name,
    a.Agent_Name,
    st.Status_Name
FROM Customers c
INNER JOIN Agent a   ON c.Agent_ID = a.Agent_ID
INNER JOIN Visa v    ON c.Visa_ID = v.Visa_ID
INNER JOIN Sponsor s ON v.Sponsor_ID = s.Sponsor_ID
INNER JOIN Status st ON c.Status_ID = st.Status_ID
WHERE st.Status_Name = ?
AND c.user_id = ?
ORDER BY c.Customers_ID DESC
";

$stmt = mysqli_prepare($con, $sql);
$status = "Pending";
mysqli_stmt_bind_param($stmt, "si", $status, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pending Customers</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background: #f1f5f9;
    font-size: 0.9rem;
    color: #1f2937;
}

/* HEADER */
.page-header{
    background: linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #ffffff;
    padding: 26px 22px;
    border-radius: 18px;
    margin-bottom: 30px;
    box-shadow: 0 14px 32px rgba(37, 99, 235, 0.25);
}

/* CARD */
.card{
    border: none;
    border-radius: 18px;
    background: #ffffff;
    box-shadow: 0 12px 26px rgba(15, 23, 42, 0.06);
    margin-bottom: 26px;
}

/* SECTION TITLE */
.section-title{
    font-size: .85rem;
    font-weight: 700;
    color: #1e40af;
    margin: 22px 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* LABEL */
.form-label{
    font-size: .75rem;
    font-weight: 600;
    color: #475569;
}

/* INPUT */
.form-control{
    background: #f8fafc;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    font-size: .85rem;
    color: #111827;
}

/* STATUS */
.status-pending{
    background:  linear-gradient(135deg, #1e3a8a, #2563eb);
    color: #f5f8f9ff;
    font-size: .75rem;
    font-weight: 700;
    padding: 7px 18px;
    border-radius: 999px;
    display: inline-block;
}

/* RESPONSIVE */
@media (max-width: 768px){
    .section-title{
        margin-top: 26px;
    }
}
</style>
</head>

<body>

<div class="container my-4">

    <!-- HEADER -->
    <div class="page-header text-center">
        <h4 class="mb-1">
            <i class="bi bi-hourglass-split me-2"></i>
            Pending Customers
        </h4>
        <small class="opacity-75">Awaiting Approval / Processing</small>
    </div>

<?php if(mysqli_num_rows($result) > 0): ?>
<?php while($row = mysqli_fetch_assoc($result)): ?>

<div class="card">
<div class="card-body">

    <!-- CUSTOMER -->
    <div class="section-title">
        <i class="bi bi-person-fill"></i> Customer Details
    </div>
    <div class="row g-3">
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Name</label>
            <input class="form-control" value="<?= htmlspecialchars($row['Name']); ?>" readonly>
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Father Name</label>
            <input class="form-control" value="<?= htmlspecialchars($row['Father_Name']); ?>" readonly>
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Passport No</label>
            <input class="form-control" value="<?= $row['Passport_No']; ?>" readonly>
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Phone</label>
            <input class="form-control" value="<?= $row['Phone']; ?>" readonly>
        </div>
    </div>

    <!-- DATES -->
    <div class="row g-3 mt-2">
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Medical Expiry</label>
            <input class="form-control" value="<?= $row['Medical_Expiry']; ?>" readonly>
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Send Date</label>
            <input class="form-control" value="<?= $row['Send_Date']; ?>" readonly>
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label">E-Number</label>
            <input class="form-control" value="<?= $row['E_Number']; ?>" readonly>
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Permission</label>
            <input class="form-control" value="<?= $row['Permission']; ?>" readonly>
        </div>
    </div>

    <!-- VISA -->
    <div class="section-title">
        <i class="bi bi-file-earmark-text"></i> Visa Information
    </div>
    <div class="row g-3">
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Visa No</label>
            <input class="form-control" value="<?= $row['Visa_No']; ?>" readonly>
        </div>
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Visa Type</label>
            <input class="form-control" value="<?= $row['Visa_Type']; ?>" readonly>
        </div>
    </div>

    <!-- SPONSOR -->
    <div class="section-title">
        <i class="bi bi-building"></i> Sponsor Details
    </div>
    <div class="row g-3">
        <div class="col-sm-6 col-md-3">
            <label class="form-label">Sponsor No</label>
            <input class="form-control" value="<?= $row['Sponsor_Number']; ?>" readonly>
        </div>
        <div class="col-sm-6 col-md-5">
            <label class="form-label">Sponsor Name</label>
            <input class="form-control" value="<?= $row['Sponsor_Name']; ?>" readonly>
        </div>
    </div>

    <!-- AGENT & STATUS -->
    <div class="section-title">
        <i class="bi bi-people-fill"></i> Agent & Status
    </div>
    <div class="row g-3 align-items-center">
        <div class="col-sm-6 col-md-4">
            <label class="form-label">Agent</label>
            <input class="form-control" value="<?= htmlspecialchars($row['Agent_Name']); ?>" readonly>
        </div>
        <div class="col-sm-6 col-md-4">
            <label class="form-label d-block">Status</label>
            <span class="status-pending"><?= $row['Status_Name']; ?></span>
        </div>
    </div>

</div>
</div>

<?php endwhile; ?>
<?php else: ?>

<div class="alert alert-info text-center">
    <i class="bi bi-info-circle-fill me-2"></i>
    No pending customers found
</div>

<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
