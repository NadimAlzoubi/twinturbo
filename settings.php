<?php
include_once('./header.php');
if ($_SESSION["sau_user_role"] != 'admin') {
    echo '<script>window.location.href = "./error403.php";</script>';
}
?>

<head>
    <title><?php echo translate('settings', $lang) . ' - ' . $translated_user_role; ?></title>
</head>

<div class="container mt-5">

    <!-- Form to Add New User -->
    <h2 class="mb-4 mt-5"><?php echo translate('add_user', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="full_name"><?php echo translate('full_name', $lang); ?></label>
                <input name="full_name" type="text" class="form-control" id="full_name" required>
            </div>
            <div class="form-group">
                <label for="username"><?php echo translate('username', $lang); ?></label>
                <input name="username" type="text" class="form-control" id="username" required>
            </div>
            <div class="form-group">
                <label for="password"><?php echo translate('password', $lang); ?></label>
                <input name="password" type="password" class="form-control" id="password" required autocomplete="off">
            </div>
            <div class="form-group">
                <label for="role"><?php echo translate('role', $lang); ?></label>
                <select name="role" class="form-select" id="role" required>
                    <option value="">--<?php echo translate('choose', $lang); ?>--</option>
                    <option value="user"><?php echo translate('user', $lang); ?></option>
                    <option value="supervisor"><?php echo translate('supervisor', $lang); ?></option>
                    <option value="admin"><?php echo translate('admin', $lang); ?></option>
                </select>
            </div>
            <div class="form-group">
                <label for="status"><?php echo translate('status', $lang); ?></label>
                <select name="status" class="form-select" id="status" required>
                    <option value="">--<?php echo translate('choose', $lang); ?>--</option>
                    <option value="1"><?php echo translate('active', $lang); ?></option>
                    <option value="0"><?php echo translate('inactive', $lang); ?></option>
                </select>
            </div>
            <div class="form-group">
                <label for="location"><?php echo translate('location', $lang); ?></label>
                <select name="location" class="form-select" id="location" required>
                    <option value="">--<?php echo translate('choose', $lang); ?>--</option>
                    <option value="uae"><?php echo translate('uae', $lang); ?></option>
                    <option value="sau"><?php echo translate('sau', $lang); ?></option>
                </select>
            </div>
        </div>
        <button name="insert-user" id="insert-user-btn" type="submit" class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-user" id="update-user-btn" type="submit" class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./settings.php" id="cancel-user-btn" class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>
    </form>

    <br>
    <h2 class="mb-4 mt-5"><?php echo translate('users', $lang); ?></h2>
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
            <button name="limit-btn" type="submit" class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>
    <div class="table-section">
        <table id="users_table" class="display cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('username', $lang); ?></th>
                    <th><?php echo translate('full_name', $lang); ?></th>
                    <th><?php echo translate('role', $lang); ?></th>
                    <th><?php echo translate('location', $lang); ?></th>
                    <th><?php echo translate('status', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit'])) {
                    $sql_limit_users = mysqli_real_escape_string($connection, $_POST['limit']);
                } else {
                    $sql_limit_users = $sql_defualt_limit;
                }
                $users = getAllUsers($sql_limit_users);
                $serial = 1;
                foreach ($users as $user) {
                    $modified = false;
                    if (strtotime($user['created_at']) === strtotime($user['updated_at'])) {
                        $infobtn = 'info';
                    } else {
                        $infobtn = 'danger';
                        $modified = true;
                    }
                    $created_at = date('Y-m-d H:i:s A', strtotime($user['created_at']));
                    $updated_at = date('Y-m-d H:i:s A', strtotime($user['updated_at']));
                    $ro = $user['role'] == 'admin' ? translate('administrator', $lang) : ($user['role'] == 'supervisor' ? translate('supervisor', $lang) : translate($user['role'], $lang));
                    $lo = translate($user['location'], $lang);
                    $st = $user['status'] == 1 ? translate('active', $lang) : translate('inactive', $lang);
                    $bg = $user['status'] == 1 ? '#00c800' : 'red';
                    $extra_info_title = translate('created_at', $lang) . ": {$created_at}\n";
                    if ($modified) {
                        $extra_info_title .= translate('updated_at', $lang) . ": {$updated_at}";
                    }

                    $extra_info_title = trim($extra_info_title);
                    echo "<tr>
                            <td>{$user['id']}</td>
                            <td>
                                <button class='btn btn-{$infobtn} btn-sm' title='" . htmlspecialchars($extra_info_title) . "'><i class='fas fa-info'></i></button>
                                " . (
                        $user_role != 'user' ? "
                                            <button class='btn btn-warning btn-sm edit-button' data-id='{$user['id']}'><i class='far fa-edit'></i></button>
                                            <button class='btn btn-danger btn-sm delete-button' data-id='{$user['id']}'><i class='far fa-trash-alt'></i></button>
                                        " : ''
                    )
                        . "
                            </td>
                            <td>{$user['username']}</td>
                            <td>{$user['full_name']}</td>
                            <td>{$ro}</td>
                            <td>{$lo}</td>
                            <td style='background-color: {$bg}'>{$st}</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <br>
    <br>
    <br>


    <!-- Form to Update Account Amount -->
    <h2 class="mb-4"><?php echo translate('update_account_amount', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="account_amount"><?php echo translate('account_amount', $lang); ?></label>
                <input name="account_amount" type="number" min="0" step="0.01" class="form-control account_amount" id="account_amount">
            </div>
        </div>
        <button name="update-bank-account-amount" type="submit" class="btn btn-primary"><?php echo translate('update', $lang); ?></button>
    </form>
    <br>
    <br>
    <!-- Form to Update Facilities Amount -->
    <h2 class="mb-4"><?php echo translate('update_facilities_amount', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="facilities_amount"><?php echo translate('facilities_amount', $lang); ?></label>
                <input name="facilities_amount" type="number" min="0" step="0.01" class="form-control facilities_amount" id="facilities_amount">
            </div>
        </div>
        <button name="update-facilities-account-amount" type="submit" class="btn btn-primary"><?php echo translate('update', $lang); ?></button>
    </form>

</div>
<?php
include_once('./footer.php');
?>