التعديلات المطلوب تطبيقها مباشرة على البرودكشن
-- جدول السائقين
ALTER TABLE `drivers` ADD `uae_id` VARCHAR(50) NULL DEFAULT NULL AFTER `vehicle_number`;
ALTER TABLE `drivers` ADD `passport_number` VARCHAR(50) NULL DEFAULT NULL AFTER `uae_id`;
ALTER TABLE `drivers` ADD `delegate` VARCHAR(255) NULL DEFAULT NULL AFTER `notes`;
-- جدول المستخدمين او الموظفين
ALTER TABLE `users` ADD `uae_id` VARCHAR(50) NULL DEFAULT NULL AFTER `personal_phone`;
ALTER TABLE `users` ADD `job` VARCHAR(255) NULL DEFAULT NULL AFTER `uae_id`;
ALTER TABLE `users` ADD `passport_number` VARCHAR(50) NULL DEFAULT NULL AFTER `job`;
ALTER TABLE `users` ADD `address` VARCHAR(255) NULL DEFAULT NULL AFTER `passport_number`;

-- جدول المرفقات
CREATE TABLE attachments (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  entity_type VARCHAR(50) NOT NULL,   -- مثل: drivers / employees / service_requests
  entity_id   INT UNSIGNED NOT NULL,
  file_path   VARCHAR(500) NOT NULL,  -- المسار الكامل في Azure
  file_name   VARCHAR(255) NOT NULL,  -- الاسم الأصلي للملف
  uploaded_by    INT UNSIGNED NULL,   -- user_id
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_entity (entity_type, entity_id)
);
-- جدول الشواحن
CREATE TABLE shippers (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  office_name VARCHAR(255),
  office_address TEXT,
  delegate VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- جدول اسعار خدمات الشاحن حسب نوع الخدمة
CREATE TABLE shipper_service_prices (
  id INT PRIMARY KEY AUTO_INCREMENT,
  shipper_id INT NOT NULL,
  service_type_id INT NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (shipper_id) REFERENCES shippers(id),
  FOREIGN KEY (service_type_id) REFERENCES service_fees_types(id),
  UNIQUE (shipper_id, service_type_id)
);
-- جداول الهواتف المتعددة
CREATE TABLE driver_phones (
  id INT PRIMARY KEY AUTO_INCREMENT,
  driver_id INT NOT NULL,
  phone VARCHAR(30) NOT NULL,
  FOREIGN KEY (driver_id) REFERENCES drivers(id)
);
CREATE TABLE user_phones (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT NOT NULL,
  phone VARCHAR(30) NOT NULL,
  type ENUM('personal','work') NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
CREATE TABLE shipper_phones (
  id INT PRIMARY KEY AUTO_INCREMENT,
  shipper_id INT NOT NULL,
  phone VARCHAR(30) NOT NULL,
  type ENUM('shipper','office') NOT NULL,
  FOREIGN KEY (shipper_id) REFERENCES shippers(id)
);

-- جدول طلبات الخدمة
CREATE TABLE service_requests (
  id INT PRIMARY KEY AUTO_INCREMENT,
  request_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  service_type_id INT NOT NULL,
  driver_id INT NOT NULL,
  user_id INT NOT NULL,
  shipper_id INT NULL,
  status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
  notes TEXT NULL,
  declaration_number VARCHAR(100) NULL,
  locked_by_user_id INT NULL,
  FOREIGN KEY (service_type_id) REFERENCES service_fees_types(id),
  FOREIGN KEY (driver_id) REFERENCES drivers(id),
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (shipper_id) REFERENCES shippers(id)
);




==============







جدول للسائقين يتكون من:
اسم السائق ✅
رقم الهاتف بحيث يمكن اضافة اكثر من رقم ✅
رقم السيارة✅
رقم الهوية الاماراتية✅
رقم جواز السفر✅
المندوب المرتبط✅
المرفقات✅
---------------------
جدول بيانات الموظفين يتكون من:
  اسم الموظف   ✅ 
رقم الهاتف الشخصي بحيث يمكن اضافة اكثر من رقم✅
رقم هاتف العمل بحيث يمكن اضافة اكثر من رقم✅
المهنة✅
رقم الهوية الاماراتية✅
رقم جواز السفر✅
العنوان في البلد الاصل✅
المرفقات✅
--------------------
جدول الشواحن يتكون من: 
اسم الشاحن✅
اسم مكتب الشحن✅
عنوان المكتب✅
رقم هاتف الشاحن بحيث يمكن اضافة اكثر من رقم✅
رقم هاتف مكتب الشحن بحيث يمكن اضافة اكثر من رقم✅
المندوب المرتبط✅
حقل قيمة الخدمة المتفق عليها تكون حسب نوع الخدمة أي كل خدمة لها سعر منفصل✅
----------------------------------
جدول الخدمات يتكون من:✅
بيان سعودي مسبق ✅
وثيقة تأمين✅
وثيقة نقل✅
فسح البطحاء✅
فسح البحرين✅
فسح ميناء جدة ✅
فسح سلوى✅
فسح الخفجي✅

----------------------------------
فورم تقديم طلب خدمة يتكون من:
نوع الخدمة ✅ 
معرف السائق ✅ 
معرف الموظف ✅ 
معرف الشاحن (اختياري) ✅ 
حالة الطلب (قيد الانتظار - جاري المعالجة - مكتمل - ملغي) ✅ 
ملاحظات ✅ 
رقم البيان (الزامي فقط في حالة نوع الخدمة بيان سعودي مسبق) ✅ 
المرفقات ✅ 


-- مخصص لجدول الخدمات
-- حالة الدفع (آجل - مسدد - ملغي) الافتراضي آجل 
-- payment_status ENUM('postpaid','paid','cancelled') DEFAULT 'postpaid',



----------------------------------
فورم طلب إلغاء خدمة لتحويل نوع الفاتورة الى ملغية يتكون من: 
معرف الخدمة
نوع الخدمة
حالة الطلب (قيد الانتظار - جاري المعالجة - مكتمل - ملغي)
رقم البيان (الزامي فقط في حالة نوع الخدمة بيان سعودي مسبق)
معرف حساب التبادل (الزامي فقط في حالة نوع الخدمة بيان سعودي مسبق)
سبب الإلغاء
--------------------------------


عملية طباعة الفاتورة للخدمة تتكون من:
تفاصيل الخدمة حسب المعرف
تفاصيل السائق حسب المعرف
تفاصيل الموظف حسب المعرف
تفاصيل الشاحن حسب المعرف
بنود الفاتورة المالية
--------------------------------------------
مطلوب فورم كشف حساب للفواتير حسب المعرفات 
---------------------------------------------
سداد الفواتير يكون عن طريق السداد بالكامل فقط من اجل تفعيل زر السداد 
مع الربط حسب الكيان والسداد حسب الفترة الزمنية او رقم الفاتورة من الى

ايضا جدول جديد مربوط بالكيانات من اجل تسجيل عمليات الدفع في حال كاملة أو جزئية مع حساب المتبقي (يحتاج شرح اضافي او اقتراحات افضل)
--------------------

--------------------
وضع صلاحيات من قبل مسؤول النظام لمشرفين استقبال الطلبات من اجل معالجة الطلبات المقدمة على حسب نوع الخدمة بحيث نفس الصلاحيات عند معالجة الغاء الطلبات حسب نوع الخدمة
-------------------
حظر فتح ومعالجة الخدمات المقدمة بعد أول مشرف يفتحها ويحدث حالتها الى تم الاستلام لتلافي العمل المزدوج على نفس الخدمة من أكثر من مشرف
------------------
امكانية تحرير بيانات السائق قبل تقديم الطلب للتأكد منها
مع اظهار رسالة تأكيدية تسأل هل البيانات بالكامل صحيحة
------------------

جدول "لحسابات التبادل" المستخدمة ويتكون من:
رقم الحساب
اسم الحساب 
الرصيد الحالي



لما يكون نوع الخدمة بيان مسبق لازم يحدد اي حساب تبادل ولما يلغي يعرف من اي حساب؟؟؟؟؟؟؟؟؟؟











فيما يلي **التصاميم الثلاثة** المطلوبة:

1. مخطط العلاقات (ERD)
2. هيكل الجداول SQL
3. مخطط تدفق الواجهات (UI Flow)

---

## 1) مخطط العلاقات (ERD – نصّي)

**الكيانات الأساسية:**

* Driver (سائق)
* DriverPhones (هواتف السائق)
* Employee (موظف)
* EmployeePhones (هواتف الموظف)
* Shipper (شاحن)
* ShipperPhones (هواتف الشاحن)
* ServiceType (نوع الخدمة)
* ShipperServicePrice (أسعار خدمات الشاحن)
* ServiceRequest (طلب خدمة)
* Invoice (فاتورة)
* Payment (عملية دفع)
* ClearingAccount (حساب تبادل)
* User (مستخدم/مشرف)
* Role / Permissions

**العلاقات:**

* Driver 1—* DriverPhones
* Employee 1—* EmployeePhones
* Shipper 1—* ShipperPhones
* Shipper 1—* ShipperServicePrice *—1 ServiceType
* ServiceRequest *—1 Driver
* ServiceRequest *—1 Employee
* ServiceRequest *—0..1 Shipper
* ServiceRequest *—1 ServiceType
* ServiceRequest 1—1 Invoice
* Invoice 1—* Payment
* Payment *—1 ClearingAccount (عند الحاجة)
* User *—* ServiceType (صلاحيات المعالجة حسب نوع الخدمة)

---

## 2) هيكل الجداول (SQL – مقترح)


-- Drivers
CREATE TABLE drivers (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  car_number VARCHAR(50),
  uae_id VARCHAR(50),
  passport_number VARCHAR(50),
  delegate_name VARCHAR(255),
  attachments JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE driver_phones (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  driver_id BIGINT NOT NULL,
  phone VARCHAR(30) NOT NULL,
  FOREIGN KEY (driver_id) REFERENCES drivers(id)
);

-- Employees
CREATE TABLE employees (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  job_title VARCHAR(255),
  uae_id VARCHAR(50),
  passport_number VARCHAR(50),
  origin_address TEXT,
  attachments JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE employee_phones (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  employee_id BIGINT NOT NULL,
  phone VARCHAR(30) NOT NULL,
  type ENUM('personal','work') NOT NULL,
  FOREIGN KEY (employee_id) REFERENCES employees(id)
);

-- Shippers
CREATE TABLE shippers (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  office_name VARCHAR(255),
  office_address TEXT,
  delegate_name VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE shipper_phones (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  shipper_id BIGINT NOT NULL,
  phone VARCHAR(30) NOT NULL,
  type ENUM('shipper','office') NOT NULL,
  FOREIGN KEY (shipper_id) REFERENCES shippers(id)
);

-- Service Types
CREATE TABLE service_types (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL UNIQUE
);

-- Shipper Service Prices
CREATE TABLE shipper_service_prices (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  shipper_id BIGINT NOT NULL,
  service_type_id BIGINT NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (shipper_id) REFERENCES shippers(id),
  FOREIGN KEY (service_type_id) REFERENCES service_types(id),
  UNIQUE (shipper_id, service_type_id)
);

-- Clearing Accounts
CREATE TABLE clearing_accounts (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  account_number VARCHAR(100) NOT NULL,
  account_name VARCHAR(255),
  current_balance DECIMAL(14,2) DEFAULT 0
);

-- Service Requests
CREATE TABLE service_requests (
  id INT PRIMARY KEY AUTO_INCREMENT,
  service_type_id INT NOT NULL,
  driver_id INT NOT NULL,
  user_id INT NOT NULL,
  shipper_id INT NULL,
  status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
  payment_status ENUM('postpaid','paid','cancelled') DEFAULT 'postpaid',
  declaration_number VARCHAR(100) NULL,
  clearance_fee DECIMAL(12,2) DEFAULT 0,
  custom_fee_desc TEXT,
  custom_fee_value DECIMAL(12,2) DEFAULT 0,
  locked_by_user_id INT NULL,
  FOREIGN KEY (service_type_id) REFERENCES service_types(id),
  FOREIGN KEY (driver_id) REFERENCES drivers(id),
  FOREIGN KEY (user_id) REFERENCES employees(id),
  FOREIGN KEY (shipper_id) REFERENCES shippers(id)
);

-- Invoices
CREATE TABLE invoices (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  service_request_id BIGINT NOT NULL UNIQUE,
  total_amount DECIMAL(14,2) NOT NULL,
  paid_amount DECIMAL(14,2) DEFAULT 0,
  remaining_amount DECIMAL(14,2) NOT NULL,
  status ENUM('open','closed','cancelled') DEFAULT 'open',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (service_request_id) REFERENCES service_requests(id)
);

-- Payments
CREATE TABLE payments (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  invoice_id BIGINT NOT NULL,
  clearing_account_id BIGINT NULL,
  amount DECIMAL(14,2) NOT NULL,
  payment_type ENUM('full','partial') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (invoice_id) REFERENCES invoices(id),
  FOREIGN KEY (clearing_account_id) REFERENCES clearing_accounts(id)
);

-- Users & Permissions (مختصر)
CREATE TABLE users (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  role ENUM('admin','supervisor') NOT NULL
);

CREATE TABLE user_service_permissions (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT NOT NULL,
  service_type_id BIGINT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (service_type_id) REFERENCES service_types(id),
  UNIQUE (user_id, service_type_id)
);
```

**ملاحظات تنفيذية:**

* حقل `locked_by_user_id` يمنع العمل المزدوج.
* عند اكتمال السداد: `invoices.status = 'closed'`.
* زر السداد يُفعّل فقط إذا `remaining_amount = total_amount` (سداد كامل فقط للتفعيل).

---














## 3) مخطط تدفق الواجهات (UI Flow)

**A. إدارة البيانات الأساسية**

* لوحة التحكم
  → السائقين (قائمة / إضافة / تعديل / مرفقات / هواتف)
  → الموظفين (قائمة / إضافة / تعديل / مرفقات / هواتف)
  → الشواحن (قائمة / إضافة / أسعار الخدمات / هواتف)
  → أنواع الخدمات
  → حسابات التبادل

**B. تقديم طلب خدمة**

* قائمة الطلبات
  → زر "طلب خدمة جديد"

  1. اختيار نوع الخدمة
  2. اختيار السائق
  3. اختيار الموظف
  4. اختيار الشاحن
  5. المرفقات
  6. حفظ الطلب → الحالة: قيد الانتظار

**C. معالجة الطلب**

* قائمة الطلبات حسب الصلاحية
  → فتح الطلب

  * أول مشرف يفتحه: الحالة = تم الاستلام + قفل الطلب
  * بقية المشرفين: عرض فقط
    → تحديث الحالة (جاري المعالجة → مكتمل)

**D. الإلغاء**

* من الطلب
  → نموذج إلغاء

  * إدخال السبب
  * إدخال رقم البيان + حساب التبادل عند اللزوم
    → حفظ → الحالة = ملغي

**E. الفوترة**

* من الطلب المكتمل
  → إنشاء فاتورة تلقائياً
  → عرض الفاتورة

**فورم سجلات الدفع حسب معرف الكيان**
  →  اختيار معرف الكيان
  → اختيار الفترة الزمنية (من إلى) 
  → ثم يتم عرض المبلغ الكامل للفواتير الغير مسددة حسب هذه الفترة
  → تعبئة المبلغ المدفوع (ممكن كامل أو جزئي)
  → زر "تسجيل عملية الدفع"
  → تحديث المتبقي → عند صفر:  تفعيل زر السداد لتحديث حالة الفواتير من (آجل إلى مسددة)

**F. التقارير**
* كشف حساب
  → تصفية حسب (سائق / موظف / شاحن / فترة / أرقام فواتير)
























## أولاً: الجداول الأساسية (Entities)

### 1) جدول السائقين (Drivers)

* الاسم الكامل
* أرقام الهاتف (متعدد القيم)
* رقم السيارة
* رقم الهوية الإماراتية
* رقم جواز السفر
* المندوب المرتبط
* المرفقات

---

### 2) جدول الموظفين (Employees)

* اسم الموظف
* أرقام الهاتف الشخصية (متعدد)
* أرقام هاتف العمل (متعدد)
* المهنة
* رقم الهوية الإماراتية
* رقم جواز السفر
* العنوان في البلد الأصل
* المرفقات

---

### 3) جدول الشواحن (Shippers)

* اسم الشاحن
* اسم مكتب الشحن
* عنوان المكتب
* أرقام هاتف الشاحن (متعدد)
* أرقام هاتف مكتب الشحن (متعدد)
* المندوب المرتبط
* أسعار الخدمات حسب نوع الخدمة
  (كل خدمة لها سعر مستقل)

---

### 4) جدول الخدمات (Service Types)

* بيان سعودي مسبق
* وثيقة تأمين
* وثيقة نقل
* فسح البطحاء
* فسح البحرين
* فسح ميناء جدة
* فسح سلوى
* فسح الخفجي

---

### 5) جدول حسابات التبادل (Clearing Accounts)

* رقم الحساب
* اسم الحساب
* الرصيد الحالي

---

## ثانياً: نماذج النظام (Forms / Workflows)

### 1) نموذج تقديم طلب خدمة

يتضمن:

* نوع الخدمة
* معرف السائق
* معرف الموظف
* معرف الشاحن (اختياري)
* حالة الطلب:
  (قيد الانتظار – جاري المعالجة – مكتمل – ملغي)
* حالة الدفع:
  (آجل – مسدد – ملغي) → الافتراضي: آجل
* رقم البيان (إلزامي فقط إذا كانت الخدمة "بيان سعودي مسبق")
* رسوم التخليص
* وصف رسم مخصص
* قيمة الرسم المخصص
* المرفقات

---

### 2) نموذج طلب إلغاء خدمة

* معرف الخدمة
* نوع الخدمة
* حالة الطلب
* رقم البيان (إلزامي فقط إذا كانت الخدمة "بيان سعودي مسبق")
* معرف حساب التبادل (إلزامي فقط في نفس الحالة أعلاه)
* سبب الإلغاء

---

## ثالثاً: الفوترة والمحاسبة

### 1) طباعة الفاتورة

تشمل:

* بيانات الخدمة
* بيانات السائق
* بيانات الموظف
* بيانات الشاحن
* البنود المالية التفصيلية

---

### 2) كشف حساب الفواتير

* البحث حسب:

  * معرف السائق
  * الموظف
  * الشاحن
  * الخدمة
* مع عرض المبالغ، المدفوع، المتبقي

---

### 3) آلية السداد

* تفعيل زر السداد فقط عند:

  * السداد الكامل
* الربط حسب:

  * الكيان (سائق / موظف / شاحن)
  * أو حسب فترة زمنية
  * أو حسب رقم فاتورة (من – إلى)

---

### 4) جدول عمليات الدفع (Payments)

مرتبط بكل الكيانات ويحتوي:

* رقم العملية
* الكيان المرتبط
* رقم الفاتورة
* المبلغ الكلي
* المبلغ المدفوع
* المتبقي
* نوع السداد (كامل / جزئي)
* تاريخ العملية

> اقتراح: جعل الفاتورة "مقفلة" عند اكتمال السداد.

---

## رابعاً: الصلاحيات وإدارة المستخدمين

### 1) صلاحيات المشرفين

يحددها مسؤول النظام حسب:

* نوع الخدمة
* معالجة الطلبات
* معالجة طلبات الإلغاء

---

### 2) منع الازدواجية في المعالجة

* عند فتح الطلب لأول مرة:

  * تتغير حالته إلى "تم الاستلام"
  * يُقفل على بقية المشرفين
  * لا يمكن فتحه إلا من نفس المشرف أو مسؤول النظام

---

### 3) التحقق من بيانات السائق

* إمكانية تعديل بيانات السائق قبل إرسال الطلب
* نافذة تأكيد:

  > هل جميع البيانات صحيحة وجاهزة للإرسال؟

---

## خامساً: العلاقات بين الجداول (Relations)

* الطلب يرتبط بـ:

  * سائق
  * موظف
  * شاحن (اختياري)
  * نوع خدمة
* الفاتورة مرتبطة بالطلب
* الدفع مرتبط بالفاتورة والكيان
* حسابات التبادل مرتبطة بخدمات "البيان السعودي المسبق"
