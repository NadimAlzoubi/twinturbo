<?php 
include_once('../inc/connect.php');
function getRecordById($id) {
    global $connection;
    $query = "SELECT driver_name, vehicle_number, phone, notes FROM drivers WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);    
}
if (isset($_GET['id'])) {
    $recordId = intval($_GET['id']);
    $record = getRecordById($recordId);
    echo json_encode($record);
}