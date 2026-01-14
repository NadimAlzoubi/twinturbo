<?php
// -----------تحديد المنطقة الزمنية---------------
if ($user_location  == 'sau') {
    date_default_timezone_set('Asia/Riyadh');
    $queryTimeZone = "SET SESSION time_zone = '+03:00'";
} else if ($user_location  == 'uae') {
    date_default_timezone_set('Asia/Dubai');
    $queryTimeZone = "SET SESSION time_zone = '+04:00'";
}
// mysqli_query($connection, $queryTimeZone);

function lo($data, $logFile = 'app.log')
{
    // تحديد توقيت تسجيل الرسالة
    $timestamp = date('Y-m-d H:i:s');

    // تحويل البيانات (إذا كانت مصفوفة أو كائن) إلى نص
    if (is_array($data) || is_object($data)) {
        $formattedData = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        $formattedData = $data; // إذا كانت نصًا عاديًا
    }

    // صيغة الرسالة المراد تسجيلها
    $logEntry = "[$timestamp] $formattedData" . PHP_EOL;

    // كتابة الرسالة إلى الملف
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}


// ----------------الحصول على كامل مستخدمين النظام----------------
function getAllUsers($limit = null)
{
    global $connection;
    $query = "SELECT * FROM users ORDER BY id DESC";
    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// ----------------الحصول على مستخدم حسب المعرف----------------
function getUserById($id)
{
    global $connection;
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// ---------------- العمليات على جدول المستخدمين ----------------
function users($option, $id = null)
{
    global $connection;
    global $lang;
    $error_msg = null;

    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $password = $_POST['password'];
    $full_name = mysqli_real_escape_string($connection, $_POST['full_name']);
    $role = mysqli_real_escape_string($connection, $_POST['role']);
    $status = isset($_POST['status']) ? $_POST['status'] : 0;
    $location = mysqli_real_escape_string($connection, $_POST['location']);
    $hashed_pass = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : null;

    $full_name = strtoupper($full_name);

    if ($option == 'i') {
        // التحقق من أن الحقول المطلوبة ليست فارغة
        if (empty($username) || empty($password) || empty($full_name) || empty($role) || empty($location)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            // التحقق من أن اسم المستخدم فريد باستخدام الدالة المعدلة
            $result = isUsernameUnique($connection, $username);
            if ($result['count'] > 0) {
                $error_msg .= translate('error_username_already_exists', $lang);
            } else {
                // إذا كان اسم المستخدم فريدًا، قم بإدراج البيانات
                $query = "INSERT INTO users (username, password, full_name, role, status, location) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($connection, $query);
                mysqli_stmt_bind_param($stmt, 'ssssis', $username, $hashed_pass, $full_name, $role, $status, $location);
            }
        }
    } else if ($option == 'u' && $id) {
        if (empty($username) || empty($full_name) || empty($role) || empty($location)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            // التحقق من أن اسم المستخدم فريد باستخدام الدالة المعدلة مع استثناء المستخدم الحالي
            $result = isUsernameUnique($connection, $username, $id);
            if ($result['count'] > 0 && $result['username'] != $username) {
                $error_msg .= translate('error_username_already_exists', $lang);
            } else {
                if ($hashed_pass) {
                    $query = "UPDATE users SET username = ?, password = ?, full_name = ?, role = ?, status = ?, location = ? WHERE id = ?";
                    $stmt = mysqli_prepare($connection, $query);
                    mysqli_stmt_bind_param($stmt, 'ssssisi', $username, $hashed_pass, $full_name, $role, $status, $location, $id);
                } else {
                    $query = "UPDATE users SET username = ?, full_name = ?, role = ?, status = ?, location = ? WHERE id = ?";
                    echo '<script>alert(' . $query . ')</script>';
                    $stmt = mysqli_prepare($connection, $query);
                    mysqli_stmt_bind_param($stmt, 'sssisi', $username, $full_name, $role, $status, $location, $id);
                }
            }
        }
    } else if ($option == 'd' && $id) {
        $query = "DELETE FROM users WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
    }

    if ($stmt && $error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./settings.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./settings.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
        mysqli_stmt_close($stmt);
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./settings.php";
            });
        </script>';
    }
}

// ----------------التحقق من تكرار اسم المستخدم----------------
function isUsernameUnique($connection, $username, $user_id = null)
{
    if ($user_id !== null) {
        $check_query = "SELECT COUNT(id), username FROM users WHERE username = ? AND id != ?";
        $stmt = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($stmt, 'si', $username, $user_id);
    } else {
        $check_query = "SELECT COUNT(id), username FROM users WHERE username = ?";
        $stmt = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($stmt, 's', $username);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_count, $retrieved_username);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return [
        'count' => $user_count,
        'username' => $retrieved_username
    ];
}

// ---------------- العمليات على جدول حساب البنك ----------------
function updateBankAccount($type)
{
    global $connection;
    global $lang;
    $error_msg = null;

    // تحديد الحقل الذي سيتم تحديثه بناءً على النوع
    $field = ($type == 'bank') ? 'account_amount' : (($type == 'tas') ? 'facilities_amount' : null);

    if ($field) {
        // الحصول على القيمة من POST
        $value = mysqli_real_escape_string($connection, $_POST[$field]);

        // التحقق من صحة القيمة
        if (!isset($value)) {
            $error_msg = translate('error_balance_is_required', $lang);
        } elseif (!is_numeric($value)) {
            $error_msg = translate('error_balance_must_be_numeric', $lang);
        }

        // إذا لم يكن هناك أخطاء، نقوم بتحديث السجل
        if ($error_msg == null) {
            // تكوين الاستعلام
            $query = "UPDATE bank_account SET $field = ? WHERE 1";

            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'd', $value);

            // تنفيذ الاستعلام
            if (mysqli_stmt_execute($stmt)) {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./settings.php";
                    });
                </script>';
            } else {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            }
        }
    } else {
        $error_msg = translate('error_invalid_type', $lang);  // في حال كان النوع غير صحيح
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./settings.php";
            });
        </script>';
    }
}








// ----------------الحصول على كامل السائقين----------------
function getAllDrivers($limit = null)
{
    global $connection;
    $query = "SELECT * FROM drivers ORDER BY id DESC";
    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


// ----------------الحصول على سائق حسب المعرف----------------
function getDriverById($id)
{
    global $connection;
    $query = "SELECT * FROM drivers WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}




// ---------------- العمليات على جدول السائقين ----------------
function drivers($option, $id = null)
{
    global $connection;
    global $lang;
    global $user_full_name;
    $error_msg = null;

    $driver_name = mysqli_real_escape_string($connection, $_POST['driver_name']);
    $vehicle_number = mysqli_real_escape_string($connection, $_POST['vehicle_number']);
    $phone_number = mysqli_real_escape_string($connection, $_POST['phone_number']);
    $driver_notes = mysqli_real_escape_string($connection, $_POST['driver_notes']);

    $driver_name = strtoupper($driver_name);
    $vehicle_number = strtoupper($vehicle_number);
    $phone_number = strtoupper($phone_number);
    $driver_notes = strtoupper($driver_notes);


    if ($option == 'i') {
        if (empty($driver_name) || empty($vehicle_number)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO drivers (driver_name, vehicle_number, phone, notes, created_by) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sssss', $driver_name, $vehicle_number, $phone_number, $driver_notes, $user_full_name);
        }
    } else if ($option == 'u' && $id) {
        if (empty($driver_name) || empty($vehicle_number)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "UPDATE drivers SET driver_name = ?, vehicle_number = ?, phone = ?, notes = ?, updated_by = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sssssi', $driver_name, $vehicle_number, $phone_number, $driver_notes, $user_full_name, $id);
        }
    } else if ($option == 'd' && $id) {
        $check_query = "SELECT COUNT(id) AS related_trips FROM trips WHERE driver_id = ?";
        $stmt_check = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($stmt_check, 'i', $id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);

        if ($row_check['related_trips'] == 0) {
            $query = "DELETE FROM drivers WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id);
        } else {
            $error_msg .= translate('error_cannot_delete_driver_linked_to_trips', $lang);
        }
    }


    if ($error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./drivers.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./drivers.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./drivers.php";
            });
        </script>';
    }
}



// ----------------الحصول على كامل انواع مصاريف الرحلات----------------
function getTripFeesTypes($limit = null)
{
    global $connection;
    $query = "SELECT * FROM trip_fees_types ORDER BY id DESC";
    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}



// ---------------- العمليات على جدول انواع مصاريف الرحلات ----------------
function tripFeesTypes($option, $id = null)
{
    global $connection;
    global $lang;
    $error_msg = null;

    $description = mysqli_real_escape_string($connection, $_POST['description']);
    $amount = mysqli_real_escape_string($connection, $_POST['amount']);

    $description = strtoupper($description);


    if ($option == 'i') {
        if (empty($description)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO trip_fees_types (fee_name, fee_amount) VALUES (?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sd', $description, $amount);
        }
    } else if ($option == 'u' && $id) {
        if (empty($description)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "UPDATE trip_fees_types SET fee_name = ?, fee_amount = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sdi', $description, $amount, $id);
        }
    } else if ($option == 'd' && $id) {
        $check_query = "SELECT COUNT(id) AS related_fees_types FROM trip_fees WHERE trip_fee_type_id = ?";
        $stmt_check = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($stmt_check, 'i', $id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);
        if ($row_check['related_fees_types'] == 0) {
            $query = "DELETE FROM trip_fees_types WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id);
        } else {
            $error_msg .= translate('error_cannot_delete_expense_type_linked_to_trips', $lang);
        }
    }

    if ($error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./trips.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./trips.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./trips.php";
            });
        </script>';
    }
}




