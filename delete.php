<?php
session_start();
include("config.php");

// Redirect if admin not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Check if delete_id is set in the URL
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Prepare and execute delete query
    $stmt_delete = mysqli_prepare($con, "DELETE FROM users WHERE user_id=?");
    mysqli_stmt_bind_param($stmt_delete, "i", $delete_id);
    if (mysqli_stmt_execute($stmt_delete)) {
        // Redirect back to users list after successful deletion
        header("Location: view_users.php?msg=deleted");
        exit();
    } else {
        // Redirect with error
        header("Location: view_users.php?msg=error");
        exit();
    }
} else {
    // If no delete_id, redirect back
    header("Location: view_users.php");
    exit();
}
?>
