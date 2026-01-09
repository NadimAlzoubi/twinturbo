<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

// بدء تخزين الإخراج
ob_start();
include('contentv2.php'); // تأكد من أن هذا الملف يحتوي على محتوى HTML صحيح
$html = ob_get_clean();

// إعداد mPDF
$mpdf = new \Mpdf\Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L', // A4 وباتجاه أفقي
    'margin_left' => 5,
    'margin_right' => 5,
    'margin_top' => 5,
    'margin_bottom' => 7,
    'margin_footer' => 2,
    'tempDir' => __DIR__ . '/tmp',
    'default_font' => 'Arial'
]);

$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
// $mpdf->SetAutoPageBreak(true, 15); // تمكين تقسيم الصفحات تلقائيًا مع حد 10

// إعداد اللغة والاتجاه (RTL أو LTR) بناءً على المعايير المرسلة
if (isset($_GET['dir']) && $_GET['dir'] == 'rtl') {
    $mpdf->SetDirectionality('rtl');
}

$title = 'Report';

// إعداد عنوان التقرير بناءً على البيانات المرسلة
if (isset($_GET['reportType']) && isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];
  
    $driverId = isset($_GET['driverId']) ? ' | (' . $_GET['driverId'] . ')' : '';
    $officeId = isset($_GET['officeId']) ? ' | (' . $_GET['officeId'] . ')' : '';
    $serviceId = isset($_GET['serviceId']) ? ' | (' . $_GET['serviceId'] . ')' : '';
    $expenseId = isset($_GET['expenseId']) ? ' | (' . $_GET['expenseId'] . ')' : '';
    $customerId = isset($_GET['customerId']) ? ' | (' . $_GET['customerId'] . ')' : '';
    $statusSummary = 
    (isset($_GET['statusPaid']) ? ' | (' . translate($_GET['statusPaid'], $lang) . ')' : '') .
    (isset($_GET['statusPending']) ? ' | (' . translate($_GET['statusPending'], $lang) . ')' : '') .
    (isset($_GET['statusCancelled']) ? ' | (' . translate($_GET['statusCancelled'], $lang) . ')' : '');

    if ($_GET['reportType'] == 'trips') {
        $title_ar = 'تقرير الرحلات: من ' . $startDate . ' إلى ' . $endDate . $driverId;
        $title_en = 'Trips Report: From ' . $startDate . ' To ' . $endDate . $driverId;
    } elseif ($_GET['reportType'] == 'sau_bills') {
        $title_ar = 'تقرير البيانات السعودية: من ' . $startDate . ' إلى ' . $endDate . $officeId;
        $title_en = 'Saudi Data Report: From ' . $startDate . ' To ' . $endDate . $officeId;
    } elseif ($_GET['reportType'] == 'services') {
        $title_ar = 'تقرير الخدمات: من ' . $startDate . ' إلى ' . $endDate . $serviceId;
        $title_en = 'Services Report: From ' . $startDate . ' To ' . $endDate . $serviceId;
    } elseif ($_GET['reportType'] == 'expenses') {
        $title_ar = 'تقرير المصاريف: من ' . $startDate . ' إلى ' . $endDate;
        $title_en = 'Expenses Report: From ' . $startDate . ' To ' . $endDate;
    } elseif ($_GET['reportType'] == 'revenue_expense') {
        $title_ar = 'تقرير الإيرادات والمصروفات: من ' . $startDate . ' إلى ' . $endDate;
        $title_en = 'Revenue and Expense Report: From ' . $startDate . ' To ' . $endDate;
    } elseif ($_GET['reportType'] == 'invoices') {
        $startDate = date("Y-m-d h:i:s A", strtotime($startDate));
        $endDate = date("Y-m-d h:i:s A", strtotime($endDate));    
        $title_ar = 'تقرير الفواتير: من ' . $startDate . ' إلى ' . $endDate . $customerId . $statusSummary;
        $title_en = 'Invoices Report: From ' . $startDate . ' To ' . $endDate . $customerId . $statusSummary;
    }

    // تعيين المنطقة الزمنية بناءً على الجلسة
    if ($_SESSION["sau_user_location"] == 'sau') {
        date_default_timezone_set('Asia/Riyadh');
    } else if ($_SESSION["sau_user_location"] == 'uae') {
        date_default_timezone_set('Asia/Dubai');
    } else {
        date_default_timezone_set('UTC'); // إعداد افتراضي في حالة عدم تحديد الموقع
    }
    $date = 'Print Date: ' . date('Y-m-d | h:i:s A');
    // date('j-m-Y h:i:s A')
    // إعداد عنوان التقرير بناءً على اللغة المختارة
    if ($_GET['dir'] == 'rtl') {
        $title = $title_ar;
    } else {
        $title = $title_en;
    }
}

$footerHTML = '
    <table style="padding: 0px; margin: 0px;">
        <tr>
            <td style="padding: 1px; border: none; width: 33%;">' . $date . '</td>
            <td style="padding: 1px; border: none; width: 33%; text-align: center; color: #000;">Page {PAGENO} of {nbpg}</td>
            <td style="padding: 1px; border: none; width: 33%; text-align: center;">
                <a href="https://www.Nadim.pro" target="_blank" style="text-decoration: none; color: #0000FF;">Powered by | www.Nadim.pro</a>
            </td>
        </tr>
    </table>
';
$mpdf->SetHTMLFooter($footerHTML);

$mpdf->WriteHTML('<h3 style="text-align:center;color:#111111;">' . $title . '</h3>');

// إضافة محتوى HTML بنفس التنسيقات والألوان
$mpdf->SetFont('aealarabiya', '', 12);

$htmlChunks = str_split($html, 100000); // تقسيم المحتوى إلى أجزاء أصغر
foreach ($htmlChunks as $chunk) {
    $mpdf->WriteHTML($chunk);
}
// $mpdf->WriteHTML($html);

// إخراج ملف PDF
$mpdf->Output("$title.pdf", 'I');
