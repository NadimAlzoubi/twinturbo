<?php
include_once('../inc/connect.php');

function getAllUsers() {
    global $connection;
    $query = "SELECT id, full_name, last_seen FROM users";
    $result = mysqli_query($connection, $query);
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    return $users;
}

$currentTime = date('Y-m-d H:i:s');
$onlineDuration = 2; // بالدقائق
$users = getAllUsers();

$response = [];
foreach ($users as $row) {
    $id = $row['id'];
    $lastSeen = strtotime($row['last_seen']);
    $timeDiff = (strtotime($currentTime) - $lastSeen) / 60; // بالوقت بالدقائق

    if ($timeDiff <= $onlineDuration) {
        $status = "متصل الآن";
    } else {
        $status = "آخر ظهور: " . date('Y-m-d H:i:s', $lastSeen);
    }

    $response[] = [
        'id' => $id,
        'status' => $status
    ];
}

// إعداد الرأس لإرسال استجابة JSON
header('Content-Type: application/json');
echo json_encode($response);