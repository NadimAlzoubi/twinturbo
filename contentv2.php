<style>
    body {
        direction: rtl;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th {
        border: 1px solid #000;
        padding: 2mm;
        text-align: center;
        font-size: 13px;
        background-color: #bbbbbb;
    }

    td {
        border: 1px solid #000;
        padding: 1.5mm;
        text-align: center;
        font-size: 12px;
    }

    tr:nth-child(even) td {
        background-color: #f2f2f2;
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

$additionalData_input = isset($_POST['driver_id']) ? $_POST['driver_id'] 
        : (isset($_POST['sau_office_id']) ? $_POST['sau_office_id'] 
        : (isset($_POST['expense_type_id']) ? $_POST['expense_type_id'] 
        : (isset($_POST['service_fee_type_id']) ? $_POST['service_fee_type_id'] 
        : (isset($_POST['customer_id']) ? $_POST['customer_id'] 
        : ''))));

$additionalData_status = isset($_POST['status']) ? $_POST['status'] : '';
$statusList = null;
if ($additionalData_status) {
    $statusList = implode("','", $additionalData_status); // يدمج القيم مع إضافة علامات الاقتباس الفردية
}



$additionalData = false;

if ($additionalData_input) {
    $additionalData_parts = explode('-', $additionalData_input);
    $additionalData = trim($additionalData_parts[0]);
}


$entity_type = $_POST['entity_type'] ?? null;


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

    if ($additionalData) {
        $query .= " AND trips.driver_id = '$additionalData'";
    }
} elseif ($reportType === 'sau_bills') {
    $query = "SELECT 
                b.*,
                o.office_name, 
                o.entity_type, 
                o.license_number
            FROM sau_bills b
            LEFT JOIN sau_offices o ON b.sau_office_id = o.id
            WHERE b.bill_date BETWEEN '$startDate' AND '$endDate'";

    if ($additionalData) {
        $query .= " AND b.sau_office_id = '$additionalData'";
    }

    if($entity_type != null){
        $query .= " AND o.entity_type = '$entity_type'";
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

    if ($additionalData) {
        $query .= " AND service_fees.service_fee_type_id = '$additionalData'";
    }

    $query .= " GROUP BY services.id";
} elseif ($reportType === 'expenses') {
    $query = "SELECT e.*, et.name AS etName 
    FROM expenses e 
    INNER JOIN expenses_types et ON e.expense_type_id = et.id 
    WHERE e.expense_date BETWEEN '$startDate' AND '$endDate'";

    if ($additionalData) {
        $query .= " AND e.expense_type_id = '$additionalData'";
    }
} elseif ($reportType === 'revenue_expense') {
    $services_query = "SELECT services.*,
            GROUP_CONCAT(service_fees.description SEPARATOR ', ') AS fee_description,
            GROUP_CONCAT(service_fees.amount SEPARATOR ', ') AS fee_amount,
            GROUP_CONCAT(service_fees.bank_deduction_amount SEPARATOR ', ') AS bank_deduction_amount,
            GROUP_CONCAT(service_fees_types.fee_name SEPARATOR ', ') AS fee_type_name,
            GROUP_CONCAT(service_fees_types.fee_amount SEPARATOR ', ') AS fee_type_amount
        FROM 
            services
        LEFT JOIN service_fees ON services.id = service_fees.service_id
        LEFT JOIN service_fees_types ON service_fees.service_fee_type_id = service_fees_types.id
        WHERE services.service_date BETWEEN '$startDate' AND '$endDate' GROUP BY services.id";

    $expenses_query = "SELECT e.*, et.name AS etName 
        FROM expenses e 
        INNER JOIN expenses_types et ON e.expense_type_id = et.id 
        WHERE e.expense_date BETWEEN '$startDate' AND '$endDate'";
} elseif ($reportType === 'invoices') {
    $query = "SELECT invoices.*,
                customers.customer_name AS customer_name_f,
                COALESCE(SUM(fees.amount), 0) AS total_fees,
                COALESCE(SUM(fees.bank_deduction), 0) AS total_bank_deduction,
                COALESCE(
                    GROUP_CONCAT(
                    CONCAT(fee_types.description, ': ', FORMAT(fees.amount, 2), 
                        IF(fees.description != '', CONCAT(' - ', fees.description), ''))
                        SEPARATOR '<br>'
                    ),
                    'No fees'
                ) AS fee_details
            FROM 
                invoices
            LEFT JOIN fees ON invoices.id = fees.invoice_id
            LEFT JOIN fee_types ON fees.fee_type_id = fee_types.id
            LEFT JOIN customers ON customers.id = invoices.customer_id
            WHERE invoices.invoice_date BETWEEN '$startDate' AND '$endDate'";
    if ($additionalData) {
        $query .= " AND invoices.customer_id = '$additionalData'";
    }
    if ($additionalData_status) {
        $query .= " AND invoices.status IN ('$statusList')";
    }
    $query .= " GROUP BY invoices.id";
}
$content = '';

if (empty($reportType) || $errormsg != null) {
    $content = '<h1>Error!</h1>';
} else {
    if ($reportType === 'revenue_expense') {
        $services_result = mysqli_query($connection, $services_query);
        $expenses_result = mysqli_query($connection, $expenses_query);
    } elseif ($query) {
        $result = mysqli_query($connection, $query);
    } else {
        die('Error executing query: ' . mysqli_error($connection));
    }
}




/// ---------------------------جدول تقرير الرحلات-----------------------
/// ---------------------------جدول تقرير الرحلات-----------------------
/// ---------------------------جدول تقرير الرحلات-----------------------
if ($reportType === 'trips') {
    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th>' . translate('serial', $lang) . '</th>
                            <th>' . translate('driver_name', $lang) . '</th>
                            <th>' . translate('trip_date', $lang) . '</th>
                            <th>' . translate('destination', $lang) . '</th>
                            <th>' . translate('trip_rent', $lang) . '</th>
                            <th>' . translate('extra_income', $lang) . '</th>
                            <th>' . translate('driver_fee', $lang) . '</th>
                            <th>' . translate('expenses_details', $lang) . '</th>
                            <th>' . translate('total_expenses', $lang) . '</th>
                            <th>' . translate('remaining', $lang) . '</th>
                            <th>' . translate('notes', $lang) . '</th>
                        </tr>
                    </thead>
                <tbody>';
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
            } elseif ($fee_type_name) {
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
                        <td>' . $trip["id"] . '</td>
                        <td>' . $trip["driver_name"] . ' | ' . $trip["vehicle_number"] . '</td>
                        <td>' . $trip["trip_date"] . '</td>
                        <td>' . $trip["destination"] . '</td>
                        <td>' . $trip["trip_rent"] . '</td>
                        <td>' . $extra_income . '</td>
                        <td>' . $trip["driver_fee"] . '</td>
                        <td>' . $fee_details . '</td>
                        <td>' . $total_fee_amount . '</td>
                        <td>' . $trip["remaining"] . '</td>
                        <td>' . $trip["notes"] . '</td>
                    </tr>';
    }

    $content .= '
                <tr>
                    <th>' . translate('serial', $lang) . '</th>
                    <th>' . translate('driver_name', $lang) . '</th>
                    <th>' . translate('trip_date', $lang) . '</th>
                    <th>' . translate('destination', $lang) . '</th>
                    <th>' . translate('trip_rent', $lang) . '</th>
                    <th>' . translate('extra_income', $lang) . '</th>
                    <th>' . translate('driver_fee', $lang) . '</th>
                    <th>' . translate('expenses_details', $lang) . '</th>
                    <th>' . translate('total_expenses', $lang) . '</th>
                    <th>' . translate('remaining', $lang) . '</th>
                    <th>' . translate('notes', $lang) . '</th>
                </tr>
                <tr>
                    <th colspan="4"><h2>' . translate('total', $lang) . '</h2></th>
                    <th><h2>' . $total_trip_rent . '</h2></th>
                    <th><h2>' . $total_extra_income . '</h2></th>
                    <th><h2>' . $total_driver_fee . '</h2></th>
                    <th colspan="2"><h2>' . $total_fee_amount_all . '</h2></th>
                    <th><h2>' . $total_remaining . '</h2></th>
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
                            <th>' . translate('serial', $lang) . '</th>
                            <th>' . translate('bill_date', $lang) . '</th>
                            <th>' . translate('office_name', $lang) . '</th>
                            <th>' . translate('license_number', $lang) . '</th>
                            <th>' . translate('bill_number', $lang) . '</th>
                            <th>' . translate('driver_name', $lang) . '</th>
                            <th>' . translate('vehicle_number', $lang) . '</th>
                            <th>' . translate('B', $lang) . '</th>
                            <th>' . translate('V', $lang) . '</th>
                            <th>' . translate('destination', $lang) . '</th>
                            <th>' . translate('price', $lang) . '</th>
                            <th>' . translate('notes', $lang) . '</th>
                        </tr>
                    </thead>
                    <tbody>';
    $total_price = 0.00;
    $total_nob = 0;
    $total_nov = 0;
    while ($sau_bill = mysqli_fetch_assoc($result)) {
        $total_price += (float) $sau_bill['price'];
        $total_nob += $sau_bill['nob'];
        $total_nov += $sau_bill['nov'];

        $content .= '<tr>
                        <td>' . $sau_bill['id'] . '</td>
                        <td>' . $sau_bill['bill_date'] . '</td>
                        <td>' . $sau_bill['office_name'] . ' | (' . translate($sau_bill['entity_type'], $lang) . ')</td>
                        <td>' . $sau_bill['license_number'] . '</td>
                        <td>' . $sau_bill['sau_bill_number'] . '</td>
                        <td>' . $sau_bill['driver_name'] . '</td>
                        <td>' . $sau_bill['vehicle_number'] . '</td>
                        <td>' . $sau_bill['nob'] . '</td>
                        <td>' . $sau_bill['nov'] . '</td>
                        <td>' . $sau_bill['destination'] . '</td>
                        <td>' . $sau_bill['price'] . '</td>
                        <td>' . $sau_bill['notes'] . '</td>
                    </tr>';
    }
    $content .= '
                <tr>
                    <th colspan="9"></th>
                    <th>' . translate('number_of_bills', $lang) . '</th>
                    <th>' . translate('number_of_vehicles', $lang) . '</th>
                    <th>' . translate('price', $lang) . '</th>
                </tr>
                <tr>
                    <th colspan="9"><h2>' . translate('total', $lang) . '</h2></th>
                    <th><h2>' . $total_nob . '</h2></th>
                    <th><h2>' . $total_nov . '</h2></th>
                    <th><h2>' . $total_price . '</h2></th>
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
                            <th>' . translate('serial', $lang) . '</th>
                            <th>' . translate('service_date', $lang) . '</th>
                            <th>' . translate('driver_name', $lang) . '</th>
                            <th>' . translate('phone_number', $lang) . '</th>
                            <th>' . translate('fees_details', $lang) . '</th>
                            <th>' . translate('V', $lang) . '</th>
                            <th>' . translate('total_fees', $lang) . '</th>
                            <th>' . translate('bank_account', $lang) . '</th>
                            <th>' . translate('notes', $lang) . '</th>
                        </tr>
                    </thead>
                <tbody>';
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
                $fee_details .= $fee_type_name . ': ' . $amount . ' - ' . $description . ' |  <span style="color: red;">-' . number_format($bank_amount, 2) . '</span><br>';
            } elseif ($description) {
                $fee_details .= $fee_type_name . ': ' . $amount . ' - ' . $description . '<br>';
            } elseif ($bank_amount) {
                $fee_details .= $fee_type_name . ': ' . $amount . ' | <span style="color: red;">-' . number_format($bank_amount, 2) . '</span><br>';
            } elseif ($fee_type_name) {
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
                        <td>' . $service['id'] . '</td>
                        <td>' . $service['service_date'] . '</td>
                        <td>' . $service['driver_name'] . ' | ' . $service['vehicle_number'] . '</td>
                        <td>' . $service['phone_number'] . '</td>
                        <td>' . $fee_details . '</td>
                        <td>' . $service['nov'] . '</td>
                        <td>' . $total_fee_amount . '</td>
                        <td><span style="color: red;">-' . $total_bank_deduction_amount . '</span></td>
                        <td>' . $service['notes'] . '</td>
                    </tr>';
    }
    $content .= '
                <tr>
                    <th colspan="6"></th>
                    <td>' . translate('number_of_vehicles', $lang) . '</th>
                    <td>' . translate('total_fees', $lang) . '</td>
                    <td>' . translate('bank_account', $lang) . '</td>
                </tr>
                <tr>
                    <th colspan="6"><h2>' . translate('total', $lang) . '</h2></th>
                    <th><h2>' . $total_vehicles . '</h2></th>
                    <th><h2>' . $total_fee_amount_all . '</h2></th>
                    <th><h2><span style="color: red;">-' . $total_bank_deduction_amount_all . '</span></h2></th>
                </tr>';

    $content .= "</tbody></table>";
}