// ----------------الحصول على كامل الرحلات----------------
function getAllTrips($limit = null)
{
    global $connection;

    $query = "SELECT trips.*,
                drivers.driver_name,
                drivers.vehicle_number,
                COALESCE(fees.fee_description, '') AS fee_description,
                COALESCE(fees.fee_amount, '') AS fee_amount,
                COALESCE(fees_types.fee_type_name, '') AS fee_type_name,
                COALESCE(fees_types.fee_type_amount, '') AS fee_type_amount
            FROM 
                trips
            JOIN 
                drivers ON trips.driver_id = drivers.id
            LEFT JOIN (
                SELECT 
                    trip_id, 
                    GROUP_CONCAT(description SEPARATOR ', ') as fee_description,
                    GROUP_CONCAT(amount SEPARATOR ', ') as fee_amount
                FROM 
                    trip_fees
                GROUP BY 
                    trip_id
            ) as fees ON trips.id = fees.trip_id
            LEFT JOIN (
                SELECT 
                    trip_fees.trip_id,
                    GROUP_CONCAT(trip_fees_types.fee_name SEPARATOR ', ') as fee_type_name,
                    GROUP_CONCAT(trip_fees_types.fee_amount SEPARATOR ', ') as fee_type_amount
                FROM 
                    trip_fees
                JOIN 
                    trip_fees_types ON trip_fees.trip_fee_type_id = trip_fees_types.id
                GROUP BY 
                    trip_fees.trip_id
            ) as fees_types ON trips.id = fees_types.trip_id
            ORDER BY 
                trips.id DESC";

    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }

    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


// ----------------الحصول على رحلة حسب المعرف----------------
function getTripById($id)
{
    global $connection;
    $query = "SELECT * FROM trips WHERE id = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}





// ------------ العمليات على الرحلات والمصاريف----------
function trips($option, $id = null)
{
    global $connection;
    global $lang;
    global $user_full_name;
    $error_msg = null;

    $driver_id_input = mysqli_real_escape_string($connection, $_POST['driver_id']);
    $hidden_driver_id = mysqli_real_escape_string($connection, $_POST['hidden_driver_id']);

    if ($driver_id_input) {
        $driver_id_parts = explode('-', $driver_id_input);
        $driver_id = trim($driver_id_parts[0]);
    } else {
        $driver_id = $hidden_driver_id;
    }

    $trip_date = mysqli_real_escape_string($connection, $_POST['trip_date']);
    $destination = strtoupper(mysqli_real_escape_string($connection, $_POST['destination']));
    $trip_rent = mysqli_real_escape_string($connection, $_POST['trip_rent']);
    $extra_income = mysqli_real_escape_string($connection, $_POST['extra_income']);
    $extra_income_des = strtoupper(mysqli_real_escape_string($connection, $_POST['extra_income_des']));
    $driver_fee = mysqli_real_escape_string($connection, $_POST['driver_fee']);
    $remaining = mysqli_real_escape_string($connection, $_POST['remaining']);
    $notes = strtoupper(mysqli_real_escape_string($connection, $_POST['notes']));

    // البيانات الخاصة بالمصاريف
    $fee_ids = $_POST['fee_ids'] ?? [];
    $quantity = $_POST['quantity'];
    $amount = $_POST['fee_amount'];
    $description = $_POST['description'];

    // fees
    $fee_type_inputs = $_POST['fee_type'];
    $fee_type_ids = array();
    foreach ($fee_type_inputs as $fee_type_input) {
        $fee_type_parts = explode('-', $fee_type_input);
        $fee_type_id = trim($fee_type_parts[0]);
        $fee_type_ids[] = mysqli_real_escape_string($connection, $fee_type_id);
    }

    if ($option == 'i') {
        // إضافة رحلة جديدة
        if (empty($driver_id) || empty($trip_date) || empty($trip_rent) || empty($destination)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            // إدخال بيانات الرحلة
            $query = "INSERT INTO trips (driver_id, trip_date, destination, trip_rent, extra_income, extra_income_des, driver_fee, remaining, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'issddssdss', $driver_id, $trip_date, $destination, $trip_rent, $extra_income, $extra_income_des, $driver_fee, $remaining, $notes, $user_full_name);

            if (mysqli_stmt_execute($stmt)) {
                $trip_id = mysqli_insert_id($connection);  // الحصول على ID الرحلة المدخلة

                // إدخال بيانات المصاريف المرتبطة بالرحلة
                foreach ($fee_type_ids as $index => $fee_type_id) {
                    if (!empty($fee_type_id) && !empty($quantity[$index]) && !empty($amount[$index])) {
                        $query_fees = "INSERT INTO trip_fees (trip_id, trip_fee_type_id, quantity, amount, description) VALUES (?, ?, ?, ?, ?)";
                        $stmt_fees = mysqli_prepare($connection, $query_fees);
                        $desc = strtoupper(mysqli_real_escape_string($connection, $description[$index]));
                        mysqli_stmt_bind_param($stmt_fees, 'iiids', $trip_id, $fee_type_id, $quantity[$index], $amount[$index], $desc);
                        mysqli_stmt_execute($stmt_fees);
                    }
                }
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            }
        }
    } else if ($option == 'u' && $id) {
        // تحديث بيانات الرحلة والمصاريف
        if (empty($driver_id) || empty($trip_date) || empty($trip_rent) || empty($destination)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            // تحديث بيانات الرحلة
            $query = "UPDATE trips SET driver_id = ?, trip_date = ?, destination = ?, trip_rent = ?, extra_income = ?, extra_income_des = ?, driver_fee = ?, remaining = ?, notes = ?, updated_at = NOW(), updated_by = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'issddssdssi', $driver_id, $trip_date, $destination, $trip_rent, $extra_income, $extra_income_des, $driver_fee, $remaining, $notes, $user_full_name, $id);

            if (mysqli_stmt_execute($stmt)) {

                // استرداد جميع الرسوم الحالية المرتبطة بالرحلة
                $query_existing_fees = "SELECT id FROM trip_fees WHERE trip_id = ?";
                $stmt_existing_fees = mysqli_prepare($connection, $query_existing_fees);
                mysqli_stmt_bind_param($stmt_existing_fees, 'i', $id);
                mysqli_stmt_execute($stmt_existing_fees);
                $result_existing_fees = mysqli_stmt_get_result($stmt_existing_fees);
                $existing_fees = mysqli_fetch_all($result_existing_fees, MYSQLI_ASSOC);

                // قائمة الرسوم الجديدة المرسلة من الواجهة
                $new_fee_ids = array_filter($fee_ids); // هذا يزيل العناصر الفارغة من القائمة

                // حذف الرسوم التي لم تعد موجودة في الواجهة
                foreach ($existing_fees as $existing_fee) {
                    if (!in_array($existing_fee['id'], $new_fee_ids)) {
                        $query_delete_fee = "DELETE FROM trip_fees WHERE id = ?";
                        $stmt_delete_fee = mysqli_prepare($connection, $query_delete_fee);
                        mysqli_stmt_bind_param($stmt_delete_fee, 'i', $existing_fee['id']);
                        mysqli_stmt_execute($stmt_delete_fee);
                    }
                }

                // تحديث بيانات المصاريف المرتبطة بالرحلة
                foreach ($fee_type_ids as $index => $fee_type_id) {
                    $fee_id = $fee_ids[$index];
                    if (!empty($fee_id)) {
                        // إذا كان هناك id، نقوم بتحديث السجل
                        if (!empty($fee_type_id) && !empty($quantity[$index]) && !empty($amount[$index])) {
                            $query_fees = "UPDATE trip_fees SET trip_fee_type_id = ?, quantity = ?, amount = ?, description = ? WHERE id = ?";
                            $stmt_fees = mysqli_prepare($connection, $query_fees);
                            $desc = strtoupper(mysqli_real_escape_string($connection, $description[$index]));
                            mysqli_stmt_bind_param($stmt_fees, 'iidsi', $fee_type_id, $quantity[$index], $amount[$index], $desc, $fee_id);
                            mysqli_stmt_execute($stmt_fees);
                        }
                    } else {
                        // إذا لم يكن هناك id، نقوم بإدراج سجل جديد
                        if (!empty($fee_type_id) && !empty($quantity[$index]) && !empty($amount[$index])) {
                            $query_fees = "INSERT INTO trip_fees (trip_id, trip_fee_type_id, quantity, amount, description) VALUES (?, ?, ?, ?, ?)";
                            $stmt_fees = mysqli_prepare($connection, $query_fees);
                            $desc = strtoupper(mysqli_real_escape_string($connection, $description[$index]));
                            mysqli_stmt_bind_param($stmt_fees, 'iiids', $id, $fee_type_id, $quantity[$index], $amount[$index], $desc);
                            mysqli_stmt_execute($stmt_fees);
                        }
                    }
                }
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./trips.php";
                    });
                </script>';
            } else {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            }
        }
    } else if ($option == 'd' && $id) {
        // حذف الرحلة والمصاريف المرتبطة بها
        $query = "DELETE FROM trip_fees WHERE trip_id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);

        $query = "DELETE FROM trips WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "' . translate('deleted_successfully', $lang) . '",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "./trips.php";
                });
            </script>';
        } else {
            $error_msg = translate('error_could_not_delete_the_record', $lang);
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./trips.php";
            });
        </script>';
    }
}


