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

// =======================
// GET AGENT ID
// =======================
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Agent ID.");
}

$agent_id = intval($_GET['id']);

// =======================
// FETCH EXISTING AGENT DATA
// =======================
$sql = "SELECT Agent_Name, Agency_Name, mobile_no 
        FROM Agent 
        WHERE Agent_ID = ? AND user_id = ? LIMIT 1";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $agent_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    die("Agent not found or you do not have permission to edit this agent.");
}

$agent = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// =======================
// HANDLE FORM SUBMISSION
// =======================
$errors = [];

if (isset($_POST['submit'])) {
    $Agent_Name  = trim($_POST['Agent_Name']);
    $Agency_Name = trim($_POST['Agency_Name']);
    $mobile_no   = trim($_POST['mobile_no']);

    // --- Validation ---
    if ($Agent_Name === '') {
        $errors[] = "Agent Name is required.";
    }

    if ($mobile_no === '') {
        $errors[] = "Mobile Number is required.";
    } elseif (!preg_match('/^\d+$/', $mobile_no)) {
        $errors[] = "Mobile Number must contain only digits.";
    }

    // --- Update if no errors ---
    if (empty($errors)) {
        $update_sql = "UPDATE Agent 
                       SET Agent_Name = ?, Agency_Name = ?, mobile_no = ? 
                       WHERE Agent_ID = ? AND user_id = ?";
        $stmt = mysqli_prepare($con, $update_sql);
        mysqli_stmt_bind_param($stmt, "sssii", $Agent_Name, $Agency_Name, $mobile_no, $agent_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect back to agent list
        header("Location: agent.php");
        exit();
    }
}
?>

<!-------------HTML PART------------------>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Agent</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f4f6f9; }
.card-header { background: #0d6efd; color: #fff; }
</style>
</head>
<body>
<div class="container mt-4">

    <div class="mb-3">
        <a href="agent.php" class="btn btn-secondary">← Back to Agent List</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">Edit Agent</div>
        <div class="card-body">
            <!-- Display Errors -->
            <?php if(!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach($errors as $err): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Agent Name</label>
                    <input type="text" name="Agent_Name" class="form-control <?= in_array('Agent Name is required.', $errors) ? 'is-invalid' : '' ?>" required 
                           value="<?= htmlspecialchars($agent['Agent_Name']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Agency Name</label>
                    <input type="text" name="Agency_Name" class="form-control" 
                           value="<?= htmlspecialchars($agent['Agency_Name']) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Mobile No</label>
                    <input type="text" name="mobile_no" class="form-control <?= in_array('Mobile Number is required.', $errors) || in_array('Mobile Number must contain only digits.', $errors) ? 'is-invalid' : '' ?>" required
                           value="<?= htmlspecialchars($agent['mobile_no']) ?>">
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Update Agent</button>
            </form>
        </div>
    </div>

</div>
</body>
</html>
