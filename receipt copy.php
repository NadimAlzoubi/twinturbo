<?php
date_default_timezone_set('Asia/Dubai');

require_once __DIR__ . '/vendor/autoload.php';
require_once './inc/connect.php';

use Mpdf\Mpdf;

/* ================== دالة فك التشفير ================== */

function decrypt($ciphertext_with_iv, $key)
{
    $ciphertext_with_iv = base64_decode($ciphertext_with_iv);
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($ciphertext_with_iv, 0, $iv_length);
    $ciphertext = substr($ciphertext_with_iv, $iv_length);
    return openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
}

/* ================== قراءة الرابط ================== */
$queryString = $_SERVER['QUERY_STRING'];
$encodeStr = decrypt($queryString, $s3key);
$idqueryParts = explode('=', $encodeStr);
$formType = $idqueryParts[0];
$invoiceID = (int)$idqueryParts[1];

/* ================== الخدمات ================== */
function getServiceById($id = null)
{
    global $connection;
    $query = "
        SELECT services.*,
            GROUP_CONCAT(service_fees.description SEPARATOR ', ') AS fee_description,
            GROUP_CONCAT(service_fees.amount SEPARATOR ', ') AS fee_amount,
            GROUP_CONCAT(service_fees.quantity SEPARATOR ', ') AS qty,
            GROUP_CONCAT(service_fees_types.fee_name SEPARATOR ', ') AS fee_type_name
        FROM services
        LEFT JOIN service_fees ON services.id = service_fees.service_id
        LEFT JOIN service_fees_types ON service_fees.service_fee_type_id = service_fees_types.id
        WHERE services.id = $id
    ";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/* ================== البيان السعودي ================== */
function getSauBillById($id)
{
    global $connection;
    $query = "
        SELECT b.*, o.office_name, o.license_number
        FROM sau_bills b
        LEFT JOIN sau_offices o ON b.sau_office_id = o.id
        WHERE b.id = $id
    ";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/* ================== تحديد النوع ================== */
if ($formType === 'rid') {
    $data = getServiceById($invoiceID);
} elseif ($formType === 'bid') {
    $data = getSauBillById($invoiceID);
} else {
    die('error');
}

/* ================== إنشاء PDF ================== */
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4',
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 15,
    'default_font' => 'aealarabiya'
]);

/* ================== ستايل ================== */
$mpdf->WriteHTML("
<style>
body { font-family: aealarabiya; font-size: 12px; }
h1 { text-align: center; font-size: 20px; }
.center { text-align: center; }
.table { width:100%; border-collapse: collapse; margin-top:10px; }
.table th, .table td { border:1px solid #000; padding:6px; text-align:center; }
.total { font-weight:bold; }
.footer { font-size:8px; text-align:center; }
</style>
");

/* ================== العنوان ================== */
$titleEn = ($formType === 'rid') ? '***Services Receipt***' : '***Saudi Bill Receipt***';
$titleAr = ($formType === 'rid') ? '***إيصال خدمات***' : '***إيصال بيان سعودي***';

$mpdf->WriteHTML("
<img src='./img/twin-logo.png' width='200'><br>
<h1>$titleEn</h1>
<div class='center'>$titleAr</div>
<br>
<div class='center'><b>Twin Turbo Express</b></div>
<div class='center'>Abu Dhabi, Al Ghuwaifat</div>
<div class='center'>| +971-566159995 | +971-542759995 |</div>
<hr>
");

/* ================== البيانات ================== */
$items = [];
$total = 0;
$cAt = '';

foreach ($data as $row) {

    if ($formType === 'rid') {
        $mpdf->WriteHTML("
            <div>Date: {$row['service_date']}</div>
            <div>Customer:<br>{$row['driver_name']}</div>
            <div>Vehicle No: {$row['vehicle_number']}</div><br>
        ");

        $fee_names = explode(',', $row['fee_type_name']);
        $fee_amounts = explode(',', $row['fee_amount']);
        $qty = explode(',', $row['qty']);

        for ($i = 0; $i < count($fee_names); $i++) {
            $amount = (float)($fee_amounts[$i] ?? 0);
            $total += $amount;
            $items[] = [
                'desc' => trim($fee_names[$i]),
                'qty' => trim($qty[$i] ?? 1),
                'amount' => number_format($amount, 2)
            ];
        }

        $cAt = $row['created_at'];
    }

    if ($formType === 'bid') {
        $mpdf->WriteHTML("
            <div>Date: {$row['bill_date']}</div>
            <div>Customer:<br>{$row['driver_name']}</div>
            <div>Vehicle No: {$row['vehicle_number']}</div><br>
        ");

        $total += (float)$row['price'];
        $items[] = [
            'desc' => $row['office_name'] . ' - ' . $row['license_number'],
            'qty' => $row['nob'],
            'amount' => number_format($row['price'], 2)
        ];

        $cAt = $row['created_at'];
    }
}

/* ================== الجدول ================== */
$mpdf->WriteHTML("<table class='table'>
<tr>
<th>" . ($formType === 'rid' ? 'Description' : 'Saudi Office') . "</th>
<th>Qty</th>
<th>Amount</th>
</tr>");

foreach ($items as $item) {
    $mpdf->WriteHTML("
        <tr>
            <td>{$item['desc']}</td>
            <td>{$item['qty']}</td>
            <td>{$item['amount']}</td>
        </tr>
    ");
}

$mpdf->WriteHTML("
<tr class='total'>
<td colspan='2'>TOTAL</td>
<td>" . number_format($total, 2) . " AED</td>
</tr>
</table>
");

/* ================== QR ================== */
$link = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$mpdf->WriteHTML("<br><div class='center'>");
$mpdf->WriteHTML('
<div style="text-align:center; margin-top:10px;">
    <barcode code="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '" type="QR" size="1.2" error="M" />
</div>
');
$mpdf->WriteHTML("</div>");

/* ================== الفوتر ================== */
$printDate = date('Y-m-d h:i:s A');
$issueDate = date('Y-m-d h:i:s A', strtotime($cAt));

$mpdf->WriteHTML("
<div class='footer'>
Twin Turbo Express<br>
E-Mail: Container3030@gmail.com<br>
Issuance Date: $issueDate<br>
Printing Date: $printDate
</div>
");

/* ================== إخراج ================== */
$mpdf->Output('receipt.pdf', 'I');
