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
// DELETE AGENT
// =======================
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']); // sanitize input

    // Ensure the agent belongs to the current user before deleting
    $check_sql = "SELECT Agent_ID FROM Agent WHERE Agent_ID = ? AND user_id = ?";
    $stmt = mysqli_prepare($con, $check_sql);
    mysqli_stmt_bind_param($stmt, "ii", $delete_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Agent exists and belongs to user, safe to delete
        $delete_sql = "DELETE FROM Agent WHERE Agent_ID = ? AND user_id = ?";
        $stmt_delete = mysqli_prepare($con, $delete_sql);
        mysqli_stmt_bind_param($stmt_delete, "ii", $delete_id, $user_id);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);

        header("Location: agent.php?msg=deleted");
        exit();
    } else {
        $error = "Agent not found or you do not have permission to delete.";
    }

    mysqli_stmt_close($stmt);
}

// =======================
// FETCH AGENTS FOR CURRENT USER
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
