<?php
include('../inc/connect.php');

if (isset($_GET['term'])) {
    $term = '%' . $_GET['term'] . '%';
    $stmt = $connection->prepare("SELECT id, name, office_name FROM shippers WHERE name LIKE ? OR office_name LIKE ? OR id LIKE ? LIMIT 10");
    $stmt->bind_param("sss", $term, $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = [
            'value' => $row['id'],
            'label' =>  $row['id'] . '- ' . $row['name'] . ' - ' . $row['office_name'],
        ];
    }
    echo json_encode($customers);

    $stmt->close();
}

$connection->close();