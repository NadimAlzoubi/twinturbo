<?php
header('Content-Type: application/json; charset=utf-8');

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
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
    exit;
}

// استقبال البيانات
$data = $_POST;

// إذا كان هناك id مرفق → تعديل، وإلا إضافة
$id = $data['id'] ?? null;

// الأعمدة المقبولة
$fields = [
    'company_name','contact_name','phone','whatsapp','email','activity_type',
    'customer_segment','import_export_focus','avg_monthly_shipments','country','city',
    'address','google_maps_url','status','account_manager','last_contact_summary', 'last_activity_date'
];

try {
    if($id) {
        // تعديل
        $set = [];
        $params = [];
        foreach($fields as $f){
            if(isset($data[$f])){
                $set[] = "$f = :$f";
                $params[":$f"] = $data[$f];
            }
        }
        $params[':id'] = $id;
        $sql = "UPDATE clients SET ".implode(',', $set)." WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success'=>true, 'message'=>'تم تعديل العميل بنجاح']);
    } else {
        // إضافة
        $cols = [];
        $placeholders = [];
        $params = [];
        foreach($fields as $f){
            if(isset($data[$f])){
                $cols[] = $f;
                $placeholders[] = ":$f";
                $params[":$f"] = $data[$f];
            }
        }
        $sql = "INSERT INTO clients (".implode(',', $cols).") VALUES (".implode(',', $placeholders).")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['success'=>true, 'message'=>'تم إضافة العميل بنجاح']);
    }
} catch (\PDOException $e) {
    echo json_encode(['success'=>false, 'message'=>$e->getMessage()]);
}