// ----------------الحصول على بيانات السعودية----------------
function getAllSauBills($limit = null)
{
    global $connection;
    $query = "SELECT 
            b.*,
            o.office_name, 
            o.license_number
        FROM sau_bills b
        LEFT JOIN sau_offices o ON b.sau_office_id = o.id
        ORDER BY b.id DESC
    ";

    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }

    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}





// ----------------الحصول على المكاتب السعودية----------------
function getAllSauOffices($limit = null)
{
    global $connection;
    $query = "SELECT * FROM sau_offices ORDER BY id DESC";

    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }

    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// ---------------- العمليات على جدول المكاتب السعودية ----------------
function sauOffices($option, $id = null)
{
    global $connection;
    global $lang;
    global $user_full_name;
    $error_msg = null;

    $office_name = mysqli_real_escape_string($connection, $_POST['office_name']);
    $entity_type = mysqli_real_escape_string($connection, $_POST['entity_type']);
    $license_number = mysqli_real_escape_string($connection, $_POST['license_number']);
    $notes = mysqli_real_escape_string($connection, $_POST['notes']);

    $office_name = strtoupper($office_name);
    $entity_type = strtolower($entity_type);
    $license_number = strtoupper($license_number);
    $notes = strtoupper($notes);


    if ($option == 'i') {
        if (empty($office_name) || empty($license_number) || empty($entity_type)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO sau_offices (office_name, entity_type, license_number, notes, created_by) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sssss', $office_name, $entity_type, $license_number, $notes, $user_full_name);
        }
    } else if ($option == 'u' && $id) {
        if (empty($office_name) || empty($license_number) || empty($entity_type)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "UPDATE sau_offices SET office_name = ?, entity_type = ?, license_number = ?, notes = ?, updated_by = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'ssssii', $office_name, $entity_type, $license_number, $notes, $user_full_name, $id);
        }
    } else if ($option == 'd' && $id) {
        $check_query = "SELECT COUNT(id) AS related_sau_bills FROM sau_bills WHERE sau_office_id = ?";
        $stmt_check = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($stmt_check, 'i', $id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);

        if ($row_check['related_sau_bills'] == 0) {
            $query = "DELETE FROM sau_offices WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id);
        } else {
            $error_msg .= translate('error_cannot_delete_saudi_office_linked_to_saudi_bills', $lang);
        }
    }


    if ($error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./sau_bills.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./sau_bills.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./sau_bills.php";
            });
        </script>';
    }
}

// ---------------- العمليات على البيانات السعودية  ----------------
function sauBills($option, $id = null)
{
    global $connection;
    global $lang;
    global $user_full_name;
    $error_msg = null;


    $sau_office_id_input = mysqli_real_escape_string($connection, $_POST['sau_office_id']);
    $hidden_sau_office_id = mysqli_real_escape_string($connection, $_POST['hidden_sau_office_id']);

    if ($sau_office_id_input) {
        $sau_office_id_parts = explode('-', $sau_office_id_input);
        $sau_office_id = trim($sau_office_id_parts[0]);
    } else {
        $sau_office_id = $hidden_sau_office_id;
    }


    $driver_name = mysqli_real_escape_string($connection, $_POST['driver_name']);
    $sau_bill_number = mysqli_real_escape_string($connection, $_POST['sau_bill_number']);
    $price = mysqli_real_escape_string($connection, $_POST['price']);
    $payment_status = mysqli_real_escape_string($connection, $_POST['payment_status']);
    $bill_date = mysqli_real_escape_string($connection, $_POST['bill_date']);
    $vehicle_number = mysqli_real_escape_string($connection, $_POST['vehicle_number']);
    $destination = mysqli_real_escape_string($connection, $_POST['destination']);
    $notes = mysqli_real_escape_string($connection, $_POST['notes']);
    $nob = mysqli_real_escape_string($connection, $_POST['nob']);
    $nov = mysqli_real_escape_string($connection, $_POST['nov']);

    $driver_name = strtoupper($driver_name);
    $vehicle_number = strtoupper($vehicle_number);
    $destination = strtoupper($destination);
    $notes = strtoupper($notes);


    if ($option == 'i') {
        if (empty($sau_office_id) || empty($driver_name) || empty($sau_bill_number) || empty($bill_date) || empty($price) || empty($vehicle_number) || empty($nob) || empty($nov) || empty($destination)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO sau_bills (bill_date, sau_office_id, sau_bill_number, driver_name, vehicle_number, nob, nov, destination, price, payment_status, notes, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sisssiisdsss', $bill_date, $sau_office_id, $sau_bill_number, $driver_name, $vehicle_number, $nob, $nov, $destination, $price, $payment_status, $notes, $user_full_name);
        }
    } else if ($option == 'u' && $id) {
        if (empty($sau_office_id) || empty($driver_name) || empty($sau_bill_number) || empty($bill_date) || empty($price) || empty($vehicle_number) || empty($nob) || empty($nov) || empty($destination)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "UPDATE sau_bills SET bill_date = ?, sau_office_id = ?, sau_bill_number = ?, driver_name = ?, vehicle_number = ?, nob = ?, nov = ?, destination = ?, price = ?, payment_status = ?, notes = ?, updated_by = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sisssiisdsssi', $bill_date, $sau_office_id, $sau_bill_number, $driver_name, $vehicle_number, $nob, $nov, $destination, $price, $payment_status, $notes, $user_full_name, $id);
        }
    } else if ($option == 'd' && $id) {
        $query = "DELETE FROM sau_bills WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
    }


    if ($error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./sau_bills.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./sau_bills.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./sau_bills.php";
            });
        </script>';
    }
}

// ----------------الحصول على كامل الخدمات----------------
// ----------------الحصول على كامل الخدمات----------------
function getAllServices($limit = null)
{
    global $connection;

    $query = "SELECT services.*,
                GROUP_CONCAT(service_fees.description SEPARATOR ', ') AS fee_description,
                GROUP_CONCAT(service_fees.amount SEPARATOR ', ') AS fee_amount,
                GROUP_CONCAT(service_fees.bank_deduction_amount SEPARATOR ', ') AS bank_deduction_amount,
                GROUP_CONCAT(service_fees_types.fee_name SEPARATOR ', ') AS fee_type_name,
                GROUP_CONCAT(service_fees_types.fee_amount SEPARATOR ', ') AS fee_type_amount
            FROM 
                services
            LEFT JOIN service_fees ON services.id = service_fees.service_id
            LEFT JOIN service_fees_types ON service_fees.service_fee_type_id = service_fees_types.id
            GROUP BY services.id
            ORDER BY services.id DESC";

    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }

    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// ----------------الحصول على كامل انواع رسوم الخدمات----------------
function getServiceFeesTypes($limit = null)
{
    global $connection;
    $query = "SELECT * FROM service_fees_types ORDER BY id DESC";
    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// ---------------- العمليات على انواع رسوم الخدمات ----------------
function serviceFeesTypes($option, $id = null)
{
    global $connection;
    global $lang;
    $error_msg = null;

    $description = mysqli_real_escape_string($connection, $_POST['description']);
    $amount = mysqli_real_escape_string($connection, $_POST['amount']);
    $bank_deduction = mysqli_real_escape_string($connection, $_POST['bank_deduction']);

    $description = strtoupper($description);


    if ($option == 'i') {
        if (empty($description)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO service_fees_types(fee_name, fee_amount, bank_deduction) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sdd', $description, $amount, $bank_deduction);
        }
    } else if ($option == 'u' && $id) {
        if (empty($description)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "UPDATE service_fees_types SET fee_name = ?, fee_amount = ?, bank_deduction = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sddi', $description, $amount, $bank_deduction, $id);
        }
    } else if ($option == 'd' && $id) {
        $check_query = "SELECT COUNT(id) AS related_fees_types FROM service_fees WHERE service_fee_type_id = ?";
        $stmt_check = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($stmt_check, 'i', $id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);
        if ($row_check['related_fees_types'] == 0) {
            $query = "DELETE FROM service_fees_types WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id);
        } else {
            $error_msg .= translate('error_cannot_delete_fees_type_linked_to_services', $lang);
        }
    }

    if ($error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./services.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./services.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./services.php";
            });
        </script>';
    }
}

