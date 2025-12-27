<?php
session_start();
include("config.php");

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please login.");
}
$user_id = intval($_SESSION['user_id']);

/* ================= VALIDATE REQUEST ================= */
if (!isset($_GET['Customers_ID'])) {
    die("Invalid request.");
}
$customer_id = intval($_GET['Customers_ID']);

/* ================= FETCH CUSTOMER ================= */
$sql = "
SELECT 
    c.*,
    v.Visa_ID, v.Visa_No, v.Visa_Type,
    s.Sponsor_ID, s.Sponsor_Number, s.Sponsor_Name
FROM Customers c
INNER JOIN Visa v ON c.Visa_ID = v.Visa_ID
INNER JOIN Sponsor s ON v.Sponsor_ID = s.Sponsor_ID
WHERE c.Customers_ID = ? AND c.user_id = ?
";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $customer_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Record not found.");
}

$row = mysqli_fetch_assoc($result);

/* ================= UPDATE PROCESS ================= */
$errors = [];
if (isset($_POST['update'])) {

    $name           = trim($_POST['Name']);
    $father_name    = trim($_POST['Father_Name']);
    $passport_no    = trim($_POST['Passport_No']);
    $phone          = trim($_POST['Phone']);
    $send_date      = $_POST['Send_Date'];
    $medical_expiry = $_POST['Medical_Expiry'];
    $e_number       = trim($_POST['E_Number']);
    $permission     = trim($_POST['Permission']);
    $amount         = trim($_POST['Amount']);
    $status_id      = intval($_POST['Status_ID']);

    $visa_no        = trim($_POST['Visa_No']);
    $visa_type      = trim($_POST['Visa_Type']);

    $sponsor_no     = trim($_POST['Sponsor_Number']);
    $sponsor_name   = trim($_POST['Sponsor_Name']);

    /* ================= VALIDATION ================= */
    if ($name === '') $errors[] = "Customer Name is required.";
    if ($father_name === '') $errors[] = "Father Name is required.";
    if ($passport_no === '') $errors[] = "Passport Number is required.";
    if ($phone === '' || !ctype_digit($phone)) $errors[] = "Valid phone number required.";
    if ($status_id === 0) $errors[] = "Status is required.";
    if ($visa_type === '') $errors[] = "Visa Type is required.";
    if ($sponsor_name === '') $errors[] = "Sponsor Name is required.";

    if (empty($errors)) {
        mysqli_begin_transaction($con);
        try {
            /* UPDATE SPONSOR */
            $sp = mysqli_prepare($con, "UPDATE Sponsor SET Sponsor_Number=?, Sponsor_Name=? WHERE Sponsor_ID=?");
            mysqli_stmt_bind_param($sp, "isi", $sponsor_no, $sponsor_name, $row['Sponsor_ID']);
            mysqli_stmt_execute($sp);

            /* UPDATE VISA */
            $vi = mysqli_prepare($con, "UPDATE Visa SET Visa_No=?, Visa_Type=? WHERE Visa_ID=?");
            mysqli_stmt_bind_param($vi, "isi", $visa_no, $visa_type, $row['Visa_ID']);
            mysqli_stmt_execute($vi);

            /* UPDATE CUSTOMER */
            $cu = mysqli_prepare($con, "UPDATE Customers SET Name=?, Father_Name=?, Passport_No=?, Phone=?, Send_Date=?, Medical_Expiry=?, E_Number=?, Permission=?, Amount=?, Status_ID=? WHERE Customers_ID=? AND user_id=?");
            mysqli_stmt_bind_param($cu, "ssssssssiiii", $name, $father_name, $passport_no, $phone, $send_date, $medical_expiry, $e_number, $permission, $amount, $status_id, $customer_id, $user_id);
            mysqli_stmt_execute($cu);

            mysqli_commit($con);

            echo "<script>alert('Customer updated successfully'); window.location='view_records.php';</script>";
            exit;
        } catch (Exception $e) {
            mysqli_rollback($con);
            $errors[] = "Update failed: " . $e->getMessage();
        }
    }
}
?>
<!------------HTML------------------>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Update Customer | OEARMS</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" href="log.jpg" type="image/x-icon">

<style>
/* ================== GLOBAL ================== */
body {
    background: #f4f6fb;
}

