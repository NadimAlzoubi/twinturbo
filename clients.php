<?php
include_once('./header.php');
?>

<head>
    <title><?php echo translate('clients_database', $lang) . ' - ' . $translated_user_role; ?></title>
    <style>
        #clientsTable thead th,
        #clientsTable tfoot th,
        #clientsTable tbody td {
            white-space: nowrap;
            word-break: normal;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<div class="container">

    <!-- Form to Add New User -->
    <h2 class="mb-4"><?php echo translate('add_client', $lang); ?></h2>
    <form id="clientForm">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label"><?php echo translate('company_name', $lang); ?></label>
                <input type="text" class="form-control" name="company_name" required>
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo translate('contact_person', $lang); ?></label>
                <input type="text" class="form-control" name="contact_name" required>
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo translate('activity_type', $lang); ?></label>
                <input type="text" class="form-control" name="activity_type">
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo translate('phone_numbers', $lang); ?></label>
                <textarea class="form-control" name="phone" placeholder="<?php echo translate('multiple_phone_numbers_placeholder', $lang); ?>"></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo translate('whatsapp_numbers', $lang); ?></label>
                <textarea type="text" class="form-control" name="whatsapp" placeholder="<?php echo translate('multiple_whatsapp_numbers_placeholder', $lang); ?>"></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo translate('email_addresses', $lang); ?></label>
                <textarea class="form-control" name="email" placeholder="<?php echo translate('multiple_email_addresses_placeholder', $lang); ?>"></textarea>
            </div>

            <div class="col-md-3">
                <label class="form-label"><?php echo translate('customer_segment', $lang); ?></label>
                <select class="form-select" name="customer_segment">
                    <option value=""><?php echo translate('choose', $lang); ?></option>
                    <option value="small"><?php echo translate('small', $lang); ?></option>
                    <option value="medium"><?php echo translate('medium', $lang); ?></option>
                    <option value="large"><?php echo translate('large', $lang); ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo translate('avg_monthly_shipments', $lang); ?></label>
                <input type="number" class="form-control" name="avg_monthly_shipments">
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo translate('import_export_focus', $lang); ?></label>
                <select class="form-select" name="import_export_focus">
                    <option value=""><?php echo translate('choose', $lang); ?></option>
                    <option value="import"><?php echo translate('import', $lang); ?></option>
                    <option value="export"><?php echo translate('export', $lang); ?></option>
                    <option value="both"><?php echo translate('both', $lang); ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo translate('country', $lang); ?></label>
                <input type="text" class="form-control" name="country">
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo translate('city', $lang); ?></label>
                <input type="text" class="form-control" name="city">
            </div>
            <div class="col-md-9">
                <label class="form-label"><?php echo translate('address', $lang); ?></label>
                <input type="text" class="form-control" name="address">
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo translate('google_maps_url', $lang); ?></label>
                <input type="text" class="form-control" name="google_maps_url" placeholder="https://maps.google.com">
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo translate('authorization_status', $lang); ?></label>
                <select class="form-select" name="status">
                    <option value="not_authorized"><?php echo translate('not_authorized', $lang); ?></option>
                    <option value="authorized"><?php echo translate('authorized', $lang); ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo translate('account_manager', $lang); ?></label>
                <input type="text" class="form-control" name="account_manager">
            </div>
            <div class="col-md-6">
                <label class="form-label"><?php echo translate('last_contact_summary', $lang); ?></label>
                <textarea class="form-control" name="last_contact_summary" placeholder="<?php echo translate('what_happened', $lang); ?>"></textarea>
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo translate('last_activity_date', $lang); ?></label>
                <input type="datetime-local" id="last_activity_date" class="form-control" name="last_activity_date">
            </div>
            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary"><?php echo translate('save_changes', $lang); ?></button>
            </div>
            <input type="hidden" name="id" value="">
        </div>
    </form>





    <h2 class="mb-4 mt-5"><?php echo translate('clients_table', $lang); ?></h2>

    <table id="clientsTable" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th><?php echo translate('client_id', $lang); ?></th>
                <th><?php echo translate('actions', $lang); ?></th>
                <th><?php echo translate('company_name', $lang); ?></th>
                <th><?php echo translate('contact_person', $lang); ?></th>
                <th><?php echo translate('phone_numbers', $lang); ?></th>
                <th><?php echo translate('whatsapp_numbers', $lang); ?></th>
                <th><?php echo translate('email_addresses', $lang); ?></th>
                <th><?php echo translate('activity_type', $lang); ?></th>
                <th><?php echo translate('customer_segment', $lang); ?></th>
                <th><?php echo translate('import_export_focus', $lang); ?></th>
                <th><?php echo translate('avg_monthly_shipments', $lang); ?></th>
                <th><?php echo translate('country', $lang); ?></th>
                <th><?php echo translate('city', $lang); ?></th>
                <th><?php echo translate('address', $lang); ?></th>
                <th><?php echo translate('google_maps_url', $lang); ?></th>
                <th><?php echo translate('authorization_status', $lang); ?></th>
                <th><?php echo translate('account_manager', $lang); ?></th>
                <th><?php echo translate('last_contact_summary', $lang); ?></th>
                <th><?php echo translate('last_activity_date', $lang); ?></th>
                <th><?php echo translate('notes', $lang); ?></th>
        </thead>
        <?php /*
        <tfoot>
            <tr>
                <th><?php echo translate('client_id', $lang); ?></th>
                <th><?php echo translate('company_name', $lang); ?></th>
                <th><?php echo translate('contact_person', $lang); ?></th>
                <th><?php echo translate('phone_numbers', $lang); ?></th>
                <th><?php echo translate('whatsapp_numbers', $lang); ?></th>
                <th><?php echo translate('email_addresses', $lang); ?></th>
                <th><?php echo translate('activity_type', $lang); ?></th>
                <th><?php echo translate('customer_segment', $lang); ?></th>
                <th><?php echo translate('import_export_focus', $lang); ?></th>
                <th><?php echo translate('avg_monthly_shipments', $lang); ?></th>
                <th><?php echo translate('country', $lang); ?></th>
                <th><?php echo translate('city', $lang); ?></th>
                <th><?php echo translate('address', $lang); ?></th>
                <th><?php echo translate('google_maps_url', $lang); ?></th>
                <th><?php echo translate('authorization_status', $lang); ?></th>
                <th><?php echo translate('account_manager', $lang); ?></th>
                <th><?php echo translate('last_contact_summary', $lang); ?></th>
                <th><?php echo translate('last_activity_date', $lang); ?></th>
                <th><?php echo translate('notes', $lang); ?></th>
                <th><?php echo translate('actions', $lang); ?></th>
            </tr>
        </tfoot>
        */?>
    </table>



