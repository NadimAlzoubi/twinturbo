<?php
include('../inc/connect.php');

if (isset($_GET['term'])) {
    $term = '%' . $_GET['term'] . '%';
    $stmt = $connection->prepare("SELECT id, customer_name, exit_clearance, exit_returns, entry_clearance, entry_returns, customer_notes FROM customers WHERE customer_name LIKE ? OR id LIKE ? LIMIT 10");
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = [
            'value' => $row['id'],
            'name' =>  $row['customer_name'],
            'label' =>  $row['id'] . '- ' . $row['customer_name'],
            'exit_clearance' => $row['exit_clearance'],
            'exit_returns' => $row['exit_returns'],
            'entry_clearance' => $row['entry_clearance'],
            'entry_returns' => $row['entry_returns'],
            'customer_notes' => $row['customer_notes']
        ];
    }
    echo json_encode($customers);

    $stmt->close();
}

$connection->close();