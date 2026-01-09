<?php
include('../inc/connect.php');

if (isset($_GET['term'])) {
    $term = '%' . $_GET['term'] . '%';
    $stmt = $connection->prepare("SELECT id, description, amount, bank_deduction FROM fee_types WHERE description LIKE ? OR id LIKE ? LIMIT 10");
    $stmt->bind_param("ss", $term, $term); 
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $fee_data = $row['id'] . '- ' . $row['description'];
        if ($row['amount'] != 0.00) {
            $fee_data .= ' | ' . $row['amount'];
        }        
        if ($row['bank_deduction'] == true) {
            $fee_data .= ' | (T)';
        }

        $customers[] = [
            'value' => $row['id'],
            'label' =>  $fee_data,
            'amount' => $row['amount'],
            'bank' => $row['bank_deduction'],
        ];
    }
    echo json_encode($customers);

    $stmt->close();
}

$connection->close();