function updateBank($op, $new_amount, $old_amount = null, $c = null)
{
    global $connection;
    $col = $c == 'm' ? 'account_amount' : 'facilities_amount';
    // تأكد من تعيين المعرف الصحيح للحساب
    $account_id = 1;

    if ($op === 'i') { // Insert: خصم القيمة الجديدة
        $query_update = "UPDATE bank_account SET $col = ($col - ?) WHERE id = ?";
        $stmt_update = mysqli_prepare($connection, $query_update);
        mysqli_stmt_bind_param($stmt_update, 'di', $new_amount, $account_id);
    } else if ($op === 'u' && $old_amount !== null) { // Update: إعادة القيمة القديمة ثم خصم القيمة الجديدة
        $query_update = "UPDATE bank_account SET $col = (($col + ?) - ?) WHERE id = ?";
        $stmt_update = mysqli_prepare($connection, $query_update);
        mysqli_stmt_bind_param($stmt_update, 'ddi', $old_amount, $new_amount, $account_id);
    } else if ($op === 'd' && $old_amount !== null) { // Delete: إعادة القيمة القديمة
        $query_update = "UPDATE bank_account SET $col = ($col + ?) WHERE id = ?";
        $stmt_update = mysqli_prepare($connection, $query_update);
        mysqli_stmt_bind_param($stmt_update, 'di', $old_amount, $account_id);
    } else {
        // حالة غير مدعومة أو قيم غير صحيحة
        echo "Invalid operation or missing old amount";
        return;
    }

    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);
}

function updateBankExpenses($op, $n, $f, $new_amount, $old_amount = null)
{
    global $connection;

    // تأكد من تعيين المعرف الصحيح للحساب
    $account_id = 1;
    $update_fields = [];

    if ($op === 'i') { 
        if ($n == 2) { // Insert: ادراج القيم
            $update_fields[] = "account_amount = (account_amount + ?)";
        } elseif ($n == 3) {
            $update_fields[] = "account_amount = (account_amount - ?)";
        }
        if ($f == 2) {
            $update_fields[] = "facilities_amount = (facilities_amount + ?)";
        } elseif ($f == 3) {
            $update_fields[] = "facilities_amount = (facilities_amount - ?)";
        }
        if (!empty($update_fields)) {
            $query_update = "UPDATE bank_account SET " . implode(", ", $update_fields) . " WHERE id = ?";
        }
        if ($n == 1 && $f == 1) {
            return;
        }
        $stmt_update = mysqli_prepare($connection, $query_update);
        if ($n != 1 && $f != 1) {
            mysqli_stmt_bind_param($stmt_update, 'ddi', $new_amount, $new_amount, $account_id);
        } else {
            mysqli_stmt_bind_param($stmt_update, 'di', $new_amount, $account_id);
        }
    } else if ($op === 'u' && $old_amount !== null) { // Update: إعادة القيمة القديمة ثم خصم القيمة الجديدة
        if ($n == 2) {
            $update_fields[] = "account_amount = ((account_amount - ?) + ?)";
        } elseif ($n == 3) {
            $update_fields[] = "account_amount = ((account_amount + ?) - ?)";
        }
        if ($f == 2) {
            $update_fields[] = "facilities_amount = ((facilities_amount - ?) + ?)";
        } elseif ($f == 3) {
            $update_fields[] = "facilities_amount = ((facilities_amount + ?) - ?)";
        }
        if (!empty($update_fields)) {
            $query_update = "UPDATE bank_account SET " . implode(", ", $update_fields) . " WHERE id = ?";
        }
        if ($n == 1 && $f == 1) {
            return;
        }
        $stmt_update = mysqli_prepare($connection, $query_update);
        if ($n != 1 && $f != 1) {
            mysqli_stmt_bind_param($stmt_update, 'ddddi', $old_amount, $new_amount, $old_amount, $new_amount, $account_id);
        } else {
            mysqli_stmt_bind_param($stmt_update, 'ddi', $new_amount, $account_id);
        }
    } else if ($op === 'd' && $old_amount !== null) { // Delete: إعادة القيمة القديمة
        if ($n == 2) {
            $update_fields[] = "account_amount = (account_amount - ?)";
        } elseif ($n == 3) {
            $update_fields[] = "account_amount = (account_amount + ?)";
        }
        if ($f == 2) {
            $update_fields[] = "facilities_amount = (facilities_amount - ?)";
        } elseif ($f == 3) {
            $update_fields[] = "facilities_amount = (facilities_amount + ?)";
        }
        if (!empty($update_fields)) {
            $query_update = "UPDATE bank_account SET " . implode(", ", $update_fields) . " WHERE id = ?";
        }
        if ($n == 1 && $f == 1) {
            return;
        }
        $stmt_update = mysqli_prepare($connection, $query_update);
        if ($n != 1 && $f != 1) {
            mysqli_stmt_bind_param($stmt_update, 'ddi', $old_amount, $old_amount, $account_id);
        } else {
            mysqli_stmt_bind_param($stmt_update, 'di', $old_amount, $account_id);
        }
    } else {
        // حالة غير مدعومة أو قيم غير صحيحة
        echo "Invalid operation or missing old amount";
        return;
    }

    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);
}


// ------------ العمليات على الرحلات والمصاريف----------
function services($option, $id = null)
{
    global $connection;
    global $lang;
    global $user_full_name;
    $error_msg = null;


    $driver_name = mysqli_real_escape_string($connection, $_POST['driver_name']);
    $vehicle_number = strtoupper(mysqli_real_escape_string($connection, $_POST['vehicle_number']));
    $notes = mysqli_real_escape_string($connection, $_POST['notes']);
    $service_date = mysqli_real_escape_string($connection, $_POST['service_date']);
    $payment_status = mysqli_real_escape_string($connection, $_POST['payment_status']);
    $phone_number = strtoupper(mysqli_real_escape_string($connection, $_POST['phone_number']));
    $nov = mysqli_real_escape_string($connection, $_POST['nov']);

    $driver_name = strtoupper($driver_name);
    $vehicle_number = strtoupper($vehicle_number);
    $notes = strtoupper($notes);
    $phone_number = strtoupper($phone_number);



    // البيانات الخاصة بالمصاريف
    $fee_ids = $_POST['fee_ids'] ?? [];
    $quantity = $_POST['quantity'];
    $amount = $_POST['fee_amount'];
    $description = $_POST['description'];


    $new_bank_amount = $_POST['bank_deduction'] ?? [];
    // تأكد من أن $new_bank_amount هو مصفوفة
    if (!is_array($new_bank_amount)) {
        $new_bank_amount = [];
    }
    $total_new_bank_amount = array_sum(array_map('floatval', $new_bank_amount));

    if (isset($_GET['old-bank-amount'])) {
        $old_bank_amount = $_GET['old-bank-amount'];
    }


    // fees
    $fee_type_inputs = $_POST['fee_type'];
    $fee_type_ids = array();
    foreach ($fee_type_inputs as $fee_type_input) {
        $fee_type_parts = explode('-', $fee_type_input);
        $fee_type_id = trim($fee_type_parts[0]);
        $fee_type_ids[] = mysqli_real_escape_string($connection, $fee_type_id);
    }

    if ($option == 'i') {
        // إضافة رحلة جديدة
        if (empty($driver_name) || empty($vehicle_number) || empty($nov) || empty($service_date)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            // إدخال بيانات الرحلة
            $query = "INSERT INTO services(service_date, payment_status, driver_name, vehicle_number, nov, phone_number, notes, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'ssssisss', $service_date, $payment_status, $driver_name, $vehicle_number, $nov, $phone_number, $notes, $user_full_name);

            if (mysqli_stmt_execute($stmt)) {
                $service_id = mysqli_insert_id($connection);  // الحصول على ID الرحلة المدخلة

                // إدخال بيانات المصاريف المرتبطة بالرحلة
                foreach ($fee_type_ids as $index => $fee_type_id) {
                    if (!empty($fee_type_id) && !empty($quantity[$index]) && !empty($amount[$index])) {
                        $query_fees = "INSERT INTO service_fees(service_id, service_fee_type_id, quantity, amount, bank_deduction_amount, description) VALUES (?, ?, ?, ?, ?, ?)";
                        $stmt_fees = mysqli_prepare($connection, $query_fees);
                        $desc = strtoupper(mysqli_real_escape_string($connection, $description[$index]));
                        mysqli_stmt_bind_param($stmt_fees, 'iiidds', $service_id, $fee_type_id, $quantity[$index], $amount[$index], $new_bank_amount[$index], $desc);
                        mysqli_stmt_execute($stmt_fees);
                    }
                }

                updateBank('i', $total_new_bank_amount, null, 'm');

                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            }
        }
    } else if ($option == 'u' && $id) {
        // تحديث بيانات الرحلة والمصاريف
        if (empty($driver_name) || empty($vehicle_number) || empty($nov) || empty($service_date)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            // تحديث بيانات الرحلة
            $query = "UPDATE services SET service_date = ?, payment_status = ?, driver_name = ?, vehicle_number = ?, nov = ?, phone_number = ?, notes = ?, updated_by = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'ssssisssi', $service_date, $payment_status, $driver_name, $vehicle_number, $nov, $phone_number, $notes, $user_full_name, $id);

            if (mysqli_stmt_execute($stmt)) {

                // استرداد جميع الرسوم الحالية المرتبطة بالرحلة
                $query_existing_fees = "SELECT id FROM service_fees WHERE service_id = ?";
                $stmt_existing_fees = mysqli_prepare($connection, $query_existing_fees);
                mysqli_stmt_bind_param($stmt_existing_fees, 'i', $id);
                mysqli_stmt_execute($stmt_existing_fees);
                $result_existing_fees = mysqli_stmt_get_result($stmt_existing_fees);
                $existing_fees = mysqli_fetch_all($result_existing_fees, MYSQLI_ASSOC);

                // قائمة الرسوم الجديدة المرسلة من الواجهة
                $new_fee_ids = array_filter($fee_ids); // هذا يزيل العناصر الفارغة من القائمة

                // حذف الرسوم التي لم تعد موجودة في الواجهة
                foreach ($existing_fees as $existing_fee) {
                    if (!in_array($existing_fee['id'], $new_fee_ids)) {
                        $query_delete_fee = "DELETE FROM service_fees WHERE id = ?";
                        $stmt_delete_fee = mysqli_prepare($connection, $query_delete_fee);
                        mysqli_stmt_bind_param($stmt_delete_fee, 'i', $existing_fee['id']);
                        mysqli_stmt_execute($stmt_delete_fee);
                    }
                }

                // تحديث بيانات المصاريف المرتبطة بالرحلة
                foreach ($fee_type_ids as $index => $fee_type_id) {
                    $fee_id = $fee_ids[$index];
                    if (!empty($fee_id)) {
                        // إذا كان هناك id، نقوم بتحديث السجل
                        if (!empty($fee_type_id) && !empty($quantity[$index]) && !empty($amount[$index])) {
                            $query_fees = "UPDATE service_fees SET service_fee_type_id = ?, quantity = ?, amount = ?, bank_deduction_amount = ?, description = ? WHERE id = ?";
                            $stmt_fees = mysqli_prepare($connection, $query_fees);
                            $desc = strtoupper(mysqli_real_escape_string($connection, $description[$index]));
                            mysqli_stmt_bind_param($stmt_fees, 'iiddsi', $fee_type_id, $quantity[$index], $amount[$index], $new_bank_amount[$index], $desc, $fee_id);
                            mysqli_stmt_execute($stmt_fees);
                        }
                    } else {
                        // إذا لم يكن هناك id، نقوم بإدراج سجل جديد
                        if (!empty($fee_type_id) && !empty($quantity[$index]) && !empty($amount[$index])) {
                            $query_fees = "INSERT INTO service_fees (service_id, service_fee_type_id, quantity, amount, bank_deduction_amount, description) VALUES (?, ?, ?, ?, ?, ?)";
                            $stmt_fees = mysqli_prepare($connection, $query_fees);
                            $desc = strtoupper(mysqli_real_escape_string($connection, $description[$index]));
                            mysqli_stmt_bind_param($stmt_fees, 'iiidds', $id, $fee_type_id, $quantity[$index], $amount[$index], $new_bank_amount[$index], $desc);
                            mysqli_stmt_execute($stmt_fees);
                        }
                    }
                }

                updateBank('u', $total_new_bank_amount, $old_bank_amount, 'm');

                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./services.php";
                    });
                </script>';
            } else {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            }
        }
    } else if ($option == 'd' && $id) {
        // حذف الرحلة والمصاريف المرتبطة بها
        $query = "DELETE FROM service_fees WHERE service_id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);

        $query = "DELETE FROM services WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "' . translate('deleted_successfully', $lang) . '",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "./services.php";
                });
            </script>';
        } else {
            $error_msg = translate('error_could_not_delete_the_record', $lang);
        }
        updateBank('d', null, $old_bank_amount, 'm');
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./services.php";
            });
        </script>';
    }
}