/// ---------------------------جدول تقرير المصاريف-----------------------
/// ---------------------------جدول تقرير المصاريف-----------------------
/// ---------------------------جدول تقرير المصاريف-----------------------
if ($reportType === 'expenses') {
    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th>' . translate('serial', $lang) . '</th>
                            <th>' . translate('date', $lang) . '</th>
                            <th>' . translate('expense_type', $lang) . '</th>
                            <th>' . translate('description', $lang) . '</th>
                            <th>' . translate('amount', $lang) . '</th>
                            <th>' . translate('bank_account', $lang) . '</th>
                            <th>' . translate('notes', $lang) . '</th>
                        </tr>
                    </thead>
                    <tbody>';
    $total_exp_amount = 0.00;
    while ($expense = mysqli_fetch_assoc($result)) {
        $newE = '';
        if ($expense['bank_deduction'] == 1) {
            $newE = '<span>' . translate('none', $lang) . '</span>';
        } elseif ($expense['bank_deduction'] == 2) {
            $newE = '<span style="color: #00c800;">' . translate('deposit', $lang) . ' (+)</span>';
        } elseif ($expense['bank_deduction'] == 3) {
            $newE = '<span style="color: red;">' . translate('debit', $lang) . ' (-)</span>';
        }

        $total_exp_amount += (float) $expense['amount'];

        $content .= '<tr>
                        <td>' . $expense['id'] . '</td>
                        <td>' . $expense['expense_date'] . '</td>
                        <td>' . $expense['etName'] . '</td>
                        <td>' . $expense['description'] . '</td>
                        <td>' . $expense['amount'] . '</td>
                        <td>' . $newE . '</td>
                        <td>' . $expense['notes'] . '</td>
                    </tr>';
    }

    $content .= '<tr>
                    <th>' . translate('serial', $lang) . '</th>
                    <th>' . translate('date', $lang) . '</th>
                    <th>' . translate('expense_type', $lang) . '</th>
                    <th>' . translate('description', $lang) . '</th>
                    <th>' . translate('amount', $lang) . '</th>
                    <th>' . translate('bank_account', $lang) . '</th>
                    <th>' . translate('notes', $lang) . '</th>
                </tr>
                <tr>
                    <th colspan="4"><h2>' . translate('total', $lang) . '</h2></th>
                    <th><h2>' . $total_exp_amount . '</h2></th>
                    <th colspan="2"></th>
                </tr>';


    $content .= "</tbody></table>";
}


