<?php
date_default_timezone_set('Asia/Dubai');
require_once('tcpdf/tcpdf.php');
require_once('./inc/connect.php');


function decrypt($ciphertext_with_iv, $key) {
    // فك ترميز النص المشفر من Base64
    $ciphertext_with_iv = base64_decode($ciphertext_with_iv);
    // استخراج IV والنص المشفر
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    $iv = substr($ciphertext_with_iv, 0, $iv_length);
    $ciphertext = substr($ciphertext_with_iv, $iv_length);
    // فك تشفير النص
    $plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $key, 0, $iv);
    return $plaintext;
}

// ---------------------

// Get and decrypt the query string
$queryString = $_SERVER['QUERY_STRING'];
$encodeStr = decrypt($queryString, $s3key);
$idqueryParts = explode('=', $encodeStr);
$invoiceID = (int)$idqueryParts[1];
$formType = $idqueryParts[0];



// ----------------الحصول على كامل الخدمات----------------
// ----------------الحصول على كامل الخدمات----------------
function getServiceById($id = null) {
    global $connection;

    $query = "SELECT services.*,
                GROUP_CONCAT(service_fees.description SEPARATOR ', ') AS fee_description,
                GROUP_CONCAT(service_fees.amount SEPARATOR ', ') AS fee_amount,
                GROUP_CONCAT(service_fees.quantity SEPARATOR ', ') AS qty,
                GROUP_CONCAT(service_fees_types.fee_name SEPARATOR ', ') AS fee_type_name,
                GROUP_CONCAT(service_fees_types.fee_amount SEPARATOR ', ') AS fee_type_amount
            FROM 
                services
            LEFT JOIN service_fees ON services.id = service_fees.service_id
            LEFT JOIN service_fees_types ON service_fees.service_fee_type_id = service_fees_types.id
            WHERE services.id = $id";    
    
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


// ----------------الحصول على بيانات السعودية----------------
function getSauBillById($id) {
    global $connection;
    $query = "SELECT 
            b.*,
            o.office_name, 
            o.license_number
        FROM sau_bills b
        LEFT JOIN sau_offices o ON b.sau_office_id = o.id
        WHERE b.id = $id";

    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// --------------------------------------------

if ($formType == 'rid') {
    // services recipte
    $services = getServiceById($invoiceID);
} elseif ($formType == 'bid'){
    // bills recipte
    $sau_bills = getSauBillById($invoiceID);
} else {
    die('error');
}


// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nadim alzoubi');
$pdf->SetTitle('Receipt');
$pdf->SetSubject('Receipt PDF');
$pdf->SetKeywords('TCPDF, PDF, receipt');

// Set default header data
$pdf->SetHeaderData('', 0, '', '');

// Set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// Set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, 5, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);  // Set to 0 if no header is used
$pdf->SetFooterMargin(10);


// Set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('aealarabiya', '', 12);

// Add a logo
$pdf->Image('./img/twin-logo.png', 15, 10, 60, '', 'PNG',);

// Title
$pdf->SetFont('aealarabiya', 'B', 20);

if ($formType == 'rid') {
    $pdf->Cell(0, 15, '***Services Receipt***', 0, 1, 'C');
    $pdf->Cell(0, 5, '***إيصال خدمات***', 0, 1, 'C');
} elseif($formType == 'bid') {
    $pdf->Cell(0, 15, '***Saudi Bill Receipt***', 0, 1, 'C');
    $pdf->Cell(0, 5, '***إيصال بيان سعودي***', 0, 1, 'C');
}


$pdf->Ln(5);

// Address

$TEL = '| +971-566159995 | +971-542759995 |';
$pdf->SetFont('aealarabiya', 'B', 20);
$pdf->Cell(0, 15, 'Twin Turbo Express', 0, 1, 'C');
$pdf->SetFont('aealarabiya', '', 12);
$pdf->Cell(0, 10, 'Abu Dhabi, Al Ghuwaifat', 0, 1, 'C');
$pdf->Cell(0, 5, $TEL, 0, 1, 'C');
$pdf->Cell(0, 5, '', 0, 1, 'C');
// $pdf->Ln(10);






if ($formType == 'rid') {
    $items = [];
    $cAt = '';
    foreach ($services as $service) {
        $text = 'Customer: ' . "\n" . $service['driver_name'];
        $vename = 'Vehicle No: ' . $service['vehicle_number'];
        $date = 'Date: ' . $service['service_date'];
        $cAt = $service['created_at'];
        // Information
        $pdf->Cell(0, 15, $date, 0, 1, 'L');
        $pdf->MultiCell(0, 5, $text, 0, 'L', 0, 1, '', '', true);
        $pdf->Cell(0, 15, $vename, 0, 1, 'L');
        $pdf->Ln(5);
        // Item list
        $pdf->SetFont('aealarabiya', '', 10);
        // تنسيق تفاصيل الرسوم وحساب المجموع
        $fee_details = '';
        $total_fee_amount = 0.00; // المجموع الابتدائي
        
        $fee_descriptions = explode(',', $service['fee_description']);
        $fee_amounts = explode(',', $service['fee_amount']);
        $fee_type_names = explode(',', $service['fee_type_name']);
        $quantity = explode(',', $service['qty']);
        

        $num_fees = count($fee_type_names); // عدد الرسوم
        for ($i = 0; $i < $num_fees; $i++) {
            $description = trim(htmlspecialchars($fee_descriptions[$i] ?? ''));
            $amount = trim(htmlspecialchars($fee_amounts[$i] ?? '0'));
            $qty = trim(htmlspecialchars($quantity[$i] ?? '0'));
            $fee_type_name = htmlspecialchars($fee_type_names[$i] ?? '');
            // حساب المجموع
            $total_fee_amount += (float) $amount;
            // تنظيم العرض
            
            $items[] = [$fee_type_name, $qty, $amount];

        }
    }

} elseif($formType == 'bid') {
    $items = [];
    $bill_price = 0.00;
    $cAt = '';
    foreach ($sau_bills as $bill) {
        $text = 'Customer: ' . "\n" . $bill['driver_name'];
        $vename = 'Vehicle No: ' . $bill['vehicle_number'];
        $date = 'Date: ' . $bill['bill_date'];
        $cAt = $bill['created_at'];

        // Information
        $pdf->Cell(0, 15, $date, 0, 1, 'L');
        $pdf->MultiCell(0, 5, $text, 0, 'L', 0, 1, '', '', true);
        $pdf->Cell(0, 15, $vename, 0, 1, 'L');
        $pdf->Ln(5);
        // Item list
        $pdf->SetFont('aealarabiya', '', 10);

        $bill_price += (float) $bill['price'];

            
        $items[] = [$bill['office_name'], $bill['license_number'], $bill['nob'], $bill['price']];
    }
}





// Table headers
$pdf->SetFillColor(255, 255, 255);
if ($formType == 'rid') {
    $pdf->Cell(120, 7, 'Description', 1, 0, 'C', 1);
} elseif($formType == 'bid') {
    $pdf->Cell(120, 7, 'Saudi Office', 1, 0, 'C', 1);
}
$pdf->Cell(20, 7, 'Qty', 1, 0, 'C', 1);
$pdf->Cell(30, 7, 'Amount', 1, 1, 'C', 1);

// Table data

if ($formType == 'rid') {
    foreach ($items as $item) {
        $pdf->Cell(120, 6, $item[0], 1, 0, 'C');
        $pdf->Cell(20, 6, $item[1], 1, 0, 'C');
        $pdf->Cell(30, 6, $item[2], 1, 1, 'C');
    }
} elseif($formType == 'bid') {
    foreach ($items as $item) {
        $of = $item[0] .' - '. $item[1]; 
        $pdf->Cell(120, 6, $of, 1, 0, 'C');
        $pdf->Cell(20, 6, $item[2], 1, 0, 'C');
        $pdf->Cell(30, 6, $item[3], 1, 1, 'C');
    }
}



// Subtotals and totals

if ($formType == 'rid') {
    $tot = number_format($total_fee_amount, 2) . ' AED';
} elseif($formType == 'bid') {
    $totb = number_format($bill_price, 2) . ' AED';
}

$pdf->Ln(5);
$pdf->SetFont('aealarabiya', 'B', 12);
$pdf->Cell(130, 7, 'TOTAL', 0, 0, 'R');
$pdf->Cell(10, 7, '', 0, 0); // خلية فارغة للمسافة بين النصوص
if ($formType == 'rid') {
    $pdf->Cell(30, 7, $tot, 0, 1, 'R');
} elseif($formType == 'bid') {
    $pdf->Cell(30, 7, $totb, 0, 1, 'R');
}
$pdf->Ln(10);

$RID = '***Receipt No. ' . $invoiceID . '***';

// Thank you message
$pdf->SetFont('aealarabiya', '', 10);
$pdf->Cell(0, 10, '***THANK YOU***', 0, 1, 'C');
$pdf->Cell(0, 5, $RID, 0, 1, 'C');
$pdf->Ln(5);



    // Program to display URL of current page.
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        $link = "https";
    else
        $link = "http";
        
    // Here append the common URL characters.
    $link .= "://";
        
    // Append the host(domain name, ip) to the URL.
    $link .= $_SERVER['HTTP_HOST'];
        
    // Append the requested resource location to the URL
    $link .= $_SERVER['REQUEST_URI'];



// QR code
$style = array(
    'border' => 1,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);
$pdf->write2DBarcode($link, 'QRCODE,H', 82, 215, 46, 46, $style, 'N');
$pdf->Ln(5);

// Footer text
$pdf->SetFont('aealarabiya', '', 8);
$pdf->MultiCell(0, 2, 'Twin Turbo Express', 0, 'C');
$pdf->MultiCell(0, 2, 'E-Mail: Container3030@gmail.com', 0, 'C');
$pdf->SetFont('aealarabiya', '', 7);
// الحصول على التاريخ والوقت الحاليين
$currentDateTime = 'Printing Date: ' . date('Y-m-d h:i:s A');
$cAt = strtotime($cAt);
$cAt = date('Y-m-d h:i:s A', $cAt);
$cAt = 'Issuance Date: ' . $cAt;
$dd = $cAt . ' - ' . $currentDateTime;
$pdf->MultiCell(0, 2, $dd, 0, 'C');


// Output PDF
$pdf->Output('receipt.pdf', 'I');
?>