// ----------------الحصول على انواع المصاريف----------------
function getAllExpensesTypes($limit = null)
{
    global $connection;
    $query = "SELECT * FROM expenses_types ORDER BY id DESC";

    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }

    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}



// ---------------- العمليات على انواع المصاريف  ----------------
function expensesTypes($option, $id = null)
{
    global $connection;
    global $lang;
    $error_msg = null;


    $expense_type_name = mysqli_real_escape_string($connection, $_POST['expense_type_name']);
    $expense_type_amount = mysqli_real_escape_string($connection, $_POST['expense_type_amount']);
    $expense_type_notes = mysqli_real_escape_string($connection, $_POST['expense_type_notes']);

    $expense_type_name = strtoupper($expense_type_name);
    $expense_type_notes = strtoupper($expense_type_notes);


    if ($option == 'i') {
        if (empty($expense_type_name)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO expenses_types(name, amount, notes)
            VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sds', $expense_type_name, $expense_type_amount, $expense_type_notes);
        }
    } else if ($option == 'u' && $id) {
        if (empty($expense_type_name)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "UPDATE expenses_types SET name = ?, amount = ?, notes = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sdsi', $expense_type_name, $expense_type_amount, $expense_type_notes, $id);
        }
    } else if ($option == 'd' && $id) {
        $check_query = "SELECT COUNT(id) AS related_expenses_types FROM expenses WHERE expense_type_id = ?";
        $stmt_check = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($stmt_check, 'i', $id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);
        if ($row_check['related_expenses_types'] == 0) {
            $query = "DELETE FROM expenses_types WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id);
        } else {
            $error_msg .= translate('error_cannot_delete_expense_type_linked_to_expenses', $lang);
        }
    }


    if ($error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./expenses.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./expenses.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./expenses.php";
            });
        </script>';
    }
}

// ----------------الحصول على المصاريف----------------
function getAllExpenses($limit = null)
{
    global $connection;
    $query = "SELECT e.*, et.name AS etName FROM expenses e
        INNER JOIN expenses_types et ON e.expense_type_id = et.id
        ORDER BY e.id DESC
    ";

    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }

    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}


// ---------------- العمليات على المصاريف  ----------------
function expenses($option, $id = null)
{
    global $connection;
    global $lang;
    global $user_full_name;
    $error_msg = null;


    $expense_type_input = mysqli_real_escape_string($connection, $_POST['expense_type_id']);
    $hidden_expense_type_id = mysqli_real_escape_string($connection, $_POST['hidden_expense_type_id']);

    if ($expense_type_input) {
        $expense_type_parts = explode('-', $expense_type_input);
        $expense_type_id = trim($expense_type_parts[0]);
    } else {
        $expense_type_id = $hidden_expense_type_id;
    }

    $old_expense_amount = mysqli_real_escape_string($connection, $_POST['old_expense_amount']);

    if (isset($_GET['old-expense-amount'])) {
        $old_expense_amount = $_GET['old-expense-amount'];
    }
    if (isset($_GET['bank-deduction'])) {
        $get_bank_deduction = $_GET['bank-deduction'];
    }
    if (isset($_GET['facilities-account'])) {
        $get_facilities_account = $_GET['facilities-account'];
    }

    $amount = mysqli_real_escape_string($connection, $_POST['amount']);
    $bank_deduction = mysqli_real_escape_string($connection, $_POST['bank_deduction']);
    $facilities_account = mysqli_real_escape_string($connection, $_POST['facilities_account']);

    // if(isset($_POST['hidden_bank_deduction'])){
    $hidden_bank_deduction = mysqli_real_escape_string($connection, $_POST['hidden_bank_deduction']);
    $hidden_facilities_account = mysqli_real_escape_string($connection, $_POST['hidden_facilities_account']);
    // }

    $expense_date = mysqli_real_escape_string($connection, $_POST['expense_date']);
    $description = mysqli_real_escape_string($connection, $_POST['description']);
    $notes = mysqli_real_escape_string($connection, $_POST['notes']);

    $description = strtoupper($description);
    $notes = strtoupper($notes);


    if ($option == 'i') {
        if (empty($expense_type_id) || empty($amount) || empty($facilities_account) || empty($bank_deduction) || empty($expense_date)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO expenses(expense_type_id, expense_date, description, amount, bank_deduction, facilities_account, notes, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'issdiiss', $expense_type_id, $expense_date, $description, $amount, $bank_deduction, $facilities_account, $notes, $user_full_name);
            updateBankExpenses('i', $bank_deduction, $facilities_account, $amount, null);
        }
    } else if ($option == 'u' && $id) {
        if (empty($expense_type_id) || empty($amount) || empty($hidden_facilities_account) || empty($hidden_bank_deduction) || empty($expense_date)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
            $error_msg .= $expense_type_id . '---(' . $amount . ')---' . $hidden_bank_deduction . '---' . $hidden_facilities_account . '---' . $expense_date;
        } else {
            $query = "UPDATE expenses SET expense_type_id = ?, expense_date = ?, description = ?, amount = ?, notes = ?, updated_by = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'issdssi', $expense_type_id, $expense_date, $description, $amount, $notes, $user_full_name, $id);
            updateBankExpenses('u', $hidden_bank_deduction, $hidden_facilities_account, $amount, $old_expense_amount);
        }
    } else if ($option == 'd' && $id) {
        $query = "DELETE FROM expenses WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        updateBankExpenses('d', $get_bank_deduction, $get_facilities_account, null, $old_expense_amount);
    }


    if ($error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./expenses.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./expenses.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./expenses.php";
            });
        </script>';
    }
}