/// ---------------------------جدول تقرير الايرادات والمصاريف-----------------------
/// ---------------------------جدول تقرير الايرادات والمصاريف-----------------------
/// ---------------------------جدول تقرير الايرادات والمصاريف-----------------------
if ($reportType === 'revenue_expense') {
    $services = mysqli_fetch_all($services_result, MYSQLI_ASSOC);
    $expenses = mysqli_fetch_all($expenses_result, MYSQLI_ASSOC);
    $content .= '<center style="text-align:center;color:red;font-size:15px;"><b>' . translate('services', $lang) . '</b></center>';
    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th>' . translate('serial', $lang) . '</th>
                            <th>' . translate('service_date', $lang) . '</th>
                            <th>' . translate('driver_name', $lang) . '</th>
                            <th>' . translate('phone_number', $lang) . '</th>
                            <th>' . translate('fees_details', $lang) . '</th>
                            <th>' . translate('V', $lang) . '</th>
                            <th>' . translate('total_fees', $lang) . '</th>
                            <th>' . translate('bank_account', $lang) . '</th>
                            <th>' . translate('notes', $lang) . '</th>
                        </tr>
                    </thead>
                <tbody>';
    $total_fee_amount_all = 0.00;
    $total_bank_deduction_amount_all = 0.00;
    $total_vehicles = 0;
    foreach ($services as $service) {
        $fee_details = '';
        $bank_deduction_details = '';
        $total_fee_amount = 0.00;
        $total_bank_deduction_amount = 0.00;
        $fee_descriptions = explode(',', $service['fee_description'] ?? '');
        $fee_amounts = explode(',', $service['fee_amount'] ?? '');
        $fee_type_names = explode(',', $service['fee_type_name'] ?? '');
        $fee_type_amounts = explode(',', $service['fee_type_amount'] ?? '');
        $bank_deduction_amount = explode(',', $service['bank_deduction_amount'] ?? '');
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
                $fee_details .= $fee_type_name . ': ' . $amount . ' - ' . $description . ' |  <span style="color: red;">-' . number_format($bank_amount, 2) . '</span><br>';
            } elseif ($description) {
                $fee_details .= $fee_type_name . ': ' . $amount . ' - ' . $description . '<br>';
            } elseif ($bank_amount) {
                $fee_details .= $fee_type_name . ': ' . $amount . ' | <span style="color: red;">-' . number_format($bank_amount, 2) . '</span><br>';
            } elseif ($fee_type_name) {
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
                        <td>' . $service['id'] . '</td>
                        <td>' . $service['service_date'] . '</td>
                        <td>' . $service['driver_name'] . ' | ' . $service['vehicle_number'] . '</td>
                        <td>' . $service['phone_number'] . '</td>
                        <td>' . $fee_details . '</td>
                        <td>' . $service['nov'] . '</td>
                        <td>' . $total_fee_amount . '</td>
                        <td><span style="color: red;">-' . $total_bank_deduction_amount . '</span></td>
                        <td>' . $service['notes'] . '</td>
                    </tr>';
    }
    $content .= '
                <tr>
                    <th colspan="6"></th>
                    <td>' . translate('number_of_vehicles', $lang) . '</th>
                    <td>' . translate('total_fees', $lang) . '</td>
                    <td>' . translate('bank_account', $lang) . '</td>
                </tr>
                <tr>
                    <th colspan="6"><h2>' . translate('total', $lang) . '</h2></th>
                    <th><h2>' . $total_vehicles . '</h2></th>
                    <th><h2>' . $total_fee_amount_all . '</h2></th>
                    <th><h2><span style="color: red;">-' . $total_bank_deduction_amount_all . '</span></h2></th>
                </tr>';

    $content .= "</tbody></table><br /><br />";

    // ///////////////////////////-------------------------------------

    $content .= '<center style="text-align:center;color:red;font-size:15px;"><b>' . translate('expenses', $lang) . '</b></center>';
    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th>' . translate('serial', $lang) . '</th>
                            <th>' . translate('date', $lang) . '</th>
                            <th>' . translate('expense_type', $lang) . '</th>
                            <th>' . translate('description', $lang) . '</th>
                            <th>' . translate('amount', $lang) . '</th>
                            <th>' . translate('bank_account', $lang) . '</th>
                            <th>' . translate('notes', $lang) . '</th>
                        </tr>
                    </thead>
                    <tbody>';
    $total_exp_amount = 0.00;
    foreach ($expenses as $expense) {
        $newE = '';
        if ($expense['bank_deduction'] == 1) {
            $newE = '<span>' . translate('none', $lang) . '</span>';
        } elseif ($expense['bank_deduction'] == 2) {
            $newE = '<span style="color: #00c800;">' . translate('deposit', $lang) . ' (+)</span>';
        } elseif ($expense['bank_deduction'] == 3) {
            $newE = '<span style="color: red;">' . translate('debit', $lang) . ' (-)</span>';
        }

        $total_exp_amount += (float) $expense['amount'];

        $content .= '<tr>
                        <td>' . $expense['id'] . '</td>
                        <td>' . $expense['expense_date'] . '</td>
                        <td>' . $expense['etName'] . '</td>
                        <td>' . $expense['description'] . '</td>
                        <td>' . $expense['amount'] . '</td>
                        <td>' . $newE . '</td>
                        <td>' . $expense['notes'] . '</td>
                    </tr>';
    }

    $content .= '<tr>
                    <th colspan="4"><h2>' . translate('total', $lang) . '</h2></th>
                    <th><h2>' . $total_exp_amount . '</h2></th>
                    <th colspan="2"></th>
                </tr>';


    $content .= "</tbody></table>";

    $content .= "<br /><br /><br />";

    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th style="font-size: large">' . translate('revenues', $lang) . '</th>
                            <th style="font-size: large">' . translate('expenses', $lang) . '</th>
                            <th style="font-size: large">' . translate('net_total', $lang) . '</th>
                        </tr>
                    </thead>
                    <tbody>';
    $content .= '<tr>
                    <th><h2 style="color: green">' . $total_fee_amount_all . '+</h2></th>
                    <th><h2 style="color: red">' . $total_exp_amount . '-</h2></th>
                    <th><h2>' . ($total_fee_amount_all - $total_exp_amount) . '</h2></th>
                </tr>';
    $content .= "</tbody></table>";
}


