<?php
// users req------------------
function handleUserRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            users('i', null);
            break;
        case 'u':
            if ($id) {
                users('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                users('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-user'])) {
    handleUserRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-user']) && isset($_GET['user-uid']) && is_numeric($_GET['user-uid'])) {
    handleUserRequests('u', $_GET['user-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['user-did']) && is_numeric($_GET['user-did'])) {
    handleUserRequests('d', $_GET['user-did']);
}



// bank account req -------------------
function handleBankAccountRequests($action, $type)
{
    switch ($action) {
        case 'u':
            updateBankAccount($type);
            break;
        default:
            break;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update-bank-account-amount'])) {
        handleBankAccountRequests('u', 'bank');
    } elseif (isset($_POST['update-facilities-account-amount'])) {
        handleBankAccountRequests('u', 'tas');
    }
}





// driver req------------------
function handledriverRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            drivers('i', null);
            break;
        case 'u':
            if ($id) {
                drivers('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                drivers('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-driver'])) {
    handledriverRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-driver']) && isset($_GET['driver-uid']) && is_numeric($_GET['driver-uid'])) {
    handledriverRequests('u', $_GET['driver-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['driver-did']) && is_numeric($_GET['driver-did'])) {
    handledriverRequests('d', $_GET['driver-did']);
}









// trip_fees_types req------------------
function handleTripFeesTypesRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            tripFeesTypes('i', null);
            break;
        case 'u':
            if ($id) {
                tripFeesTypes('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                tripFeesTypes('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-type-of-trip-expenses'])) {
    handleTripFeesTypesRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-type-of-trip-expenses']) && isset($_GET['type-of-trip-expenses-uid']) && is_numeric($_GET['type-of-trip-expenses-uid'])) {
    handleTripFeesTypesRequests('u', $_GET['type-of-trip-expenses-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type-of-trip-expenses-did']) && is_numeric($_GET['type-of-trip-expenses-did'])) {
    handleTripFeesTypesRequests('d', $_GET['type-of-trip-expenses-did']);
}





// trip req------------------
function handleTripRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            trips('i', null);
            break;
        case 'u':
            if ($id) {
                trips('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                trips('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-trip'])) {
    handleTripRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-trip']) && isset($_GET['trip-uid']) && is_numeric($_GET['trip-uid'])) {
    handleTripRequests('u', $_GET['trip-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['trip-did']) && is_numeric($_GET['trip-did'])) {
    handleTripRequests('d', $_GET['trip-did']);
}











// sau office req------------------
function handleSauOfficeRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            sauOffices('i', null);
            break;
        case 'u':
            if ($id) {
                sauOffices('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                sauOffices('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-sau-office'])) {
    handleSauOfficeRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-sau-office']) && isset($_GET['sau-office-uid']) && is_numeric($_GET['sau-office-uid'])) {
    handleSauOfficeRequests('u', $_GET['sau-office-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['sau-office-did']) && is_numeric($_GET['sau-office-did'])) {
    handleSauOfficeRequests('d', $_GET['sau-office-did']);
}



// sau office req------------------
function handleSauBillsRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            sauBills('i', null);
            break;
        case 'u':
            if ($id) {
                sauBills('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                sauBills('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-sau-bill'])) {
    handleSauBillsRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-sau-bill']) && isset($_GET['sau-bill-uid']) && is_numeric($_GET['sau-bill-uid'])) {
    handleSauBillsRequests('u', $_GET['sau-bill-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['sau-bill-did']) && is_numeric($_GET['sau-bill-did'])) {
    handleSauBillsRequests('d', $_GET['sau-bill-did']);
}




// services fees req------------------
function handleServicesFeesTypesRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            serviceFeesTypes('i', null);
            break;
        case 'u':
            if ($id) {
                serviceFeesTypes('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                serviceFeesTypes('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-type-of-service-fee'])) {
    handleServicesFeesTypesRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-type-of-service-fee']) && isset($_GET['type-of-service-fee-uid']) && is_numeric($_GET['type-of-service-fee-uid'])) {
    handleServicesFeesTypesRequests('u', $_GET['type-of-service-fee-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type-of-service-fee-did']) && is_numeric($_GET['type-of-service-fee-did'])) {
    handleServicesFeesTypesRequests('d', $_GET['type-of-service-fee-did']);
}



// services req------------------
function handleServicesRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            services('i', null);
            break;
        case 'u':
            if ($id) {
                services('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                services('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-service'])) {
    handleServicesRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-service']) && isset($_GET['service-uid']) && is_numeric($_GET['service-uid'])) {
    handleServicesRequests('u', $_GET['service-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['service-did']) && is_numeric($_GET['service-did'])) {
    handleServicesRequests('d', $_GET['service-did']);
}










// expenses req------------------
function handleExpensesTypesRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            expensesTypes('i', null);
            break;
        case 'u':
            if ($id) {
                expensesTypes('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                expensesTypes('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-expense-type'])) {
    handleExpensesTypesRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-expense-type']) && isset($_GET['expense-type-uid']) && is_numeric($_GET['expense-type-uid'])) {
    handleExpensesTypesRequests('u', $_GET['expense-type-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['expense-type-did']) && is_numeric($_GET['expense-type-did'])) {
    handleExpensesTypesRequests('d', $_GET['expense-type-did']);
}








// expenses req------------------
function handleExpensesRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            expenses('i', null);
            break;
        case 'u':
            if ($id) {
                expenses('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                expenses('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-expense'])) {
    handleExpensesRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-expense']) && isset($_GET['expense-uid']) && is_numeric($_GET['expense-uid'])) {
    handleExpensesRequests('u', $_GET['expense-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['expense-did']) && is_numeric($_GET['expense-did'])) {
    handleExpensesRequests('d', $_GET['expense-did']);
}




















// invoices fees req------------------
function handleInvoicesFeesTypesRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            invoiceFeesTypes('i', null);
            break;
        case 'u':
            if ($id) {
                invoiceFeesTypes('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                invoiceFeesTypes('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-type-of-invoice-fee'])) {
    handleInvoicesFeesTypesRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-type-of-invoice-fee']) && isset($_GET['type-of-invoice-fee-uid']) && is_numeric($_GET['type-of-invoice-fee-uid'])) {
    handleInvoicesFeesTypesRequests('u', $_GET['type-of-invoice-fee-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['type-of-invoice-fee-did']) && is_numeric($_GET['type-of-invoice-fee-did'])) {
    handleInvoicesFeesTypesRequests('d', $_GET['type-of-invoice-fee-did']);
}



// invoices req------------------
function handleInvoicesRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            invoices('i', null);
            break;
        case 'u':
            if ($id) {
                invoices('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                invoices('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-invoice'])) {
    handleInvoicesRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-invoice']) && isset($_GET['invoice-uid']) && is_numeric($_GET['invoice-uid'])) {
    handleInvoicesRequests('u', $_GET['invoice-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['invoice-did']) && is_numeric($_GET['invoice-did'])) {
    handleInvoicesRequests('d', $_GET['invoice-did']);
}



// paid inv req -------------------
function handlePayInvoiceRequests($action, $id = null)
{
    switch ($action) {
        case 'u':
            pay_invoice('u', $id);
            break;
        default:
            break;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['invoice-pid']) && is_numeric($_GET['invoice-pid'])) {
    handlePayInvoiceRequests('u', $_GET['invoice-pid']);
}







// invoices fees req------------------
function handleCustomersRequests($action, $id = null)
{
    switch ($action) {
        case 'i':
            customers('i', null);
            break;
        case 'u':
            if ($id) {
                customers('u', $id);
            }
            break;
        case 'd':
            if ($id) {
                customers('d', $id);
            }
            break;
        default:
            break;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['insert-customer'])) {
    handleCustomersRequests('i');
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-customer']) && isset($_GET['customer-uid']) && is_numeric($_GET['customer-uid'])) {
    handleCustomersRequests('u', $_GET['customer-uid']);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['customer-did']) && is_numeric($_GET['customer-did'])) {
    handleCustomersRequests('d', $_GET['customer-did']);
}