// ----------------الحصول معلومات لوحة التحكم----------------
function getDashboard($startDate = null, $endDate = null)
{
    global $connection;

    $tripDateCondition = "";
    $sauBillsDateCondition = "";
    $servicesDateCondition = "";
    $expensesDateCondition = "";
    $expensesDateCondition2 = "WHERE bank_deduction = 2";
    $expensesDateCondition3 = "WHERE bank_deduction = 3";


    if ($startDate && $endDate) {
        $tripDateCondition = "WHERE DATE(trip_date) BETWEEN '$startDate' AND '$endDate'";
        $sauBillsDateCondition = "WHERE DATE(bill_date) BETWEEN '$startDate' AND '$endDate'";
        $servicesDateCondition = "WHERE DATE(service_date) BETWEEN '$startDate' AND '$endDate'";
        $expensesDateCondition = "WHERE DATE(expense_date) BETWEEN '$startDate' AND '$endDate'";

        $expensesDateCondition2 = "WHERE bank_deduction = 2 AND DATE(expense_date) BETWEEN '$startDate' AND '$endDate'";
        $expensesDateCondition3 = "WHERE bank_deduction = 3 AND DATE(expense_date) BETWEEN '$startDate' AND '$endDate'";
    } elseif ($startDate) {
        $tripDateCondition = "WHERE DATE(trip_date) = '$startDate'";
        $sauBillsDateCondition = "WHERE DATE(bill_date) = '$startDate'";
        $servicesDateCondition = "WHERE DATE(service_date) = '$startDate'";
        $expensesDateCondition = "WHERE DATE(expense_date) = '$startDate'";

        $expensesDateCondition2 = "WHERE bank_deduction = 2 AND DATE(expense_date) = '$startDate'";
        $expensesDateCondition3 = "WHERE bank_deduction = 3 AND DATE(expense_date) = '$startDate'";
    }

    $query = "SELECT 
        (SELECT COUNT(id) FROM drivers) AS total_drivers,
        (SELECT COUNT(id) FROM trips $tripDateCondition) AS total_trips, 
        (SELECT SUM(remaining) FROM trips $tripDateCondition) AS total_trips_remaining, 
        (SELECT COUNT(id) FROM sau_bills $sauBillsDateCondition) AS total_sau_bills, 
        (SELECT SUM(price) FROM sau_bills $sauBillsDateCondition) AS total_sau_bills_amount, 
        (SELECT SUM(nob) FROM sau_bills $sauBillsDateCondition) AS total_nob_sau_bills, 
        (SELECT SUM(nov) FROM sau_bills $sauBillsDateCondition) AS total_nov_sau_bills, 
        (SELECT COUNT(id) FROM services $servicesDateCondition) AS total_services, 
        (SELECT SUM(nov) FROM services $servicesDateCondition) AS total_nov_services, 
        (SELECT COUNT(id) FROM sau_offices) AS total_clients,
        (SELECT SUM(amount) FROM service_fees WHERE service_id IN (SELECT id FROM services $servicesDateCondition)) AS total_services_amount, 
        (SELECT SUM(bank_deduction_amount) FROM service_fees WHERE service_id IN (SELECT id FROM services $servicesDateCondition)) AS total_services_bank_deduction_amount, 
        (SELECT COUNT(id) FROM expenses $expensesDateCondition) AS total_expenses,
        (SELECT SUM(amount) FROM expenses $expensesDateCondition) AS total_expenses_amount,
        (SELECT SUM(amount) FROM expenses $expensesDateCondition2) AS total_deposit_expenses_amount,
        (SELECT SUM(amount) FROM expenses $expensesDateCondition3) AS total_expenses_bank_deduction_amount
    ";

    $result = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($result);
}

///// ----------------------التشفير-------
function encrypt($plaintext, $key)
{
    // إعداد IV (Initial Vector) عشوائي للتشفير
    $iv_length = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($iv_length);
    // تشفير النص
    $ciphertext = openssl_encrypt($plaintext, 'aes-256-cbc', $key, 0, $iv);
    // دمج IV مع النص المشفر
    $ciphertext_with_iv = base64_encode($iv . $ciphertext);
    return $ciphertext_with_iv;
}












































// ----------------الحصول على كامل الفواتير----------------
// ----------------الحصول على كامل الفواتير----------------
function getAllInvoices($limit = null)
{
    global $connection;

    $query = "SELECT 
        invoices.*,
        customers.customer_name AS customer_name_f,
        COALESCE(SUM(fees.amount), 0) AS total_fees,
        COALESCE(SUM(fees.bank_deduction), 0) AS total_bank_deduction,
        COALESCE(
            GROUP_CONCAT(
            CONCAT(fee_types.description, ': ', FORMAT(fees.amount, 2), 
                IF(fees.description != '', CONCAT(' - ', fees.description), ''))
                SEPARATOR '<br>'
            ),
            'No fees'
        ) AS fee_details
    FROM 
        invoices
    LEFT JOIN 
        fees ON invoices.id = fees.invoice_id
    LEFT JOIN 
        fee_types ON fees.fee_type_id = fee_types.id
    LEFT JOIN 
        customers ON customers.id = invoices.customer_id
    GROUP BY 
        invoices.id
    ORDER BY 
        invoices.id
    DESC
    ";

    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }

    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// ----------------الحصول على كامل انواع رسوم الفواتير----------------
function getInvoiceFeesTypes($limit = null)
{
    global $connection;
    $query = "SELECT * FROM fee_types ORDER BY id DESC";
    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// ---------------- العمليات على انواع رسوم الفواتير ----------------
function invoiceFeesTypes($option, $id = null)
{
    global $connection;
    global $lang;
    $error_msg = null;

    $description = mysqli_real_escape_string($connection, $_POST['description']);
    $amount = mysqli_real_escape_string($connection, $_POST['amount']);
    $bank_deduction = mysqli_real_escape_string($connection, $_POST['bank_deduction']);

    $description = strtoupper($description);


    if ($option == 'i') {
        if (empty($description)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO fee_types(description, amount, bank_deduction) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sdd', $description, $amount, $bank_deduction);
        }
    } else if ($option == 'u' && $id) {
        if (empty($description)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "UPDATE fee_types SET description = ?, amount = ?, bank_deduction = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sddi', $description, $amount, $bank_deduction, $id);
        }
    } else if ($option == 'd' && $id) {
        $check_query = "SELECT COUNT(id) AS related_fees_types FROM fees WHERE fee_type_id = ?";
        $stmt_check = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($stmt_check, 'i', $id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);
        if ($row_check['related_fees_types'] == 0) {
            $query = "DELETE FROM fee_types WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id);
        } else {
            $error_msg .= translate('error_cannot_delete_fees_type_linked_to_invoices', $lang);
        }
    }

    if ($error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./invoices.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./invoices.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./invoices.php";
            });
        </script>';
    }
}



