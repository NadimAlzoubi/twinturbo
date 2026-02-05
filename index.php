<?php
    include_once('./header.php');
?>

<head>
    <title><?php echo translate('dashboard', $lang) . ' - ' . $translated_user_role; ?></title>
</head>



<?php
// تعيين متغيرات التاريخ
$paramType = isset($_POST['paramType']) ? $_POST['paramType'] : 'today';
// تعيين التواريخ بناءً على نوع البارامترات
switch ($paramType) {
    case 'today':
        $startDate = date('Y-m-d');
        $endDate = $startDate;
        break;
    case 'yesterday':
        $startDate = date('Y-m-d', strtotime('-1 day'));
        $endDate = $startDate;
        break;
    case 'period':
        $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : null;
        $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : null;
        break;
    case 'all':
    default:
        $startDate = null;
        $endDate = null;
        break;
}

// استدعاء الدالة للحصول على بيانات لوحة التحكم
$dashboardData = getDashboard($startDate, $endDate);

$totalDrivers = $dashboardData['total_drivers'] ?? 0;
$totalTrips = $dashboardData['total_trips'] ?? 0;
$totalTripsRemaining = $dashboardData['total_trips_remaining'] ?? 0;
$totalSauBills = $dashboardData['total_sau_bills'] ?? 0;
$totalSauBillsAmount = $dashboardData['total_sau_bills_amount'] ?? 0;
$totalNobSauBills = $dashboardData['total_nob_sau_bills'] ?? 0;
$totalNovSauBills = $dashboardData['total_nov_sau_bills'] ?? 0;
$totalServices = $dashboardData['total_services'] ?? 0;
$totalNovServices = $dashboardData['total_nov_services'] ?? 0;
$totalExpenses = $dashboardData['total_expenses'] ?? 0;
$totalExpensesAmount = $dashboardData['total_expenses_amount'] ?? 0;
$totalServicesAmount = $dashboardData['total_services_amount'] ?? 0;
$totalClients = $dashboardData['total_clients'] ?? 0;

$totalServicesBankAmount = $dashboardData['total_services_bank_deduction_amount'] ?? 0;
$totalExpensesBankAmount = $dashboardData['total_expenses_bank_deduction_amount'] ?? 0;
$totalExpensesBankAmount2 = $dashboardData['total_deposit_expenses_amount'] ?? 0;

$totalBankDeductionAmount = $totalServicesBankAmount + $totalExpensesBankAmount ?? 0;

?>
<a class="navbar-brand d-none d-lg-block text-center" style="font-size: 1rem; margin-top: 1rem; font-weight: bold" href="./index.php">"<?php echo $user_full_name . ' | ' . $translated_user_role . ' | ' . translate($user_location, $lang) ?>"</a>
<div class="container mt-3">
    



<form class="d-flex flex-wrap justify-content-around align-items-center gap-2" id="dashboardForm" method="post">
    <div class="form-group d-flex flex-column align-items-start mb-3">
        <label for="paramType"><?php echo translate('date', $lang) ?></label>
        <select class="form-select" id="paramType" name="paramType" onchange="updateDates()" style="width: auto;">
            <option value="today" <?php if ($paramType == 'today') echo 'selected'; ?>><?php echo translate('today', $lang) ?></option>
            <option value="yesterday" <?php if ($paramType == 'yesterday') echo 'selected'; ?>><?php echo translate('yesterday', $lang) ?></option>
            <option value="period" <?php if ($paramType == 'period') echo 'selected'; ?>><?php echo translate('period', $lang) ?></option>
            <option value="all" <?php if ($paramType == 'all') echo 'selected'; ?>><?php echo translate('all', $lang) ?></option>
        </select>
    </div>    
    <div class="form-group d-flex flex-column align-items-start mb-3" id="str-date-div">
        <label for="startDate"><?php echo translate('start_date', $lang) ?></label>
        <input class="form-control" type="date" id="startDate" name="startDate" disabled style="width: auto;">
    </div>
    <div class="form-group d-flex flex-column align-items-start mb-3" id="end-date-div">
        <label for="endDate"><?php echo translate('end_date', $lang) ?></label>
        <input class="form-control" type="date" id="endDate" name="endDate" disabled style="width: auto;">
    </div>
    <div class="form-group d-flex flex-column align-items-end mb-3">
        <label for="1" class="invisible">-</label>
        <button class="btn btn-info mt-auto" type="submit"><?php echo translate('query', $lang) ?></button>
    </div>
