<?php
include_once('./header.php');
?>

<head>
    <title><?php echo translate('expenses', $lang) . ' - ' . $translated_user_role; ?></title>
</head>









<div class="container mt-5">
    <h2 class="mb-4"><?php echo translate('add_expense', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="expense_date"><?php echo translate('date', $lang); ?></label>
                <input type="date" class="form-control" id="expense_date" name="expense_date" required>
            </div>
            <div class="form-group">
                <label for="expense_type_id"><?php echo translate('expense_type', $lang); ?></label>
                <input type="text" class="form-control" id="expense_type_id" name="expense_type_id" required
                    placeholder="<?php echo translate('type_to_search', $lang); ?>...">
                <input type="hidden" class="form-control" id="hidden_expense_type_id" name="hidden_expense_type_id">
            </div>
            <div class="form-group">
                <label for="amount"><?php echo translate('amount', $lang); ?></label>
                <input type="number" class="form-control" id="amount" name="amount" required>
                <input type="hidden" class="form-control" id="old_expense_amount" name="old_expense_amount">
            </div>
            <div class="form-group">
                <label for="description"><?php echo translate('description', $lang); ?> <?php echo translate('optional', $lang); ?></label>
                <input type="text" class="form-control" id="description" name="description">
            </div>
            <div class="form-group">
                <label for="bank_deduction"><?php echo translate('bank_account_balance', $lang); ?></label>
                <select name="bank_deduction" class="form-select" id="bank_deduction" required>
                    <option value="1" selected><?php echo translate('none', $lang); ?></option>
                    <option value="2" style="color: #00c800"><?php echo translate('deposit', $lang); ?> (+)</option>
                    <option value="3" style="color: red"><?php echo translate('debit', $lang); ?> (-)</option>
                </select>
            </div>
            <div class="form-group">
                <label for="facilities_account"><?php echo translate('facilities_account_balance', $lang); ?></label>
                <select name="facilities_account" class="form-select" id="facilities_account" required>
                    <option value="1" selected><?php echo translate('none', $lang); ?></option>
                    <option value="2" style="color: #00c800"><?php echo translate('deposit', $lang); ?> (+)</option>
                    <option value="3" style="color: red"><?php echo translate('debit', $lang); ?> (-)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="notes"><?php echo translate('notes', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input class="form-control" id="notes" name="notes">
            </div>
        </div>
        <button name="insert-expense" id="insert-expense-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-expense" id="update-expense-btn" type="submit"
            class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./expenses.php" id="cancel-expense-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>
    </form>









    <h2 class="mb-4 mt-5"><?php echo translate('expenses', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit = isset($_POST['limit-expenses']) ? $_POST['limit-expenses'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-expenses" class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-expenses" class="form-select me-2" id="limit-expenses" required style="width: auto;">
                <option value="25" <?php if ($selected_limit == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-expenses-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="expenses_table" class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('date', $lang); ?></th>
                    <th><?php echo translate('expense_type', $lang); ?></th>
                    <th><?php echo translate('description', $lang); ?></th>
                    <th><?php echo translate('amount', $lang); ?></th>
                    <th><?php echo translate('bank_account_balance', $lang); ?></th>
                    <th><?php echo translate('facilities_account_balance', $lang); ?></th>
                    <th><?php echo translate('notes', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-expenses'])) {
                    $sql_limit_expenses = mysqli_real_escape_string($connection, $_POST['limit-expenses']);
                } else {
                    $sql_limit_expenses = $sql_defualt_limit;
                }
                $expenses = getAllExpenses($sql_limit_expenses);
                $serial = 1;
                foreach ($expenses as $expense) {
                    $modified = false;
                    if (strtotime($expense['created_at']) === strtotime($expense['updated_at'])) {
                        $infobtn = 'info';
                    } else {
                        $infobtn = 'danger';
                        $modified = true;
                    }
                    $created_at = date('Y-m-d H:i:s A', strtotime($expense['created_at']));
                    $created_by = $expense['created_by'];
                    $updated_at = date('Y-m-d H:i:s A', strtotime($expense['updated_at']));
                    $updated_by = $expense['updated_by'];
                    $extra_info_title = translate('created_at', $lang) . ": {$created_at}\n" .
                        translate('created_by', $lang) . ": {$created_by}\n";
                    if ($modified) {
                        $extra_info_title .= translate('updated_at', $lang) . ": {$updated_at}\n" .
                            translate('updated_by', $lang) . ": {$updated_by}";
                    }
                    $extra_info_title = trim($extra_info_title);


                    $newE = '';

                    if ($expense['bank_deduction'] == 1) {
                        $newE = '<span>' . translate('none', $lang) . '</span>';
                    } elseif ($expense['bank_deduction'] == 2) {
                        $newE = '<span style="color: #00c800;">' . translate('deposit', $lang) . ' (+)</span>';
                    } elseif ($expense['bank_deduction'] == 3) {
                        $newE = '<span style="color: red;">' . translate('debit', $lang) . ' (-)</span>';
                    }

                    if ($expense['facilities_account'] == 1) {
                        $newF = '<span>' . translate('none', $lang) . '</span>';
                    } elseif ($expense['facilities_account'] == 2) {
                        $newF = '<span style="color: #00c800;">' . translate('deposit', $lang) . ' (+)</span>';
                    } elseif ($expense['facilities_account'] == 3) {
                        $newF = '<span style="color: red;">' . translate('debit', $lang) . ' (-)</span>';
                    }


                    echo "<tr>
                <td>{$expense['id']}</td>
                <td>
                    <button class='btn btn-{$infobtn} btn-sm' title='" . htmlspecialchars($extra_info_title) . "'><i class='fas fa-info'></i></button>
                    " . (
                        $user_role != 'user' ? "
                                <button class='btn btn-warning btn-sm edit-button' data-id='{$expense['id']}'><i class='far fa-edit'></i></button>
                                <button class='btn btn-danger btn-sm delete-button' data-facilities_account={$expense['facilities_account']} data-bank_deduction={$expense['bank_deduction']} data-old_expense_amount='{$expense['amount']}' data-id='{$expense['id']}'><i class='far fa-trash-alt'></i></button>
                            " : ''
                    )
                        . "
                </td>
                <td>{$expense['expense_date']}</td>
                <td>{$expense['etName']}</td>
                <td>{$expense['description']}</td>
                <td>{$expense['amount']}</td>
                <td>{$newE}</td>
                <td>{$newF}</td>
                <td>{$expense['notes']}</td>
            </tr>";
                }
                ?>
            </tbody>
        </table>

    </div>











    <h2 class="mb-4 mt-5"><?php echo translate('add_expense_type', $lang); ?></h2>
    <form method="post">
        <div class="form-container">
            <div class="form-group">
                <label for="expense_type_name"><?php echo translate('description', $lang); ?></label>
                <input type="text" class="form-control" id="expense_type_name" name="expense_type_name" required>
            </div>
            <div class="form-group">
                <label for="expense_type_amount"><?php echo translate('amount', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label></label>
                <input type="number" min="0" step="0.01" class="form-control" id="expense_type_amount" name="expense_type_amount">
            </div>
            <div class="form-group">
                <label for="expense_type_notes"><?php echo translate('notes', $lang); ?>
                    <?php echo translate('optional', $lang); ?></label>
                <input class="form-control" id="expense_type_notes" name="expense_type_notes">
            </div>
        </div>
        <button name="insert-expense-type" id="insert-expense-type-btn" type="submit"
            class="btn btn-primary"><?php echo translate('add', $lang); ?></button>
        <button style="display: none;" name="update-expense-type" id="update-expense-type-btn" type="submit"
            class="btn btn-warning"><?php echo translate('update', $lang); ?></button>
        <a style="display: none;" href="./expenses.php" id="cancel-expense-type-btn"
            class="btn btn-secondary"><?php echo translate('cancel', $lang); ?></a>
    </form>



    <h2 class="mb-4 mt-5"><?php echo translate('expenses_types', $lang); ?></h2>
    <div class="mb-4 mt-2 d-flex align-items-center">
        <?php
        $selected_limit_expenses_types = isset($_POST['limit-expenses-types']) ? $_POST['limit-expenses-types'] : $sql_defualt_limit;
        ?>
        <form method="post" class="d-flex align-items-center ms-2 gap-3">
            <label for="limit-expenses-types" class="me-2 mb-0"><?php echo translate('select_query_limit', $lang); ?></label>
            <select name="limit-expenses-types" class="form-select me-2" id="limit-expenses-types" required style="width: auto;">
                <option value="25" <?php if ($selected_limit_expenses_types == '25') echo 'selected'; ?>>25</option>
                <option value="50" <?php if ($selected_limit_expenses_types == '50') echo 'selected'; ?>>50</option>
                <option value="100" <?php if ($selected_limit_expenses_types == '100') echo 'selected'; ?>>100</option>
                <option value="250" <?php if ($selected_limit_expenses_types == '250') echo 'selected'; ?>>250</option>
                <option value="500" <?php if ($selected_limit_expenses_types == '500') echo 'selected'; ?>>500</option>
                <option value="" <?php if ($selected_limit_expenses_types == '') echo 'selected'; ?>>
                    <?php echo translate('all', $lang); ?></option>
            </select>
            <button name="limit-expenses-types-btn" type="submit"
                class="btn btn-info btn-md"><?php echo translate('query', $lang); ?></button>
        </form>
    </div>

    <div class="table-section">
        <table id="expenses_types_table1" class="display nowrap cell-border hover table table-striped table-bordered">
            <thead>
                <tr>
                    <th><?php echo translate('id', $lang); ?></th>
                    <th><?php echo translate('action', $lang); ?></th>
                    <th><?php echo translate('description', $lang); ?></th>
                    <th><?php echo translate('amount', $lang); ?></th>
                    <th><?php echo translate('notes', $lang); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($_POST['limit-expenses-types'])) {
                    $sql_limit_expenses_types = mysqli_real_escape_string($connection, $_POST['limit-expenses-types']);
                } else {
                    $sql_limit_expenses_types = $sql_defualt_limit;
                }
                $expenses_types = getAllExpensesTypes($sql_limit_expenses_types);
                $serial = 1;
                foreach ($expenses_types as $expense_type) {
                    echo "<tr>
                <td>{$expense_type['id']}</td>
                <td>
                    " . (
                        $user_role != 'user' ? "
                                <button class='btn btn-warning btn-sm edit-button' data-id='{$expense_type['id']}'><i class='far fa-edit'></i></button>
                                <button class='btn btn-danger btn-sm delete-button' data-id='{$expense_type['id']}'><i class='far fa-trash-alt'></i></button>
                            " : ''
                    )
                        . "
                </td>
                <td>{$expense_type['name']}</td>
                <td>{$expense_type['amount']}</td>
                <td>{$expense_type['notes']}</td>
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