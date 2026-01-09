<style>
    body {
        direction: rtl;
        /* font-family: 'aealarabiya'; */
    }
    table {
        width: 100%;
        border-collapse: collapse;
        page-break-inside: avoid !important;
    }
    th {
        border: 1px solid #000;
        padding: 2mm;
        text-align: center;
        font-size: 10px;
        background-color: #eee;
    }
    td {
        border: 1px solid #000;
        padding: 2mm; 
        text-align: center;
        font-size: 10px;
    }
    h1{
        text-align: center;
    }
</style>
<?php
include('./inc/connect.php');
include('./lang.php');
$errormsg = null;

$lang = isset($_GET['dir']) ? ($_GET['dir'] == 'rtl' ? 'ar' : 'en') : 'ar';
// الحصول على البيانات من النموذج
$reportType = $_POST['report_type'];
$startDate = $_POST['start_date'];
$endDate = $_POST['end_date'];

$additionalData_input = isset($_POST['driver_id']) ? $_POST['driver_id'] : 
                  (isset($_POST['sau_office_id']) ? $_POST['sau_office_id'] : 
                  (isset($_POST['expense_type_id']) ? $_POST['expense_type_id'] : 
                  (isset($_POST['service_fee_type_id']) ? $_POST['service_fee_type_id'] : '')));

// $additionalData_input = isset($_POST['driver_id']) ? $_POST['driver_id'] : (isset($_POST['sau_office_id']) ? $_POST['sau_office_id'] : (isset($_POST['expense_type_id']) ? $_POST['expense_type_id'] : ''));

$additionalData = false;

if($additionalData_input){
    $additionalData_parts = explode('-', $additionalData_input);
    $additionalData = trim($additionalData_parts[0]);
}


// بناء الاستعلام بناءً على نوع التقرير
$query = "";
if ($reportType === 'trips') {
    $query = "SELECT trips.*,
                drivers.driver_name,
                drivers.vehicle_number,
                COALESCE(fees.fee_description, '') AS fee_description,
                COALESCE(fees.fee_amount, '') AS fee_amount,
                COALESCE(fees_types.fee_type_name, '') AS fee_type_name,
                COALESCE(fees_types.fee_type_amount, '') AS fee_type_amount
            FROM 
                trips
            JOIN 
                drivers ON trips.driver_id = drivers.id
            LEFT JOIN (
                SELECT 
                    trip_id, 
                    GROUP_CONCAT(description SEPARATOR ', ') as fee_description,
                    GROUP_CONCAT(amount SEPARATOR ', ') as fee_amount
                FROM 
                    trip_fees
                GROUP BY 
                    trip_id
            ) as fees ON trips.id = fees.trip_id
            LEFT JOIN (
                SELECT 
                    trip_fees.trip_id,
                    GROUP_CONCAT(trip_fees_types.fee_name SEPARATOR ', ') as fee_type_name,
                    GROUP_CONCAT(trip_fees_types.fee_amount SEPARATOR ', ') as fee_type_amount
                FROM 
                    trip_fees
                JOIN 
                    trip_fees_types ON trip_fees.trip_fee_type_id = trip_fees_types.id
                GROUP BY 
                    trip_fees.trip_id
            ) as fees_types ON trips.id = fees_types.trip_id
            WHERE 
                trips.trip_date BETWEEN '$startDate' AND '$endDate'";

    if($additionalData){
        $query .= " AND trips.driver_id = '$additionalData'";
    }
} elseif ($reportType === 'sau_bills') {
    $query = "SELECT 
                b.*,
                o.office_name, 
                o.license_number
            FROM sau_bills b
            LEFT JOIN sau_offices o ON b.sau_office_id = o.id
            WHERE b.bill_date BETWEEN '$startDate' AND '$endDate'";

    if($additionalData){
        $query .= " AND b.sau_office_id = '$additionalData'";
    }
} elseif ($reportType === 'services') {
    $query = "SELECT services.*,
                GROUP_CONCAT(service_fees.description SEPARATOR ', ') AS fee_description,
                GROUP_CONCAT(service_fees.amount SEPARATOR ', ') AS fee_amount,
                GROUP_CONCAT(service_fees.bank_deduction_amount SEPARATOR ', ') AS bank_deduction_amount,
                GROUP_CONCAT(service_fees_types.fee_name SEPARATOR ', ') AS fee_type_name,
                GROUP_CONCAT(service_fees_types.fee_amount SEPARATOR ', ') AS fee_type_amount
            FROM 
                services
            LEFT JOIN service_fees ON services.id = service_fees.service_id
            LEFT JOIN service_fees_types ON service_fees.service_fee_type_id = service_fees_types.id
            WHERE services.service_date BETWEEN '$startDate' AND '$endDate'";

            if($additionalData){
                $query .= " AND service_fees.service_fee_type_id = '$additionalData'";
            }
            
            $query .= " GROUP BY services.id";

    
} elseif ($reportType === 'expenses') {
    $query = "SELECT e.*, et.name AS etName 
    FROM expenses e 
    INNER JOIN expenses_types et ON e.expense_type_id = et.id 
    WHERE e.expense_date BETWEEN '$startDate' AND '$endDate'";

    if($additionalData){
        $query .= " AND e.expense_type_id = '$additionalData'";
    }
}

