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

    if (localStorage.getItem("selectedLang")) {
        url.searchParams.set("lang", localStorage.getItem("selectedLang"));
        url.searchParams.set("collapsed", localStorage.getItem("collapsed"));
    } else {
        // If selectedLang is empty, you can handle it as needed, e.g., set a default language
        url.searchParams.set("lang", "ar");
        url.searchParams.set("collapsed", "true");
    }
    // Replace the current URL with the updated URL
    window.history.replaceState({}, document.title, url.toString());
</script>

<?php
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'ar';
$collapsed = isset($_GET['collapsed']) ? $_GET['collapsed'] : 'false';
// Get the selected language from the cookie or set a default value
$lang = isset($_COOKIE['selectedLang']) ? $_COOKIE['selectedLang'] : 'ar';
$collapsed = (isset($_COOKIE['collapsed']) && $_COOKIE['collapsed'] == "true") ? 'collapsed' : '';

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
    <link rel="stylesheet" href="./css/all.min.css">
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />
    <!-- <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        html,
        body {
            margin: 0;
            height: 100%;
            overflow: hidden;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen;
            background-color: #f4f6f9;
        }

        .layout {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 260px;
            background: #1f2933;
            color: #fff;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease;
            height: 100vh;
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 1rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            justify-content: space-between;
        }

        .sidebar-title {
            white-space: nowrap;
        }

        .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }

        .nav-link:hover {
            background: #111827;
            color: #fff;
        }

        .nav-link span {
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .sidebar-title {
            display: none;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: #fff;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .submenu {
            padding-left: 1.5rem;
            display: none;
        }

        .submenu a {
            font-size: 0.9rem;
        }

        main.content {
            flex: 1;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 1rem;
        }
    </style>
</head>

<body <?php echo translate('dir', $lang) ?> style="display:flex; flex-direction:column; min-height:100vh; margin:0;">
    <?php
    include_once('./inc/functions.php');
    include_once('./inc/handle_requests.php');
    ?>
    <!-- Header -->
    <div class="layout">
        <aside class="sidebar <?php echo $collapsed ?>" id="sidebar">
            <div class="sidebar-header">
                <span class="sidebar-title" id="sidebarTitle">Twin Turbo</span>
                <button class="toggle-btn" id="toggleBtn">☰</button>
            </div>

            <nav id="menu" class="nav flex-column">

                <a id="index-li" class="nav-link" href="./index.php"><i class="fa-solid fa-grip"></i> <span><?php echo translate('dashboard', $lang) ?></span></a>
                <a id="drivers-li" class="nav-link" href="./drivers.php"><i class="fa-solid fa-truck-moving"></i> <span><?php echo translate('drivers', $lang) ?></span></a>
                <!-- <a id="trips-li" class="nav-link" href="./trips.php"><i class="fa-solid fa-map-location-dot"></i> <span><?php echo translate('trips', $lang) ?></span></a> -->
                <a id="sau_bills-li" class="nav-link" href="./sau_bills.php"><i class="fa-solid fa-file-lines"></i> <span><?php echo translate('saudi_bills', $lang) ?></span></a>
                <a id="invoices-li" class="nav-link" href="./invoices.php"><i class="fa-solid fa-file-invoice-dollar"></i> <span><?php echo translate('invoices', $lang) ?></span></a>
                <!-- <a id="service-request-li" class="nav-link" href="./service-request.php"><i class="fa-solid fa-tools"></i> <span><?php echo translate('service_request', $lang) ?></span></a> -->
                <a id="services-li" class="nav-link" href="./services.php"><i class="fa-solid fa-tools"></i> <span><?php echo translate('services', $lang) ?></span></a>
                <a id="expenses-li" class="nav-link" href="./expenses.php"><i class="fa-solid fa-sack-dollar"></i> <span><?php echo translate('expenses', $lang) ?></span></a>
                <a id="reports-li" class="nav-link" href="./reports.php"><i class="fa-solid fa-file-pdf"></i> <span><?php echo translate('reports', $lang) ?></span></a>
                <a id="clients-li" class="nav-link" href="./clients.php"><i class="fa-solid fa-user-tie"></i> <span><?php echo translate('clients', $lang) ?></span></a>
                <a id="settings-li" class="nav-link" href="./settings.php"><i class="fa-solid fa-gear"></i> <span><?php echo translate('settings', $lang) ?></span></a>

                <a id="toggleDarkMode" class="nav-link"></a>
                <a id="langToggle" class="nav-link"></a>
                <form method="POST" class="nav-link" style="padding: 0; margin: 0;">
                    <button type="submit" name="logout"
                        class="nav-link"><?php //echo translate('Logout', $lang) 
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
            </nav>
        </aside>

        <script>
            /* ========== DARK MODE ========== */
            const darkToggleBtn = document.getElementById("toggleDarkMode");

            function setDarkModeUI(isDark) {
                document.body.classList.toggle("dark-mode", isDark);

                if (!darkToggleBtn) return;

                darkToggleBtn.innerHTML = isDark ?
                    `<svg fill="#FFD700" height="20" width="20" viewBox="0 0 457.32 457.32" xmlns="http://www.w3.org/2000/svg"><path d="M228.66,112.692c-63.945,0-115.968,52.022-115.968,115.967c0,63.945,52.023,115.968,115.968,115.968s115.968-52.023,115.968-115.968C344.628,164.715,292.605,112.692,228.66,112.692z"/><path d="M401.429,228.66l42.467-57.07c2.903-3.9,3.734-8.966,2.232-13.59-1.503-4.624-5.153-8.233-9.794-9.683l-67.901-21.209.811-71.132c.056-4.862-2.249-9.449-6.182-12.307-3.934-2.858-9.009-3.633-13.615-2.077l-67.399,22.753L240.895,6.322C238.082,2.356,233.522,0,228.66,0c-4.862,0-9.422,2.356-12.235,6.322l-41.154,58.024-67.4-22.753c-4.607-1.555-9.682-.781-13.615,2.077-3.933,2.858-6.238,7.445-6.182,12.307l.812,71.132-67.901,21.209c-4.641,1.45-8.291,5.059-9.793,9.683-1.503,4.624-.671,9.689,2.232,13.59l42.467,57.07-42.467,57.07c-2.903,3.9-3.734,8.966-2.232,13.59,1.502,4.624,5.153,8.233,9.793,9.683l67.901,21.208-.812,71.132c-.056,4.862,2.249,9.449,6.182,12.307,3.934,2.857,9.007,3.632,13.615,2.077l67.4-22.753 41.154,58.024c2.813,3.966,7.373,6.322,12.235,6.322s9.422-2.356,12.235-6.322l41.154-58.024 67.399,22.753c4.606,1.555,9.681.781,13.615-2.077,3.933-2.858,6.238-7.445,6.182-12.306l-.811-71.133 67.901-21.208c4.641-1.45,8.291-5.059,9.794-9.683,1.502-4.624.671-9.689-2.232-13.59L401.429,228.66z"/></svg>` :
                    `<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13 6V3M18.5 12V7M14.5 4.5H11.5M21 9.5H16M15.5548 16.8151C16.7829 16.8151 17.9493 16.5506 19 16.0754C17.6867 18.9794 14.7642 21 11.3698 21C6.74731 21 3 17.2527 3 12.6302C3 9.23576 5.02061 6.31331 7.92462 5C7.44944 6.05072 7.18492 7.21708 7.18492 8.44523C7.18492 13.0678 10.9322 16.8151 15.5548 16.8151Z" stroke="#D3D3D3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
            }

            const savedDarkMode = localStorage.getItem("darkMode") === "true";
            setDarkModeUI(savedDarkMode);

            if (darkToggleBtn) {
                darkToggleBtn.addEventListener("click", function() {
                    const isDark = !document.body.classList.contains("dark-mode");
                    setDarkModeUI(isDark);
                    localStorage.setItem("darkMode", isDark);
                });
            }
        </script>
        <main class="content">