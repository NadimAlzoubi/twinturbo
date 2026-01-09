<?php
date_default_timezone_set("Asia/Dubai");
include('./inc/connect.php');
include('./inc/functions.php');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

use Mpdf\Mpdf;

require_once __DIR__ . '/vendor/autoload.php';

use thesmarter\Tafqeet\Core\Tafqeet;

$mpdf = new Mpdf([
    'margin_left' => 5,
    'margin_right' => 5,
    'mode' => 'utf-8',
    'default_font' => 'Arial' // Set Arial as the default font
], null);
$mpdf->autoScriptToLang = true;
$mpdf->autoLangToFont = true;
$mpdf->SetLeftMargin(0);
$mpdf->SetRightMargin(0);
$mpdf->SetTopMargin(5);
$mpdf->SetAutoPageBreak(false);



function decrypt($ciphertext_with_iv, $key)
{
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

$queryString = $_SERVER['QUERY_STRING'];
$encodeStr = decrypt($queryString, $s3key);
// if (strpos($encodeStr, '&')) {
//     $queryParts = explode('&', $encodeStr);
//     $deinvoiceID = $queryParts[1];
//     $idqueryParts = explode('=', $deinvoiceID);
// } else {
//     $idqueryParts = explode('=', $encodeStr);
// }



$queryParts = [];
if (strpos($encodeStr, '&')) {
    $queryParts = explode('&', $encodeStr);
    $deinvoiceID = $queryParts[1];
    $idqueryParts = explode('=', $deinvoiceID);
} else {
    $idqueryParts = explode('=', $encodeStr);
    $queryParts[0] = $encodeStr; // تهيئة القيمة الافتراضية لتجنب undefined
}


$invoiceID = $idqueryParts[1];
$invoiceID = (int)$invoiceID;






if ($invoiceID) {
    $result_pdf_info = getPdfFileFormat();
    foreach ($result_pdf_info as $row) {
        $coNameAr = $row['coNameAr'];
        $coNameEn = $row['coNameEn'];
        $exPhone = $row['exPhone'];
        $enPhone = $row['enPhone'];
        $addressAr = $row['addressAr'];
        $addressEn = $row['addressEn'];
        $eMail = $row['eMail'];
        $mbox = $row['mbox'];
        $bankNameAr = $row['bankNameAr'];
        $bankNameEn = $row['bankNameEn'];
        $accountNum = $row['accountNum'];
        $iban = $row['iban'];
        $logo = $row['logo'];
        $estamp = $row['stamp'];
        $trn = $row['trn'];
        $qrCode = $row['qrcode'];
    }

    $showQR = 'hidden';
    if ($qrCode) {
        $showQR = 'visible';
    }
    
    $stamp = ''; // قيمة افتراضية
    if (isset($queryParts[0]) && $queryParts[0] == "estamp=on") {
        $stamp = '<img src="./vendor/' . $estamp . '" style="width: 3cm;" />';
    }


    // if ($queryParts[0] == "estamp=on") {
    //     $stamp = '<img src="./vendor/' . $estamp . '" style="width: 3cm;" />';
    // }

    $result = getInvoiceById($invoiceID);
    if ($result) {
        $invId = $result['id'];
        $result['port'] == 'exit' ? $sectionAr = 'خروج' : $sectionAr = 'دخول';
        $result['port'] == 'exit' ? $sectionEn = 'Exit' : $sectionEn = 'Entry';


        // Set the watermark image
        $watermarkImage = './vendor/' . $logo; // Replace with the path to your watermark image
        $mpdf->SetWatermarkImage($watermarkImage);

        // Optional: Adjust watermark opacity
        $mpdf->showWatermarkImage = true; // Show the watermark image
        $mpdf->watermarkImageAlpha = 0.2; // Adjust opacity (0.0 to 1.0, where 0.0 is fully transparent and 1.0 is fully opaque)



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

        $qrImg = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $link;


        $mpdf->SetHTMLFooter('
            <table style="width: 100%; border-collapse: collapse; table-layout: fixed;">
            <tr>
            <td style="width: 33%; vertical-align: bottom;">' . '' . '</td>
            <td style="width: 33%; text-align: center;">
            <img src="' . $qrImg . '" style="width: 90px; float: left; margin-bottom: -60px; visibility: ' . $showQR . ';" />
            </td>
            <td style="width: 33%;"></td>
                </tr>
                <tr>
                    <td style="width: 30%; text-align: center;border: 1px solid;">
                        <table style="table-layout: fixed; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 4px; width: 30%;" dir="rtl">التوقيع | الختم</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 44%; text-align: center; font-weight: bold;">
                    </td>
                    <td style="width: 30%; border: 1px solid; text-align: center;">
                    Accountant | المحاسب
                    </td>
                </tr>

                <tr>
                    <td style="width: 33%; text-align: center;border: 1px solid;">
                        <table style="table-layout: fixed; border-collapse: collapse;">
                            <tr>
                                <td style="width: 30%;" dir="rtl">Signatur</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 44%; text-align: center; font-weight: bold;">
                    </td>
                    <td style="width: 33%; border: 1px solid; text-align: center;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 50%; text-align: center; direction: rtl; padding: 5px">
                                    ' . $result['created_by'] . '
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </table>

                <table style="width: 100%; text-align: center; border-collapse: collapse;">
                    <tr>
                        <td style="width: 30%; border: 1px solid;" dir="ltr">
                        ' . $stamp . '
                        </td>
                        <td style="width: 40%; text-transform: capitalize; line-height: 1.7">
                            ' . $bankNameAr . '<br/>
                            ' . $bankNameEn . '<br/>
                            Account: ' . $accountNum . '<br/>
                            IBAN: ' . $iban . '<br/>
                            E-Mail: ' . $eMail . '
                        </td>
                        <td style="width: 30%; border: 1px solid">
                            Issuance Date<br/>
                            ' . date("Y-m-d h:i:s A", strtotime($result['invoice_date'])) . '<br/><br/>
                            Printing Date<br/>
                            ' . date("Y-m-d h:i:s A") . '
                        </td>
                    </tr>
                </table>
                ');


        $header = '
                        <div style="position: absolute; bottom: 10px; left: 60px; font-size: 12px;">
                            <a href="https://www.nadim.pro" target="_blank" style="text-decoration: none; color: #00f;">Powered By | www.Nadim.pro</a>
                        </div>
                    ';

        // تعيين الترويسة للصفحات
        $mpdf->SetHTMLHeader($header);


        $html = '
            <div style="font-weight: bold; position: relative;">
                <h2 style="text-align: center;">' . $coNameEn . '</h2>
                <h2 style="line-height: 0.1; text-align: center">' . $coNameAr . '</h2>
            </div>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33%; text-align: left; line-height: 1.5; text-transform: capitalize;">
                        Tel: ' . $exPhone . '
                        <br />
                        Tel: ' . $enPhone . '
                        <br />
                        P.O.Box: ' . $mbox . '
                        <br />
                        Address: ' . $addressEn . '
                        <br />
                        <span style="text-transform: uppercase;">T.R.N: ' . $trn . '</span>
                    </td>
                    <br />

                    <td style="width: 33%; text-align: center;">
                        <img src="./vendor/' . $logo . '" style="width: 200px;" />
                    </td>
                    <br />

                    <td style="width: 33%; text-align: right; line-height: 1.5" dir="rtl">
                        هاتف: <span dir="ltr">' . $exPhone . '</span>
                        <br />
                        هاتف: <span dir="ltr">' . $enPhone . '</span>
                        <br />
                        صندوق البريد: <span dir="ltr">' . $mbox . '</span>
                        <br />
                        العنوان: ' . $addressAr . '
                        <br />
                        <span style="text-transform: uppercase;">الرقم الضريبي: ' . $trn . '</span> 
                    </td>
                    <br />

                </tr>
                <tr>
                    <td style="width: 33%; border: 1px solid; text-align: center;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 16%; text-align: left;">No:</td>
                                <td style="width: 54%;"><b>' . $result['id'] . '</b></td>
                                <td style="width: 30%; text-align: right; direction: rtl">رقم الفاتورة:</td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 33%; text-align: center; font-size: 22px; text-transform: uppercase;">
                    <span>Invoice | <b>' . $sectionEn . '</b></span>
                    <br />
                    <span>فاتورة | <b>' . $sectionAr . '</b></span>
                    </td>
                    <td style="width: 33%; text-align: right;border: 1px solid;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 20%; text-align: left;">Date:</td>
                                <td style="width: 60%; text-align: center;">' . date("Y-m-d", strtotime($result['invoice_date'])) . '</td>
                                <td style="width: 20%; text-align: right; direction: rtl">التاريخ:</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="border: 1px solid;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 17%;text-align: left;">DST: </td>
                            <td style="width: 53%; text-align: center;"><b>' . $result['destination_country'] . '</b></td>
                            <td style="width: 30%; text-align: right; direction: rtl;">بلد المقصد: </td>
                        </tr>
                    </table>        
                    </td>
                    <td colspan=2 style="border: 1px solid">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 15%; text-align: left;">Customer Name:</td>
                                <td style="width: 70%; text-align: center; text-transform: capitalize;"><b>' . $result['c_customer_name'] . '</b></td>
                                <td style="width: 15%; text-align: right; direction: rtl;">اسم العميل:</td>
                            </tr>
                        </table>        
                    </td>
                </tr>
                
                
                <tr>
                    <td colspan=3 style="border: 1px solid; text-align: center; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 12%;text-align: left;">Vehicle No:</td>
                                <td style="width: 78%; text-align: center; font-size: 14px;"><b>' . $result['vehicle_plate_number'] . '</b></td>
                                <td style="width: 10%; text-align: right; direction: rtl;">رقم المركبة:</td>
                            </tr>
                        </table>        
                    </td>
                </tr>
                <tr>
                    <td colspan=3 style="border: 1px solid; text-align: center; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 14%; text-align: left;">Driver Name:</td>
                                <td style="width: 76%; text-align: center; font-size: 14px;"><b>' . $result['driver_name'] . '</b></td>
                                <td style="width: 10%; text-align: right; direction: rtl;">اسم السائق:</td>
                            </tr>
                        </table>        
                    </td>
                </tr>


                <tr>
                    <td colspan=3 style="border: 1px solid; text-align: center; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 10%;text-align: left;">Bill No:</td>
                                <td style="width: 80%;text-align: center;"><b>' . $result['declaration_number'] . '</b></td>
                                <td style="width: 10%;text-align: right; direction: rtl;">رقم البيان: </td>
                            </tr>
                        </table>        
                    </td>
                </tr>
                
                
                
                <tr>
                    <td colspan=2 style="border: 1px solid; text-align: center; padding: 5px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 18%; text-align: left;">Goods Desc:</td>
                                <td style="width: 71%; text-align: center; font-size: 13px; word-wrap: break-word;">' . $result['goods_description'] . '</td>
                                <td style="width: 11%; text-align: right; direction: rtl;">البضاعة:</td>
                            </tr>
                        </table>        
                    </td>
                    <td style="border: 1px solid">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 20%; text-align: left;">Cust. INV#:</td>
                                <td style="width: 60%; text-align: center; font-size: 10px; word-wrap: break-word;">' . $result['customer_invoice_number'] . '</td>
                                <td style="width: 20%; text-align: right; direction: rtl;">فاتورة العميل: </td>
                            </tr>
                        </table>        
                    </td>
                </tr>

                <tr>
                    <td colspan=3 style="border: 1px solid; padding: 7px;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="width: 15%; text-align: left;">Exporter/Importer:</td>
                                <td style="width: 73%; text-align: center;"><b>' . $result['exporter_importer_name'] . '</b></td>
                                <td style="width: 12%; text-align: right; direction: rtl;">المصدّر/المستورد: </td>
                            </tr>
                        </table>        
                    </td>
                </tr>
            </table>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td colspan="3">
                        <table style="width: 100%; border-collapse: collapse; border: 1px solid black;">
                            <tr>
                            <td style="width: 40%; text-align: left; padding: 7px;">
                                <b>Description</b>
                            </td>
                            <td style="width: 40%; text-align: right; padding: 7px;">
                                <b>التفاصيل</b>
                            </td>
                            <td style="width: 20%; text-align: center;"><b>Amount | القيمة</b></td>
                            </tr>';

        if (floatval($result['total_fees'])) {
            // فصل الرسوم بناءً على <br>
            $fees = explode('<br>', $result['fee_d']);
            // حلقة لإضافة كل رسم إلى الجدول
            foreach ($fees as $fee) {
                // فصل اسم الرسم والقيمة باستخدام @
                $parts = explode('@', $fee);
                $description123 = isset($parts[0]) ? $parts[0] : ''; // الجزء الأول (الوصف)
                $amount123 = isset($parts[1]) ? $parts[1] : '';      // الجزء الثاني (القيمة)

                // إضافة الرسم إلى الجدول
                $html .= '<tr>
                            <td colspan="2" style="width: 40%; text-align: center; border: 1px solid black; padding: 5px;">' . $description123 . '</td>
                            <td style="width: 20%; text-align: center; border: 1px solid black;">' . $amount123 . '</td>
                        </tr>';
            }
        }



        if ($result['notes']) {
            $html .= '<tr>
                        <td dir="rtl" colspan=2 style="width: 50%; text-align: right; border: 1px solid black; padding: 5px;">
                        <b>ملاحظات:</b> ' . $result['notes'] . '
                        </td>
                        <td style="width: 20%; text-align: center; border: 1px solid black;">-</td>
                    </tr>';
        }



        $html .= '<tr style="background-color: #ddd">
                <td colspan=2 style="width: 80%; border: 1px solid black; padding: 5px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="font-size: 17px; font-weight: bold; text-align: left;"></td>
                            <td style="font-size: 17px; font-weight: bold; text-align: center;" dir="rtl" id="total_in_arabic">';
        if (is_numeric($result['total_fees']) && floor($result['total_fees']) == $result['total_fees']) {
            $integerNumber = intval($result['total_fees']);
            $html .= Tafqeet::arablic($integerNumber);
        } else {
            $html .= Tafqeet::arablic($result['total_fees']);
        }
        $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $words = $formatter->format($result['total_fees']);
        $html .= '<br/><small style="text-transform: capitalize; font-size: 14px;">' . $words . ' Dirhams Only</small></td>
                                            <td style="font-size: 17px; font-weight: bold; text-align: right;" dir="rtl"></td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="font-size: 17px; font-weight: bold; width: 20%; text-align: center; border: 1px solid black;">' . $result['total_fees'] . ' AED</td>
                            </tr>       
                        </table>
                    </td>
                </tr>
            </table>';
    }
}
$mpdf->WriteHTML($html);
$mpdf->Output('invoice-' . $invId . '.pdf', 'I');