$content = '';

if (empty($query) || empty($reportType) || $errormsg != null) {
    $content = '<h1>Error!</h1>';
} else {   
    $result = mysqli_query($connection, $query);
}






/// ---------------------------جدول تقرير الرحلات-----------------------
/// ---------------------------جدول تقرير الرحلات-----------------------
/// ---------------------------جدول تقرير الرحلات-----------------------
if ($reportType === 'trips') {
    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th width="6.545%">'.translate('serial', $lang).'</th>
                            <th width="16.09%">'.translate('driver_name', $lang).'</th>
                            <th width="8.09%">'.translate('trip_date', $lang).'</th>
                            <th width="6.09%">'.translate('destination', $lang).'</th>
                            <th width="7.09%">'.translate('trip_rent', $lang).'</th>
                            <th width="9.09%">'.translate('extra_income', $lang).'</th>
                            <th width="7.09%">'.translate('driver_fee', $lang).'</th>
                            <th width="15.645%">'.translate('expenses_details', $lang).'</th>
                            <th width="8.09%">'.translate('total_expenses', $lang).'</th>
                            <th width="10.09%">'.translate('remaining', $lang).'</th>
                            <th width="6.09%">'.translate('notes', $lang).'</th>
                        </tr>
                    </thead>
                <tbody>';
    $serial = 1;
    $total_extra_income = 0.00; 
    $total_trip_rent = 0.00; 
    $total_driver_fee = 0.00; 
    $total_remaining = 0.00; 
    $total_fee_amount_all = 0.00; 
    while ($trip = mysqli_fetch_assoc($result)) {
        // تنسيق تفاصيل الرسوم وحساب المجموع
        $fee_details = '';
        $total_fee_amount = 0.00; 
        $fee_descriptions = explode(',', $trip['fee_description']);
        $fee_amounts = explode(',', $trip['fee_amount']);
        $fee_type_names = explode(',', $trip['fee_type_name']);
        $fee_type_amounts = explode(',', $trip['fee_type_amount']);
        $num_fees = count($fee_descriptions); // عدد الرسوم
        for ($i = 0; $i < $num_fees; $i++) {
            $description = trim(htmlspecialchars($fee_descriptions[$i] ?? ''));
            $amount = trim(htmlspecialchars($fee_amounts[$i] ?? '0'));
            $fee_type_name = htmlspecialchars($fee_type_names[$i] ?? '');
            $fee_type_amount = htmlspecialchars($fee_type_amounts[$i] ?? '0');
            // حساب المجموع
            $total_fee_amount += (float) $amount;
            // تنظيم العرض
            if ($description && $fee_type_name) {
                $fee_details .= "{$fee_type_name}: {$description} - {$amount}<br>";
            } elseif($fee_type_name) {
                $fee_details .= "{$fee_type_name}: {$amount}<br>";
            } else {
                $fee_details .= "";
            }
        }
        if ($trip['extra_income']) {
            $extra_income = $trip['extra_income'];
        } 
        if ($trip['extra_income_des']) {
            $extra_income = "{$trip['extra_income_des']}: {$extra_income}<br>";
        }
        $total_extra_income += (float) $trip['extra_income'];
        $total_trip_rent += (float) $trip['trip_rent'];
        $total_driver_fee += (float) $trip['driver_fee'];
        $total_remaining += (float) $trip['remaining'];
        $total_fee_amount_all += (float) $total_fee_amount;
        $fee_details = preg_replace('/<br\s*\/?>\s*$/i', '', $fee_details);
        $extra_income = preg_replace('/<br\s*\/?>\s*$/i', '', $extra_income);
        $content .= '<tr>
                        <td width="6.545%">'.$serial.'</td>
                        <td width="16.09%">'.$trip["driver_name"].' | '.$trip["vehicle_number"].'</td>
                        <td width="8.09%">'.$trip["trip_date"].'</td>
                        <td width="6.09%">'.$trip["destination"].'</td>
                        <td width="7.09%">'.$trip["trip_rent"].'</td>
                        <td width="9.09%">'.$extra_income.'</td>
                        <td width="7.09%">'.$trip["driver_fee"].'</td>
                        <td width="15.645%">'.$fee_details.'</td>
                        <td width="8.09%">'.$total_fee_amount.'</td>
                        <td width="10.09%">'.$trip["remaining"].'</td>
                        <td width="6.09%">'.$trip["notes"].'</td>
                    </tr>';
        $serial++;
    }

    $content .= '
                <tr>
                    <th colspan="11"></th>
                </tr>
                <tr>
                    <th width="6.545%">'.translate('serial', $lang).'</th>
                    <th width="16.09%">'.translate('driver_name', $lang).'</th>
                    <th width="8.09%">'.translate('trip_date', $lang).'</th>
                    <th width="6.09%">'.translate('destination', $lang).'</th>
                    <th width="7.09%">'.translate('trip_rent', $lang).'</th>
                    <th width="9.09%">'.translate('extra_income', $lang).'</th>
                    <th width="7.09%">'.translate('driver_fee', $lang).'</th>
                    <th width="15.645%">'.translate('expenses_details', $lang).'</th>
                    <th width="8.09%">'.translate('total_expenses', $lang).'</th>
                    <th width="10.09%">'.translate('remaining', $lang).'</th>
                    <th width="6.09%">'.translate('notes', $lang).'</th>
                </tr>
                <tr>
                    <th colspan="4"><h2>'.translate('total', $lang).'</h2></th>
                    <th><h2>'.$total_trip_rent.'</h2></th>
                    <th><h2>'.$total_extra_income.'</h2></th>
                    <th><h2>'.$total_driver_fee.'</h2></th>
                    <th colspan="2"><h2>'.$total_fee_amount_all.'</h2></th>
                    <th><h2>'.$total_remaining.'</h2></th>
                    <th></th>
                </tr>';

    $content .= "</tbody></table>";
}

