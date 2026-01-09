<?php
session_start();
// تضمين مكتبة TCPDF
require_once('tcpdf/tcpdf.php');

// بدء تخزين الإخراج
ob_start();

// تضمين ملف PHP للحصول على محتواه
include('content.php');

// الحصول على الإخراج المخزن
$html = ob_get_clean();

// إنشاء كائن TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// تخصيص التذييل عبر إعادة تعريف دالة Footer
class MYPDF extends TCPDF {
    public function Footer() {
        $this->SetY(-10);
        $this->SetFont('aealarabiya', '', 8);
        $pageNum = 'Page ' . $this->getAliasNumPage() . ' of ' . $this->getAliasNbPages();
        // تعيين المنطقة الزمنية بناءً على الجلسة
        if($_SESSION["sau_user_location"] == 'sau') {
            date_default_timezone_set('Asia/Riyadh');
        } else if ($_SESSION["sau_user_location"] == 'uae') {
            date_default_timezone_set('Asia/Dubai');
        } else {
            date_default_timezone_set('UTC'); // إعداد افتراضي في حالة عدم تحديد الموقع
        }
        $date = 'Printed on ' . date('Y-m-d | h:i:s A');
        $this->SetTextColor(0, 0, 200);
        $this->Cell(0, 5, $pageNum . ' - ' . $date, 0, 0, 'L');

        $link = '<a href="http://www.Nadim.pro" style="text-decoration: none; color: #0000FF;" target="_blank">Powered by | www.Nadim.pro</a></span>';
        $this->WriteHTMLCell(0, 5, 12, -10, $link, 0, 0, false, true, 'R', true);
    }
}

// إعادة تعريف الكائن باستخدام الفئة المخصصة
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// إعدادات المستند
$pdf->setPrintHeader(false);
// $pdf->setPrintFooter(false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nadim Alzoubi');
$pdf->SetTitle('Report PDF');
$pdf->SetSubject('TCPDF');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
$pdf->SetMargins(10, 5, 10);


if(isset($_GET['dir'])){
    if($_GET['dir'] == 'rtl'){
        // إعداد الاتجاه إلى RTL
        $pdf->setRTL(true);
    }
}

$title = 'Report';

// إعداد عنوان التقرير بناءً على البيانات المرسلة
if(isset($_GET['reportType']) && isset($_GET['start_date']) && isset($_GET['end_date'])){
    $startDate = $_GET['start_date'];
    $endDate = $_GET['end_date'];

    $driverId = isset($_GET['driverId']) ? ' | ('.$_GET['driverId'].')' : '';
    $officeId = isset($_GET['officeId']) ? ' | ('.$_GET['officeId'].')' : '';
    $serviceId = isset($_GET['serviceId']) ? ' | ('.$_GET['serviceId'].')' : '';
    $expenseId = isset($_GET['expenseId']) ? ' | ('.$_GET['expenseId'].')' : '';
        
    if($_GET['reportType'] == 'trips'){
        $title_ar = 'تقرير الرحلات: من ' . $startDate . ' إلى ' . $endDate . $driverId;
        $title_en = 'Trips Report: From ' . $startDate . ' To ' . $endDate . $driverId;
    } elseif($_GET['reportType'] == 'sau_bills'){
        $title_ar = 'تقرير البيانات السعودية: من ' . $startDate . ' إلى ' . $endDate . $officeId;
        $title_en = 'Saudi Data Report: From ' . $startDate . ' To ' . $endDate . $officeId;
    } elseif($_GET['reportType'] == 'services'){
        $title_ar = 'تقرير الخدمات: من ' . $startDate . ' إلى ' . $endDate . $serviceId;
        $title_en = 'Services Report: From ' . $startDate . ' To ' . $endDate . $serviceId;
    } elseif($_GET['reportType'] == 'expenses'){
        $title_ar = 'تقرير المصاريف: من ' . $startDate . ' إلى ' . $endDate . $expenseId;
        $title_en = 'Expenses Report: From ' . $startDate . ' To ' . $endDate . $expenseId;
    }

    // إعداد عنوان التقرير بناءً على اللغة المختارة
    if($_GET['dir'] == 'rtl'){
        $title = $title_ar;
    } else {
        $title = $title_en;
    }
}


// إضافة صفحة بحجم A4 وباتجاه أفقي
$pdf->AddPage('L', 'A4');



// إعداد الخط
$pdf->SetFont('aealarabiya', '', 14); // تأكد من أن الخط موجود في المجلد الصحيح

// إضافة العنوان إلى PDF
$pdf->MultiCell(0, 10, $title, 0, 'C', 0, 1, '', '', true);

// إعداد خصائص الجدول
$pdf->SetCellPadding(1); // تعيين padding للخلايا



// إضافة محتوى HTML
$pdf->SetFont('aealarabiya', '', 12);
$pdf->writeHTML($html, true, false, true, false, '');

// إخراج ملف PDF
$pdf->Output('report.pdf', 'I');
?>
