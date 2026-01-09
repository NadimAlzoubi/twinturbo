<?php 
include_once('../inc/connect.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // استعلام لجلب بيانات المصاريف
    $query = "
    SELECT 
        invf.id, 
        invf.invoice_id, 
        invf.fee_type_id, 
        invf.quantity, 
        invf.amount, 
        invf.description, 
        invft.description AS fee_type_name, 
        invft.amount AS fee_type_amount, 
        invft.bank_deduction AS fee_type_bank_deduction
    FROM 
        fees invf
    JOIN 
        fee_types invft 
    ON 
        invf.fee_type_id = invft.id 
    WHERE 
        invf.invoice_id = ?
";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $fees = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $fees[] = $row;
    }

    echo json_encode($fees);
}

mysqli_close($connection);
?>