/// ---------------------------جدول تقرير البيانات السعودية-----------------------
/// ---------------------------جدول تقرير البيانات السعودية-----------------------
/// ---------------------------جدول تقرير البيانات السعودية-----------------------
if ($reportType === 'sau_bills') {
    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th width="6%">'.translate('serial', $lang).'</th>
                            <th width="8%">'.translate('bill_date', $lang).'</th>
                            <th width="15%">'.translate('office_name', $lang).'</th>
                            <th width="8%">'.translate('license_number', $lang).'</th>
                            <th width="8%">'.translate('bill_number', $lang).'</th>
                            <th width="18%">'.translate('driver_name', $lang).'</th>
                            <th width="8%">'.translate('vehicle_number', $lang).'</th>
                            <th width="3%">'.translate('B', $lang).'</th>
                            <th width="3%">'.translate('V', $lang).'</th>
                            <th width="7%">'.translate('destination', $lang).'</th>
                            <th width="8%">'.translate('price', $lang).'</th>
                            <th width="8%">'.translate('notes', $lang).'</th>
                        </tr>
                    </thead>
                    <tbody>';
    $serial = 1;
    $total_price = 0.00; 
    $total_nob = 0;
    $total_nov = 0;
    while ($sau_bill = mysqli_fetch_assoc($result)) {
        $total_price += (float) $sau_bill['price'];
        $total_nob += $sau_bill['nob'];
        $total_nov += $sau_bill['nov'];

        $content .= '<tr>
                        <td width="6%">'.$serial.'</td>
                        <td width="8%">'.$sau_bill['bill_date'].'</td>
                        <td width="15%">'.$sau_bill['office_name'].'</td>
                        <td width="8%">'.$sau_bill['license_number'].'</td>
                        <td width="8%">'.$sau_bill['sau_bill_number'].'</td>
                        <td width="18%">'.$sau_bill['driver_name'].'</td>
                        <td width="8%">'.$sau_bill['vehicle_number'].'</td>
                        <td width="3%">'.$sau_bill['nob'].'</td>
                        <td width="3%">'.$sau_bill['nov'].'</td>
                        <td width="7%">'.$sau_bill['destination'].'</td>
                        <td width="8%">'.$sau_bill['price'].'</td>
                        <td width="8%">'.$sau_bill['notes'].'</td>
                    </tr>';
        $serial++;
    }
    $content .= '<tr>
                    <th colspan="12"></th>
                </tr>
                <tr>
                    <th width="50%" colspan="9"></th>
                    <th width="15%">'.translate('number_of_bills', $lang).'</th>
                    <th width="15%">'.translate('number_of_vehicles', $lang).'</th>
                    <th width="20%">'.translate('price', $lang).'</th>
                </tr>
                <tr>
                    <th width="50%" colspan="9"><h2>'.translate('total', $lang).'</h2></th>
                    <th width="15%"><h2>'.$total_nob.'</h2></th>
                    <th width="15%"><h2>'.$total_nov.'</h2></th>
                    <th width="20%"><h2>'.$total_price.'</h2></th>
                </tr>';
    $content .= "</tbody></table>";
}











