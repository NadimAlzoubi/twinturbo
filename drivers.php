<?php
include_once('./header.php');
?>

<head>
    <title><?php echo translate('drivers', $lang) . ' - ' . $translated_user_role; ?></title>
</head>
<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_driver', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="driver_name"><?php echo translate('driver_name', $lang); ?></label>
                <input name="driver_name" type="text" class="form-control" id="driver_name" required>
            </div>
            <div class="form-group">
                <label for="vehicle_number"><?php echo translate('vehicle_number', $lang); ?></label>
                <input name="vehicle_number" type="text" class="form-control" id="vehicle_number" required>
            </div>
            <div class="form-group">
                <label for="phone_number"><?php echo translate('phone_number', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="phone_number" type="text" class="form-control" id="phone_number">
            </div>
            <div class="form-group">
                <label for="notes"><?php echo translate('notes', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input name="driver_notes" type="text" class="form-control" id="notes">
            </div>
        </div>
        <button name="insert-driver" id="insert-driver-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-driver" id="update-driver-btn" type="submit"
            class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./drivers.php" id="cancel-driver-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>
    </form>


    <h2 class="mb-4 mt-5"><?php echo translate('drivers', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit = isset($_POST['limit']) ? $_POST['limit'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit" class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit" class="form-select me-2" id="limit" required style="width: auto;">
                <option value="25" <?php if ($selected_limit == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="drivers_table" class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('driver_name', $lang); ?></th>
                    <th><?php echo translate('vehicle_number', $lang); ?></th>
                    <th><?php echo translate('phone_number', $lang); ?></th>
                    <th><?php echo translate('notes', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit'])) {
                    $sql_limit_drivers = mysqli_real_escape_string($connection, $_POST['limit']);
                } else {
                    $sql_limit_drivers = $sql_defualt_limit;
                }
                $drivers = getAllDrivers($sql_limit_drivers);
                foreach ($drivers as $driver) {
                    $modified = false;
                    if (strtotime($driver['created_at']) === strtotime($driver['updated_at'])) {
                        $infobtn = 'info';
                    } else {
                        $infobtn = 'danger';
                        $modified = true;
                    }
                    $created_at = date('Y-m-d H:i:s A', strtotime($driver['created_at']));
                    $created_by = $driver['created_by'];
                    $updated_at = date('Y-m-d H:i:s A', strtotime($driver['updated_at']));
                    $updated_by = $driver['updated_by'];
                    $extra_info_title = translate('created_at', $lang) . ": {$created_at}\n" .
                        translate('created_by', $lang) . ": {$created_by}\n";
                    if ($modified) {
                        $extra_info_title .= translate('updated_at', $lang) . ": {$updated_at}\n" .
                            translate('updated_by', $lang) . ": {$updated_by}";
                    }
                    $extra_info_title = trim($extra_info_title);
                    echo "<tr>
                            <td>{$driver['id']}</td>
                            <td>
                                <button class='btn btn-{$infobtn} btn-sm' title='" . htmlspecialchars($extra_info_title) . "'><i class='fas fa-info'></i></button>
                                " . (
                        $user_role != 'user' ? "
                                            <button class='btn btn-warning btn-sm edit-button' data-id='{$driver['id']}'><i class='far fa-edit'></i></button>
                                            <button class='btn btn-danger btn-sm delete-button' data-id='{$driver['id']}'><i class='far fa-trash-alt'></i></button>
                                        " : ''
                    )
                        . "
                            </td>
                            <td>{$driver['driver_name']}</td>
                            <td>{$driver['vehicle_number']}</td>
                            <td>{$driver['phone']}</td>
                            <td>{$driver['notes']}</td>
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