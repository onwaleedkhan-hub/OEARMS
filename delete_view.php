<?php
session_start();
include("config.php");

if (!isset($_SESSION['user_id'])) {
    die("<h3 class='text-center text-danger mt-5'>Access Denied. Please login.</h3>");
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['Customers_ID'])) {
    die("<h3 class='text-center text-danger mt-5'>Invalid Request</h3>");
}

$customers_id = intval($_GET['Customers_ID']);
/* ===== FETCH CUSTOMER FOR CONFIRMATION ===== */
$sql = "SELECT Customers_ID, Name, Passport_No 
        FROM Customers 
        WHERE Customers_ID=? AND user_id=?";

$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $customers_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("<h3 class='text-center text-danger mt-5'>Record Not Found</h3>");
}

$row = mysqli_fetch_assoc($result);

/* ===== DELETE RECORD ===== */
if (isset($_POST['confirm_delete'])) {

    $delete = "DELETE FROM Customers WHERE Customers_ID=? AND user_id=?";
    $del_stmt = mysqli_prepare($con, $delete);
    mysqli_stmt_bind_param($del_stmt, "ii", $customers_id, $user_id);

    if (mysqli_stmt_execute($del_stmt)) {
        echo "<script>
                alert('Customer record deleted successfully');
                window.location='view_records.php';
              </script>";
        exit;
    } else {
        $error = "Failed to delete record. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Delete Customer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="icon" href="log.jpg" type="image/x-icon">
</head>

<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow delete-card">
                <div class="delete-header">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Confirm Delete
                </div>

                <div class="card-body text-center p-4">

                    <h5 class="mb-3 text-danger fw-bold">
                        Are you sure you want to delete this customer?
                    </h5>

                    <p class="mb-2">
                        <strong>Name:</strong> <?= htmlspecialchars($row['Name']); ?>
                    </p>

                    <p class="mb-4">
                        <strong>Passport No:</strong> <?= htmlspecialchars($row['Passport_No']); ?>
                    </p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="view_details.php?id=<?= $row['Customer_ID']; ?>" 
                           class="btn btn-secondary btn-delete">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>

                        <button type="submit" name="confirm_delete" 
                                class="btn btn-danger btn-delete">
                            <i class="bi bi-trash"></i> Delete Permanently
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
