<?php
session_start();
include("config.php");

// =======================
// CHECK LOGIN
// =======================
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Initialize error and success messages
$errors = [];
$success = "";

// =======================
// DELETE AGENT
// =======================
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $sql = "DELETE FROM Agent WHERE Agent_ID = ? AND user_id = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $delete_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: agent.php");
    exit();
}

// =======================
// INSERT AGENT WITH VALIDATION
// =======================
if (isset($_POST['submit'])) {
    $Agent_Name  = trim($_POST['Agent_Name']);
    $Agency_Name = trim($_POST['Agency_Name']);
    $mobile_no   = trim($_POST['mobile_no']);

    // --- SERVER-SIDE VALIDATION ---
    
    // Required fields
    if (empty($Agent_Name)) {
        $errors[] = "Agent Name is required.";
    }

    if (empty($mobile_no)) {
        $errors[] = "Mobile Number is required.";
    } elseif (!preg_match('/^\d+$/', $mobile_no)) {
        $errors[] = "Mobile Number must contain only digits.";
    } elseif (strlen($mobile_no) < 7 || strlen($mobile_no) > 15) {
        $errors[] = "Mobile Number must be between 7 and 15 digits.";
    }

    // Optional: Validate Agency Name length
    if (!empty($Agency_Name) && strlen($Agency_Name) > 100) {
        $errors[] = "Agency Name is too long (max 100 characters).";
    }

    // Insert into database if no errors
    if (empty($errors)) {
        $sql = "INSERT INTO Agent (Agent_Name, Agency_Name, mobile_no, user_id)
                VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $Agent_Name, $Agency_Name, $mobile_no, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success = "Agent added successfully!";
    }
}

// =======================
// SELECT AGENTS FOR CURRENT USER
// =======================
$sql = "SELECT Agent_ID, Agent_Name, Agency_Name, mobile_no 
        FROM Agent 
        WHERE user_id = ? 
        ORDER BY Agent_ID ASC";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Agent Management</title>
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!--Custom CSS-->
<link rel="stylesheet" href="agentstyle.css">
<!-- Favicon -->
<link rel="icon" href="log.jpg" type="image/png">
<style>
    body { background-color: #f8f9fa; }
    .card-header { background-color: #0d6efd; color: #fff; font-weight: 600; }
    .btn-primary { border-radius: 6px; }
    .btn-warning, .btn-danger { border-radius: 6px; }
    .table th, .table td { vertical-align: middle; text-align: center; }
</style>
</head>
<body>

<div class="container mt-5">

    <!-- Back to Dashboard -->
    <div class="mb-3">
        <a href="dashboard.php" class="btn btn-secondary">&larr; Back to Dashboard</a>
    </div>

    <!-- Display errors -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success !== ''): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Add Agent Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">Add New Agent</div>
        <div class="card-body">
            <form method="POST">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Agent Name</label>
                        <input type="text" name="Agent_Name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Agency Name</label>
                        <input type="text" name="Agency_Name" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Mobile No</label>
                        <input type="text" name="mobile_no" class="form-control" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" name="submit" class="btn btn-primary">Save Agent</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Agent List -->
    <div class="card shadow-sm">
        <div class="card-header">Agent List</div>
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Agent ID</th>
                        <th>Agent Name</th>
                        <th>Agency</th>
                        <th>Mobile</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['Agent_ID']; ?></td>
                                <td><?= htmlspecialchars($row['Agent_Name']); ?></td>
                                <td><?= htmlspecialchars($row['Agency_Name']); ?></td>
                                <td><?= htmlspecialchars($row['mobile_no']); ?></td>
                                <td>
                                    <a href="agent_update.php?id=<?= $row['Agent_ID']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="agent_delete.php?delete=<?= $row['Agent_ID']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this agent?');">
                                       Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted">No agents found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
