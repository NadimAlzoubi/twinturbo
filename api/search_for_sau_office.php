<?php
include('../inc/connect.php');

$translate = [
    'ar' => [
        'office' => 'مكتب',
        'agent' => 'مندوب',
        'shipper' => 'شاحن',
        'company' => 'شركة',
    ],
    'en' => [
        'office' => 'Office',
        'agent' => 'Agent',
        'shipper' => 'Shipper',
        'company' => 'Company',
    ],
];

function translate($key, $lang = 'en')
{
    global $translate;
    return $translate[$lang][$key] ?? $key;
}
$lang = isset($_GET['lang']) && $_GET['lang'] === 'ar' ? 'ar' : 'en';

if (isset($_GET['term'])) {
    $term = '%' . $_GET['term'] . '%';
    $stmt = $connection->prepare("SELECT id, office_name, entity_type, license_number FROM sau_offices WHERE office_name LIKE ? OR license_number LIKE ? OR id LIKE ? LIMIT 10");
    $stmt->bind_param("sss", $term, $term, $term);
    $stmt->execute();
    $result = $stmt->get_result();

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $customers[] = [
            'value' => $row['id'],
            'label' =>  $row['id'] . '- ' . $row['office_name'] . ' | ' . translate($row['entity_type'], $lang) . ' | '  . $row['license_number']
        ];
    }
    echo json_encode($customers);

    $stmt->close();
}

$connection->close();