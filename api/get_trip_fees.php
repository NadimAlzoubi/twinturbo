<?php 
include_once('../inc/connect.php');

if (isset($_GET['trip_id'])) {
    $trip_id = intval($_GET['trip_id']);

    // استعلام لجلب بيانات المصاريف
    $query = "
    SELECT 
        tf.id, 
        tf.trip_id, 
        tf.trip_fee_type_id, 
        tft.fee_name AS fee_type_name, 
        tft.fee_amount AS fee_type_amount, 
        tf.quantity, 
        tf.amount, 
        tf.description 
    FROM 
        trip_fees tf
    JOIN 
        trip_fees_types tft 
    ON 
        tf.trip_fee_type_id = tft.id 
    WHERE 
        tf.trip_id = ?
";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $trip_id);
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
