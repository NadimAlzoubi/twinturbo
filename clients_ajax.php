<?php
// إعداد الاتصال بقاعدة البيانات
$host = "localhost";
$user = "root";
$pass = "";
$db = "twinturbo";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    echo json_encode([
        "error" => $e->getMessage()
    ]);
    exit;
}

// استقبال بيانات DataTables
$draw = $_POST['draw'] ?? 1;
$start = $_POST['start'] ?? 0;
$length = $_POST['length'] ?? 10;

// البحث العام
$search = $_POST['search']['value'] ?? '';

// الأعمدة
$columns = [
    'id',
    'company_name',
    'contact_name',
    'phone',
    'whatsapp',
    'email',
    'activity_type',
    'customer_segment',
    'import_export_focus',
    'avg_monthly_shipments',
    'country',
    'city',
    'address',
    'google_maps_url',
    'status',
    'account_manager',
    'last_contact_summary',
    'last_activity_date',
    'notes'
];

// ترتيب الأعمدة
$orderColumn = $columns[$_POST['order'][0]['column'] ?? 0] ?? 'id';
$orderDir = $_POST['order'][0]['dir'] ?? 'asc';

// الفلاتر لكل عمود
$columnSearch = [];
foreach ($columns as $index => $col) {
    if (!empty($_POST['columns'][$index]['search']['value'])) {
        $columnSearch[$col] = $_POST['columns'][$index]['search']['value'];
    }
}

// بناء الاستعلام الرئيسي
$sql = "SELECT * FROM clients WHERE 1=1";

// البحث العام
$params = [];
if ($search) {
    $sql .= " AND (" . implode(" OR ", array_map(fn($c) => "$c LIKE :search", $columns)) . ")";
    $params[':search'] = "%$search%";
}

// البحث لكل عمود
foreach ($columnSearch as $col => $val) {
    $sql .= " AND $col LIKE :$col";
    $params[":$col"] = "%$val%";
}

// عدد السجلات بعد الفلترة
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$recordsFiltered = $stmt->rowCount();

// إضافة ORDER و LIMIT
$sql .= " ORDER BY $orderColumn $orderDir LIMIT :start, :length";
$stmt = $pdo->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':start', (int)$start, PDO::PARAM_INT);
$stmt->bindValue(':length', (int)$length, PDO::PARAM_INT);
$stmt->execute();

// الصفوف كما هي كـ associative array
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);


foreach ($data as &$row) {
    $row['actions'] = '<button class="btn btn-sm btn-primary" onclick="editClient('.$row['id'].')">تعديل</button>';

    if (empty($row['last_activity_date']) || $row['last_activity_date'] === '0000-00-00 00:00:00') {
        $row['last_activity_date'] = null;
    }

    // تحويل الايميلات إلى روابط mailto
    if (!empty($row['email'])) {
        $emails = explode('|', $row['email']);
        $emails = array_map('trim', $emails); // إزالة الفراغات
        $row['email'] = implode('<br>', array_map(fn($e) => "<a href='mailto:$e'>$e</a>", $emails));
    }

    // تحويل أرقام الواتساب إلى رابط واتساب
    if (!empty($row['whatsapp'])) {
        $whatsapps = explode('|', $row['whatsapp']);
        $whatsapps = array_map('trim', $whatsapps);
        $row['whatsapp'] = implode('<br>', array_map(fn($w) => "<a href='https://wa.me/$w' target='_blank'>$w</a>", $whatsapps));
    }

    // تحويل أرقام الهاتف إلى رابط اتصال
    if (!empty($row['phone'])) {
        $phones = explode('|', $row['phone']);
        $phones = array_map('trim', $phones);
        $row['phone'] = implode('<br>', array_map(fn($p) => "<a href='tel:$p'>$p</a>", $phones));
    }

    if (!empty($row['google_maps_url'])) {
        $row['google_maps_url'] = "<a href='$row[google_maps_url]' target='_blank'>Google Maps <i class='fa-solid fa-arrow-up-right-from-square'></i></a>";
    }
    // تحويل الحالة إلى بادجات ملونة
    if (!empty($row['status']) && $row['status'] === 'not_authorized') {
        $row['status'] = '<span class="badge bg-danger">Not Authorized | غير مفوض</span>';
    } elseif (!empty($row['status']) && $row['status'] === 'authorized') {
        $row['status'] = '<span class="badge bg-success">Authorized | مفوض</span>';
    }

    if (!empty($row['import_export_focus']) && $row['import_export_focus'] === 'import') {
        $row['import_export_focus'] = '<span class="badge bg-secondary">Import | استيراد</span>';
    } elseif (!empty($row['import_export_focus']) && $row['import_export_focus'] === 'export') {
        $row['import_export_focus'] = '<span class="badge bg-secondary">Export | تصدير</span>';
    } elseif (!empty($row['import_export_focus']) && $row['import_export_focus'] === 'both') {
        $row['import_export_focus'] = '<span class="badge bg-secondary">Both | كلاهما</span>';
    }

}
unset($row);



// عدد السجلات الإجمالي
$totalStmt = $pdo->query("SELECT COUNT(*) FROM clients");
$recordsTotal = $totalStmt->fetchColumn();

// تجهيز البيانات ل DataTables
$response = [
    "draw" => intval($draw),
    "recordsTotal" => intval($recordsTotal),
    "recordsFiltered" => intval($recordsFiltered),
    "data" => $data  // <-- هنا لا نستخدم array_values
];

// إرجاع JSON
header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