/* ================== CARD ================== */
.card {
    border-radius: 16px;
    border: none;
}

/* ================== SECTION HEADERS ================== */
.section-title {
    padding: 12px 18px;
    border-radius: 12px;
    color: #fff;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

/* Gradients */
.customer-gradient {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.visa-gradient {
    background: linear-gradient(135deg, #11998e, #38ef7d);
}

.sponsor-gradient {
    background: linear-gradient(135deg, #f7971e, #ffd200);
    color: #212529;
}

.status-gradient {
    background: linear-gradient(135deg, #ff512f, #dd2476);
}

/* ================== FORM ================== */
.form-control,
.form-select {
    border-radius: 10px;
}

.form-control:focus,
.form-select:focus {
    box-shadow: 0 0 0 .15rem rgba(13,110,253,.15);
}

/* ================== BUTTON ================== */
.btn-success {
    border-radius: 12px;
    font-weight: 600;
    transition: all .3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px rgba(25,135,84,.35);
}
</style>
</head>

<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white rounded-top">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square me-2"></i>
                        Update Customer Record
                    </h4>
                </div>

                <div class="card-body p-4">

                    <!-- ERRORS -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0">
                                <?php foreach ($errors as $e): ?>
                                    <li><?= htmlspecialchars($e); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <!-- CUSTOMER INFO -->
                        <h5 class="section-title customer-gradient mb-3">
                            <i class="bi bi-person-fill"></i> Customer Information
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="Name" class="form-control" value="<?= htmlspecialchars($row['Name']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Father Name</label>
                                <input type="text" name="Father_Name" class="form-control" value="<?= htmlspecialchars($row['Father_Name']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Passport No</label>
                                <input type="text" name="Passport_No" class="form-control" value="<?= htmlspecialchars($row['Passport_No']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Phone</label>
                                <input type="text" name="Phone" class="form-control" value="<?= htmlspecialchars($row['Phone']); ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Amount</label>
                                <input type="number" name="Amount" class="form-control" value="<?= htmlspecialchars($row['Amount']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Send Date</label>
                                <input type="date" name="Send_Date" class="form-control" value="<?= $row['Send_Date']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Medical Expiry</label>
                                <input type="date" name="Medical_Expiry" class="form-control" value="<?= $row['Medical_Expiry']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-Number</label>
                                <input type="text" name="E_Number" class="form-control" value="<?= htmlspecialchars($row['E_Number']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Permission</label>
                                <input type="text" name="Permission" class="form-control" value="<?= htmlspecialchars($row['Permission']); ?>">
                            </div>
                        </div>

                        <!-- VISA INFO -->
                        <h5 class="section-title visa-gradient mb-3">
                            <i class="bi bi-passport-fill"></i> Visa Information
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Visa Number</label>
                                <input type="number" name="Visa_No" class="form-control" value="<?= $row['Visa_No']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Visa Type</label>
                                <input type="text" name="Visa_Type" class="form-control" value="<?= htmlspecialchars($row['Visa_Type']); ?>">
                            </div>
                        </div>

                        <!-- SPONSOR INFO -->
                        <h5 class="section-title sponsor-gradient mb-3">
                            <i class="bi bi-people-fill"></i> Sponsor Information
                        </h5>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Sponsor Number</label>
                                <input type="number" name="Sponsor_Number" class="form-control" value="<?= $row['Sponsor_Number']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sponsor Name</label>
                                <input type="text" name="Sponsor_Name" class="form-control" value="<?= htmlspecialchars($row['Sponsor_Name']); ?>">
                            </div>
                        </div>

                        <!-- STATUS -->
                        <h5 class="section-title status-gradient mb-3">
                            <i class="bi bi-check-circle-fill"></i> Status Information
                        </h5>

                        <div class="mb-4">
                            <select name="Status_ID" class="form-select">
                                <?php
                                $status = mysqli_query($con, "SELECT * FROM Status");
                                while ($s = mysqli_fetch_assoc($status)) {
                                    $selected = ($s['Status_ID'] == $row['Status_ID']) ? "selected" : "";
                                    echo "<option value='{$s['Status_ID']}' $selected>{$s['Status_Name']}</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" name="update" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-save me-2"></i> Update Customer
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
