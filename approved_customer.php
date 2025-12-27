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
   FETCH APPROVED CUSTOMERS
========================= */
$sql = "
SELECT 
    c.Customers_ID,
    c.Name,
    c.Father_Name,
    c.Passport_No,
    c.Phone,
    c.Send_Date,
    c.Medical_Expiry,
    c.E_Number,
    c.Permission,
    c.Amount,
    v.Visa_No,
    v.Visa_Type,
    s.Sponsor_Number,
    s.Sponsor_Name,
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
$status = "Approved";
mysqli_stmt_bind_param($stmt, "si", $status, $user_id);
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
<!---favicon-->
<link rel="icon" type="image/png" href="log.jpg"/>
<style>
body{
    background: #f1f4f9;
    font-size: 0.9rem;
}

.page-header{
    background: linear-gradient(135deg, #4f46e5, #06b6d4);
    color: #fff;
    padding: 20px;
    border-radius: 14px;
    margin-bottom: 25px;
}

.card{
    border: none;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    margin-bottom: 25px;
}

.section-title{
    font-size: 0.85rem;
    font-weight: 700;
    color: #4f46e5;
    margin: 18px 0 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.form-label{
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
}

.form-control{
    background: #f9fafb;
    border-radius: 10px;
    font-size: 0.85rem;
}

.status-badge{
    background: linear-gradient(135deg, #22c55e, #16a34a);
    color: #fff;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-block;
}

@media (max-width: 768px){
    .section-title{
        margin-top: 25px;
    }
}
</style>
</head>

<body>

<div class="container my-4">

    <!-- Header -->
    <div class="page-header text-center">
        <h4 class="mb-1">
            <i class="bi bi-check-circle-fill me-2"></i>
            Approved Customers
        </h4>
        <small>Verified & Approved Records</small>
    </div>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>

        <div class="card">
            <div class="card-body">

                <!-- Customer Info -->
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

                <!-- Dates -->
                <div class="row g-3 mt-2">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label">Send Date</label>
                        <input class="form-control" value="<?= $row['Send_Date']; ?>" readonly>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label">Medical Expiry</label>
                        <input class="form-control" value="<?= $row['Medical_Expiry']; ?>" readonly>
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

                <!-- Visa -->
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

                <!-- Sponsor -->
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

                <!-- Status -->
                <div class="section-title">
                    <i class="bi bi-info-circle-fill"></i> Status
                </div>
                <span class="status-badge">
                    <?= $row['Status_Name']; ?>
                </span>

            </div>
        </div>

        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            No approved customers found
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