function invoices($option, $id = null)
{
    global $connection;
    global $lang;
    global $user_full_name;
    $error_msg = null;

    $customer_id_input = mysqli_real_escape_string($connection, $_POST['customer_id']);
    $hidden_customer_id = mysqli_real_escape_string($connection, $_POST['hidden_customer_id'] ?? null);

    if ($customer_id_input) {
        $customer_id_parts = explode('-', $customer_id_input);
        $customer_id = trim($customer_id_parts[0]);
    } else {
        $customer_id = $hidden_customer_id;
    }

    $invoice_date = mysqli_real_escape_string($connection, $_POST['invoice_date'] ?? date('Y-m-d h:i:s A'));
    $port = mysqli_real_escape_string($connection, $_POST['port'] ?? 'exit');
    $status = mysqli_real_escape_string($connection, $_POST['status'] ?? 'Draft');
    $customer_name = mysqli_real_escape_string($connection, $_POST['customer_name'] ?? 'N/A');
    $exporter_importer_name = mysqli_real_escape_string($connection, $_POST['exporter_importer_name'] ?? 'N/A');
    $driver_name = mysqli_real_escape_string($connection, $_POST['driver_name'] ?? 'N/A');
    $destination_country = mysqli_real_escape_string($connection, $_POST['destination_country'] ?? 'N/A');
    $declaration_number = mysqli_real_escape_string($connection, $_POST['declaration_number'] ?? 0);
    $vehicle_plate_number = mysqli_real_escape_string($connection, $_POST['vehicle_plate_number'] ?? 0);
    $declaration_count = mysqli_real_escape_string($connection, $_POST['declaration_count'] ?? 1);
    $vehicle_count = mysqli_real_escape_string($connection, $_POST['vehicle_count'] ?? 1);
    $customer_invoice_number = mysqli_real_escape_string($connection, $_POST['customer_invoice_number'] ?? null);
    $goods_description = mysqli_real_escape_string($connection, $_POST['goods_description'] ?? null);
    $notes = mysqli_real_escape_string($connection, $_POST['notes'] ?? null);
    $returned_amount = mysqli_real_escape_string($connection, $_POST['returned_amount'] ?? 0);
    $is_postpaid = mysqli_real_escape_string($connection, $_POST['is_postpaid'] ?? 0);


    // البيانات الخاصة بالمصاريف
    $fee_ids = $_POST['fee_ids'] ?? [];
    $quantity = $_POST['quantity'] ?? [];
    $amount = $_POST['fee_amount'] ?? [];
    $description = $_POST['description'] ?? [];



    $new_bank_amount = $_POST['bank_deduction'] ?? [];
    // تأكد من أن $new_bank_amount هو مصفوفة
    if (!is_array($new_bank_amount)) {
        $new_bank_amount = [];
    }
    $total_new_bank_amount = array_sum(array_map('floatval', $new_bank_amount));

    if (isset($_GET['old-bank-amount'])) {
        $old_bank_amount = $_GET['old-bank-amount'];
    }


    // fees
    $fee_type_inputs = $_POST['fee_type'];
    $fee_type_ids = array();
    foreach ($fee_type_inputs as $fee_type_input) {
        $fee_type_parts = explode('-', $fee_type_input);
        $fee_type_id = trim($fee_type_parts[0]);
        $fee_type_ids[] = mysqli_real_escape_string($connection, $fee_type_id);
    }

    if ($option == 'i') {
        // إضافة رحلة جديدة
        if (empty($port) || empty($status) || empty($customer_id) || empty($exporter_importer_name)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO invoices(port, invoice_date, status, customer_id, customer_name, exporter_importer_name, driver_name, destination_country, declaration_number, vehicle_plate_number, declaration_count, vehicle_count, customer_invoice_number, goods_description, notes, returned_amount, is_postpaid, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param(
                $stmt,
                'ssssssssssiisssdis',
                $port,
                $invoice_date,
                $status,
                $customer_id,
                $customer_name,
                $exporter_importer_name,
                $driver_name,
                $destination_country,
                $declaration_number,
                $vehicle_plate_number,
                $declaration_count,
                $vehicle_count,
                $customer_invoice_number,
                $goods_description,
                $notes,
                $returned_amount,
                $is_postpaid,
                $user_full_name
            );

            if (mysqli_stmt_execute($stmt)) {
                $invoice_id = mysqli_insert_id($connection);  // الحصول على ID الرحلة المدخلة

                // إدخال بيانات المصاريف المرتبطة بالرحلة
                foreach ($fee_type_ids as $index => $fee_type_id) {
                    if (!empty($fee_type_id) && !empty($amount[$index])) {
                        $query_fees = "INSERT INTO fees(invoice_id, fee_type_id, amount, bank_deduction, description) VALUES (?, ?, ?, ?, ?)";
                        $stmt_fees = mysqli_prepare($connection, $query_fees);
                        $desc = strtoupper(mysqli_real_escape_string($connection, $description[$index]));
                        mysqli_stmt_bind_param($stmt_fees, 'iidds', $invoice_id, $fee_type_id, $amount[$index], $new_bank_amount[$index], $desc);
                        mysqli_stmt_execute($stmt_fees);
                    }
                }

                updateBank('i', $total_new_bank_amount, null, 't');

                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            }
        }
    } else if ($option == 'u' && $id) {
        // تحديث بيانات الرحلة والمصاريف
        if (empty($port) || empty($status) || empty($customer_id) || empty($exporter_importer_name)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            // تحديث بيانات الرحلة
            $query = "UPDATE invoices 
            SET port= ?, invoice_date= ?, status= ?, customer_id= ?, customer_name= ?, exporter_importer_name= ?, driver_name= ?, destination_country= ?, declaration_number= ?, vehicle_plate_number= ?, declaration_count= ?, vehicle_count= ?, customer_invoice_number= ?, goods_description= ?, notes= ?, returned_amount= ?, is_postpaid= ?, updated_by= ?
            WHERE id = ?";

            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param(
                $stmt,
                'sssissssssiisssdisi',
                $port,
                $invoice_date,
                $status,
                $customer_id,
                $customer_name,
                $exporter_importer_name,
                $driver_name,
                $destination_country,
                $declaration_number,
                $vehicle_plate_number,
                $declaration_count,
                $vehicle_count,
                $customer_invoice_number,
                $goods_description,
                $notes,
                $returned_amount,
                $is_postpaid,
                $user_full_name,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {

                // استرداد جميع الرسوم الحالية المرتبطة بالرحلة
                $query_existing_fees = "SELECT id FROM fees WHERE invoice_id = ?";
                $stmt_existing_fees = mysqli_prepare($connection, $query_existing_fees);
                mysqli_stmt_bind_param($stmt_existing_fees, 'i', $id);
                mysqli_stmt_execute($stmt_existing_fees);
                $result_existing_fees = mysqli_stmt_get_result($stmt_existing_fees);
                $existing_fees = mysqli_fetch_all($result_existing_fees, MYSQLI_ASSOC);

                // قائمة الرسوم الجديدة المرسلة من الواجهة
                $new_fee_ids = array_filter($fee_ids); // هذا يزيل العناصر الفارغة من القائمة

                // حذف الرسوم التي لم تعد موجودة في الواجهة
                foreach ($existing_fees as $existing_fee) {
                    if (!in_array($existing_fee['id'], $new_fee_ids)) {
                        $query_delete_fee = "DELETE FROM fees WHERE id = ?";
                        $stmt_delete_fee = mysqli_prepare($connection, $query_delete_fee);
                        mysqli_stmt_bind_param($stmt_delete_fee, 'i', $existing_fee['id']);
                        mysqli_stmt_execute($stmt_delete_fee);
                    }
                }


                foreach ($fee_type_ids as $index => $fee_type_id) {
                    $fee_id = $fee_ids[$index];
                    if (!empty($fee_id)) {
                        // إذا كان هناك id، نقوم بتحديث السجل
                        if (!empty($fee_type_id) && !empty($amount[$index])) {
                            $query_fees = "UPDATE fees SET fee_type_id = ?, amount = ?, bank_deduction = ?, description = ? WHERE id = ?";
                            $stmt_fees = mysqli_prepare($connection, $query_fees);
                            $desc = strtoupper(mysqli_real_escape_string($connection, $description[$index]));
                            mysqli_stmt_bind_param($stmt_fees, 'iddsi', $fee_type_id, $amount[$index], $new_bank_amount[$index], $desc, $fee_id);
                            mysqli_stmt_execute($stmt_fees);
                        }
                    } else {
                        // إذا لم يكن هناك id، نقوم بإدراج سجل جديد
                        if (!empty($fee_type_id) && !empty($amount[$index])) {
                            $query_fees = "INSERT INTO fees (invoice_id, fee_type_id, amount, bank_deduction, description) VALUES (?, ?, ?, ?, ?)";
                            $stmt_fees = mysqli_prepare($connection, $query_fees);
                            $desc = strtoupper(mysqli_real_escape_string($connection, $description[$index]));
                            mysqli_stmt_bind_param($stmt_fees, 'iidds', $id, $fee_type_id, $amount[$index], $new_bank_amount[$index], $desc);
                            mysqli_stmt_execute($stmt_fees);
                        }
                    }
                }
                if ($status != 'Cancelled') {
                    updateBank('u', $total_new_bank_amount, $old_bank_amount, 't');
                } else {
                    updateBank('d', null, $old_bank_amount, 't');
                }

                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./invoices.php";
                    });
                </script>';
            } else {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            }
        }
    } else if ($option == 'd' && $id) {
        // حذف الرحلة والمصاريف المرتبطة بها
        $query = "DELETE FROM invoices WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (mysqli_stmt_execute($stmt)) {
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "' . translate('deleted_successfully', $lang) . '",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = "./invoices.php";
                });
            </script>';
        } else {
            $error_msg = translate('error_could_not_delete_the_record', $lang);
        }
        updateBank('d', null, $old_bank_amount, 't');
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./invoices.php";
            });
        </script>';
    }
}



// --------------------دفع فاتورة------------------------
function pay_invoice($option, $id = null)
{
    global $connection;
    global $lang;
    global $user_full_name;
    $error_msg = null;
    if ($option == 'u' && $id) {
        $query = "UPDATE invoices SET status= ?, payment_date= ?, is_postpaid= ?, updated_by= ? WHERE id = ?";
        $stmt = mysqli_prepare($connection, $query);

        // تعريف القيم كمتغيرات
        $status = 'Paid';
        $payment_date = date('Y-m-d H:i:s');
        $is_postpaid = 1;
        $updated_by = $user_full_name;
        $invoice_id = $id;

        mysqli_stmt_bind_param(
            $stmt,
            'ssisi',
            $status,
            $payment_date,
            $is_postpaid,
            $updated_by,
            $invoice_id
        );
        if (mysqli_stmt_execute($stmt)) {
            echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('paid_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./invoices.php";
                    });
                </script>';
        } else {
            $error_msg = translate('error_could_not_update_the_record', $lang);
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./invoices.php";
            });
        </script>';
    }
}

























