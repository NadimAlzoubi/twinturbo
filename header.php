<?php
session_start();
date_default_timezone_set('Asia/Dubai');

$config = include('z.php');
// TODO اضافة رسالة مع الصفحة $MESSAGE
if ($config['underMaintenance']) {
    header("Location: The-Site-Under-Maintenance.php");
    exit();
}

// التحقق من تسجيل الدخول
if (!isset($_SESSION["sau_user_id"]) || !isset($_SESSION["sau_user_role"])) {
    header("Location: login.php");
}
?>

<script>
    let url = new URL(window.location.href);

    // Check if selectedLang is not empty and set the "lang" parameter accordingly
    if (localStorage.getItem("selectedLang")) {
        url.searchParams.set("lang", localStorage.getItem("selectedLang"));
    } else {
        // If selectedLang is empty, you can handle it as needed, e.g., set a default language
        url.searchParams.set("lang", "ar");
    }
    // Replace the current URL with the updated URL
    window.history.replaceState({}, document.title, url.toString());
</script>

<?php
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ar';
// Get the selected language from the cookie or set a default value
$lang = isset($_COOKIE['selectedLang']) ? $_COOKIE['selectedLang'] : 'ar';

include_once('./lang.php');
include_once('./inc/connect.php');
include_once('./constants.php');



$user_id = $_SESSION["sau_user_id"];
$user_full_name = $_SESSION["sau_user_full_name"];
$user_role = $_SESSION["sau_user_role"];
$user_status = $_SESSION["sau_user_status"];
$user_location = $_SESSION["sau_user_location"];


switch ($user_role) {
    case 'admin':
        $translated_user_role = translate('administrator', $lang);
        break;
    case 'supervisor':
        $translated_user_role = translate('supervisor', $lang);
        break;
    default:
        $translated_user_role = translate('user', $lang);
        break;
}