/// ---------------------------جدول تقرير الفواتير-----------------------
/// ---------------------------جدول تقرير الفواتير-----------------------
/// ---------------------------جدول تقرير الفواتير-----------------------
if ($reportType === 'invoices') {
    $content .= '<table border="1" cellpadding="2">
                    <thead>
                        <tr>
                            <th>' . translate('serial', $lang) . '</th>
                            <th>' . translate('status', $lang) . '</th>
                            <th>' . translate('date', $lang) . '</th>
                            <th>' . translate('customer_name', $lang) . '</th>
                            <th>' . translate('exporter_importer_name', $lang) . '</th>
                            <th>' . translate('driver_name', $lang) . '</th>
                            <th>' . translate('B', $lang) . '</th>
                            <th>' . translate('V', $lang) . '</th>
                            <th>' . translate('fees_details', $lang) . '</th>
                            <th>' . translate('total_fees', $lang) . '</th>
                            <th>' . translate('bank_account', $lang) . '</th>
                            <th>' . translate('notes', $lang) . '</th>
                        </tr>
                    </thead>
                <tbody>';
    $total_fee_amount_all = 0;
    $total_bank_deduction_amount_all = 0;
    $total_vehicles = 0;
    $total_bills = 0;
    while ($invoice = mysqli_fetch_assoc($result)) {
        // تنسيق تفاصيل الرسوم وحساب المجموع
        $total_fee_amount_all += (float) $invoice['total_fees'];
        $total_bank_deduction_amount_all += (float) $invoice['total_bank_deduction'];
        $total_vehicles += $invoice['vehicle_count'];
        $total_bills += $invoice['declaration_count'];
        $content .= '<tr>
                        <td>' . $invoice['id'] . '</td>
                        <td>' . translate(strtolower($invoice['status']), $lang) . '</td>
                        <td>' . $invoice['invoice_date'] . '</td>
                        <td>' . $invoice['customer_name_f'] . '</td>
                        <td>' . $invoice['exporter_importer_name'] . '</td>
                        <td>' . $invoice['driver_name'] . ' | ' . $invoice['vehicle_plate_number'] . '</td>
                        <td>' . $invoice['declaration_count'] . '</td>
                        <td>' . $invoice['vehicle_count'] . '</td>
                        <td>' . $invoice['fee_details'] . '</td>
                        <td>' . $invoice['total_fees'] . '</td>
                        <td><span style="color: red;">-' . $invoice['total_bank_deduction'] . '</span></td>
                        <td>' . $invoice['notes'] . '</td>
                    </tr>';
    }
    $content .= '
                <tr>
                    <th colspan="8"></th>
                    <td>' . translate('number_of_bills', $lang) . ' | B</th>
                    <td>' . translate('number_of_vehicles', $lang) . ' | V</th>
                    <td>' . translate('total_fees', $lang) . '</td>
                    <td>' . translate('bank_account', $lang) . '</td>
                </tr>
                <tr>
                    <th colspan="8"><h2>' . translate('total', $lang) . '</h2></th>
                    <th><h2>' . $total_bills . '</h2></th>
                    <th><h2>' . $total_vehicles . '</h2></th>
                    <th><h2>' . $total_fee_amount_all . '</h2></th>
                    <th><h2><span style="color: red;">-' . $total_bank_deduction_amount_all . '</span></h2></th>
                </tr>';

    $content .= "</tbody></table>";
}


mysqli_close($connection);
echo $content;
