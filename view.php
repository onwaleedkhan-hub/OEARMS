<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    die("<h2 class='text-center text-danger'>Access Denied. Please login first</h2>");
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("<h2 class='text-center text-danger'>Invalid Request</h2>");
}

$id = mysqli_real_escape_string($con, $_GET['id']); // Prevent SQL Injection

$sql = "SELECT 
            c.Customers_ID, c.Name, c.Father_Name, c.Passport_No, c.Phone,
            c.Send_Date, c.Medical_Expiry, c.E_Number, c.Permission, c.Amount,
            v.Visa_No, v.Visa_Type,
            s.Sponsor_Number, s.Sponsor_Name,
            e.Location AS Embassy_Location,
            a.Agent_Name,
            st.Status_Name
        FROM Customers c
        LEFT JOIN Visa v ON c.Visa_ID = v.Visa_ID
        LEFT JOIN Sponsor s ON v.Sponsor_ID = s.Sponsor_ID
        LEFT JOIN Embassy e ON v.Embassy_ID = e.Embassy_ID
        LEFT JOIN Agent a ON c.Agent_ID = a.Agent_ID
        LEFT JOIN Status st ON c.Status_ID = st.Status_ID
        WHERE c.user_id = '$user_id' AND c.Customers_ID = '$id'";

$result = mysqli_query($con, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    die("<h2 class='text-center text-danger'>Record Not Found</h2>");
}

$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Details</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<!-- Custom CSS -->
<link rel="stylesheet" href="view_style.css">
<!--favicon-->
<link rel="icon" type="image/png" href="log.jpg"/>
</head>
<body>

<div class="container my-5">

    <!-- Page Title -->
    <h2 class="page-title text-center mb-5"><i class="bi bi-person-lines-fill"></i> Customer Full Details</h2>

    <!-- Action Buttons -->
    <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
        <a href="view_records.php" class="btn btn-gradient btn-custom shadow-sm">
            <i class="bi bi-arrow-left-circle"></i> Back
        </a>
        <a href="view_update.php?Customers_ID=<?= $row['Customers_ID']; ?>" class="btn btn-gradient-warning btn-custom shadow-sm">
            <i class="bi bi-pencil-square"></i> Update
        </a>
        <a href="delete_view.php?Customers_ID=<?= $row['Customers_ID']; ?>" class="btn btn-gradient-danger btn-custom shadow-sm" onclick="return confirm('Are you sure you want to delete this record?');">
            <i class="bi bi-trash"></i> Delete
        </a>
    </div>

    <!-- Customer Sections -->
    <div class="row g-4">
        <!-- Personal Info -->
        <div class="col-md-6">
            <div class="card shadow-sm custom-card">
                <div class="card-header text-white text-center"><i class="bi bi-person-circle"></i> Personal Information</div>
                <div class="card-body">
                    <p><strong>Name:</strong> <?= htmlspecialchars($row['Name']); ?></p>
                    <p><strong>Father Name:</strong> <?= htmlspecialchars($row['Father_Name']); ?></p>
                    <p><strong>Passport No:</strong> <?= htmlspecialchars($row['Passport_No']); ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($row['Phone']); ?></p>
                    <p><strong>Send Date:</strong> <?= htmlspecialchars($row['Send_Date']); ?></p>
                    <p><strong>Medical Expiry:</strong> <?= htmlspecialchars($row['Medical_Expiry']); ?></p>
                </div>
            </div>
        </div>

        <!-- Visa Info -->
        <div class="col-md-6">
            <div class="card shadow-sm custom-card">
                <div class="card-header text-white text-center"><i class="bi bi-card-checklist"></i> Visa Information</div>
                <div class="card-body">
                    <p><i class="bi"></i> <strong>E-Number:</strong> <?= htmlspecialchars($row['E_Number']); ?></p>
                    <p><i class="bi"></i> <strong>Permission:</strong> <?= htmlspecialchars($row['Permission']); ?></p>
                    <p><i class="bi"></i> <strong>Amount:</strong> <?= htmlspecialchars($row['Amount']); ?></p>
                    <p><i class="bi"></i> <strong>Visa No:</strong> <?= htmlspecialchars($row['Visa_No']); ?></p>
                    <p><i class="bi"></i> <strong>Visa Type:</strong> <?= htmlspecialchars($row['Visa_Type']); ?></p>
                </div>
            </div>
        </div>

        <!-- Sponsor Info -->
        <div class="col-md-6">
            <div class="card shadow-sm custom-card">
                <div class="card-header text-white text-center"><i class="bi bi-people-fill"></i> Sponsor Information</div>
                <div class="card-body">
                    <p><i class="bi "></i> <strong>Sponsor No:</strong> <?= htmlspecialchars($row['Sponsor_Number']); ?></p>
                    <p><i class="bi"></i> <strong>Sponsor Name:</strong> <?= htmlspecialchars($row['Sponsor_Name']); ?></p>
                    <p><i class="bi "></i> <strong>Embassy:</strong> <?= htmlspecialchars($row['Embassy_Location']); ?></p>
                </div>
            </div>
        </div>

        <!-- Agent & Status -->
        <div class="col-md-6">
            <div class="card shadow-sm custom-card">
                <div class="card-header text-white text-center"><i class="bi bi-person-badge-fill"></i> Agent & Status</div>
                <div class="card-body text-center">
                    <p><i class="bi "></i> <strong>Agent:</strong> <?= htmlspecialchars($row['Agent_Name']); ?></p>
                    <p><i class="bi "></i> <strong>Status:</strong>
                        <?php
                        $status = strtolower(htmlspecialchars($row['Status_Name']));
                        if($status == 'approved'){
                            echo "<span class='badge badge-gradient-approved'><i class='bi bi-check-circle'></i> $status</span>";
                        } elseif($status == 'pending'){
                            echo "<span class='badge badge-gradient-pending'><i class='bi bi-clock'></i> $status</span>";
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
