<?php 
include_once('../inc/connect.php');
function getRecordById($id) {
    global $connection;
    $query = "SELECT 
            e.*,
            et.name AS etName,
            et.amount AS etAmount,
            et.notes AS etNote
            FROM expenses e
        INNER JOIN expenses_types et ON e.expense_type_id = et.id
        WHERE e.id = ?
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