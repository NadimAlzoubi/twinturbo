<?php
    session_start();
?>
<!-- updates.php -->
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="./img/updates.svg">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Logs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-weight: 600;
        }

        .log-container {
            max-width: 70%;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .log-entry {
            padding: 10px;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
        }

        .log-entry:last-child {
            border-bottom: none;
        }

        h3 {
            color: #333;
        }

        ul {
            padding-left: 20px;
        }

        .row {
            display: flex;
            /* flex-direction: column; */
            justify-content: space-between;
            /* عرض العناصر عمودياً */
            gap: 10px;
            /* إضافة مسافة بين العناصر إذا رغبت بذلك */
        }

        .details-ar {
            text-align: right;
            direction: rtl;
        }

        .details-en {
            text-align: left;
            direction: ltr;
        }

       
    </style>
</head>

<body>
    <center><h2>Update logs | سجلات التحديثات</h2></center>
    <div class="log-container">
        <?php
        // قراءة الملف وعرض السجلات
        $log_file = 'version_logs.log';
        if (filesize($log_file) != 0) {
            $logs = file($log_file);
            $entry = "";
            foreach ($logs as $log) {
                if (strpos($log, 'Version:') !== false) {
                    if (!empty($entry)) {
                        echo $entry . "</ul></div>";
                    }
                    $entry = "<div class='log-entry'><h3>" . htmlentities(trim($log)) . "</h3><ul>";
                } elseif (strpos($log, '- AR:') !== false) {
                    // تقسيم التفاصيل بين العربية والإنجليزية
                    $parts = explode('| EN:', htmlentities(trim($log)));
                    $detail_ar = str_replace('- AR:', '', $parts[0]);
                    $detail_en = $parts[1] ?? '';

                    $entry .= "<li class='row'><span class='details-en'>$detail_en</span><span class='details-ar'>$detail_ar</span></li>";
                }
            }
            if (!empty($entry)) {
                echo $entry . "</ul></div>";
            }
        } else {
            echo "<center>لا توجد سجلات حتى الآن</center>";
        }
        ?>
    </div>
</body>

</html>