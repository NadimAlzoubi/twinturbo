<?php
include_once('./header.php');
?>

<head>
    <title><?php echo translate('invoices', $lang) . ' - ' . $translated_user_role; ?></title>
</head>

<div class="container">
    <h2 class="mb-4"><?php echo translate('add_invoice', $lang); ?></h2>


    <div class="alert alert-info" style="font-weight: bold; display: none" role="alert">
        <span class="ml-1 mr-1" id="pop_exit_clearance"></span>
        <span class="ml-1 mr-1" id="pop_exit_returns"></span>
        <span class="ml-1 mr-1" id="pop_entry_clearance"></span>
        <span class="ml-1 mr-1" id="pop_entry_returns"></span>
        <span class="ml-1 mr-1" id="pop_customer_notes"></span>
    </div>


    <form method="post" id="invoiceForm">
        <div class="form-container">
            <div class="form-group">
                <label for="invoice_date"><?php echo translate('date', $lang); ?></label>
                <input type="datetime-local" class="form-control" id="invoice_date" name="invoice_date" required>
            </div>
            <div class="form-group">
                <label for="port"><?php echo translate('port', $lang); ?></label>
                <select name="port" class="form-select" id="port" required>
                    <option value="" selected><?php echo translate('choose', $lang); ?></option>
                    <option value="exit"><?php echo translate('exit', $lang); ?></option>
                    <option value="entry"><?php echo translate('entry', $lang); ?></option>
                </select>
            </div>
            <div class="form-group">
                <label for="status"><?php echo translate('status', $lang); ?></label>
                <select name="status" class="form-select" id="status" required>
                    <option selected><?php echo translate('choose', $lang); ?></option>
                    <option value="Paid"><?php echo translate('paid', $lang); ?></option>
                    <option value="Pending"><?php echo translate('pending', $lang); ?></option>
                    <?php //if (isset($_GET['invoice-uid'])): ?>
                        <!-- <option value="Cancelled"><?php //echo translate('cancelled', $lang); ?></option> -->
                    <?php //endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="customer_id"><?php echo translate('customer_id', $lang); ?></label>
                <input type="text" class="form-control" id="customer_id" name="customer_id" required placeholder="<?php echo translate('type_to_search', $lang); ?>...">
                <input type="hidden" class="form-control" id="hidden_customer_id" name="hidden_customer_id">
            </div>
            <?php /*<div class="form-group">
                <label for="customer_name"><?php echo translate('customer_name', $lang); ?></label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" readonly>
            </div> */ ?>
            <div class="form-group">
                <label for="exporter_importer_name"><?php echo translate('exporter_importer_name', $lang); ?></label>
                <input type="text" class="form-control" id="exporter_importer_name" name="exporter_importer_name" required>
            </div>
            <div class="form-group">
                <label for="driver_name"><?php echo translate('driver_name', $lang); ?></label>
                <input type="text" class="form-control" id="driver_name" name="driver_name" required>
            </div>
            <div class="form-group">
                <label for="destination_country"><?php echo translate('destination_country', $lang); ?></label>
                <input type="text" class="form-control" id="destination_country" name="destination_country" required>
            </div>
            <div class="form-group">
                <label for="declaration_number"><?php echo translate('declaration_number', $lang); ?></label>
                <input type="text" class="form-control" id="declaration_number" name="declaration_number" required>
            </div>
            <div class="form-group">
                <label for="vehicle_plate_number"><?php echo translate('vehicle_plate_number', $lang); ?></label>
                <input type="text" class="form-control" id="vehicle_plate_number" name="vehicle_plate_number" required>
            </div>
            <div class="form-group">
                <label for="declaration_count"><?php echo translate('declaration_count', $lang); ?></label>
                <input type="number" value="1" min="1" class="form-control" id="declaration_count" name="declaration_count" required>
            </div>
            <div class="form-group">
                <label for="vehicle_count"><?php echo translate('vehicle_count', $lang); ?></label>
                <input type="number" value="1" min="1" class="form-control" id="vehicle_count" name="vehicle_count" required>
            </div>
            <div class="form-group">
                <label for="customer_invoice_number"><?php echo translate('customer_invoice_number', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input type="text" class="form-control" id="customer_invoice_number" name="customer_invoice_number">
            </div>
            <div class="form-group">
                <label for="goods_description"><?php echo translate('goods_description', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input type="text" class="form-control" id="goods_description" name="goods_description">
            </div>
            <div class="form-group">
                <label for="notes"><?php echo translate('notes', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input type="text" class="form-control" id="notes" name="notes">
            </div>
            <?php /*
            <div class="form-group">
                <label for="returned_amount"><?php echo translate('returned_amount', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input type="number" value="0" min="0" class="form-control" id="returned_amount" name="returned_amount">
            </div>
            */ ?>
        </div>





        <div class="text-center"><?php echo translate('invoice_fees', $lang); ?></div>
        <hr class="m-3">


        <div id="feesContainer">

        </div>

        <div class="d-flex flex-column flex-md-row align-items-center justify-content-start gap-4">
            <button type="button" id="addFee" class="btn btn-primary flex-grow-1 w-50 w-md-auto">
                <?php echo translate('add_a_new_fee', $lang); ?>
            </button>
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-around gap-2 w-75 w-md-auto">
                <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                    <label for="total_sum" class="mb-1 mb-md-0">
                        <?php echo translate('total', $lang); ?>
                    </label>
                    <input readonly id="total_sum" type="number" class="form-control w-75 w-md-auto text-center">
                </div>
                <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                    <label for="total_bank_sum" class="mb-1 mb-md-0 w-50 w-sm-75">
                    <small>
                        <?php echo translate('facilities_deduction', $lang); ?>
                    </small>
                    </label>
                    <input readonly id="total_bank_sum" type="number" class="form-control w-75 w-md-auto text-center">
                </div>
            </div>
        </div>



        <hr class="m-3">

        <button name="insert-invoice" id="insert-invoice-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-invoice" id="update-invoice-btn" type="submit"
            class="btn btn-warning" value="1"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./invoices.php" id="cancel-invoice-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>

    </form>




    <h2 class="mb-4 mt-5"><?php echo translate('invoices', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit = isset($_POST['limit-invoice']) ? $_POST['limit-invoice'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-invoice" class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-invoice" class="form-select me-2" id="limit-invoice" required style="width: auto;">
                <option value="25" <?php if ($selected_limit == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-invoice-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="invoices_table" class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('invoice_no', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('status', $lang); ?></th>
                    <th><?php echo translate('date', $lang); ?></th>
                    <th><?php echo translate('customer_name', $lang); ?></th>
                    <th><?php echo translate('exporter_importer_name', $lang); ?></th>
                    <th><?php echo translate('driver_name', $lang); ?></th>
                    <th><?php echo translate('count', $lang); ?></th>
                    <th><?php echo translate('fees_details', $lang); ?></th>
                    <th><?php echo translate('total_fees', $lang); ?></th>
                    <th><?php echo translate('facilities_deduction', $lang); ?></th>
                    <th><?php echo translate('notes', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php


                if (isset($_POST['limit-invoice'])) {
                    $sql_limit_invoices = mysqli_real_escape_string($connection, $_POST['limit-invoice']);
                } else {
                    $sql_limit_invoices = $sql_defualt_limit;
                }
                $invoices = getAllInvoices($sql_limit_invoices);
                foreach ($invoices as $invoice) {
                    // $print_text = "rid=" . $invoice['id'];
                    // $print_encrypted_text = encrypt($print_text, $s3key);

                    $print_text = "invoiceID=" . $invoice['id'];
                    $eprint_text = "estamp=on&invoiceID=" . $invoice['id'];
                    $TasVal = $invoice['total_bank_deduction'];
                    $print_encrypted_text = encrypt($print_text, $s3key);
                    $eprint_encrypted_text = encrypt($eprint_text, $s3key);


                    $modified = false;

                    // إذا كان تاريخ الإنشاء يساوي تاريخ التحديث
                    if (strtotime($invoice['created_at']) === strtotime($invoice['updated_at'])) {
                        $infobtn = 'info'; // الزر يبقى info
                    } else {
                        // إذا كان التحديث ناتجًا عن عملية الدفع
                        if (!is_null($invoice['payment_date']) && strtotime($invoice['payment_date']) === strtotime($invoice['updated_at'])) {
                            $infobtn = 'info'; // الزر يبقى info لأن التحديث ليس تعديلًا
                        } else {
                            $infobtn = 'danger'; // الزر يصبح danger لأن التحديث تعديل حقيقي
                            $modified = true;    // التحديث يُعتبر تعديلًا
                        }
                    }

                    $payment_date = is_null($invoice['payment_date'])
                        ? null
                        : date('Y-m-d H:i:s A', strtotime($invoice['payment_date']));
                    $created_at = date('Y-m-d H:i:s A', strtotime($invoice['created_at']));
                    $created_by = $invoice['created_by'];
                    $updated_at = date('Y-m-d H:i:s A', strtotime($invoice['updated_at']));
                    $updated_by = $invoice['updated_by'];
                    $extra_info_title = translate('created_at', $lang) . ": {$created_at}\n" .
                        translate('created_by', $lang) . ": {$created_by}\n";
                    if ($modified) {
                        $extra_info_title .= translate('updated_at', $lang) . ": {$updated_at}\n" .
                            translate('updated_by', $lang) . ": {$updated_by}\n";
                    }
                    if ($invoice['is_postpaid']) {
                        $extra_info_title .= translate('payment_date', $lang) . ": {$payment_date}";
                    }
                    $extra_info_title = trim($extra_info_title);

                    $status_colors = [
                        'Paid' => '#00c800',       // أخضر للحالة المدفوعة
                        'Pending' => '#FFFF00',    // برتقالي للحالة المعلقة
                        'Cancelled' => '#FF0000',  // أحمر للحالة الملغاة
                        'Draft' => '#555555'     // ذهبي للحالة المتأخرة
                    ];
                    $status_color = $status_colors[$invoice['status']] ?? '#000000'; // لون افتراضي أسود إذا لم تكن الحالة موجودة



                    echo "<tr>
                    <td>{$invoice['id']}</td>
                    <td>
                        " . (
                        ($invoice['is_postpaid'] != true && $invoice['status'] == 'Pending') ?
                        " <button class='btn btn-dark btn-sm pay-invoice-button' data-id='{$invoice['id']}' title='Pay the invoice'><i class='fas fa-dollar-sign'></i></button>"
                        :
                        ''
                    ) . "
                         <button class='btn btn-{$infobtn} btn-sm' title='" . htmlspecialchars($extra_info_title) . "'><i class='fas fa-info'></i></button>
                        " . (
                        ($user_role != 'user' && $invoice['status'] != 'Cancelled') ?
                        " <button class='btn btn-warning btn-sm edit-button' data-old_bank_amount='{$invoice['total_bank_deduction']}' data-id='{$invoice['id']}'><i class='far fa-edit'></i></button>"
                        :
                        ''
                    ) . (
                        ($invoice['status'] != 'Cancelled') ?
                        " <a href='./generate-invoice.php?{$print_encrypted_text}' target='_blank' class='btn btn-success btn-sm' title='Print'><i class='fas fa-print'></i></a>"
                        :
                        ''
                    ) . "
                    </td>
                    <td style='background-color: $status_color'>" . translate(strtolower($invoice['status']), $lang) . "</td>
                    <td>{$invoice['invoice_date']}</td>
                    <td>{$invoice['customer_name_f']}</td>
                    <td>{$invoice['exporter_importer_name']}</td>
                    <td>{$invoice['driver_name']} | {$invoice['vehicle_plate_number']}</td>
                    <td>B: {$invoice['declaration_count']} | V: {$invoice['vehicle_count']}</td>
                    <td>{$invoice['fee_details']}</td>
                    <td>{$invoice['total_fees']}</td>
                    <td>{$invoice['total_bank_deduction']}</td>
                    <td>{$invoice['notes']}</td>
                </tr>";
                    // removed delete button!
                    // <button class='btn btn-danger btn-sm delete-button' data-old_bank_amount='{$invoice['total_bank_deduction']}' data-id='{$invoice['id']}'><i class='far fa-trash-alt'></i></button>
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

































<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_customer', $lang); ?></h2>
    <form method="post" id="add_customer_form">
        <div class="form-container">
            <div class="form-group">
                <label for="customer_name"><?php echo translate('customer_name', $lang); ?></label>
                <input name="customer_name" type="text" class="form-control" id="customer_name" required>
            </div>
            <div class="form-group">
                <label for="exit_clearance"><?php echo translate('exit_clearance', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="exit_clearance" type="number" min="0" step="0.01" class="form-control" id="exit_clearance">
            </div>
            <?php /*
            <div class="form-group">
                <label for="exit_returns"><?php echo translate('exit_returns', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="exit_returns" type="number" min="0" step="0.01" class="form-control" id="exit_returns">
            </div>
            */ ?>
            <div class="form-group">
                <label for="entry_clearance"><?php echo translate('entry_clearance', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="entry_clearance" type="number" min="0" step="0.01" class="form-control" id="entry_clearance">
            </div>
            <?php /*
            <div class="form-group">
                <label for="entry_returns"><?php echo translate('entry_returns', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="entry_returns" type="number" min="0" step="0.01" class="form-control" id="entry_returns">
            </div>
            */ ?>
            <div class="form-group">
                <label for="customer_notes"><?php echo translate('customer_notes', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="customer_notes" type="text" class="form-control" id="customer_notes">
            </div>
        </div>
        <button name="insert-customer" id="insert-customer-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-customer" id="update-customer-btn"
            type="submit" class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./invoices.php" id="cancel-customer-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>

    </form>


    <h2 class="mb-4 mt-5"><?php echo translate('customers', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit_customer = isset($_POST['limit-customer']) ? $_POST['limit-customer'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-customer"
                class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-customer" class="form-select me-2" id="limit-customer" required
                style="width: auto;">
                <option value="25" <?php if ($selected_limit_customer == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit_customer == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit_customer == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit_customer == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit_customer == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit_customer == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-customer-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="customers_table"
            class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('customer_name', $lang); ?></th>
                    <th><?php echo translate('exit_clearance', $lang); ?></th>
                    <?php /*<th><?php echo translate('exit_returns', $lang); ?></th>*/ ?>
                    <th><?php echo translate('entry_clearance', $lang); ?></th>
                    <?php /*<th><?php echo translate('entry_returns', $lang); ?></th>*/ ?>
                    <th><?php echo translate('notes', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-customer'])) {
                    $sql_limit_customers = mysqli_real_escape_string($connection, $_POST['limit-customer']);
                } else {
                    $sql_limit_customers = $sql_defualt_limit;
                }
                $invoices_fees_types = getCustomers($sql_limit_customers);
                foreach ($invoices_fees_types as $customer) {
                    echo "<tr>
                            <td>{$customer['id']}</td>
                            <td>
                            " . (
                        $user_role != 'user' ? "
                                        <button class='btn btn-warning btn-sm edit-button' data-id='{$customer['id']}'><i class='far fa-edit'></i></button>
                                        <button class='btn btn-danger btn-sm delete-button' data-id='{$customer['id']}'><i class='far fa-trash-alt'></i></button>
                                    " : ''
                    )
                        . "
                            </td>
                            <td>{$customer['customer_name']}</td>
                            <td>{$customer['exit_clearance']}</td>
                            <td>{$customer['entry_clearance']}</td>
                            <td>{$customer['customer_notes']}</td>
                            </tr>";
                        }
                        // <td>{$customer['entry_returns']}</td>
                        // <td>{$customer['exit_returns']}</td>
                ?>
            </tbody>
        </table>
    </div>
</div>




























<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_types_of_invoice_fees', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="description"><?php echo translate('description', $lang); ?></label>
                <input name="description" type="text" class="form-control" id="description" required>
            </div>
            <div class="form-group">
                <label for="amount"><?php echo translate('amount', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="amount" type="number" min="0" step="0.01" class="form-control" id="amount">
            </div>
            <div class="form-group">
                <label for="bank_deduction"><?php echo translate('deducted_from_facilities', $lang); ?>
                </label>
                <select name="bank_deduction" class="form-select" id="bank_deduction" required>
                    <option value="" selected><?php echo translate('choose', $lang); ?></option>
                    <option value="1"><?php echo translate('yes', $lang); ?></option>
                    <option value="0"><?php echo translate('no', $lang); ?></option>
                </select>
            </div>
        </div>
        <button name="insert-type-of-invoice-fee" id="insert-type-of-invoice-fee-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-type-of-invoice-fee" id="update-type-of-invoice-fee-btn"
            type="submit" class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./invoices.php" id="cancel-type-of-invoice-fee-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>

    </form>


    <h2 class="mb-4 mt-5"><?php echo translate('fees_types', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit_invoice_fee_type = isset($_POST['limit-invoice-fee-type']) ? $_POST['limit-invoice-fee-type'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-invoice-fee-type"
                class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-invoice-fee-type" class="form-select me-2" id="limit-invoice-fee-type" required
                style="width: auto;">
                <option value="25" <?php if ($selected_limit_invoice_fee_type == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit_invoice_fee_type == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit_invoice_fee_type == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit_invoice_fee_type == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit_invoice_fee_type == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit_invoice_fee_type == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-invoice-fee-type-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="invoice_fees_types_table"
            class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('description', $lang); ?></th>
                    <th><?php echo translate('amount', $lang); ?></th>
                    <th><?php echo translate('deducted_from_facilities', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-invoice-fee-type'])) {
                    $sql_limit_invoices_fee_type = mysqli_real_escape_string($connection, $_POST['limit-invoice-fee-type']);
                } else {
                    $sql_limit_invoices_fee_type = $sql_defualt_limit;
                }
                $invoices_fees_types = getInvoiceFeesTypes($sql_limit_invoices_fee_type);
                foreach ($invoices_fees_types as $invoice_fee_type) {
                    $isT = $invoice_fee_type['bank_deduction'] == 1 ? translate('yes', $lang) : translate('no', $lang);
                    echo "<tr>
                            <td>{$invoice_fee_type['id']}</td>
                            <td>
                            " . (
                        $user_role != 'user' ? "
                                        <button class='btn btn-warning btn-sm edit-button' data-id='{$invoice_fee_type['id']}'><i class='far fa-edit'></i></button>
                                        <button class='btn btn-danger btn-sm delete-button' data-id='{$invoice_fee_type['id']}'><i class='far fa-trash-alt'></i></button>
                                    " : ''
                    )
                        . "
                            </td>
                            <td>{$invoice_fee_type['description']}</td>
                            <td>{$invoice_fee_type['amount']}</td>
                            <td>{$isT}</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>



















<?php
include_once('./footer.php');
?>