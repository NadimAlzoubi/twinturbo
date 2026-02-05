<?php
include('../inc/connect.php');

if (isset($_GET['term'])) {
    $term = '%' . $_GET['term'] . '%';
    $stmt = $connection->prepare("SELECT id, full_name FROM users WHERE full_name LIKE ? OR id LIKE ? LIMIT 10");
    $stmt->bind_param("ss", $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = [
            'value' => $row['id'],
            'label' =>  $row['id'] . '- ' . $row['full_name']
        ];
    }
    echo json_encode($customers);

    $stmt->close();
}

$connection->close();