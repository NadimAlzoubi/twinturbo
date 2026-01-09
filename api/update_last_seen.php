<?php
include_once('../inc/connect.php');


function lastSeen($id) {
    global $connection; // استخدم المتغير العام للاتصال بقاعدة البيانات
    $currentTime = date('Y-m-d H:i:s');
    
    // تكوين الاستعلام لتحديث وقت آخر ظهور
    $query = "UPDATE users SET last_seen = ? WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'si', $currentTime, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        die("Failed to prepare statement: " . mysqli_error($connection));
    }

    // تكوين الاستعلام لاسترجاع وقت آخر ظهور
    $query = "SELECT last_seen FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $last_seen);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        die("Failed to prepare statement: " . mysqli_error($connection));
    }
}

session_start();
if (isset($_SESSION['sau_user_id'])) {
    $user_id = $_SESSION['sau_user_id'];
    lastSeen($user_id);
}