<?php
include_once('./header.php');
?>

<head>
    <title><?php echo translate('trips', $lang) . ' - ' . $translated_user_role; ?></title>
</head>

<div class="container">
    <h2 class="mb-4"><?php echo translate('add_trip', $lang); ?></h2>
    <form method="post" id="tripForm">
        <div class="form-container">
            <div class="form-group">
                <label for="trip_date"><?php echo translate('trip_date', $lang); ?></label>
                <input type="date" class="form-control" id="trip_date" name="trip_date" required>
            </div>
            <div class="form-group">
                <label for="driver_id"><?php echo translate('driver_id', $lang); ?></label>
                <input type="text" class="form-control" id="driver_id" name="driver_id" required placeholder="<?php echo translate('type_to_search', $lang); ?>...">
                <input type="hidden" class="form-control" id="hidden_driver_id" name="hidden_driver_id">
            </div>
            <div class="form-group">
                <label for="trip_rent"><?php echo translate('trip_rent', $lang); ?></label>
                <input type="number" min="0" step="0.01" class="form-control" id="trip_rent" name="trip_rent" required>
            </div>
            <div class="form-group">
                <label for="destination"><?php echo translate('destination', $lang); ?></label>
                <input type="text" class="form-control" id="destination" name="destination" required>
            </div>
            <div class="form-group">
                <label for="driver_fee"><?php echo translate('driver_fee', $lang); ?> <?php echo translate('optional', $lang); ?></label>
                <input type="number" min="0" step="0.01" class="form-control" id="driver_fee" name="driver_fee">
            </div>
            <div class="form-group">
                <label for="extra_income"><?php echo translate('extra_income', $lang); ?> <?php echo translate('optional', $lang); ?></label>
                <input type="number" min="0" step="0.01" class="form-control" id="extra_income" name="extra_income">
            </div>
            <div class="form-group">
                <label for="extra_income_des"><?php echo translate('extra_income_description', $lang); ?> <?php echo translate('optional', $lang); ?></label>
                <input type="text" class="form-control" id="extra_income_des" name="extra_income_des">
            </div>
            <div class="form-group">
                <label for="notes"><?php echo translate('notes', $lang); ?> <?php echo translate('optional', $lang); ?></label>
                <input type="text" class="form-control" id="notes" name="notes">
            </div>
        </div>

        <div class="text-center"><?php echo translate('trip_expenses', $lang); ?></div>
        <hr class="m-3">



        <div id="feesContainer">

        </div>



        <div class="d-flex flex-column flex-md-row align-items-center justify-content-start gap-4">
            <button type="button" id="addFee" class="btn btn-primary flex-grow-1 w-100 w-md-auto">
                <?php echo translate('add_a_new_expense', $lang); ?>
            </button>
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-around gap-3 w-100 w-md-auto">
                <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                    <label for="total_sum" class="mb-1 mb-md-0"><?php echo translate('total', $lang); ?></label>
                    <input readonly id="total_sum" type="number" class="form-control w-100 w-md-auto text-center">
                </div>
                <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                    <label for="remaining" class="mb-1 mb-md-0"><?php echo translate('remaining', $lang); ?></label>
                    <input readonly type="number" min="0" step="0.01" class="form-control w-100 w-md-auto text-center" id="remaining" name="remaining" required>
                </div>
            </div>
        </div>

        <hr class="m-3">

        <button name="insert-trip" id="insert-trip-btn" type="submit" class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-trip" id="update-trip-btn" type="submit" class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./trips.php" id="cancel-trip-btn" class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>

    </form>

    <h2 class="mb-4 mt-5"><?php echo translate('trips', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit = isset($_POST['limit-trip']) ? $_POST['limit-trip'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <input type="hidden" name="form_type" value="limit_trip_form">
            <label for="limit-trip" class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-trip" class="form-select me-2" id="limit-trip" required style="width: auto;">
                <option value="25" <?php if ($selected_limit == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-trip-btn" type="submit" class="btn btn-info btn-md">
                <?php echo translate('query', $lang); ?>
            </button>
        </form>
    </div>

    <div class="table-section">
        <table id="trips_table" class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('driver_name', $lang); ?></th>
                    <th><?php echo translate('trip_date', $lang); ?></th>
                    <th><?php echo translate('destination', $lang); ?></th>
                    <th><?php echo translate('trip_rent', $lang); ?></th>
                    <th><?php echo translate('extra_income', $lang); ?></th>
                    <th><?php echo translate('driver_fee', $lang); ?></th>
                    <th><?php echo translate('expenses_details', $lang); ?></th>
                    <th><?php echo translate('total_expenses', $lang); ?></th>
                    <th><?php echo translate('remaining', $lang); ?></th>
                    <th><?php echo translate('notes', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-trip'])) {
                    $sql_limit_trip = mysqli_real_escape_string($connection, $_POST['limit-trip']);
                } else {
                    $sql_limit_trip = $sql_defualt_limit;
                }
                $trips = getAllTrips($sql_limit_trip);
                $serial = 1;
                foreach ($trips as $trip) {
                    $modified = false;
                    if (strtotime($trip['created_at']) === strtotime($trip['updated_at'])) {
                        $infobtn = 'info';
                    } else {
                        $infobtn = 'danger';
                        $modified = true;
                    }
                    $created_at = date('Y-m-d H:i:s A', strtotime($trip['created_at']));
                    $created_by = $trip['created_by'];
                    $updated_at = date('Y-m-d H:i:s A', strtotime($trip['updated_at']));
                    $updated_by = $trip['updated_by'];
                    $extra_info_title = translate('created_at', $lang) . ": {$created_at}\n" .
                        translate('created_by', $lang) . ": {$created_by}\n";
                    if ($modified) {
                        $extra_info_title .= translate('updated_at', $lang) . ": {$updated_at}\n" .
                            translate('updated_by', $lang) . ": {$updated_by}";
                    }
                    $extra_info_title = trim($extra_info_title);
                    // تنسيق تفاصيل الرسوم وحساب المجموع
                    $fee_details = '';
                    $total_fee_amount = 0.00; // المجموع الابتدائي
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
                        if ($trip['extra_income']) {
                            $extra_income = $trip['extra_income'];
                        }
                        if ($trip['extra_income_des']) {
                            $extra_income = "{$trip['extra_income_des']}: {$extra_income}<br>";
                        }
                    }
                    echo "<tr>
                <td>{$trip['id']}</td>
                <td>
                    <button class='btn btn-{$infobtn} btn-sm' title='" . htmlspecialchars($extra_info_title) . "'><i class='fas fa-info'></i></button>
                    " . (
                        $user_role != 'user' ? "
                                <button class='btn btn-warning btn-sm edit-button' data-id='{$trip['id']}'><i class='far fa-edit'></i></button>
                                <button class='btn btn-danger btn-sm delete-button' data-id='{$trip['id']}'><i class='far fa-trash-alt'></i></button>
                            " : ''
                    )
                        . "
                </td>
                <td>{$trip['driver_name']} | {$trip['vehicle_number']}</td>
                <td>{$trip['trip_date']}</td>
                <td>{$trip['destination']}</td>
                <td>{$trip['trip_rent']}</td>
                <td>{$extra_income}</td>
                <td>{$trip['driver_fee']}</td>
                <td>{$fee_details}</td>
                <td>" . number_format($total_fee_amount, 2) . "</td>
                <td>{$trip['remaining']}</td>
                <td>{$trip['notes']}</td>
            </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>










<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_types_of_trip_expenses', $lang); ?></h2>
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
        </div>
        <button name="insert-type-of-trip-expenses" id="insert-type-of-trip-expenses-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-type-of-trip-expenses" id="update-type-of-trip-expenses-btn"
            type="submit" class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./trips.php" id="cancel-type-of-trip-expenses-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>
    </form>

    <h2 class="mb-4 mt-5"><?php echo translate('expenses_types', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        // تحقق مما إذا تم إرسال النموذج واحفظ القيمة المحددة في متغير
        $selected_limit_fee_type = isset($_POST['limit-fee-type']) ? $_POST['limit-fee-type'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <input type="hidden" name="form_type" value="limit_fee_type_form">
            <label for="limit-fee-type" class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-fee-type" class="form-select me-2" id="limit-fee-type" required style="width: auto;">
                <option value="25" <?php if ($selected_limit_fee_type == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit_fee_type == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit_fee_type == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit_fee_type == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit_fee_type == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit_fee_type == '') echo 'selected'; ?>><?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-fee-type-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="expenses_types_table" class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('description', $lang); ?></th>
                    <th><?php echo translate('amount', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-fee-type'])) {
                    $sql_limit_fee_type = mysqli_real_escape_string($connection, $_POST['limit-fee-type']);
                } else {
                    $sql_limit_fee_type = $sql_defualt_limit;
                }
                $trips_fees_types = getTripFeesTypes($sql_limit_fee_type);
                $serial = 1;
                foreach ($trips_fees_types as $trip_fee_type) {
                    echo "<tr>
                            <td>{$trip_fee_type['id']}</td>
                            <td>
                            " . (
                        $user_role != 'user' ? "
                                        <button class='btn btn-warning btn-sm edit-button' data-id='{$trip_fee_type['id']}'><i class='far fa-edit'></i></button>
                                        <button class='btn btn-danger btn-sm delete-button' data-id='{$trip_fee_type['id']}'><i class='far fa-trash-alt'></i></button>
                                    " : ''
                    )
                        . "
                            </td>
                            <td>{$trip_fee_type['fee_name']}</td>
                            <td>{$trip_fee_type['fee_amount']}</td>
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