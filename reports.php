<?php
include_once('./header.php');
?>

<head>
    <title><?php echo translate('reports', $lang) . ' - ' . $translated_user_role; ?></title>

    <style>
        .selected-item {
            display: inline-block;
            background-color: #e0e0e0;
            border-radius: 5px;
            padding: 5px;
            margin-right: 5px;
        }

        .selected-item a {
            text-decoration: none;
            color: red;
        }
    </style>
</head>

<div class="container">
    <h2 class="mb-4"><?php echo translate('generate_report', $lang); ?></h2>

    <?php
    if (!isset($_GET['report_type'])) {
        $_GET['report_type'] = 'trips';
    }
    ?>

    <form id="reportForm" method="post" action="./generate_pdfv2.php" target="_blank">
        <div class="row">
            <div class="col-md-12 w-50" style="margin: 0 auto;">
                <div class="form-group">
                    <label for="report_type"><?php echo translate('report_type', $lang); ?></label>
                    <select class="form-select" id="report_type" name="report_type" required>
                        <option value="trips" <?php if ($_GET['report_type'] == 'trips') echo 'selected'; ?>><?php echo translate('trips_report', $lang); ?></option>
                        <option value="invoices" <?php if ($_GET['report_type'] == 'invoices') echo 'selected'; ?>><?php echo translate('invoices_report', $lang); ?></option>
                        <option value="sau_bills" <?php if ($_GET['report_type'] == 'sau_bills') echo 'selected'; ?>><?php echo translate('saudi_bills_report', $lang); ?></option>
                        <option value="services" <?php if ($_GET['report_type'] == 'services') echo 'selected'; ?>><?php echo translate('services_report', $lang); ?></option>
                        <option value="expenses" <?php if ($_GET['report_type'] == 'expenses') echo 'selected'; ?>><?php echo translate('expenses_report', $lang); ?></option>
                        <option value="revenue_expense" <?php if ($_GET['report_type'] == 'revenue_expense') echo 'selected'; ?>><?php echo translate('revenue_expense_report', $lang); ?></option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="start_date"><?php echo translate('start_date', $lang); ?></label>
                    <input type="<?php echo $_GET['report_type'] == 'invoices' ? 'datetime-local' : 'date'; ?>" class="form-control" id="start_date" name="start_date" required>
                </div>

                <div class="form-group">
                    <label for="end_date"><?php echo translate('end_date', $lang); ?></label>
                    <input type="<?php echo $_GET['report_type'] == 'invoices' ? 'datetime-local' : 'date'; ?>" class="form-control" id="end_date" name="end_date" required>
                </div>

                <div class="form-group" id="additional_fields">
                    <!-- حقول إضافية بناءً على نوع التقرير المحدد -->
                    <?php
                    if (isset($_GET['report_type'])) {
                        $reportType = $_GET['report_type'];
                        if ($reportType == 'trips') {
                            echo '<label for="driver_id">' . translate('driver_id', $lang) . '</label>';
                            echo '<input type="text" class="form-control" id="driver_id" name="driver_id" placeholder="' . translate('type_to_search', $lang) . '...">';
                        } elseif ($reportType == 'sau_bills') {
                            echo '<label for="sau_office_id">' . translate('sau_office_id', $lang) . '</label>';
                            echo '<input type="text" class="form-control" id="sau_office_id" name="sau_office_id" placeholder="' . translate('type_to_search', $lang) . '...">';
                            echo '<div class="form-group mt-4">
                                    <label class="form-check-label" style="font-weight: bold;">' . translate('entity_type', $lang) . '</label>
                                    <div style="margin-bottom: 10px; display: flex; gap: 10px;">
                                        <label class="form-check-label" for="entity_type1">' . translate('office', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="entity_type1" name="entity_type" value="office">
                                        <label class="form-check-label" for="entity_type2">' . translate('agent', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="entity_type2" name="entity_type" value="agent">
                                        <label class="form-check-label" for="entity_type3">' . translate('shipper', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="entity_type3" name="entity_type" value="shipper">
                                        <label class="form-check-label" for="entity_type4">' . translate('company', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="entity_type4" name="entity_type" value="company">
                                    </div>
                            </div>';
                            echo '<div class="form-group mt-4">
                                    <label class="form-check-label" style="font-weight: bold;">' . translate('payment_status', $lang) . '</label>
                                    <div style="margin-bottom: 10px; display: flex; gap: 10px;">
                                        <label class="form-check-label" for="payment_status1">' . translate('cash', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="payment_status1" name="payment_status" value="cash">
                                        <label class="form-check-label" for="payment_status2">' . translate('credit', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="payment_status2" name="payment_status" value="credit">
                                        <label class="form-check-label" for="payment_status3">' . translate('transfer', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="payment_status3" name="payment_status" value="transfer">
                                    </div>
                            </div>';
                        } elseif ($reportType == 'services') {
                            echo '<label for="service_fee_type_id">' . translate('service_fee_type_id', $lang) . '</label>';
                            echo '<input type="text" class="form-control trip_fee_type_id" id="service_fee_type_id" name="service_fee_type_id" placeholder="' . translate('type_to_search', $lang) . '...">';

                            echo '<div class="form-group mt-4">
                                    <label class="form-check-label" style="font-weight: bold;">' . translate('payment_status', $lang) . '</label>
                                    <div style="margin-bottom: 10px; display: flex; gap: 10px;">
                                        <label class="form-check-label" for="payment_status1">' . translate('cash', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="payment_status1" name="payment_status" value="cash">
                                        <label class="form-check-label" for="payment_status2">' . translate('credit', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="payment_status2" name="payment_status" value="credit">
                                        <label class="form-check-label" for="payment_status3">' . translate('transfer', $lang) . '</label>
                                        <input type="radio" class="form-check-input" id="payment_status3" name="payment_status" value="transfer">
                                    </div>
                            </div>';
                        } elseif ($reportType == 'expenses') {
                            echo '<label for="expense_type_id">' . translate('expense_type_id', $lang) . '</label>';
                            echo '<input type="text" class="form-control" id="expense_type_id" name="expense_type_id" placeholder="' . translate('type_to_search', $lang) . '...">';
                        } elseif ($reportType == 'invoices') {
                            echo '<label for="customer_id">' . translate('customer_id', $lang) . '</label>';
                            echo '<input type="text" class="form-control" id="customer_id" name="customer_id" placeholder="' . translate('type_to_search', $lang) . '...">';
                            echo '<br>';
                            echo '<label for=""><b>' . translate('status', $lang) . ': </b> </label>';
                            echo '<div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="paid" name="status[]" id="status_paid" checked>
                                    <label class="form-check-label" for="status_paid">
                                        ' . translate('paid', $lang) . '
                                    </label>
                                </div>';
                            echo '<div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="pending" name="status[]" id="status_pending" checked>
                                    <label class="form-check-label" for="status_pending">
                                        ' . translate('pending', $lang) . '
                                    </label>
                                </div>';
                            echo '<div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" value="cancelled" name="status[]" id="status_cancelled">
                                    <label class="form-check-label" for="status_cancelled">
                                        ' . translate('cancelled', $lang) . '
                                    </label>
                                </div>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <button style="width: fit-content; margin: 0 auto;" name="generate_report" id="generate_report_btn" type="submit" class="btn btn-primary"><?php echo translate('generate', $lang); ?></button>
            </div>
        </div>

    </form>
</div>

<!-- إضافة JavaScript لتحديث الحقول الإضافية بناءً على نوع التقرير -->
<script>
    document.getElementById('report_type').addEventListener('change', function() {
        var reportType = this.value;
        window.location.href = '?report_type=' + reportType;
    });


    document.getElementById('reportForm').addEventListener('submit', function(event) {
        event.preventDefault();
        var dir = 'rtl';
        if (localStorage.getItem('selectedLang') === 'en') {
            dir = 'ltr';
        }
        var reportType = document.getElementById('report_type').value;
        var startDate = document.getElementById('start_date').value;
        var endDate = document.getElementById('end_date').value;
        // استخدام القيم من GET إذا كانت موجودة
        startDate = startDate || new URLSearchParams(window.location.search).get('start_date');
        endDate = endDate || new URLSearchParams(window.location.search).get('end_date');


        var driverIdElement = document.getElementById('driver_id');
        var driverId = driverIdElement ? driverIdElement.value : 0;

        var officeIdElement = document.getElementById('sau_office_id');
        var officeId = officeIdElement ? officeIdElement.value : 0;

        var serviceIdElement = document.getElementById('service_fee_type_id');
        var serviceId = serviceIdElement ? serviceIdElement.value : 0;

        var expenseIdElement = document.getElementById('expense_type_id');
        var expenseId = expenseIdElement ? expenseIdElement.value : 0;

        var customerIdElement = document.getElementById('customer_id');
        var customerId = customerIdElement ? customerIdElement.value : 0;

        var statusPaidElement = document.getElementById('status_paid');
        var statusPendingElement = document.getElementById('status_pending');
        var statusCancelledElement = document.getElementById('status_cancelled');
        var statusPaid = statusPaidElement ? (statusPaidElement.checked ? statusPaidElement.value : 0) : null;
        var statusPending = statusPendingElement ? (statusPendingElement.checked ? statusPendingElement.value : 0) : null;
        var statusCancelled = statusCancelledElement ? (statusCancelledElement.checked ? statusCancelledElement.value : 0) : null;

        var actionUrl = './generate_pdfv2.php?dir=' + encodeURIComponent(dir) +
            '&reportType=' + encodeURIComponent(reportType) +
            '&start_date=' + encodeURIComponent(startDate) +
            '&end_date=' + encodeURIComponent(endDate);

        if (driverId) {
            actionUrl += '&driverId=' + encodeURIComponent(driverId);
        }
        if (officeId) {
            actionUrl += '&officeId=' + encodeURIComponent(officeId);
        }
        if (expenseId) {
            actionUrl += '&expenseId=' + encodeURIComponent(expenseId);
        }
        if (serviceId) {
            actionUrl += '&serviceId=' + encodeURIComponent(serviceId);
        }
        if (customerId) {
            actionUrl += '&customerId=' + encodeURIComponent(customerId);
        }
        if (statusPaid) {
            actionUrl += '&statusPaid=' + encodeURIComponent(statusPaid);
        }
        if (statusPending) {
            actionUrl += '&statusPending=' + encodeURIComponent(statusPending);
        }
        if (statusCancelled) {
            actionUrl += '&statusCancelled=' + encodeURIComponent(statusCancelled);
        }

        this.action = actionUrl;
        this.submit();
    });
</script>


<?php
include_once('./footer.php');
?>