</div>
<?php
include_once('./footer.php');
?>



<script>
    $(document).ready(function() {
        // التحقق مما إذا كان الجدول معرفاً مسبقاً وتدميره
        if ($.fn.DataTable.isDataTable('#clientsTable')) {
            $('#clientsTable').DataTable().destroy();
            $('#clientsTable').empty(); // اختياري لتنظيف المحتوى تماماً
        }
        // إنشاء DataTable مرة واحدة
        var table = $('#clientsTable').DataTable({
            "destroy": true, // تأكيد تدمير أي نسخة قديمة
            "retrieve": true, // إذا فشل التدمير، استرجع النسخة الموجودة بدلاً من إظهار خطأ
            "scrollX": true,
            "paging": true,
            "searching": true,
            "ordering": true,
            "responsive": true,
            "lengthChange": true,
            "pageLength": 10,
            "autoWidth": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "clients_ajax.php",
                "type": "POST",
                "cache": true
            },
            "dom": 'Bfrtip',
            "buttons": [{
                extend: 'excelHtml5',
                text: 'Excel',
                title: 'Clients_Export'
            }],
            // "order": [[0, 'desc']],
            // "initComplete": function() {
            //     // إضافة input لكل footer لفلترة الأعمدة بعد الانتهاء من التهيئة
            //     this.api().columns().every(function(index) {
            //         // أرقام الأعمدة التي تريد تفعيل الفلتر لها فقط
            //         const searchableColumns = [0, 1, 2, 3, 4, 5, 6, 7, 8, 10, 11, 12, 13, 14, 15, 18];
            //         // 1 = اسم الشركة
            //         // 2 = الشخص المسؤول
            //         // 3 = أرقام الهاتف
            //         // 4 = أرقام الواتساب
            //         // 5 = عناوين البريد الإلكتروني
            //         // 6 = نوع النشاط
            //         // 7 = جزء العميل
            //         // 8 = تركيز الاستيراد والتصدير
            //         // 10 = الدولة
            //         // 11 = المدينة
            //         // 14 = الحالة
            //         // 15 = مدير الحساب
            //         // 18 = ملاحظات
                    
            //         if (!searchableColumns.includes(index)) {
            //             return; // تجاهل باقي الأعمدة
            //         }

            //         var column = this;
            //         var footerCell = $(column.footer());

            //         if (footerCell.length) {
            //             $('<input type="text" placeholder="بحث ' + footerCell.text() + '" />')
            //                 .appendTo(footerCell.empty())
            //                 .on('keyup change clear', function() {
            //                     if (column.search() !== this.value) {
            //                         column.search(this.value).draw();
            //                     }
            //                 });
            //         }
            //     });

            // },
            "columns": [{
                    "data": "id"
                },
                {
                    data: "actions",
                    orderable: false,
                    searchable: false
                },
                {
                    "data": "company_name"
                },
                {
                    "data": "contact_name"
                },
                {
                    "data": "phone"
                },
                {
                    "data": "whatsapp"
                },
                {
                    "data": "email"
                },
                {
                    "data": "activity_type"
                },
                {
                    "data": "customer_segment"
                },
                {
                    "data": "import_export_focus"
                },
                {
                    "data": "avg_monthly_shipments"
                },
                {
                    "data": "country"
                },
                {
                    "data": "city"
                },
                {
                    "data": "address"
                },
                {
                    "data": "google_maps_url"
                },
                {
                    "data": "status"
                },
                {
                    "data": "account_manager"
                },
                {
                    "data": "last_contact_summary"
                },
                {
                    "data": "last_activity_date"
                },
                {
                    "data": "notes"
                }
            ]
        });

        // إرسال الفورم عبر Ajax
        $('#clientForm').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();

            $.ajax({
                url: 'client_save.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                        table.ajax.reload(null, false); // تحديث الجدول بدون إعادة تهيئة
                        $('#clientForm')[0].reset();
                    } else {
                        alert("حدث خطأ: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert("حدث خطأ في الاتصال بالسيرفر.");
                }
            });
        });

        // تعبئة الفورم عند تعديل العميل
        window.editClient = function(clientId) {
            $.ajax({
                url: 'client_get.php',
                type: 'GET',
                data: {
                    id: clientId
                },
                dataType: 'json',
                success: function(data) {
                    if (data) {
                        for (let key in data) {
                            $('#clientForm [name="' + key + '"]').val(data[key]);
                        }
                    }
                }
            });
        }

    });
</script>