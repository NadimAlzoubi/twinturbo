<?php 
include_once('../inc/connect.php');

function getRecordById($id) {
    global $connection;
    $query = "SELECT 
            service_requests.*,
            drivers.driver_name             AS driver_name,
            drivers.vehicle_number          AS driver_vehicle_number,
            shippers.name                   AS shipper_name,
            shippers.office_name            AS shipper_office_name,
            users.full_name                 AS user_name
        FROM service_requests
        LEFT JOIN drivers  ON service_requests.driver_id  = drivers.id
        LEFT JOIN shippers ON service_requests.shipper_id = shippers.id
        LEFT JOIN users    ON service_requests.user_id    = users.id
        WHERE service_requests.id = ?";

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