// زر تسجيل الخروج
if (isset($_POST["logout"])) {
    session_destroy();
    header("Location: login.php");
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="./img/lightning-1-svgrepo-com.svg">
    <link rel="stylesheet" href="./css/style.css?v=<?php echo $nadim->next_version; ?>">
    <link rel="stylesheet" href="./css/dark-mode.css?v=<?php echo $nadim->next_version; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <!-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body <?php echo translate('dir', $lang) ?>>
    <?php
    include_once('./inc/functions.php');
    include_once('./inc/handle_requests.php');
    ?>

    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand d-lg-none d-md-block" style="margin-right: 1rem; margin-left: 1rem; font-size: 1rem; font-weight: bold" href="./index.php">"<?php echo $user_full_name . ' | ' . $translated_user_role . ' | ' . translate($user_location, $lang) ?>"</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon" style="width: 1rem; height: 1rem;"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav m-auto">
                <li class="nav-item mr-1 ml-1" id="index-li">
                    <a class="nav-link" href="./index.php"><?php echo translate('dashboard', $lang) ?></a>
                </li>
                <li class="nav-item mr-1 ml-1" id="drivers-li">
                    <a class="nav-link" href="./drivers.php"><?php echo translate('drivers', $lang) ?></a>
                </li>
                <li class="nav-item mr-1 ml-1" id="trips-li">
                    <a class="nav-link" href="./trips.php"><?php echo translate('trips', $lang) ?></a>
                </li>
                <li class="nav-item mr-1 ml-1" id="sau_bills-li">
                    <a class="nav-link" href="./sau_bills.php"><?php echo translate('saudi_bills', $lang) ?></a>
                </li>
                <li class="nav-item mr-1 ml-1" id="invoices-li">
                    <a class="nav-link" href="./invoices.php"><?php echo translate('invoices', $lang) ?></a>
                </li>
                <li class="nav-item mr-1 ml-1" id="services-li">
                    <a class="nav-link" href="./services.php"><?php echo translate('services', $lang) ?></a>
                </li>
                <li class="nav-item mr-1 ml-1" id="expenses-li">
                    <a class="nav-link" href="./expenses.php"><?php echo translate('expenses', $lang) ?></a>
                </li>
                <li class="nav-item mr-1 ml-1" id="reports-li">
                    <a class="nav-link" href="./reports.php"><?php echo translate('reports', $lang) ?></a>
                </li>
                <li class="nav-item mr-1 ml-1">
                    <a class="nav-link account_amount d-flex align-items-center justify-content-center gap-2"></a>
                </li>
                <?php if ($_SESSION["sau_user_role"] == 'admin'): ?>
                    <li class="nav-item mr-1 ml-1" id="settings-li">
                        <a class="btn btn-success m-1" href="./settings.php">
                            <svg height="20px" width="20px" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512" xml:space="preserve" fill="#ffffff" stroke="#ffffff">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <style type="text/css">
                                        .st0 {
                                            fill: #ffffff;
                                        }
                                    </style>
                                    <g>
                                        <path class="st0" d="M502.325,307.303l-39.006-30.805c-6.215-4.908-9.665-12.429-9.668-20.348c0-0.084,0-0.168,0-0.252 c-0.014-7.936,3.44-15.478,9.667-20.396l39.007-30.806c8.933-7.055,12.093-19.185,7.737-29.701l-17.134-41.366 c-4.356-10.516-15.167-16.86-26.472-15.532l-49.366,5.8c-7.881,0.926-15.656-1.966-21.258-7.586 c-0.059-0.06-0.118-0.119-0.177-0.178c-5.597-5.602-8.476-13.36-7.552-21.225l5.799-49.363 c1.328-11.305-5.015-22.116-15.531-26.472L337.004,1.939c-10.516-4.356-22.646-1.196-29.701,7.736l-30.805,39.005 c-4.908,6.215-12.43,9.665-20.349,9.668c-0.084,0-0.168,0-0.252,0c-7.935,0.014-15.477-3.44-20.395-9.667L204.697,9.675 c-7.055-8.933-19.185-12.092-29.702-7.736L133.63,19.072c-10.516,4.356-16.86,15.167-15.532,26.473l5.799,49.366 c0.926,7.881-1.964,15.656-7.585,21.257c-0.059,0.059-0.118,0.118-0.178,0.178c-5.602,5.598-13.36,8.477-21.226,7.552 l-49.363-5.799c-11.305-1.328-22.116,5.015-26.472,15.531L1.939,174.996c-4.356,10.516-1.196,22.646,7.736,29.701l39.006,30.805 c6.215,4.908,9.665,12.429,9.668,20.348c0,0.084,0,0.167,0,0.251c0.014,7.935-3.44,15.477-9.667,20.395L9.675,307.303 c-8.933,7.055-12.092,19.185-7.736,29.701l17.134,41.365c4.356,10.516,15.168,16.86,26.472,15.532l49.366-5.799 c7.882-0.926,15.656,1.965,21.258,7.586c0.059,0.059,0.118,0.119,0.178,0.178c5.597,5.603,8.476,13.36,7.552,21.226l-5.799,49.364 c-1.328,11.305,5.015,22.116,15.532,26.472l41.366,17.134c10.516,4.356,22.646,1.196,29.701-7.736l30.804-39.005 c4.908-6.215,12.43-9.665,20.348-9.669c0.084,0,0.168,0,0.251,0c7.936-0.014,15.478,3.44,20.396,9.667l30.806,39.007 c7.055,8.933,19.185,12.093,29.701,7.736l41.366-17.134c10.516-4.356,16.86-15.168,15.532-26.472l-5.8-49.366 c-0.926-7.881,1.965-15.656,7.586-21.257c0.059-0.059,0.119-0.119,0.178-0.178c5.602-5.597,13.36-8.476,21.225-7.552l49.364,5.799 c11.305,1.328,22.117-5.015,26.472-15.531l17.134-41.365C514.418,326.488,511.258,314.358,502.325,307.303z M281.292,329.698 c-39.68,16.436-85.172-2.407-101.607-42.087c-16.436-39.68,2.407-85.171,42.087-101.608c39.68-16.436,85.172,2.407,101.608,42.088 C339.815,267.771,320.972,313.262,281.292,329.698z"></path>
                                    </g>
                                </g>
                            </svg>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item mr-1 ml-1">
                    <button id="toggleDarkMode" class="btn btn-secondary m-1">Dark mode</button>
                </li>
                <li class="nav-item mr-1 ml-1">
                    <button id="langToggle" class="btn m-1 btn-primary">Language</button>
                </li>
                <li class="nav-item mr-1 ml-1">
                    <form method="POST" class="nav-link text-light" style="margin-bottom: 0; padding: 0;">
                        <button type="submit" name="logout"
                            class="btn btn-danger m-1"><?php //echo translate('Logout', $lang) 
                                                        ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor"
                                class="bi bi-box-arrow-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z" />
                                <path fill-rule="evenodd"
                                    d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z" />
                            </svg>
                        </button>
                    </form>
                </li>
                <li class="nav-item mr-1 ml-1">
                    <?php if (isset($_SESSION["sau_ver"]) && $_SESSION["sau_ver"] != $nadim->next_version): ?>
                        <button title="Updates" class="btn btn-sm btn-sm">
                            <a id="updates-svg" class="nav-link" href="./updates.php" target="_blank">
                                <svg fill="#ffff00" version="1.1" id="Layer_1" xmlns:x="&amp;ns_extend;" xmlns:i="&amp;ns_ai;" xmlns:graph="&amp;ns_graphs;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 24.00 24.00" enable-background="new 0 0 24 24" xml:space="preserve" stroke="#ffff00" stroke-width="0.00024000000000000003">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <metadata>
                                            <sfw xmlns="&amp;ns_sfw;">
                                                <slices> </slices>
                                                <slicesourcebounds width="505" height="984" bottomleftorigin="true" x="0" y="-552"> </slicesourcebounds>
                                            </sfw>
                                        </metadata>
                                        <g>
                                            <g>
                                                <g>
                                                    <path d="M12,22C6.5,22,2,17.5,2,12c0-0.6,0.4-1,1-1s1,0.4,1,1c0,4.4,3.6,8,8,8s8-3.6,8-8s-3.6-8-8-8C9.3,4,6.8,5.3,5.4,7.6 C5,8,4.4,8.1,4,7.8C3.5,7.5,3.4,6.9,3.7,6.4C5.5,3.7,8.7,2,12,2c5.5,0,10,4.5,10,10S17.5,22,12,22z"></path>
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path d="M12,13c-0.6,0-1-0.4-1-1V7c0-0.6,0.4-1,1-1s1,0.4,1,1v5C13,12.6,12.6,13,12,13z"></path>
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path d="M15,16c-0.3,0-0.5-0.1-0.7-0.3l-3-3c-0.4-0.4-0.4-1,0-1.4s1-0.4,1.4,0l3,3c0.4,0.4,0.4,1,0,1.4C15.5,15.9,15.3,16,15,16z "></path>
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path d="M8,8H4C3.4,8,3,7.6,3,7V3c0-0.6,0.4-1,1-1s1,0.4,1,1v3h3c0.6,0,1,0.4,1,1S8.6,8,8,8z"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <?php echo "v" . $nadim->next_version; ?>
                            </a>
                        </button>
                    <?php else: ?>
                        <button title="Updates" class="btn btn-sm btn-sm">
                            <a id="" class="nav-link" href="./updates.php" target="_blank">
                                <svg fill="#ff9900" version="1.1" id="Layer_1" xmlns:x="&amp;ns_extend;" xmlns:i="&amp;ns_ai;" xmlns:graph="&amp;ns_graphs;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="20" height="20" viewBox="0 0 24.00 24.00" enable-background="new 0 0 24 24" xml:space="preserve" stroke="#ff9900" stroke-width="0.00024000000000000003">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <metadata>
                                            <sfw xmlns="&amp;ns_sfw;">
                                                <slices> </slices>
                                                <slicesourcebounds width="505" height="984" bottomleftorigin="true" x="0" y="-552"> </slicesourcebounds>
                                            </sfw>
                                        </metadata>
                                        <g>
                                            <g>
                                                <g>
                                                    <path d="M12,22C6.5,22,2,17.5,2,12c0-0.6,0.4-1,1-1s1,0.4,1,1c0,4.4,3.6,8,8,8s8-3.6,8-8s-3.6-8-8-8C9.3,4,6.8,5.3,5.4,7.6 C5,8,4.4,8.1,4,7.8C3.5,7.5,3.4,6.9,3.7,6.4C5.5,3.7,8.7,2,12,2c5.5,0,10,4.5,10,10S17.5,22,12,22z"></path>
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path d="M12,13c-0.6,0-1-0.4-1-1V7c0-0.6,0.4-1,1-1s1,0.4,1,1v5C13,12.6,12.6,13,12,13z"></path>
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path d="M15,16c-0.3,0-0.5-0.1-0.7-0.3l-3-3c-0.4-0.4-0.4-1,0-1.4s1-0.4,1.4,0l3,3c0.4,0.4,0.4,1,0,1.4C15.5,15.9,15.3,16,15,16z "></path>
                                                </g>
                                            </g>
                                            <g>
                                                <g>
                                                    <path d="M8,8H4C3.4,8,3,7.6,3,7V3c0-0.6,0.4-1,1-1s1,0.4,1,1v3h3c0.6,0,1,0.4,1,1S8.6,8,8,8z"></path>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </a>
                        </button>
                    <?php endif ?>
                </li>
            </ul>
        </div>
    </nav>
    <script>
        // dark mode
        const toggleButton = document.getElementById("toggleDarkMode");
        // Get the current selected language from local storage
        let selectedLang = localStorage.getItem("selectedLang");
        // Load the dark mode preference from localStorage
        const isDarkMode = localStorage.getItem("darkMode") === "true";
        if (isDarkMode) {
            document.body.classList.add("dark-mode");
            toggleButton.innerHTML = `
      <svg fill="#FFD700" height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 457.32 457.32" xml:space="preserve" stroke="#FFD700"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="XMLID_467_"> <path id="XMLID_922_" d="M228.66,112.692c-63.945,0-115.968,52.022-115.968,115.967c0,63.945,52.023,115.968,115.968,115.968 s115.968-52.023,115.968-115.968C344.628,164.715,292.605,112.692,228.66,112.692z"></path> <path id="XMLID_1397_" d="M401.429,228.66l42.467-57.07c2.903-3.9,3.734-8.966,2.232-13.59c-1.503-4.624-5.153-8.233-9.794-9.683 l-67.901-21.209l0.811-71.132c0.056-4.862-2.249-9.449-6.182-12.307c-3.934-2.858-9.009-3.633-13.615-2.077l-67.399,22.753 L240.895,6.322C238.082,2.356,233.522,0,228.66,0c-4.862,0-9.422,2.356-12.235,6.322l-41.154,58.024l-67.4-22.753 c-4.607-1.555-9.682-0.781-13.615,2.077c-3.933,2.858-6.238,7.445-6.182,12.307l0.812,71.132l-67.901,21.209 c-4.641,1.45-8.291,5.059-9.793,9.683c-1.503,4.624-0.671,9.689,2.232,13.59l42.467,57.07l-42.467,57.07 c-2.903,3.9-3.734,8.966-2.232,13.59c1.502,4.624,5.153,8.233,9.793,9.683l67.901,21.208l-0.812,71.132 c-0.056,4.862,2.249,9.449,6.182,12.307c3.934,2.857,9.007,3.632,13.615,2.077l67.4-22.753l41.154,58.024 c2.813,3.966,7.373,6.322,12.235,6.322c4.862,0,9.422-2.356,12.235-6.322l41.154-58.024l67.399,22.753 c4.606,1.555,9.681,0.781,13.615-2.077c3.933-2.858,6.238-7.445,6.182-12.306l-0.811-71.133l67.901-21.208 c4.641-1.45,8.291-5.059,9.794-9.683c1.502-4.624,0.671-9.689-2.232-13.59L401.429,228.66z M228.66,374.627 c-80.487,0-145.968-65.481-145.968-145.968S148.173,82.692,228.66,82.692s145.968,65.48,145.968,145.967 S309.147,374.627,228.66,374.627z"></path> </g> </g></svg>
    `;
        } else {
            toggleButton.innerHTML = `
    <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M13 6V3M18.5 12V7M14.5 4.5H11.5M21 9.5H16M15.5548 16.8151C16.7829 16.8151 17.9493 16.5506 19 16.0754C17.6867 18.9794 14.7642 21 11.3698 21C6.74731 21 3 17.2527 3 12.6302C3 9.23576 5.02061 6.31331 7.92462 5C7.44944 6.05072 7.18492 7.21708 7.18492 8.44523C7.18492 13.0678 10.9322 16.8151 15.5548 16.8151Z" stroke="#D3D3D3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
  `;
        }
        toggleButton.addEventListener("click", () => {
            document.body.classList.toggle("dark-mode");
            // Change the button text based on the current mode
            if (document.body.classList.contains("dark-mode")) {
                toggleButton.innerHTML = `
      <svg fill="#FFD700" height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 457.32 457.32" xml:space="preserve" stroke="#FFD700"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="XMLID_467_"> <path id="XMLID_922_" d="M228.66,112.692c-63.945,0-115.968,52.022-115.968,115.967c0,63.945,52.023,115.968,115.968,115.968 s115.968-52.023,115.968-115.968C344.628,164.715,292.605,112.692,228.66,112.692z"></path> <path id="XMLID_1397_" d="M401.429,228.66l42.467-57.07c2.903-3.9,3.734-8.966,2.232-13.59c-1.503-4.624-5.153-8.233-9.794-9.683 l-67.901-21.209l0.811-71.132c0.056-4.862-2.249-9.449-6.182-12.307c-3.934-2.858-9.009-3.633-13.615-2.077l-67.399,22.753 L240.895,6.322C238.082,2.356,233.522,0,228.66,0c-4.862,0-9.422,2.356-12.235,6.322l-41.154,58.024l-67.4-22.753 c-4.607-1.555-9.682-0.781-13.615,2.077c-3.933,2.858-6.238,7.445-6.182,12.307l0.812,71.132l-67.901,21.209 c-4.641,1.45-8.291,5.059-9.793,9.683c-1.503,4.624-0.671,9.689,2.232,13.59l42.467,57.07l-42.467,57.07 c-2.903,3.9-3.734,8.966-2.232,13.59c1.502,4.624,5.153,8.233,9.793,9.683l67.901,21.208l-0.812,71.132 c-0.056,4.862,2.249,9.449,6.182,12.307c3.934,2.857,9.007,3.632,13.615,2.077l67.4-22.753l41.154,58.024 c2.813,3.966,7.373,6.322,12.235,6.322c4.862,0,9.422-2.356,12.235-6.322l41.154-58.024l67.399,22.753 c4.606,1.555,9.681,0.781,13.615-2.077c3.933-2.858,6.238-7.445,6.182-12.306l-0.811-71.133l67.901-21.208 c4.641-1.45,8.291-5.059,9.794-9.683c1.502-4.624,0.671-9.689-2.232-13.59L401.429,228.66z M228.66,374.627 c-80.487,0-145.968-65.481-145.968-145.968S148.173,82.692,228.66,82.692s145.968,65.48,145.968,145.967 S309.147,374.627,228.66,374.627z"></path> </g> </g></svg>
    `;
            } else {
                toggleButton.innerHTML = `
      <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke=""><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M13 6V3M18.5 12V7M14.5 4.5H11.5M21 9.5H16M15.5548 16.8151C16.7829 16.8151 17.9493 16.5506 19 16.0754C17.6867 18.9794 14.7642 21 11.3698 21C6.74731 21 3 17.2527 3 12.6302C3 9.23576 5.02061 6.31331 7.92462 5C7.44944 6.05072 7.18492 7.21708 7.18492 8.44523C7.18492 13.0678 10.9322 16.8151 15.5548 16.8151Z" stroke="#D3D3D3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
    `;
            }
            // Update localStorage
            localStorage.setItem(
                "darkMode",
                document.body.classList.contains("dark-mode")
            );
        });
    </script>