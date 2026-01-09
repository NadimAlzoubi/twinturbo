<?php
include('../inc/connect.php');

if (isset($_GET['term'])) {
    $term = '%' . $_GET['term'] . '%';
    $stmt = $connection->prepare("SELECT id, name, amount, notes FROM expenses_types WHERE name LIKE ? OR id LIKE ? LIMIT 10");
    $stmt->bind_param("ss", $term, $term); 
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $fee_data = $row['id'] . '- ' . $row['name'];
        if ($row['amount'] != 0.00) {
            $fee_data .= ' | ' . $row['amount'];
        }        

        $customers[] = [
            'value' => $row['id'],
            'label' =>  $fee_data,
            'amount' => $row['amount'],
        ];
    }
    echo json_encode($customers);

    $stmt->close();
}

$connection->close();