// ----------------الحصول على فاتورة حسب المعرف----------------
function getInvoiceById($id)
{
    global $connection;
    $query = "SELECT 
            invoices.*,
            COALESCE(SUM(fees.amount), 0) AS total_fees,
            COALESCE(SUM(fees.bank_deduction), 0) AS total_bank_deduction,
            COALESCE(
                GROUP_CONCAT(
                CONCAT(
                    fee_types.description, 
                    ': ', 
                    FORMAT(fees.amount, 2), 
                    IF(fees.description != '', CONCAT(' - ', fees.description), ''))
                    SEPARATOR '<br>'
                ),
                'No fees'
            ) AS fee_details,
            COALESCE(
                GROUP_CONCAT(
                CONCAT(
                    fee_types.description, 
                    IF(fees.description != '', CONCAT(' - ', fees.description), '')), 
                    '@',
                    FORMAT(fees.amount, 2) 
                    SEPARATOR '<br>'
                ),
                'No fees'
            ) AS fee_d,
            customers.customer_name AS c_customer_name
        FROM 
            invoices
        LEFT JOIN 
            fees ON invoices.id = fees.invoice_id
        LEFT JOIN 
            fee_types ON fees.fee_type_id = fee_types.id
        LEFT JOIN 
            customers ON invoices.customer_id = customers.id
        WHERE 
            invoices.id = ?
        GROUP BY 
            invoices.id
        ORDER BY 
            invoices.id
    ";


    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($result);
}

// ----------------الحصول على معلومات هيكل الملفات----------------
function getPdfFileFormat()
{
    global $connection;
    $query = "SELECT * FROM pdf_file_format";
    $result = mysqli_query($connection, $query);
    return $result;
}
// ----------------الحصول على معلومات هيكل الملفات حسب المعرف----------------
function getPdfFileFormatById($id)
{
    global $connection;
    $query = "SELECT * FROM pdf_file_format WHERE id = $id";
    $result = mysqli_query($connection, $query);
    return $result;
}

// ----------------العمليات على معلومات هيكل الملفات----------------
function pdfFileFormat($option, $id)
{
    global $connection;
    $error_msg = null;
    $coNameAr = mysqli_real_escape_string($connection, $_POST['coNameAr']);
    $coNameEn = mysqli_real_escape_string($connection, $_POST['coNameEn']);
    $exPhone = mysqli_real_escape_string($connection, $_POST['exPhone']);
    $enPhone = mysqli_real_escape_string($connection, $_POST['enPhone']);
    $addressAr = mysqli_real_escape_string($connection, $_POST['addressAr']);
    $addressEn = mysqli_real_escape_string($connection, $_POST['addressEn']);
    $eMail = mysqli_real_escape_string($connection, $_POST['eMail']);
    $bankNameAr = mysqli_real_escape_string($connection, $_POST['bankNameAr']);
    $bankNameEn = mysqli_real_escape_string($connection, $_POST['bankNameEn']);
    $accountNum = mysqli_real_escape_string($connection, $_POST['accountNum']);
    $iban = mysqli_real_escape_string($connection, $_POST['iban']);
    $mbox = mysqli_real_escape_string($connection, $_POST['mbox']);
    $trn = mysqli_real_escape_string($connection, $_POST['trn']);
    $logo = $_FILES['logo'];
    $stamp = $_FILES['stamp'];

    if ($option == 'u') {
        if (!empty($logo['name'])) {
            $allowedExtensions = array("jpg", "jpeg", "png", "gif");
            $fileExtension = strtolower(pathinfo($logo['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                $error_msg .= 'Error: Invalid file format. Please upload an image file (jpg, jpeg, png, gif).';
            } else {
                $uploadDirectory = './img/';
                if (!move_uploaded_file($logo['tmp_name'], $uploadDirectory . $logo['name'])) {
                    $error_msg .= 'Error: Failed to move the uploaded file.';
                }
            }
        }
        if (!empty($stamp['name'])) {
            $allowedExtensions = array("jpg", "jpeg", "png", "gif");
            $fileExtension = strtolower(pathinfo($stamp['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                $error_msg .= 'Error: Invalid file format. Please upload an image file (jpg, jpeg, png, gif).';
            } else {
                $uploadDirectory = './img/';
                if (!move_uploaded_file($stamp['tmp_name'], $uploadDirectory . $stamp['name'])) {
                    $error_msg .= 'Error: Failed to move the uploaded file.';
                }
            }
        }

        $query = "UPDATE pdf_file_format SET coNameAr = '$coNameAr', coNameEn = '$coNameEn', exPhone = '$exPhone', enPhone = '$enPhone', addressAr = '$addressAr', addressEn = '$addressEn', eMail = '$eMail', mbox = '$mbox', bankNameAr = '$bankNameAr', bankNameEn = '$bankNameEn', accountNum = '$accountNum', iban = '$iban', trn = '$trn'";
        if (!empty($logo['name'])) {
            $query .= ", logo = '" . mysqli_real_escape_string($connection, $logo['name']) . "'";
        }
        if (!empty($stamp['name'])) {
            $query .= ", stamp = '" . mysqli_real_escape_string($connection, $stamp['name']) . "'";
        }

        $query .= " WHERE id = $id";
        if (empty($coNameAr) || empty($coNameEn)) {
            $error_msg .= 'Error: Some required data is missing.';
        }
    }


    if ($error_msg == null) {
        if (mysqli_query($connection, $query)) {
            if ($option == 'u') {
                echo '<script>alert("Updated successfully!"); window.location.href = "./settings.php";</script>';
            }
        }
    } else {
        echo '<script>alert("' . $error_msg . '"); window.location.href = "./settings.php";</script>';
    }
}


// ----------------الحصول على كامل العملاء----------------
function getCustomers($limit = null)
{
    global $connection;
    $query = "SELECT * FROM customers ORDER BY id DESC";
    if ($limit != null) {
        $query .= " LIMIT " . intval($limit);
    }
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}





// ---------------- العمليات على انواع رسوم الفواتير ----------------
function customers($option, $id = null)
{
    global $connection;
    global $lang;
    $error_msg = null;

    $customer_name = mysqli_real_escape_string($connection, $_POST['customer_name']);
    $exit_clearance = mysqli_real_escape_string($connection, $_POST['exit_clearance']);
    $exit_returns = mysqli_real_escape_string($connection, $_POST['exit_returns']);
    $entry_clearance = mysqli_real_escape_string($connection, $_POST['entry_clearance']);
    $entry_returns = mysqli_real_escape_string($connection, $_POST['entry_returns']);
    $customer_notes = mysqli_real_escape_string($connection, $_POST['customer_notes']);



    if ($option == 'i') {
        if (empty($customer_name)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "INSERT INTO customers(customer_name, exit_clearance, exit_returns, entry_clearance, entry_returns, customer_notes) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sdddds', $customer_name, $exit_clearance, $exit_returns, $entry_clearance, $entry_returns, $customer_notes);
        }
    } else if ($option == 'u' && $id) {
        if (empty($customer_name)) {
            $error_msg .= translate('error_some_required_data_is_missing', $lang);
        } else {
            $query = "UPDATE customers SET customer_name = ?, exit_clearance = ?, exit_returns = ?, entry_clearance = ?, entry_returns = ?, customer_notes = ? WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'sddddsi', $customer_name, $exit_clearance, $exit_returns, $entry_clearance, $entry_returns, $customer_notes, $id);
        }
    } else if ($option == 'd' && $id) {
        $check_query = "SELECT COUNT(id) AS related_invoices FROM invoices WHERE customer_id = ?";
        $stmt_check = mysqli_prepare($connection, $check_query);
        mysqli_stmt_bind_param($stmt_check, 'i', $id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);
        if ($row_check['related_invoices'] == 0) {
            $query = "DELETE FROM customers WHERE id = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, 'i', $id);
        } else {
            $error_msg .= translate('error_cannot_delete_customers_linked_to_invoices', $lang);
        }
    }

    if ($error_msg == null) {
        if (mysqli_stmt_execute($stmt)) {
            if ($option == 'i') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('saved_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = window.location.href;
                    });
                </script>';
            } else if ($option == 'u') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('updated_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./invoices.php";
                    });
                </script>';
            } else if ($option == 'd') {
                echo '<script>
                    Swal.fire({
                        icon: "success",
                        title: "' . translate('deleted_successfully', $lang) . '",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.href = "./invoices.php";
                    });
                </script>';
            }
        } else {
            if ($option == 'i') {
                $error_msg = translate('error_could_not_insert_the_record', $lang);
            } elseif ($option == 'u') {
                $error_msg = translate('error_could_not_update_the_record', $lang);
            } elseif ($option == 'd') {
                $error_msg = translate('error_could_not_delete_the_record', $lang);
            } else {
                $error_msg = translate('error_unknown_operation', $lang);
            }
        }
    }

    // إذا كان هناك خطأ، اعرض رسالة الخطأ
    if ($error_msg != null) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "' . translate('error', $lang) . '",
                text: "' . $error_msg . '"
            }).then(() => {
                window.location.href = "./invoices.php";
            });
        </script>';
    }
}
