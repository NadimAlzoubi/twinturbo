<?php
include('../inc/connect.php');

if (isset($_GET['term'])) {
    $term = '%' . $_GET['term'] . '%';
    $stmt = $connection->prepare("SELECT id, driver_name, vehicle_number FROM drivers WHERE driver_name LIKE ? OR vehicle_number LIKE ? OR id LIKE ? LIMIT 10");
    $stmt->bind_param("sss", $term, $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = [
            'value' => $row['id'],
            'label' =>  $row['id'] . '- ' . $row['driver_name'] . ' | ' . $row['vehicle_number']
        ];
    }
    echo json_encode($customers);

    $stmt->close();
}

$connection->close();