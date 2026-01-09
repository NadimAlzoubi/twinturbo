<?php 
include_once('../inc/connect.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // استعلام لجلب بيانات المصاريف
    $query = "
    SELECT 
        sf.id, 
        sf.service_id, 
        sf.service_fee_type_id, 
        sf.quantity, 
        sf.amount, 
        sf.description, 
        sft.fee_name AS fee_type_name, 
        sft.fee_amount AS fee_type_amount, 
        sft.bank_deduction AS fee_type_bank_deduction
    FROM 
        service_fees sf
    JOIN 
        service_fees_types sft 
    ON 
        sf.service_fee_type_id = sft.id 
    WHERE 
        sf.service_id = ?
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
