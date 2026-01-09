<?php
include_once('./header.php');
?>

<head>
    <title><?php echo translate('saudi_bills', $lang) . ' - ' . $translated_user_role; ?></title>
</head>



<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_saudi_bill', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="bill_date"><?php echo translate('bill_date', $lang); ?></label>
                <input type="date" class="form-control" id="bill_date" name="bill_date" required>
            </div>
            <div class="form-group">
                <label for="sau_office_id"><?php echo translate('sau_office_id', $lang); ?></label>
                <input type="text" min="0" class="form-control" id="sau_office_id" name="sau_office_id" required
                    placeholder="<?php echo translate('type_to_search', $lang); ?>...">
                <input type="hidden" class="form-control" id="hidden_sau_office_id" name="hidden_sau_office_id">
            </div>
            <div class="form-group">
                <label for="driver_name"><?php echo translate('driver_name', $lang); ?></label>
                <input type="text" class="form-control" id="driver_name" name="driver_name" required>
            </div>
            <div class="form-group">
                <label for="vehicle_number"><?php echo translate('vehicle_number', $lang); ?></label>
                <input type="text" class="form-control" id="vehicle_number" name="vehicle_number" required>
            </div>
            <div class="form-group">
                <label for="sau_bill_number"><?php echo translate('sau_bill_number', $lang); ?></label>
                <input type="text" class="form-control" id="sau_bill_number" name="sau_bill_number" required>
            </div>
            <div class="form-group">
                <label for="destination"><?php echo translate('destination', $lang); ?></label>
                <input type="text" class="form-control" id="destination" name="destination" required>
            </div>
            <div class="form-group">
                <label for="nob"><?php echo translate('number_of_bills', $lang); ?></label>
                <input type="number" min="1" value="1" class="form-control" id="nob" name="nob" required>
            </div>
            <div class="form-group">
                <label for="nov"><?php echo translate('number_of_vehicles', $lang); ?></label>
                <input type="number" min="1" value="1" class="form-control" id="nov" name="nov" required>
            </div>
            <div class="form-group">
                <label for="price"><?php echo translate('price', $lang); ?></label>
                <input type="number" min="0" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="form-group">
                <label for="notes"><?php echo translate('notes', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input class="form-control" id="notes" name="notes">
            </div>
        </div>
        <button name="insert-sau-bill" id="insert-sau-bill-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-sau-bill" id="update-sau-bill-btn" type="submit"
            class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./sau_bills.php" id="cancel-sau-bill-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>
    </form>









    <h2 class="mb-4 mt-5"><?php echo translate('saudi_bills', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit = isset($_POST['limit-sau-bills']) ? $_POST['limit-sau-bills'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-sau-bills" class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-sau-bills" class="form-select me-2" id="limit-sau-bills" required style="width: auto;">
                <option value="25" <?php if ($selected_limit == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-sau-bills-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="sau_bills_table" class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('bill_date', $lang); ?></th>
                    <th><?php echo translate('office_name', $lang); ?></th>
                    <th><?php echo translate('license_number', $lang); ?></th>
                    <th><?php echo translate('bill_number', $lang); ?></th>
                    <th><?php echo translate('driver_name', $lang); ?></th>
                    <th><?php echo translate('vehicle_number', $lang); ?></th>
                    <th><?php echo translate('number_of_bills', $lang); ?></th>
                    <th><?php echo translate('number_of_vehicles', $lang); ?></th>
                    <th><?php echo translate('destination', $lang); ?></th>
                    <th><?php echo translate('price', $lang); ?></th>
                    <th><?php echo translate('notes', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-sau-bills'])) {
                    $sql_limit_sau_bills = mysqli_real_escape_string($connection, $_POST['limit-sau-bills']);
                } else {
                    $sql_limit_sau_bills = $sql_defualt_limit;
                }
                $sau_bills = getAllSauBills($sql_limit_sau_bills);
                $serial = 1;
                foreach ($sau_bills as $sau_bill) {
                    $print_text = "bid=" . $sau_bill['id'];
                    $print_encrypted_text = encrypt($print_text, $s3key);

                    $modified = false;
                    if (strtotime($sau_bill['created_at']) === strtotime($sau_bill['updated_at'])) {
                        $infobtn = 'info';
                    } else {
                        $infobtn = 'danger';
                        $modified = true;
                    }
                    $created_at = date('Y-m-d H:i:s A', strtotime($sau_bill['created_at']));
                    $created_by = $sau_bill['created_by'];
                    $updated_at = date('Y-m-d H:i:s A', strtotime($sau_bill['updated_at']));
                    $updated_by = $sau_bill['updated_by'];
                    $extra_info_title = translate('created_at', $lang) . ": {$created_at}\n" .
                        translate('created_by', $lang) . ": {$created_by}\n";
                    if ($modified) {
                        $extra_info_title .= translate('updated_at', $lang) . ": {$updated_at}\n" .
                            translate('updated_by', $lang) . ": {$updated_by}";
                    }
                    $extra_info_title = trim($extra_info_title);
                    echo "<tr>
                <td>{$sau_bill['id']}</td>
                <td>
                    <button class='btn btn-{$infobtn} btn-sm' title='" . htmlspecialchars($extra_info_title) . "'><i class='fas fa-info'></i></button>
                    " . (
                        $user_role != 'user' ? "
                                <button class='btn btn-warning btn-sm edit-button' data-id='{$sau_bill['id']}'><i class='far fa-edit'></i></button>
                                <button class='btn btn-danger btn-sm delete-button' data-id='{$sau_bill['id']}'><i class='far fa-trash-alt'></i></button>
                            " : ''
                    )
                        . "
                    <a href='./receipt.php?{$print_encrypted_text}' target='_blank' class='btn btn-success btn-sm' title='Print'><i class='fas fa-print'></i></a>
                </td>
                <td>{$sau_bill['bill_date']}</td>
                <td>{$sau_bill['office_name']}</td>
                <td>{$sau_bill['license_number']}</td>
                <td>{$sau_bill['sau_bill_number']}</td>
                <td>{$sau_bill['driver_name']}</td>
                <td>{$sau_bill['vehicle_number']}</td>
                <td>{$sau_bill['nob']}</td>
                <td>{$sau_bill['nov']}</td>
                <td>{$sau_bill['destination']}</td>
                <td>{$sau_bill['price']}</td>
                <td>{$sau_bill['notes']}</td>
            </tr>";
                }
                ?>
            </tbody>
        </table>

    </div>



















    <h2 class="mb-4 mt-5"><?php echo translate('add_saudi_office', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="office_name"><?php echo translate('office_name', $lang); ?></label>
                <input type="text" class="form-control" id="office_name" name="office_name" required>
            </div>
            <div class="form-group">
                <label for="license_number"><?php echo translate('license_number', $lang); ?></label>
                <input type="text" class="form-control" id="license_number" name="license_number" required>
            </div>
            <div class="form-group">
                <label for="notes"><?php echo translate('notes', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input class="form-control" id="notes" name="notes">
            </div>
        </div>
        <button name="insert-sau-office" id="insert-sau-office-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-sau-office" id="update-sau-office-btn" type="submit"
            class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./sau_bills.php" id="cancel-sau-office-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>
    </form>



    <h2 class="mb-4 mt-5"><?php echo translate('saudi_offices', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit_sau_offices = isset($_POST['limit-sau-offices']) ? $_POST['limit-sau-offices'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-sau-offices" class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-sau-offices" class="form-select me-2" id="limit-sau-offices" required style="width: auto;">
                <option value="25" <?php if ($selected_limit_sau_offices == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit_sau_offices == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit_sau_offices == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit_sau_offices == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit_sau_offices == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit_sau_offices == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-sau-offices-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="sau_offices_table" class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('office_name', $lang); ?></th>
                    <th><?php echo translate('license_number', $lang); ?></th>
                    <th><?php echo translate('notes', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-sau-offices'])) {
                    $sql_limit_sau_offices = mysqli_real_escape_string($connection, $_POST['limit-sau-offices']);
                } else {
                    $sql_limit_sau_offices = $sql_defualt_limit;
                }
                $sau_offices = getAllSauOffices($sql_limit_sau_offices);
                $serial = 1;
                foreach ($sau_offices as $sau_office) {
                    $modified = false;
                    if (strtotime($sau_office['created_at']) === strtotime($sau_office['updated_at'])) {
                        $infobtn = 'info';
                    } else {
                        $infobtn = 'danger';
                        $modified = true;
                    }
                    $created_at = date('Y-m-d H:i:s A', strtotime($sau_office['created_at']));
                    $created_by = $sau_office['created_by'];
                    $updated_at = date('Y-m-d H:i:s A', strtotime($sau_office['updated_at']));
                    $updated_by = $sau_office['updated_by'];
                    $extra_info_title = translate('created_at', $lang) . ": {$created_at}\n" .
                        translate('created_by', $lang) . ": {$created_by}\n";
                    if ($modified) {
                        $extra_info_title .= translate('updated_at', $lang) . ": {$updated_at}\n" .
                            translate('updated_by', $lang) . ": {$updated_by}";
                    }
                    $extra_info_title = trim($extra_info_title);
                    echo "<tr>
                <td>{$sau_office['id']}</td>
                <td>
                    <button class='btn btn-{$infobtn} btn-sm' title='" . htmlspecialchars($extra_info_title) . "'><i class='fas fa-info'></i></button>
                    " . (
                        $user_role != 'user' ? "
                                <button class='btn btn-warning btn-sm edit-button' data-id='{$sau_office['id']}'><i class='far fa-edit'></i></button>
                                <button class='btn btn-danger btn-sm delete-button' data-id='{$sau_office['id']}'><i class='far fa-trash-alt'></i></button>
                            " : ''
                    )
                        . "
                </td>
                <td>{$sau_office['office_name']}</td>
                <td>{$sau_office['license_number']}</td>
                <td>{$sau_office['notes']}</td>
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