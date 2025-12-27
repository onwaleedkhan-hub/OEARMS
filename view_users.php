<?php
session_start();
include("config.php");

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

/* ================= FETCH USERS (WITH PASSWORD) ================= */
$sql = "SELECT user_id, name, agency, phone, email, username, password
        FROM users
        ORDER BY user_id ASC";

$result = mysqli_query($con, $sql);

if (!$result) {
    die("Query Failed: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Users List | OEARMS</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<!-- Custom CSS -->
<link rel="stylesheet" href="view_usersstyle.css">
<!-- Favicon -->    
<link rel="icon" type="image/png" href="log.jpg">
<style>
.password-text {
    font-family: monospace;
    letter-spacing: 1px;
}
</style>
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-primary">
            <i class="bi bi-people-fill me-2"></i> Users List
        </h3>
        <a href="home.php" class="btn btn-success rounded-pill">
            <i class="bi bi-house-door me-1"></i> Home
        </a>
    </div>

    <div class="table-responsive shadow rounded bg-white">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Agency</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="text-center"><?= $row['user_id']; ?></td>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= htmlspecialchars($row['agency']) ?: '<span class="text-muted">N/A</span>'; ?></td>
                    <td><?= htmlspecialchars($row['phone']); ?></td>
                    <td><?= htmlspecialchars($row['email']); ?></td>
                    <td><?= htmlspecialchars($row['username']); ?></td>

                    <!-- PLAIN PASSWORD WITH TOGGLE -->
                    <td class="text-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="password-text me-2"
                                  id="pwd-<?= $row['user_id']; ?>"
                                  data-password="<?= htmlspecialchars($row['password']); ?>">
                                ********
                            </span>

                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary p-1"
                                    onclick="togglePassword(<?= $row['user_id']; ?>)">
                                <i class="bi bi-eye-fill" id="icon-<?= $row['user_id']; ?>"></i>
                            </button>
                        </div>
                    </td>

                    <td class="text-center">
                        <a href="update.php?user_id=<?= $row['user_id']; ?>"
                           class="btn btn-primary btn-sm rounded-pill mb-1 px-3">
                            <i class="bi bi-pencil-fill me-1"></i> Edit
                        </a>

                        <a href="?delete_id=<?= $row['user_id']; ?>"
                           class="btn btn-danger btn-sm rounded-pill mb-1"
                           onclick="return confirm('Are you sure?');">
                            <i class="bi bi-trash-fill me-1"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-danger py-4">
                        No users found.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
function togglePassword(id) {
    const pwd = document.getElementById('pwd-' + id);
    const icon = document.getElementById('icon-' + id);

    if (pwd.innerText === '********') {
        pwd.innerText = pwd.dataset.password;
        icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
    } else {
        pwd.innerText = '********';
        icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
    }
}
</script>

</body>
</html>
