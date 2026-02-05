<?php
include_once('./header.php');
?>

<head>
    <title><?php echo translate('services', $lang) . ' - ' . $translated_user_role; ?></title>
</head>

<div class="container mt-1">
    <h2 class="mb-4"><?php echo translate('service_request', $lang); ?></h2>
    <form method="post" id="service-request-form">
        <div class="form-container">
            <div class="form-group">
                <label for="service_type"><?php echo translate('service_type', $lang); ?></label>
                <select class="form-select" id="service_type" name="service_type" required>
                    <?php
                        $services_fees_types = getServiceFeesTypes();
                        foreach ($services_fees_types as $service_fee_type) {
                            echo "<option value='{$service_fee_type['id']}'>{$service_fee_type['fee_name']}</option>";
                        }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="driver_id"><?php echo translate('driver_id', $lang); ?></label>
                <input type="text" class="form-control" id="driver_id" name="driver_id" required placeholder="<?php echo translate('type_to_search', $lang); ?>...">
                <input type="hidden" class="form-control" id="hidden_driver_id" name="hidden_driver_id">
                <input type="hidden" class="form-control" id="driver_uae_id" name="driver_uae_id">
                <input type="hidden" class="form-control" id="driver_passport_number" name="driver_passport_number">
            </div>

            <div class="form-group">
                <label for="user_id"><?php echo translate('user_id', $lang); ?></label>
                <input type="text" class="form-control" id="user_id" name="user_id" required placeholder="<?php echo translate('type_to_search', $lang); ?>...">
                <input type="hidden" class="form-control" id="hidden_user_id" name="hidden_user_id">
            </div>

            <div class="form-group">
                <label for="shipper_id"><?php echo translate('shipper_id', $lang); ?></label>
                <input type="text" class="form-control" id="shipper_id" name="shipper_id" required placeholder="<?php echo translate('type_to_search', $lang); ?>...">
                <input type="hidden" class="form-control" id="hidden_shipper_id" name="hidden_shipper_id">
            </div>

            <div class="form-group">
                <label for="notes"><?php echo translate('notes', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label></label>
                <input type="text" class="form-control" id="notes" name="notes">
            </div>
        </div>





        <button name="insert-service-request" id="insert-service-request-btn" type="submit"
            class="btn btn-primary"><?php echo translate('create', $lang); ?></button>
        <button style="display: none;" name="update-service-request" id="update-service-request-btn" type="submit"
            class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./service-request.php" id="cancel-service-request-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>

    </form>





    <h2 class="mb-4 mt-5"><?php echo translate('service_requests', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit_service_requests = isset($_POST['limit-service-requests']) ? $_POST['limit-service-requests'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-service-requests"
                class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-service-requests" class="form-select me-2" id="limit-service-requests" required
                style="width: auto;">
                <option value="25" <?php if ($selected_limit_service_requests == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit_service_requests == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit_service_requests == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit_service_requests == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit_service_requests == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit_service_requests == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-service-requests-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="service_requests_table"
            class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('request_date', $lang); ?></th>
                    <th><?php echo translate('status', $lang); ?></th>
                    <th><?php echo translate('service_type_name', $lang); ?></th>
                    <th><?php echo translate('driver_name', $lang); ?></th>
                    <th><?php echo translate('shipper_name', $lang); ?></th>
                    <th><?php echo translate('user_name', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-service-requests'])) {
                    $sql_limit_services_requests = mysqli_real_escape_string($connection, $_POST['limit-service-requests']);
                } else {
                    $sql_limit_services_requests = $sql_defualt_limit;
                }
                $service_requests = getAllServiceRequests($sql_limit_services_requests);
                $serial = 1;
                foreach ($service_requests as $service_request) {
                    $service_request['request_date'] = date('d-m-Y H:i', strtotime($service_request['request_date']));

                    echo "<tr>
                            <td>{$service_request['id']}</td>
                            <td>
                            " . (
                        $user_role != 'user' ? "
                                        <button class='btn btn-warning btn-sm edit-button' data-id='{$service_request['id']}'><i class='far fa-edit'></i></button>
                                        <button class='btn btn-danger btn-sm delete-button' data-id='{$service_request['id']}'><i class='far fa-trash-alt'></i></button>
                                    " : ''
                    )
                        . "
                            </td>
                            <td>{$service_request['request_date']}</td>
                            <td>{$service_request['status']}</td>
                            <td>{$service_request['service_type_name']}</td>
                            <td>{$service_request['driver_name']} | {$service_request['driver_vehicle_number']}</td>
                            <td>{$service_request['shipper_name']} | {$service_request['shipper_office_name']}</td>
                            <td>{$service_request['user_name']}</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>




























    <h2 class="mb-4 mt-5"><?php echo translate('services', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit = isset($_POST['limit-service']) ? $_POST['limit-service'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-service" class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-service" class="form-select me-2" id="limit-service" required style="width: auto;">
                <option value="25" <?php if ($selected_limit == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-service-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="services_table" class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('service_date', $lang); ?></th>
                    <th><?php echo translate('payment_status', $lang); ?></th>
                    <th><?php echo translate('driver_name', $lang); ?></th>
                    <th><?php echo translate('phone_number', $lang); ?></th>
                    <th><?php echo translate('fees_details', $lang); ?></th>
                    <th><?php echo translate('number_of_vehicles', $lang); ?></th>
                    <th><?php echo translate('total_fees', $lang); ?></th>
                    <th><?php echo translate('bank_deduction', $lang); ?></th>
                    <th><?php echo translate('notes', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php


                if (isset($_POST['limit-service'])) {
                    $sql_limit_services = mysqli_real_escape_string($connection, $_POST['limit-service']);
                } else {
                    $sql_limit_services = $sql_defualt_limit;
                }
                $services = getAllServices($sql_limit_services);
                $serial = 1;
                foreach ($services as $service) {
                    $print_text = "rid=" . $service['id'];
                    $print_encrypted_text = encrypt($print_text, $s3key);

                    $modified = false;
                    if (strtotime($service['created_at']) === strtotime($service['updated_at'])) {
                        $infobtn = 'info';
                    } else {
                        $infobtn = 'danger';
                        $modified = true;
                    }
                    $created_at = date('Y-m-d H:i:s A', strtotime($service['created_at']));
                    $created_by = $service['created_by'];
                    $updated_at = date('Y-m-d H:i:s A', strtotime($service['updated_at']));
                    $updated_by = $service['updated_by'];
                    $extra_info_title = translate('created_at', $lang) . ": {$created_at}\n" .
                        translate('created_by', $lang) . ": {$created_by}\n";
                    if ($modified) {
                        $extra_info_title .= translate('updated_at', $lang) . ": {$updated_at}\n" .
                            translate('updated_by', $lang) . ": {$updated_by}";
                    }
                    $extra_info_title = trim($extra_info_title);

                    // تنسيق تفاصيل الرسوم وحساب المجموع
                    $fee_details = '';
                    $bank_deduction_details = '';

                    $total_fee_amount = 0.00; // المجموع الابتدائي
                    $total_bank_deduction_amount = 0.00; // المجموع الابتدائي

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
                            $fee_details .= "{$fee_type_name}: {$amount} - {$description} | (<font color='red'>-{$bank_amount}</font>)<br>";
                        } elseif ($description) {
                            $fee_details .= "{$fee_type_name}: {$amount} - {$description}<br>";
                        } elseif ($bank_amount) {
                            $fee_details .= "{$fee_type_name}: {$amount} | (<font color='red'>-{$bank_amount}</font>)<br>";
                        } elseif ($fee_type_name) {
                            $fee_details .= "{$fee_type_name}: {$amount}<br>";
                        } else {
                            $fee_details .= "";
                        }
                    }

                    $payment_status = translate($service['payment_status'], $lang);
                    $color = ['cash' => 'success', 'credit' => 'warning', 'transfer' => 'info'];

                    echo "<tr>
                    <td>{$service['id']}</td>
                    <td>
                        <button class='btn btn-{$infobtn} btn-sm' title='" . htmlspecialchars($extra_info_title) . "'><i class='fas fa-info'></i></button>
                        " . (
                        $user_role != 'user' ? "
                                    <button class='btn btn-warning btn-sm edit-button' data-old_bank_amount='{$total_bank_deduction_amount}' data-id='{$service['id']}'><i class='far fa-edit'></i></button>
                                    <button class='btn btn-danger btn-sm delete-button' data-old_bank_amount='{$total_bank_deduction_amount}' data-id='{$service['id']}'><i class='far fa-trash-alt'></i></button>
                                " : ''
                    )
                        . "
                        <a href='./receipt.php?{$print_encrypted_text}' target='_blank' class='btn btn-success btn-sm' title='Print'><i class='fas fa-print'></i></a>
                    </td>
                    <td>{$service['service_date']}</td>
                    <td class='bg-{$color[$service['payment_status']]}'>{$payment_status}</td>
                    <td>{$service['driver_name']} | {$service['vehicle_number']}</td>
                    <td>{$service['phone_number']}</td>
                    <td>{$fee_details}</td>
                    <td>{$service['nov']}</td>
                    <td>" . number_format($total_fee_amount, 2) . "</td>
                    <td>" . number_format($total_bank_deduction_amount, 2) . "</td>
                    <td>{$service['notes']}</td>
                </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>































<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_types_of_service_fees', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="description"><?php echo translate('description', $lang); ?></label>
                <input name="description" type="text" class="form-control" id="description" required>
            </div>
            <div class="form-group">
                <label for="amount"><?php echo translate('amount', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="amount" type="number" min="0" class="form-control" id="amount">
            </div>
            <div class="form-group">
                <label for="bank_deduction"><?php echo translate('bank_deduction', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="bank_deduction" type="number" min="0" class="form-control" id="bank_deduction">
            </div>
        </div>
        <button name="insert-type-of-service-fee" id="insert-type-of-service-fee-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-type-of-service-fee" id="update-type-of-service-fee-btn"
            type="submit" class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./services.php" id="cancel-type-of-service-fee-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>

    </form>


    <h2 class="mb-4 mt-5"><?php echo translate('fees_types', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit_service_fee_type = isset($_POST['limit-service-fee-type']) ? $_POST['limit-service-fee-type'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-service-fee-type"
                class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-service-fee-type" class="form-select me-2" id="limit-service-fee-type" required
                style="width: auto;">
                <option value="25" <?php if ($selected_limit_service_fee_type == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit_service_fee_type == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit_service_fee_type == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit_service_fee_type == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit_service_fee_type == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit_service_fee_type == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-service-fee-type-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="service_fees_types_table"
            class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('description', $lang); ?></th>
                    <th><?php echo translate('amount', $lang); ?></th>
                    <th><?php echo translate('bank_deduction', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-service-fee-type'])) {
                    $sql_limit_services_fee_type = mysqli_real_escape_string($connection, $_POST['limit-service-fee-type']);
                } else {
                    $sql_limit_services_fee_type = $sql_defualt_limit;
                }
                $services_fees_types = getServiceFeesTypes($sql_limit_services_fee_type);
                $serial = 1;
                foreach ($services_fees_types as $service_fee_type) {
                    echo "<tr>
                            <td>{$service_fee_type['id']}</td>
                            <td>
                            " . (
                        $user_role != 'user' ? "
                                        <button class='btn btn-warning btn-sm edit-button' data-id='{$service_fee_type['id']}'><i class='far fa-edit'></i></button>
                                        <button class='btn btn-danger btn-sm delete-button' data-id='{$service_fee_type['id']}'><i class='far fa-trash-alt'></i></button>
                                    " : ''
                    )
                        . "
                            </td>
                            <td>{$service_fee_type['fee_name']}</td>
                            <td>{$service_fee_type['fee_amount']}</td>
                            <td>{$service_fee_type['bank_deduction']}</td>
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