/// ---------------------------جدول تقرير الخدمات-----------------------
/// ---------------------------جدول تقرير الخدمات-----------------------
/// ---------------------------جدول تقرير الخدمات-----------------------
if ($reportType === 'services') {
    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th width="8%">'.translate('serial', $lang).'</th>
                            <th width="8%">'.translate('service_date', $lang).'</th>
                            <th width="19%">'.translate('driver_name', $lang).'</th>
                            <th width="9%">'.translate('phone_number', $lang).'</th>
                            <th width="28%">'.translate('fees_details', $lang).'</th>
                            <th width="4%">'.translate('V', $lang).'</th>
                            <th width="8%">'.translate('total_fees', $lang).'</th>
                            <th width="8%">'.translate('bank_account', $lang).'</th>
                            <th width="8%">'.translate('notes', $lang).'</th>
                        </tr>
                    </thead>
                <tbody>';
    $serial = 1;
    $total_fee_amount_all = 0.00;
    $total_bank_deduction_amount_all = 0.00;
    $total_vehicles = 0;
    while ($service = mysqli_fetch_assoc($result)) {
        // تنسيق تفاصيل الرسوم وحساب المجموع
        $fee_details = '';
        $bank_deduction_details = '';
        $total_fee_amount = 0.00; 
        $total_bank_deduction_amount = 0.00; 
        $fee_descriptions = explode(',', $service['fee_description']);
        $fee_amounts = explode(',', $service['fee_amount']);
        $fee_type_names = explode(',', $service['fee_type_name']);
        $fee_type_amounts = explode(',', $service['fee_type_amount']);
        $bank_deduction_amount = explode(',', $service['bank_deduction_amount']);
        $num_fees = count($fee_type_names); // عدد الرسوم
        for ($i = 0; $i < $num_fees; $i++) {
            $description = trim(htmlspecialchars($fee_descriptions[$i] ?? ''));
            $amount = trim(htmlspecialchars($fee_amounts[$i] ?? '0'));
            $bank_amount = trim(htmlspecialchars($bank_deduction_amount[$i] ?? '0'));
            $fee_type_name = htmlspecialchars($fee_type_names[$i] ?? '');
            $fee_type_amount = htmlspecialchars($fee_type_amounts[$i] ?? '0');
            // حساب المجموع
            $total_fee_amount += (float) $amount;
            $total_bank_deduction_amount += (float) $bank_amount;
            // تنظيم العرض
            if ($description && $bank_amount) {
                $fee_details .= $fee_type_name . ': ' . $amount . ' - ' . $description . ' |  <span style="color: red;">-'.number_format($bank_amount, 2).'</span><br>';
            } elseif ($description) {
                $fee_details .= $fee_type_name . ': ' . $amount . ' - ' . $description . '<br>';
            } elseif ($bank_amount) {
                $fee_details .= $fee_type_name . ': ' . $amount . ' | <span style="color: red;">-' . number_format($bank_amount, 2) . '</span><br>';
            } elseif ($fee_type_name){ 
                $fee_details .= $fee_type_name . ': ' . $amount . '<br>';
            } else {
                $fee_details .= '';
            }
        }
        $total_fee_amount_all += (float) $total_fee_amount;
        $total_bank_deduction_amount_all += (float) $total_bank_deduction_amount;
        $total_vehicles += $service['nov'];
        $fee_details = preg_replace('/<br\s*\/?>\s*$/i', '', $fee_details);
        $content .= '<tr>
                        <td width="8%">'.$serial.'</td>
                        <td width="8%">'.$service['service_date'].'</td>
                        <td width="19%">'.$service['driver_name'].' | '.$service['vehicle_number'].'</td>
                        <td width="9%">'.$service['phone_number'].'</td>
                        <td width="28%">'.$fee_details.'</td>
                        <td width="4%">'.$service['nov'].'</td>
                        <td width="8%">'.$total_fee_amount.'</td>
                        <td width="8%"><span style="color: red;">-' . $total_bank_deduction_amount . '</span></td>
                        <td width="8%">'.$service['notes'].'</td>
                    </tr>';
        $serial++;
    }
    $content .= '<tr>
                    <th colspan="9"></th>
                </tr>
                <tr>
                    <th width="50%" colspan="6"></th>
                    <th width="14%">'.translate('number_of_vehicles', $lang).'</th>
                    <th width="18%">'.translate('total_fees', $lang).'</th>
                    <th width="18%">'.translate('bank_account', $lang).'</th>
                </tr>
                <tr>
                    <th width="50%" colspan="6"><h2>'.translate('total', $lang).'</h2></th>
                    <th width="14%"><h2>'.$total_vehicles.'</h2></th>
                    <th width="18%"><h2>'.$total_fee_amount_all.'</h2></th>
                    <th width="18%"><h2><span style="color: red;">-'.$total_bank_deduction_amount_all . '</span></h2></th>
                </tr>';

    $content .= "</tbody></table>";
}














