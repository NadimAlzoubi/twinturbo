<?php 
include_once('../inc/connect.php');

function getRecordById($id) {
    global $connection;
    $query = "
        SELECT 
            b.id, 
            b.bill_date, 
            b.sau_office_id, 
            b.sau_bill_number, 
            b.driver_name, 
            b.vehicle_number, 
            b.nob, 
            b.nov, 
            b.destination, 
            b.price, 
            b.notes,
            o.office_name, 
            o.entity_type,
            o.license_number
        FROM 
            sau_bills b
        JOIN 
            sau_offices o 
        ON 
            b.sau_office_id = o.id
        WHERE 
            b.id = ?";
    
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