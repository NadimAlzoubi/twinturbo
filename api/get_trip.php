<?php 
include_once('../inc/connect.php');

function getRecordById($id) {
    global $connection;
    $query = "
        SELECT 
            t.id, 
            t.driver_id, 
            t.trip_date, 
            t.destination, 
            t.trip_rent, 
            t.extra_income, 
            t.extra_income_des, 
            t.driver_fee, 
            t.remaining, 
            t.notes,
            d.driver_name, 
            d.vehicle_number
        FROM trips t
        INNER JOIN drivers d ON t.driver_id = d.id
        WHERE t.id = ?
    ";
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