</form>

    <br>

    <!-- Dashboard Cards -->
    <div class="row">
        <!-- bank_account_balance Card -->
         <div class="col-lg-6 col-md-12">
            <div class="card text-white" style="background-color: #4f0000;">
                <div class="card-body">
                    <div class="card-icon" id="dollar-sign-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="card-title">
                        <?php echo translate('bank_account', $lang); ?>
                    </div>
                    <div class="card-text d-flex align-items-center gap-2 account_amount">
                    <?php //echo translate('main', $lang) . ': '?>
                    <!-- <p id="bank-home-page" class="card-text" style="color: #00c800; margin-bottom: 0;"></p>
                        | 
                    <p class="card-text" style="color: red; margin-bottom: 0;"><?php //echo translate('bank_deduction', $lang) . ': -' . $totalBankDeductionAmount; ?></p> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Bills Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="card-icon" id="invoice-dollar-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="card-title">
                        <?php echo translate('saudi_bills', $lang); ?>
                    </div>
                    <p class="card-text">
                        <?php 
                            echo translate('number_of_records', $lang).":  $totalSauBills  | "
                                .translate('number_of_bills', $lang).": $totalNobSauBills | "
                                .translate('number_of_vehicles', $lang).": $totalNovSauBills | "
                                .translate('total_amount', $lang).": $totalSauBillsAmount"
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <!-- </div> -->
    <!--  -->
    <!--  -->
    <!--  -->
    <!--  -->
    <!--  -->
    <!--  -->
    <!--  -->
    <!--  -->
    <!-- <div class="row"> -->
        
        <!-- Services Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="card-icon" id="cash-register-icon">
                        <i class="fa fa-cash-register"></i>
                    </div>
                    <div class="card-title">
                        <?php echo translate('services', $lang); ?>
                    </div>
                    <p class="card-text">
                        <?php 
                            echo translate('number_of_records', $lang).":  $totalServices  | "
                            .translate('number_of_vehicles', $lang).": $totalNovServices | "
                            .translate('total_amount', $lang).": $totalServicesAmount | "
                            .translate('bank_deduction', $lang).": -$totalServicesBankAmount"
                        ?>
                    </p>
                </div>
            </div>
        </div>


        <!-- Expenses Card -->
        <div class="col-lg-6 col-md-12">
            <div class="card text-white" style="background-color: #007c95;">
                <div class="card-body">
                    <div class="card-icon" id="wallet-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="card-title">
                        <?php echo translate('expenses', $lang); ?>
                    </div>
                    <p class="card-text">
                        <?php 
                            echo translate('number_of_records', $lang).":  $totalExpenses  | "
                                .translate('deposit', $lang).": $totalExpensesBankAmount2 | "
                                .translate('debit', $lang).": -$totalExpensesBankAmount | "
                                .translate('total_amount', $lang).": $totalExpensesAmount"
                        ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Trips Card -->
        <div class="col-lg-4 col-md-12">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="card-icon" id="route-icon">
                        <i class="fas fa-route"></i>
                    </div>
                    <div class="card-title">
                        <?php echo translate('trips', $lang); ?>
                    </div>
                    <p class="card-text"><?php echo translate('number_of_records', $lang).": $totalTrips | ".translate('remaining', $lang).": $totalTripsRemaining" ?></p>
                </div>
            </div>
        </div>

        <!-- Drivers Card -->
        <div class="col-lg-4 col-md-12">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="card-icon" id="user-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="card-title">
                        <?php echo translate('drivers', $lang); ?>
                    </div>
                    <p class="card-text"><?php echo $totalDrivers ?></p>
                </div>
            </div>
        </div>


        <!-- Clients Card -->
        <div class="col-lg-4 col-md-12">
            <div class="card text-white bg-dark">
                <div class="card-body">
                    <div class="card-icon" id="users-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-title">
                        <?php echo translate('saudi_offices', $lang); ?>
                    </div>
                    <p class="card-text"><?php echo $totalClients ?></p>
                </div>
            </div>
        </div>
    </div>

<!-- 
    <div class="row">    
        Services Card
         <div class="col-lg-12 col-md-12"> 
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="card-icon" id="cash-register-icon">
                        <i class="fa fa-cash-register"></i>
                    </div>
                    <div class="card-title">
                        <?php //echo translate('users', $lang); ?>
                    </div>
                    <p class="card-text">
                        <?php
                            // $users = getAllUsers();
                            // foreach ($users as $row) {
                            //     $id = $row['id'];
                            //     echo "
                            //         <span>{$row['full_name']} -</span> 
                            //         <span id='lastSeen-$id'></span>
                            //     <br>";
                            // }
                        ?>

                    </p>
                </div>
            </div>
        </div>
    </div> -->

</div>

<?php
    include_once('./footer.php');
?>
