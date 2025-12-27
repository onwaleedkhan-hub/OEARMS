<?php 
include("config.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];

if (isset($_POST['submit'])) {

    // --- Fetch Form Inputs ---
$Sponsor_Number_Input = isset($_POST['Sponsor_Number']) ? trim($_POST['Sponsor_Number']) : '';
$Sponsor_Name         = isset($_POST['Sponsor_Name']) ? trim($_POST['Sponsor_Name']) : '';
$Visa_Type            = isset($_POST['Visa_Type']) ? trim($_POST['Visa_Type']) : '';
$Visa_No              = isset($_POST['Visa_No']) ? trim($_POST['Visa_No']) : '';
$Status_ID            = isset($_POST['Status_ID']) ? intval($_POST['Status_ID']) : 0;
$Name                 = isset($_POST['Name']) ? trim($_POST['Name']) : '';
$Father_Name          = isset($_POST['Father_Name']) ? trim($_POST['Father_Name']) : '';
$Passport_No          = isset($_POST['Passport_No']) ? trim($_POST['Passport_No']) : '';
$Phone                = isset($_POST['Phone']) ? trim($_POST['Phone']) : '';
$Send_Date            = isset($_POST['Send_Date']) ? trim($_POST['Send_Date']) : '';
$Medical_Expiry       = isset($_POST['Medical_Expiry']) ? trim($_POST['Medical_Expiry']) : '';
$E_Number             = isset($_POST['E_Number']) ? trim($_POST['E_Number']) : '';
$Permission           = isset($_POST['Permission']) ? trim($_POST['Permission']) : '';
$Amount               = isset($_POST['Amount']) ? trim($_POST['Amount']) : '';
$Embassy_ID           = isset($_POST['Embassy_ID']) ? intval($_POST['Embassy_ID']) : 0;
$Agent_ID             = isset($_POST['Agent_ID']) ? intval($_POST['Agent_ID']) : 0;


    // --- Validation ---
    if ($Name === '') $errors[] = "Customer Name is required.";
    if ($Father_Name === '') $errors[] = "Father Name is required.";
    if ($Passport_No === '') $errors[] = "Passport Number is required.";
    if ($Phone === '') $errors[] = "Phone Number is required.";
    if ($Embassy_ID === 0) $errors[] = "Please select an Embassy.";
    if ($Agent_ID === 0) $errors[] = "Please select an Agent.";
    if ($Status_ID === 0) $errors[] = "Status is required."; // ✅ FIX

    if ($Sponsor_Number_Input !== '' && !is_numeric($Sponsor_Number_Input))
        $errors[] = "Sponsor Number must be numeric.";

    if ($Visa_No !== '' && !is_numeric($Visa_No))
        $errors[] = "Visa Number must be numeric.";

    if ($Amount !== '' && !is_numeric($Amount))
        $errors[] = "Amount must be numeric.";

    // --- Show Errors ---
    if (!empty($errors)) {
        echo "<div class='container'><div class='alert alert-danger'><ul>";
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul></div></div>";
    } else {

        // Insert Sponsor
        $stmt = mysqli_prepare($con,
            "INSERT INTO sponsor (Sponsor_Number, Sponsor_Name) VALUES (?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "is", $Sponsor_Number_Input, $Sponsor_Name);
        mysqli_stmt_execute($stmt);
        $Sponsor_ID = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);

        // Insert Visa
        $stmt = mysqli_prepare($con,
            "INSERT INTO visa (Visa_Type, Visa_No, Sponsor_ID, Embassy_ID)
             VALUES (?, ?, ?, ?)"
        );
        mysqli_stmt_bind_param($stmt, "siii",
            $Visa_Type, $Visa_No, $Sponsor_ID, $Embassy_ID
        );
        mysqli_stmt_execute($stmt);
        $Visa_ID = mysqli_insert_id($con);
        mysqli_stmt_close($stmt);

        // Insert Customer (NO STATUS INSERT ❌)
        $stmt = mysqli_prepare($con,
            "INSERT INTO customers
            (Name, Father_Name, Passport_No, Phone, Send_Date,
             Medical_Expiry, E_Number, Permission, Amount,
             Visa_ID, Agent_ID, user_id, Status_ID)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssdiiii",
            $Name,
            $Father_Name,
            $Passport_No,
            $Phone,
            $Send_Date,
            $Medical_Expiry,
            $E_Number,
            $Permission,
            $Amount,
            $Visa_ID,
            $Agent_ID,
            $user_id,
            $Status_ID
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "<script>alert('Data Saved Successfully');</script>";
    }
}
?>
<!------------------html code------------------>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Customer Record</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<!-- Favicon -->
<link rel="icon" href="log.jpg" type="image/x-icon">
<!-- Custom CSS -->
<link rel="stylesheet" href="recordstyle.css">
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg shadow-sm mb-4" style="background: linear-gradient(90deg, #4075afff, #2496b5ff);">
  <div class="container justify-content-center">
    <ul class="navbar-nav flex-row gap-4">
      <li class="nav-item">
        <a class="nav-link text-white fw-semibold px-3 py-2 rounded hover-effect" href="dashboard.php">
          <i class="bi bi-speedometer2 me-1"></i>Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white fw-semibold px-3 py-2 rounded hover-effect" href="View_records.php">
          <i class="bi bi-card-list me-1"></i>View All Customers
        </a>
      </li>
    </ul>
  </div>
</nav>

<div class="container">
    <form method="POST" action="" class="needs-validation form-custom mx-auto" style="max-width:600px;" novalidate>

        <!-- Customer Information -->
        <div class="card">
            <div class="card-header header-customer">
                <i class="bi bi-person-fill me-2"></i>Customer Information
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="Name" class="form-control" placeholder="Enter Name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Father Name</label>
                    <input type="text" name="Father_Name" class="form-control" placeholder="Enter Father Name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Passport No</label>
                    <input type="text" name="Passport_No" class="form-control" placeholder="Enter Passport No">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="Phone" class="form-control" placeholder="Enter Phone Number">
                </div>
                <div class="mb-3">
                    <label class="form-label">Amount</label>
                    <input type="number" name="Amount" class="form-control" placeholder="Enter Amount">
                </div>
                <div class="mb-3">
                    <label class="form-label">Send Date</label>
                    <input type="date" name="Send_Date" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Medical Expiry</label>
                    <input type="date" name="Medical_Expiry" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">E-Number</label>
                    <input type="text" name="E_Number" class="form-control" placeholder="Enter E-Number">
                </div>
                <div class="mb-3">
                    <label class="form-label">Permission</label>
                    <input type="text" name="Permission" class="form-control" placeholder="Enter Permission">
                </div>
            </div>
        </div>

        <!-- Visa Information -->
        <div class="card">
            <div class="card-header header-visa">
                <i class="bi bi-passport-fill me-2"></i>Visa Information
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Visa Type</label>
                    <input type="text" name="Visa_Type" class="form-control" placeholder="Enter Visa Type">
                </div>
                <div class="mb-3">
                    <label class="form-label">Visa No</label>
                    <input type="number" name="Visa_No" class="form-control" placeholder="Enter Visa Number">
                </div>
            </div>
        </div>

        <!-- Sponsor Information -->
        <div class="card">
            <div class="card-header header-sponsor">
                <i class="bi bi-people-fill me-2"></i>Sponsor Information
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Sponsor Number</label>
                    <input type="number" name="Sponsor_Number" class="form-control" placeholder="Enter Sponsor Number">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sponsor Name</label>
                    <input type="text" name="Sponsor_Name" class="form-control" placeholder="Enter Sponsor Name">
                </div>
            </div>
        </div>

        <!-- Embassy / Agent -->
        <div class="card">
            <div class="card-header header-embassy">
                <i class="bi bi-building me-2"></i>Embassy / Agent
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Embassy</label>
                    <select class="form-select" name="Embassy_ID" required>
                        <option value="">-- Select Embassy --</option>
                        <?php
                        $embassy_sql = "SELECT Embassy_ID, Location FROM Embassy ORDER BY Location ASC";
                        $embassy_result = mysqli_query($con, $embassy_sql);
                        while ($row = mysqli_fetch_assoc($embassy_result)) {
                            echo "<option value='" . $row['Embassy_ID'] . "'>" . htmlspecialchars($row['Location']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Agent</label>
                    <select class="form-select" name="Agent_ID">
                        <option value="">-- Select Agent --</option>
                        <?php
                        $agent_sql = "SELECT Agent_ID, Agent_Name FROM Agent WHERE user_id=? ORDER BY Agent_Name ASC";
                        if ($stmt = mysqli_prepare($con, $agent_sql)) {
                            mysqli_stmt_bind_param($stmt, "i", $user_id);
                            mysqli_stmt_execute($stmt);
                            $agent_result = mysqli_stmt_get_result($stmt);
                            if (mysqli_num_rows($agent_result) > 0) {
                                while ($row = mysqli_fetch_assoc($agent_result)) {
                                    echo "<option value='" . $row['Agent_ID'] . "'>" . htmlspecialchars($row['Agent_Name']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>No agents available</option>";
                            }
                            mysqli_stmt_close($stmt);
                        } else {
                            echo "<option value=''>Error loading agents</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="card">
            <div class="card-header header-status">
                <i class="bi bi-check-circle-fill me-2"></i>Status
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <select class="form-select" name="Status_ID">
                        <option value="">-- Select Status --</option>
                        <?php
                        $query = "SELECT Status_ID, Status_Name FROM Status ORDER BY Status_Name ASC";
                        $result = mysqli_query($con, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='{$row['Status_ID']}'>{$row['Status_Name']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="text-center mb-5">
            <button type="submit" name="submit" class="btn btn-primary btn-lg w-50">
                <i class="bi bi-save me-2"></i>Save Record
            </button>
        </div>

    </form>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
