<!-- update_form.php -->
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل تحديث جديد</title>
    <style>
        body { font-family: Arial, sans-serif; direction: rtl; }
        form { max-width: 600px; margin: 50px auto; }
        label { display: block; margin-bottom: 10px; }
        input, textarea { width: 100%; padding: 8px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 10px 15px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #218838; }
        .add-more { background-color: #007bff; margin-top: 10px; }
        .row { display: flex; flex-direction: row-reverse; }
        .row div { flex: 1; margin-left: 10px; }
    </style>
</head>
<body>
    <h2>تسجيل تحديث جديد للنظام</h2>
    <form method="post">
        <label for="version">رقم الإصدار:</label>
        <input type="text" id="version" name="version" required>

        <label>تفاصيل التحديث:</label>
        <div id="update-details">
            <div class="row">
                <div>
                    <input name="details_en[]" placeholder="Details in English" required style="direction: ltr;">
                </div>
                <div>
                    <input name="details_ar[]" placeholder="تفاصيل بالعربية" required>
                </div>
            </div>
        </div>
        <button type="button" class="add-more" onclick="addDetail()">أضف تفاصيل أخرى</button>

        <label for="date">تاريخ التحديث:</label>
        <input type="date" id="date" name="date" required>

        <button type="submit">حفظ التحديث</button>
    </form>

    <script>
        function addDetail() {
            var detailDiv = document.createElement('div');
            detailDiv.className = 'row';
            detailDiv.innerHTML = `
                <div><input name="details_en[]" placeholder="Details in English" required style="direction: ltr;"></div>
                <div><input name="details_ar[]" placeholder="تفاصيل بالعربية" required></div>
            `;
            document.getElementById('update-details').appendChild(detailDiv);
        }
    </script>
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // جلب البيانات من الفورم
    $version = htmlspecialchars($_POST['version']);
    $details_ar = $_POST['details_ar']; // تفاصيل باللغة العربية
    $details_en = $_POST['details_en']; // تفاصيل باللغة الإنجليزية
    $date = htmlspecialchars($_POST['date']);

    // تنسيق البيانات لتخزينها مع التفاصيل
    $log_entry = "Version: $version | Date: $date | Updates:\n";
    for ($i = 0; $i < count($details_ar); $i++) {
        $log_entry .= "- AR: " . htmlspecialchars($details_ar[$i]) . " | EN: " . htmlspecialchars($details_en[$i]) . "\n";
    }
    $log_entry .= "\n"; // إضافة مسافة بين كل تحديث

    // قراءة محتوى ملف السجلات الحالي
    $current_logs = file_get_contents('version_logs.log');

    // إضافة السجل الجديد في البداية
    $new_logs = $log_entry . $current_logs;

    // كتابة السجلات الجديدة إلى الملف
    file_put_contents('version_logs.log', $new_logs, LOCK_EX);

    // إعادة توجيه إلى صفحة عرض السجلات بعد الحفظ
    header('Location: updates.php');
    exit();
}

?>
