<?php
include('../inc/connect.php');

if (isset($_GET['term'])) {
    $term = '%' . $_GET['term'] . '%';
    $stmt = $connection->prepare("SELECT id, fee_name, fee_amount FROM trip_fees_types WHERE fee_name LIKE ? OR id LIKE ? LIMIT 10");
    $stmt->bind_param("ss", $term, $term); 
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $fee_data = $row['id'] . '- ' . $row['fee_name'];
        if ($row['fee_amount'] != 0.00) {
            $fee_data .= ' | ' . $row['fee_amount'];
        }        

        $customers[] = [
            'value' => $row['id'],
            'label' =>  $fee_data,
            'amount' => $row['fee_amount'],
        ];
    }
    echo json_encode($customers);

    $stmt->close();
}

$connection->close();