if ($reportType === 'expenses') {
    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th width="10%">'.translate('serial', $lang).'</th>
                            <th width="10%">'.translate('date', $lang).'</th>
                            <th width="15%">'.translate('expense_type', $lang).'</th>
                            <th width="25%">'.translate('description', $lang).'</th>
                            <th width="15%">'.translate('amount', $lang).'</th>
                            <th width="15%">'.translate('bank_account', $lang).'</th>
                            <th width="10%">'.translate('notes', $lang).'</th>
                        </tr>
                    </thead>
                    <tbody>';
    $serial = 1;
    $total_exp_amount = 0.00;
    while ($expense = mysqli_fetch_assoc($result)) {
        $newE = '';
        if($expense['bank_deduction'] == 1) {
            $newE = '<span>'.translate('none', $lang).'</span>';
        } elseif ($expense['bank_deduction'] == 2) {
            $newE = '<span style="color: #00c800;">'.translate('deposit', $lang).' (+)</span>';
        } elseif ($expense['bank_deduction'] == 3) {
            $newE = '<span style="color: red;">'.translate('debit', $lang).' (-)</span>';
        }

        $total_exp_amount += (float) $expense['amount'];

        $content .= '<tr>
                        <td width="10%">'.$serial.'</td>
                        <td width="10%">'.$expense['expense_date'].'</td>
                        <td width="15%">'.$expense['etName'].'</td>
                        <td width="25%">'.$expense['description'].'</td>
                        <td width="15%">'.$expense['amount'].'</td>
                        <td width="15%">'.$newE.'</td>
                        <td width="10%">'.$expense['notes'].'</td>
                    </tr>';
        $serial++;
    }

    $content .= '
                <tr>
                    <th colspan="7"></th>
                </tr>                        
                <tr>
                    <th width="10%">'.translate('serial', $lang).'</th>
                    <th width="10%">'.translate('date', $lang).'</th>
                    <th width="15%">'.translate('expense_type', $lang).'</th>
                    <th width="25%">'.translate('description', $lang).'</th>
                    <th width="15%">'.translate('amount', $lang).'</th>
                    <th width="15%">'.translate('bank_account', $lang).'</th>
                    <th width="10%">'.translate('notes', $lang).'</th>
                </tr>
                <tr>
                    <th colspan="4"><h2>'.translate('total', $lang).'</h2></th>
                    <th><h2>'.$total_exp_amount.'</h2></th>
                    <th colspan="2"></th>
                </tr>';
    
    
    $content .= "</tbody></table>";
}


mysqli_close($connection);
echo $content;