function translate(key, lang = "en") {
  return window.translations[lang][key] || key;
}

var lang = window.lang;
function arabicToEnglishNumbers(input) {
  const arabicNumbers = ["٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩"];
  const englishNumbers = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
  let output = input;
  for (let i = 0; i < arabicNumbers.length; i++) {
    const regex = new RegExp(arabicNumbers[i], "g");
    output = output.replace(regex, englishNumbers[i]);
  }
  return output;
}

function convertInputToEnglish(event) {
  const inputField = event.target;
  inputField.value = arabicToEnglishNumbers(inputField.value);
}

document.addEventListener("DOMContentLoaded", function () {
  const numberInputs = document.querySelectorAll('input[type="number"], input[type="text"], input[type="date"]');
  numberInputs.forEach(function (input) {
    input.addEventListener("input", convertInputToEnglish);
  });
});

function toggleLanguage() {
  let url = new URL(window.location.href);
  let currentLang = url.searchParams.get("lang");
  // Toggle between "ar" and "en"
  let newLang = currentLang === "ar" ? "en" : "ar";
  // Update the URL with the new language
  url.searchParams.set("lang", newLang);
  window.history.replaceState({}, document.title, url.toString());
  // Store the selected language in local storage
  localStorage.setItem("selectedLang", newLang);
  document.cookie = `selectedLang=${newLang}; expires=Wed, 31 Dec 2099 23:59:59 UTC; path=/`;
  location.reload();
}

function toggleCollapsed(sidebar) {
  let url = new URL(window.location.href);
  let currentCollapsed = url.searchParams.get("collapsed");
  // Toggle between "true" and "false"
  let newCollapsed = currentCollapsed === "false" ? "true" : "false";
  // Update the URL with the new collapsed
  url.searchParams.set("collapsed", newCollapsed);
  window.history.replaceState({}, document.title, url.toString());
  // Store the selected collapsed in local storage
  localStorage.setItem("collapsed", newCollapsed);
  document.cookie = `collapsed=${newCollapsed}; expires=Wed, 31 Dec 2099 23:59:59 UTC; path=/`;
  // sidebar.classList.toggle('collapsed');

  location.reload();
}

document.addEventListener("DOMContentLoaded", function () {
  /* ========== SIDEBAR ========== */
  const sidebar = document.getElementById("sidebar");
  const toggleBtn = document.getElementById("toggleBtn");
  const title = document.getElementById("sidebarTitle");
  const toolsToggle = document.getElementById("toolsToggle");
  const toolsMenu = document.getElementById("toolsMenu");
  const menu = document.getElementById("menu");
  const links = menu.querySelectorAll("a");

  function updateToggleUI() {
    if (!sidebar || !toggleBtn) return;

    const isCollapsed = sidebar.classList.contains("collapsed");
    toggleBtn.textContent = isCollapsed ? "☰" : "✖";
    toggleBtn.style.margin = isCollapsed ? "auto" : "";

    links.forEach((link) => {
      link.style.margin = isCollapsed ? "0.2rem auto" : "";
    });

    if (title) {
      title.style.display = isCollapsed ? "none" : "inline-block";
    }
  }

  if (sidebar && toggleBtn) {
    toggleBtn.addEventListener("click", function () {
      // sidebar.classList.toggle('collapsed');
      updateToggleUI();
      toggleCollapsed(sidebar);
    });
  }

  if (toolsToggle && toolsMenu) {
    toolsToggle.addEventListener("click", function (e) {
      e.preventDefault();
      const isOpen = toolsMenu.classList.contains("open");
      toolsMenu.classList.toggle("open", !isOpen);
      toolsMenu.style.display = isOpen ? "none" : "block";
    });
  }

  updateToggleUI();

});

let selectedLang = localStorage.getItem("selectedLang");

if (!selectedLang) {
  selectedLang = "ar";
  localStorage.setItem("selectedLang", selectedLang);
}

let langToggleBtn = document.getElementById("langToggle");
if (selectedLang == "ar") {
  langToggleBtn.innerHTML = `
    <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#813737"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12.913 17H20.087M12.913 17L11 21M12.913 17L15.7783 11.009C16.0092 10.5263 16.1246 10.2849 16.2826 10.2086C16.4199 10.1423 16.5801 10.1423 16.7174 10.2086C16.8754 10.2849 16.9908 10.5263 17.2217 11.009L20.087 17M20.087 17L22 21M2 5H8M8 5H11.5M8 5V3M11.5 5H14M11.5 5C11.0039 7.95729 9.85259 10.6362 8.16555 12.8844M10 14C9.38747 13.7248 8.76265 13.3421 8.16555 12.8844M8.16555 12.8844C6.81302 11.8478 5.60276 10.4266 5 9M8.16555 12.8844C6.56086 15.0229 4.47143 16.7718 2 18" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
  `;
} else if (selectedLang == "en") {
  langToggleBtn.innerHTML = `
    <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#813737"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M12.913 17H20.087M12.913 17L11 21M12.913 17L15.7783 11.009C16.0092 10.5263 16.1246 10.2849 16.2826 10.2086C16.4199 10.1423 16.5801 10.1423 16.7174 10.2086C16.8754 10.2849 16.9908 10.5263 17.2217 11.009L20.087 17M20.087 17L22 21M2 5H8M8 5H11.5M8 5V3M11.5 5H14M11.5 5C11.0039 7.95729 9.85259 10.6362 8.16555 12.8844M10 14C9.38747 13.7248 8.76265 13.3421 8.16555 12.8844M8.16555 12.8844C6.81302 11.8478 5.60276 10.4266 5 9M8.16555 12.8844C6.56086 15.0229 4.47143 16.7718 2 18" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
  `;
}

langToggleBtn.addEventListener("click", toggleLanguage);

if (!selectedLang) {
  // Get the current URL
  let url = new URL(window.location.href);
  // Set the "lang" parameter to "ar"
  url.searchParams.set("lang", selectedLang);
  // Replace the current URL with the updated URL
  window.history.replaceState({}, document.title, url.toString());
}

$(document).ready(function () {
  // const updatesButton = document.getElementById("updates-svg");

  // // دالة لإضافة حركة الوميض
  // function blinkButton() {
  //   updatesButton.classList.add("blink");

  //   // إزالة الحركة بعد فترة قصيرة
  //   setTimeout(() => {
  //     updatesButton.classList.remove("blink");
  //   }, 2000); // مدة الوميض
  // }

  // // تنفيذ حركة الوميض لأول مرة عند تحميل الصفحة
  // window.onload = function () {
  //   blinkButton();

  //   // تكرار الحركة كل 10 ثواني
  //   setInterval(blinkButton, 6000); // 10000 مللي ثانية = 10 ثواني
  // };

  var table = $(".table:not(#clientsTable)").DataTable({
    scrollX: true,
    order: [[0, "desc"]],
    "data-ordering": true,
    dom: "<'top'Blif>rt<'bottom'ip><'clear'>",
    buttons: [
      {
        extend: "excel",
        text: "Export to Excel",
      },
    ],
  });

  // إزالة الكلاس 'selected' من جميع الصفوف عند تحميل الصفحة
  table.rows().every(function () {
    this.nodes().to$().removeClass("selected");
  });

  // ------------
  table.on("click", "tbody tr", function () {
    var $row = table.row(this).nodes().to$();
    var hasClass = $row.hasClass("selected");
    if (hasClass) {
      $row.removeClass("selected");
    } else {
      $row.addClass("selected");
    }
  });
});

document.querySelectorAll('input[type="date"]').forEach(function (input) {
  const today = new Date();
  const offset = today.getTimezoneOffset();
  today.setMinutes(today.getMinutes() - offset);
  const localDate = today.toISOString().split("T")[0];
  input.value = localDate;
});

document.querySelectorAll('input[type="datetime-local"]').forEach(function (input) {
  const now = new Date();
  const offset = now.getTimezoneOffset();
  now.setMinutes(now.getMinutes() - offset);
  if (input.id === "last_activity_date") {
    return; // تخطي هذا الحقل
  }

  if (input.id === "start_date") {
    // ضبط الوقت ليكون 12:00 AM
    const localDateTime = now.toISOString().split("T")[0] + "T00:00";
    input.value = localDateTime;
  } else if (input.id === "end_date") {
    // ضبط الوقت ليكون 11:59 PM
    const localDateTime = now.toISOString().split("T")[0] + "T23:59";
    input.value = localDateTime;
  } else {
    // استخدام الوقت الحالي
    const localDateTime = now.toISOString().slice(0, 16); // YYYY-MM-DDTHH:mm
    input.value = localDateTime;
  }
});

const _url = window.location.href;
const urlObject = new URL(_url);
const pathSegments = urlObject.pathname.split("/");
const pageName = pathSegments[pathSegments.length - 1].split(".")[0] + "-li";
const staticPageName = pathSegments[pathSegments.length - 1].split(".")[0];
const pageElement = document.getElementById(pageName);
if (pageElement) {
  pageElement.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.3), 0 6px 20px rgba(0, 0, 0, 0.3)";
  pageElement.style.fontWeight = "bold";
} else {
  console.warn(`No element found with id: ${pageName}`);
}

// ------------الحصول على رصيد البنك-----------
$(document).ready(function () {
  $.ajax({
    url: "./api/get_bank_balance.php",
    type: "GET",
    data: { id: 1 },
    dataType: "json",
    success: function (data) {
      $("#bank-home-page").text(data.account_amount);
      $("#account_amount").val(data.account_amount);
      $("#facilities_amount").val(data.facilities_amount);
      let aa = data.account_amount < 0 ? "text-danger" : "";
      let fa = data.facilities_amount < 0 ? "text-danger" : "";
      $(".account_amount").append("<span class='" + aa + "'>" + translate("main", lang) + ": " + data.account_amount + "</span>");
      $(".account_amount").append("<span class='" + fa + "'> | " + translate("facilities", lang) + ": " + data.facilities_amount + "</span>");
      $(".account_amount").append(' <svg xmlns="http://www.w3.org/2000/svg" fill="#00c800" width="20px" height="20px" viewBox="0 0 16 16"><path d="M12.32 8a3 3 0 0 0-2-.7H5.63A1.59 1.59 0 0 1 4 5.69a2 2 0 0 1 0-.25 1.59 1.59 0 0 1 1.63-1.33h4.62a1.59 1.59 0 0 1 1.57 1.33h1.5a3.08 3.08 0 0 0-3.07-2.83H8.67V.31H7.42v2.3H5.63a3.08 3.08 0 0 0-3.07 2.83 2.09 2.09 0 0 0 0 .25 3.07 3.07 0 0 0 3.07 3.07h4.74A1.59 1.59 0 0 1 12 10.35a1.86 1.86 0 0 1 0 .34 1.59 1.59 0 0 1-1.55 1.24h-4.7a1.59 1.59 0 0 1-1.55-1.24H2.69a3.08 3.08 0 0 0 3.06 2.73h1.67v2.27h1.25v-2.27h1.7a3.08 3.08 0 0 0 3.06-2.73v-.34A3.06 3.06 0 0 0 12.32 8z"/></svg>');
    },
    error: function (xhr, status, error) {
      console.error("Error: " + error);
    },
  });
});

//---------------- التأكيد على حذف مستخدم-------------------
$("#users_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./settings.php?user-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ---------------تجهيز مستخدم للتعديل------------
$(document).ready(function () {
  $("#users_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("user-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    $("#password").removeAttr("required");
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_user.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        $("#username").val(data.username);
        $("#full_name").val(data.full_name);
        $("#role").val(data.role);
        $("#status").val(data.status);
        $("#location").val(data.location);
        $("#insert-user-btn").hide();
        $("#update-user-btn").attr("data-id", id).show();
        $("#cancel-user-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});

//---------------- التأكيد على حذف سائق-------------------
$("#drivers_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./drivers.php?driver-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ---------------تجهيز سائق للتعديل------------
$(document).ready(function () {
  $("#drivers_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("driver-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_driver.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        $("#driver_name").val(data.driver_name);
        $("#vehicle_number").val(data.vehicle_number);
        $("#phone_number").val(data.phone);
        $("#notes").val(data.notes);
        $("#insert-driver-btn").hide();
        $("#update-driver-btn").attr("data-id", id).show();
        $("#cancel-driver-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});

// ------- البحث عن سائق------------
$(document).ready(function () {
  var validDriverIds = {};
  var oldValue;

  $("#driver_id").on("focus", function () {
    oldValue = $(this).val();
    $(this)
      .autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "./api/search_for_driver.php",
            type: "GET",
            dataType: "json",
            data: {
              term: request.term,
            },
            success: function (data) {
              if (data.length === 0) {
                response([
                  {
                    label: translate("no_results", lang),
                    value: "",
                    valid: false,
                  },
                ]);
              } else {
                data = data.map(function (item) {
                  item.valid = true;
                  validDriverIds[item.label] = item.value;
                  return item;
                });
                response(data);
              }
            },
          });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
          if (ui.item.valid) {
            $("#driver_id").val(ui.item.label);
            if($("#driver_uae_id")){
              $("#driver_uae_id").val(ui.item.hidden_values.uae_id);
            }
            if($("#driver_passport_number")){
              $("#driver_passport_number").val(ui.item.hidden_values.passport_number);
            }
            // addSelectedItem(ui.item.label);
          } else {
            $("#driver_id").val("");
          }
          return false;
        },
      })
      .autocomplete("instance")._renderItem = function (ul, item) {
      return $("<li>")
        .append("<div>" + item.label + "</div>")
        .appendTo(ul);
    };
  });

  $("#driver_id").on("blur", function () {
    var newValue = $(this).val();
    if (!validDriverIds.hasOwnProperty(newValue)) {
      $(this).val(oldValue);
    }
  });

  // التحقق عند إرسال النموذج
  $("form").on("submit", function () {
    var value = $("#driver_id").val();
    if (!validDriverIds.hasOwnProperty(value)) {
      $("#driver_id").val("");
    }
  });
});

// ------- البحث عن موظف / مستخدم------------
$(document).ready(function () {
  var validUserIds = {};
  var oldValue;

  $("#user_id").on("focus", function () {
    oldValue = $(this).val();
    $(this)
      .autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "./api/search_for_user.php",
            type: "GET",
            dataType: "json",
            data: {
              term: request.term,
            },
            success: function (data) {
              if (data.length === 0) {
                response([
                  {
                    label: translate("no_results", lang),
                    value: "",
                    valid: false,
                  },
                ]);
              } else {
                data = data.map(function (item) {
                  item.valid = true;
                  validUserIds[item.label] = item.value;
                  return item;
                });
                response(data);
              }
            },
          });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
          if (ui.item.valid) {
            $("#user_id").val(ui.item.label);
            // addSelectedItem(ui.item.label);
          } else {
            $("#user_id").val("");
          }
          return false;
        },
      })
      .autocomplete("instance")._renderItem = function (ul, item) {
      return $("<li>")
        .append("<div>" + item.label + "</div>")
        .appendTo(ul);
    };
  });

  $("#user_id").on("blur", function () {
    var newValue = $(this).val();
    if (!validUserIds.hasOwnProperty(newValue)) {
      $(this).val(oldValue);
    }
  });

  // التحقق عند إرسال النموذج
  $("form").on("submit", function () {
    var value = $("#user_id").val();
    if (!validUserIds.hasOwnProperty(value)) {
      $("#user_id").val("");
    }
  });
});

// ------- البحث عن شاحن------------
$(document).ready(function () {
  var validShipperIds = {};
  var oldValue;

  $("#shipper_id").on("focus", function () {
    oldValue = $(this).val();
    $(this)
      .autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "./api/search_for_shipper.php",
            type: "GET",
            dataType: "json",
            data: {
              term: request.term,
            },
            success: function (data) {
              if (data.length === 0) {
                response([
                  {
                    label: translate("no_results", lang),
                    value: "",
                    valid: false,
                  },
                ]);
              } else {
                data = data.map(function (item) {
                  item.valid = true;
                  validShipperIds[item.label] = item.value;
                  return item;
                });
                response(data);
              }
            },
          });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
          if (ui.item.valid) {
            $("#shipper_id").val(ui.item.label);
            // addSelectedItem(ui.item.label);
          } else {
            $("#shipper_id").val("");
          }
          return false;
        },
      })
      .autocomplete("instance")._renderItem = function (ul, item) {
      return $("<li>")
        .append("<div>" + item.label + "</div>")
        .appendTo(ul);
    };
  });

  $("#shipper_id").on("blur", function () {
    var newValue = $(this).val();
    if (!validShipperIds.hasOwnProperty(newValue)) {
      $(this).val(oldValue);
    }
  });

  // التحقق عند إرسال النموذج
  $("form").on("submit", function () {
    var value = $("#shipper_id").val();
    if (!validShipperIds.hasOwnProperty(value)) {
      $("#shipper_id").val("");
    }
  });
});

// ------- البحث عن نوع مصاريف الرحلة------------
if (staticPageName == "trips") {
$(document).ready(function () {
  var validFeeTypes = {};

  // وظيفة لحساب وتحديث قيمة السعر بناءً على الكمية والسعر
  function updateFeeAmount(input) {
    const formGroup = $(input).closest(".form-group");
    const quantity = formGroup.find('input[name="quantity[]"]').val();
    const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
    const feeAmount = feeAmountField.data("price");

    const newAmount = (parseFloat(quantity) || 0) * (parseFloat(feeAmount) || 0);
    feeAmountField.val(newAmount.toFixed(2));

    // تحديث المجموع بعد تعديل السعر
    updateTotal();
  }

  // وظيفة لحساب المجموع الإجمالي
  function updateTotal() {
    let total = 0;

    // جمع القيم من حقول السعر
    $("#feesContainer")
      .find(".form-group")
      .each(function () {
        const feeAmount = $(this).find('input[name="fee_amount[]"]').val();
        total += parseFloat(feeAmount) || 0;
      });

    // تحديث حقل المجموع
    $("#total_sum").val(total.toFixed(2));

    // تأكد من أن المتغيرات التالية مُعرفة ولها قيم صحيحة
    const trip_rent = parseFloat($("#trip_rent").val()) || 0;
    const driver_fee = parseFloat($("#driver_fee").val()) || 0;
    const extra_income = parseFloat($("#extra_income").val()) || 0;

    // حساب قيمة المتبقي وتحديث الحقل
    const remaining = (trip_rent - total - driver_fee + extra_income).toFixed(2);
    $("#remaining").val(remaining);
  }

  // استدعاء updateTotal عند حدوث تغييرات في الحقول ذات الصلة
  $(document).on("input", 'input[name="quantity[]"], input[name="fee_amount[]"]', updateTotal);
  $(document).on("change", "#trip_rent, #driver_fee, #extra_income", updateTotal);

  // عند تغيير قيمة حقل الكمية
  $(document).on("input", 'input[name="quantity[]"]', function () {
    updateFeeAmount(this);
  });

  // عند تغيير قيمة حقل السعر مباشرة (إذا كان هناك نوع من التعديل اليدوي)
  $(document).on("input", 'input[name="fee_amount[]"]', function () {
    updateTotal();
  });

  var oldValue;
  // عند إضافة حقل جديد
  $(document).on("focus", ".trip_fee_type_id", function () {
    $(this)
      .autocomplete({
        source: function (request, response) {
          var loadingMessage = {
            label: translate("loading", lang),
            value: "",
            valid: false,
          };
          response([loadingMessage]);
          $.ajax({
            url: "./api/search_for_trip_fees_types.php",
            type: "GET",
            dataType: "json",
            data: {
              term: request.term,
              id: request.term,
            },
            success: function (data) {
              if (data.length === 0) {
                data = [
                  {
                    label: translate("no_results", lang),
                    value: "",
                    valid: false,
                  },
                ];
              } else {
                data = data.map(function (item) {
                  item.valid = true;
                  validFeeTypes[item.label] = item.value;
                  return item;
                });
              }
              response(data);
            },
          });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
          // تحقق مما إذا كان العنصر الذي تم اختياره هو "لا نتائج" أو غير صالح
          if (ui.item.valid) {
            $(this).val(ui.item.label); // تعيين القيمة في الحقل الحالي
            const formGroup = $(this).closest(".form-group");
            const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
            feeAmountField.data("price", ui.item.amount);
            updateFeeAmount(this);
          } else {
            $(this).val(""); // إفراغ الحقل إذا لم يكن العنصر صالحًا
          }
          return false;
        },
        change: function (event, ui) {
          // تحقق مما إذا كان العنصر الذي تم اختياره هو "لا نتائج" أو غير صالح
          if (ui.item && ui.item.valid) {
            $(this).val(ui.item.label); // تعيين القيمة في الحقل الحالي
          } else {
            $(this).val(""); // إفراغ الحقل إذا لم يكن العنصر صالحًا
          }
        },
      })
      .autocomplete("instance")._renderItem = function (ul, item) {
      return $("<li>")
        .append("<div>" + item.label + "</div>")
        .appendTo(ul);
    };
  });

  // عند إزالة حقل
  $(document).on("click", ".btn-remove-fee", function () {
    $(this).closest(".form-group").remove();
    updateTotal();
  });

  // حساب المجموع الأولي عند تحميل الصفحة
  updateTotal();
});
}
// ---------- اضافة وازالة حقول مصاريف الرحلة-----------
$(document).ready(function () {
  $("#addFee").click(function () {
    $("#feesContainer").append(`
          <div class="form-group" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center;">
            <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap; width: 35%">
                <label for="fee_type" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("type", lang)}</label>
                <input type="text" class="form-control trip_fee_type_id" name="fee_type[]" required placeholder="${translate("type_to_search", lang)}...">
                  <input type="hidden" class="form-control" name="bank_deduction[]" required>
            </div> 
            ${
              staticPageName != "invoices"
                ? `   
            <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                <label for="quantity" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("quantity", lang)}</label>
                <input type="number" min="1" value="1" class="form-control" name="quantity[]" required>
            </div>
            `
                : ``
            }
            <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                <label for="fee_amount" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("price", lang)}</label>
                <input type="number" min="0" step="0.01" class="form-control" name="fee_amount[]" required>
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                <label for="description" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("additional_description", lang)} ${translate("optional", lang)}</label>
                <input type="text" class="form-control" name="description[]">
            </div>
            <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                <label for="" style="margin-right: 0.5rem; margin-left: 0.5rem;"><br></label>
                <button type="button" class="btn btn-danger btn-remove-fee"><i class="far fa-times-circle"></i></button>
            </div>
        </div>
      `);
    $("#feesContainer").children().last().find('input[name="fee_type[]"]').focus();
  });
  $(document).on("click", ".btn-remove-fee", function () {
    $(this).closest(".form-group").remove();
  });
});

//---------------- التأكيد على حذف نوع مصاريف رحلة-------------------
$("#expenses_types_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./trips.php?type-of-trip-expenses-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ---------------تجهيز نوع مصروف رحلة للتعديل------------
$(document).ready(function () {
  $("#expenses_types_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("type-of-trip-expenses-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_type_of_trip_expenses.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        $("#description").val(data.fee_name);
        $("#amount").val(data.fee_amount);

        $("#insert-type-of-trip-expenses-btn").hide();
        $("#update-type-of-trip-expenses-btn").attr("data-id", id).show();
        $("#cancel-type-of-trip-expenses-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});

//---------------- التأكيد على حذف رحلة-------------------
$("#trips_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./trips.php?trip-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// // ---------------تجهيز رحلة للتعديل------------
// // ---------- حلقة لجلب مصاريف الرحلة وانشاء الحقول من اجل التعديل مع id كل مصروف -----------
if (staticPageName == "trips") {
$(document).ready(function () {
  var validFeeTypes = {};

  // وظيفة لحساب وتحديث قيمة السعر بناءً على الكمية والسعر
  function updateFeeAmount(input) {
    const formGroup = $(input).closest(".form-group");
    const quantity = formGroup.find('input[name="quantity[]"]').val();
    const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
    const feeAmount = feeAmountField.data("price");

    const newAmount = (parseFloat(quantity) || 0) * (parseFloat(feeAmount) || 0);
    feeAmountField.val(newAmount.toFixed(2));

    // تحديث المجموع بعد تعديل السعر
    updateTotal();
  }

  // وظيفة لحساب المجموع الإجمالي
  function updateTotal() {
    let total = 0;

    // جمع القيم من حقول السعر
    $("#feesContainer")
      .find(".form-group")
      .each(function () {
        const feeAmount = $(this).find('input[name="fee_amount[]"]').val();
        total += parseFloat(feeAmount) || 0;
      });

    // تحديث حقل المجموع
    $("#total_sum").val(total.toFixed(2));

    // تأكد من أن المتغيرات التالية مُعرفة ولها قيم صحيحة
    const trip_rent = parseFloat($("#trip_rent").val()) || 0;
    const driver_fee = parseFloat($("#driver_fee").val()) || 0;
    const extra_income = parseFloat($("#extra_income").val()) || 0;

    // حساب قيمة المتبقي وتحديث الحقل
    const remaining = (trip_rent - total - driver_fee + extra_income).toFixed(2);
    $("#remaining").val(remaining);
  }

  // البحث عن نوع مصاريف الرحلة
  $(document).on("focus", ".trip_fee_type_id", function () {
    $(this)
      .autocomplete({
        source: function (request, response) {
          var loadingMessage = {
            label: translate("loading", lang),
            value: "",
            valid: false,
          };
          response([loadingMessage]);
          $.ajax({
            url: "./api/search_for_trip_fees_types.php",
            type: "GET",
            dataType: "json",
            data: {
              term: request.term,
              id: request.term,
            },
            success: function (data) {
              if (data.length === 0) {
                data = [
                  {
                    label: translate("no_results", lang),
                    value: "",
                    valid: false,
                  },
                ];
              } else {
                data = data.map(function (item) {
                  item.valid = true;
                  validFeeTypes[item.label] = item.value;
                  return item;
                });
              }
              response(data);
            },
          });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
          // تحقق مما إذا كان العنصر الذي تم اختياره هو "لا نتائج" أو غير صالح
          if (ui.item.valid) {
            $(this).val(ui.item.label); // تعيين القيمة في الحقل الحالي
            const formGroup = $(this).closest(".form-group");
            const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
            feeAmountField.data("price", ui.item.amount);
            updateFeeAmount(this);
          } else {
            $(this).val(""); // إفراغ الحقل إذا لم يكن العنصر صالحًا
          }
          return false;
        },
        change: function (event, ui) {
          // تحقق مما إذا كان العنصر الذي تم اختياره هو "لا نتائج" أو غير صالح
          if (ui.item && ui.item.valid) {
            $(this).val(ui.item.label); // تعيين القيمة في الحقل الحالي
          } else {
            $(this).val(""); // إفراغ الحقل إذا لم يكن العنصر صالحًا
          }
        },
      })
      .autocomplete("instance")._renderItem = function (ul, item) {
      return $("<li>")
        .append("<div>" + item.label + "</div>")
        .appendTo(ul);
    };
  });

  // عند إزالة حقل
  $(document).on("click", ".btn-remove-fee", function () {
    $(this).closest(".form-group").remove();
    updateTotal();
  });

  // حساب المجموع الأولي عند تحميل الصفحة
  updateTotal();

  // معالجة زر التعديل
  $("#trips_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("trip-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_trip.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        var driverFormat = data.driver_id + "- " + data.driver_name + " | " + data.vehicle_number;
        $("#driver_id").val(driverFormat);
        $("#hidden_driver_id").val(data.driver_id);

        $("#trip_rent").val(data.trip_rent);
        $("#extra_income").val(data.extra_income);
        $("#trip_date").val(data.trip_date);
        $("#driver_fee").val(data.driver_fee);
        $("#extra_income_des").val(data.extra_income_des);
        $("#destination").val(data.destination);
        $("#notes").val(data.notes);
        $("#remaining").val(data.remaining);

        $("#insert-trip-btn").hide();
        $("#update-trip-btn").attr("data-id", id).show();
        $("#cancel-trip-btn").show();

        // جلب مصاريف الرحلة
        $.ajax({
          url: "./api/get_trip_fees.php",
          type: "GET",
          data: { trip_id: id },
          success: async function (response) {
            // تحويل الاستجابة JSON إلى كائن JavaScript
            const fees = JSON.parse(response);

            // إفراغ الحاوية قبل إضافة الحقول
            $("#feesContainer").empty();

            // إنشاء الحقول بناءً على البيانات المستلمة
            fees.forEach((fee) => {
              var fee_type_data = fee.trip_fee_type_id + "- " + fee.fee_type_name;
              if (fee.fee_type_amount != 0.0) {
                fee_type_data += " | " + fee.fee_type_amount;
              }

              $("#feesContainer").append(`
                                  <div class="form-group" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center;">
                                    <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap; width: 35%">
                                      <label for="fee_type" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("type", lang)}</label>
                                      <input type="text" class="form-control trip_fee_type_id" name="fee_type[]" value="${fee_type_data}" required placeholder="${translate("type_to_search", lang)}...">
                                      <input type="hidden" class="form-control" name="fee_ids[]" value="${fee.id}">
                                  </div>    
                                  <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                      <label for="quantity" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("quantity", lang)}</label>
                                      <input type="number" min="1" class="form-control" name="quantity[]" value="${fee.quantity}" required>
                                  </div>
                                  <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                      <label for="fee_amount" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("price", lang)}</label>
                                      <input type="number" min="0" step="0.01" class="form-control" name="fee_amount[]" value="${fee.amount}" required>
                                  </div>
                                  <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                      <label for="description" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("additional_description", lang)} ${translate("optional", lang)}</label>
                                      <input type="text" class="form-control" name="description[]" value="${fee.description}">
                                  </div>
                                    <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                        <label for="" style="margin-right: 0.5rem; margin-left: 0.5rem;"><br></label>
                                        <button type="button" class="btn btn-danger btn-remove-fee"><i class="far fa-times-circle"></i></button>
                                    </div>
                              </div>
                          `);

              // تحديث قيمة المبلغ بناءً على الكمية والسعر لكل حقل
              const formGroup = $("#feesContainer").find(".form-group").last(); // الحصول على آخر حقل مضاف
              const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
              feeAmountField.data("price", fee.fee_type_amount);
            });
            // ديث المجموع بعد إضافة الحقول الجديدة
            // updateTotal();
            await fetchAndCalculateTotal();
            Swal.close();
          },
          error: function (xhr, status, error) {
            Swal.fire({
              icon: "error",
              title: translate("error", lang),
              text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
            });
          },
        });
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });

  async function fetchAndCalculateTotal() {
    // ننتظر قليلاً حتى يتم إضافة الحقول مع قيمها
    await new Promise((resolve) => setTimeout(resolve, 100));

    // وظيفة لحساب المجموع الإجمالي
    let total = 0;
    // جمع القيم من حقول السعر
    $("#feesContainer")
      .find(".form-group")
      .each(function () {
        const feeAmount = $(this).find('input[name="fee_amount[]"]').val();
        total += parseFloat(feeAmount) || 0;
      });
    // تحديث حقل المجموع
    $("#total_sum").val(total.toFixed(2));
    // تأكد من أن المتغيرات التالية مُعرفة ولها قيم صحيحة
    const trip_rent = parseFloat($("#trip_rent").val()) || 0;
    const driver_fee = parseFloat($("#driver_fee").val()) || 0;
    const extra_income = parseFloat($("#extra_income").val()) || 0;
    // حساب قيمة المتبقي وتحديث الحقل
    const remaining = (trip_rent - total - driver_fee + extra_income).toFixed(2);
    $("#remaining").val(remaining);
  }

  // استدعاء updateTotal عند حدوث تغييرات في الحقول ذات الصلة
  $(document).on("input", 'input[name="quantity[]"], input[name="fee_amount[]"]', updateTotal);
  $(document).on("change", "#trip_rent, #driver_fee, #extra_income", updateTotal);

  // عند تغيير قيمة حقل الكمية
  $(document).on("input", 'input[name="quantity[]"]', function () {
    updateFeeAmount(this);
  });

  // عند تغيير قيمة حقل السعر مباشرة (إذا كان هناك نوع من التعديل اليدوي)
  $(document).on("input", 'input[name="fee_amount[]"]', function () {
    updateTotal();
  });
});
}
//---------------- التأكيد على حذف مكتب سعودي-------------------
$("#sau_offices_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./sau_bills.php?sau-office-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ---------------تجهيز مكتب سعودي للتعديل------------
$(document).ready(function () {
  $("#sau_offices_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("sau-office-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_sau_office.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        $("#office_name").val(data.office_name);
        $("#entity_type").val(data.entity_type);
        $("#license_number").val(data.license_number);
        $("#notes").val(data.notes);

        $("#insert-sau-office-btn").hide();
        $("#update-sau-office-btn").attr("data-id", id).show();
        $("#cancel-sau-office-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});

// ------- البحث عن مكتب سعودي------------
$(document).ready(function () {
  var validSauOfficeIds = {};
  var oldValue;

  $("#sau_office_id").on("focus", function () {
    oldValue = $(this).val();
    $(this)
      .autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "./api/search_for_sau_office.php",
            type: "GET",
            dataType: "json",
            data: {
              term: request.term,
              lang: lang,
            },
            success: function (data) {
              if (data.length === 0) {
                response([
                  {
                    label: translate("no_results", lang),
                    value: "",
                    valid: false,
                  },
                ]);
              } else {
                data = data.map(function (item) {
                  item.valid = true;
                  validSauOfficeIds[item.label] = item.value;
                  return item;
                });
                response(data);
                // console.log(validSauOfficeIds);
              }
            },
          });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
          if (ui.item.valid) {
            $("#sau_office_id").val(ui.item.label);
          } else {
            $("#sau_office_id").val("");
          }
          return false;
        },
      })
      .autocomplete("instance")._renderItem = function (ul, item) {
      return $("<li>")
        .append("<div>" + item.label + "</div>")
        .appendTo(ul);
    };
  });

  $("#sau_office_id").on("blur", function () {
    var newValue = $(this).val();
    if (!validSauOfficeIds.hasOwnProperty(newValue)) {
      $(this).val(oldValue);
    }
  });

  // التحقق عند إرسال النموذج
  $("form").on("submit", function () {
    var value = $("#sau_office_id").val();
    if (!validSauOfficeIds.hasOwnProperty(value)) {
      $("#sau_office_id").val("");
    }
  });
});

// ---------------تجهيز بيان سعودي للتعديل------------
$(document).ready(function () {
  $("#sau_bills_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("sau-bill-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_sau_bill.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        var sauOfficeFormat = data.sau_office_id + "- " + data.office_name + " | " + translate(data.entity_type, lang) + " | " + data.license_number;
        $("#sau_office_id").val(sauOfficeFormat);
        $("#hidden_sau_office_id").val(data.sau_office_id);

        $("#driver_name").val(data.driver_name);
        $("#sau_bill_number").val(data.sau_bill_number);
        $("#bill_date").val(data.bill_date);
        $("#vehicle_number").val(data.vehicle_number);
        $("#nob").val(data.nob);
        $("#nov").val(data.nov);
        $("#destination").val(data.destination);
        $("#price").val(data.price);
        $("#payment_status").val(data.payment_status);
        $("#notes").val(data.notes);

        $("#insert-sau-bill-btn").hide();
        $("#update-sau-bill-btn").attr("data-id", id).show();
        $("#cancel-sau-bill-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});

//---------------- التأكيد على حذف بيان سعودي-------------------
$("#sau_bills_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");

  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./sau_bills.php?sau-bill-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

//---------------- التأكيد على حذف نوع رسوم خدمة-------------------
$("#service_fees_types_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./services.php?type-of-service-fee-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ---------------تجهيز نوع رسوم خدمة للتعديل------------
$(document).ready(function () {
  $("#service_fees_types_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("type-of-service-fee-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_type_of_service_fee.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        $("#description").val(data.fee_name);
        $("#bank_deduction").val(data.bank_deduction);
        $("#amount").val(data.fee_amount);

        $("#insert-type-of-service-fee-btn").hide();
        $("#update-type-of-service-fee-btn").attr("data-id", id).show();
        $("#cancel-type-of-service-fee-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});























// // ---------------تجهيز طلب خدمة ------------
if (staticPageName == "service-request") {
$(document).ready(function () {

  $("#service-request-form").on("submit", function(event){
      event.preventDefault(); // منع الإرسال الافتراضي

      const form = this; // حفظ المرجع للفورم الأصلي

    // جلب القيم من الحقول
    const serviceType = $(form).find('select[name="service_type"] option:selected').text();
    const driver = $(form).find('input[name="driver_id"]').val();
    const parts = driver.split('|');
    const driverName = parts[0] ? parts[0].trim() : '';
    const vehicleNumber = parts[1] ? parts[1].trim() : '';
    const userName = $(form).find('input[name="user_id"]').val();
    const shipperName = $(form).find('input[name="shipper_id"]').val();
    const driverUaeId = $(form).find('input[name="driver_uae_id"]').val();
    const driverPassportNumber = $(form).find('input[name="driver_passport_number"]').val();
    const textAlignStyle = lang == "ar" ? "text-align: right;" : "text-align: left;";
    let htmlContent = `
          <div style="${textAlignStyle}">
            <p><strong>${translate("service_type", lang)}:</strong> ${serviceType}</p>
            <p><strong>${translate("driver_name", lang)}:</strong> ${driverName}</p>
            <p><strong>${translate("vehicle_number", lang)}:</strong> ${vehicleNumber}</p>
            <p><strong>${translate("uae_id", lang)}:</strong> ${driverUaeId}</p>
            <p><strong>${translate("passport_number", lang)}:</strong> ${driverPassportNumber}</p>
            <p><strong>${translate("shipper_name", lang)}:</strong> ${shipperName}</p>
            <p><strong>${translate("applicant", lang)}:</strong> ${userName}</p>
          </div>
        `;
      Swal.fire({
          icon: "question",
          title: translate("are_you_sure", lang),
           html: htmlContent,
          // text: translate("check_the_data_before_submit", lang),
          showConfirmButton: true,
          showCancelButton: true,
          confirmButtonText: translate("send_request", lang),
          cancelButtonText: translate("cancel", lang),
      }).then((result) => {
          if (result.isConfirmed) {
          $('<input>')
            .attr({
              type: 'hidden',
              name: 'insert-service-request',
              value: '1'
            })
            .appendTo(form);
          form.submit(); // استخدم HTMLFormElement.submit() لتجنب إعادة استدعاء الـ handler
          } else if (result.isDismissed) {
              Swal.fire({
                  icon: "info",
                  title: translate("canceled", lang),
                  text: translate("request_has_been_canceled", lang),
                  showConfirmButton: false,
                  timer: 1000,
              });
          }
      });
  });

  // معالجة زر التعديل
  $("#service_requests_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("service-request-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_service_request.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        $("#service_type").val(data.service_type_id);
        $("#driver_id").val(`${data.driver_id}- ${data.driver_name} | ${data.driver_vehicle_number}`);
        $("#user_id").val(`${data.user_id}- ${data.user_name}`);
        $("#shipper_id").val(`${data.shipper_id}- ${data.shipper_name} | ${data.shipper_office_name}`);
        $("#notes").val(data.notes);

        $("#insert-service-request-btn").hide();
        $("#update-service-request-btn").attr("data-id", id).show();
        $("#cancel-service-request-btn").show();
        
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });





//---------------- التأكيد على حذف خدمة-------------------
$("#service_requests_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./service-request.php?service-request-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});






});
}


































// // ---------------تجهيز خدمة ------------
if (staticPageName == "services" || staticPageName == "reports") {
$(document).ready(function () {
  var validFeeTypes = {};

  // وظيفة لحساب وتحديث قيمة السعر بناءً على الكمية والسعر
  function updateFeeAmount(input) {     
    const formGroup = $(input).closest(".form-group");
    const quantity = formGroup.find('input[name="quantity[]"]').val();
    const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
    const unitPrice = parseFloat(feeAmountField.data("price")) || 0;

    const feeBankAmountField = formGroup.find('input[name="bank_deduction[]"]');
    const baseBank = parseFloat(feeBankAmountField.data("bank")) || 0;
    

    const newAmount = quantity * unitPrice;
    const newBankAmount = quantity * baseBank;

    feeAmountField.val(newAmount.toFixed(2));
    feeBankAmountField.data("bank_final", newBankAmount.toFixed(2));
    feeBankAmountField.val(newBankAmount.toFixed(2));

    // تحديث المجموع بعد تعديل السعر
    updateTotal();
  }

  // وظيفة لحساب المجموع الإجمالي
  function updateTotal() {
    let total = 0;
    let totalBank = 0;

    // جمع القيم من حقول السعر
    $("#feesContainer")
      .find(".form-group")
      .each(function () {
        const feeAmount = $(this).find('input[name="fee_amount[]"]').val();
        total += parseFloat(feeAmount) || 0;
        
        const bankValue = $(this).find('input[name="bank_deduction[]"]').data("bank_final");
        totalBank += parseFloat(bankValue) || 0;
      });

    // تحديث حقل المجموع
    $("#total_sum").val(total.toFixed(2));
    $("#total_bank_sum").val(totalBank.toFixed(2));
  }

  // البحث عن نوع رسوم الخدمة
    $(document).on("focus", ".trip_fee_type_id", function () {
      $(this)
        .autocomplete({
          source: function (request, response) {
            var loadingMessage = {
              label: translate("loading", lang),
              value: "",
              valid: false,
            };
            response([loadingMessage]);
            $.ajax({
              url: "./api/search_for_service_fees_types.php",
              type: "GET",
              dataType: "json",
              data: {
                term: request.term,
              },
              success: function (data) {
                if (data.length === 0) {
                  data = [
                    {
                      label: translate("no_results", lang),
                      value: "",
                      valid: false,
                    },
                  ];
                } else {
                  data = data.map(function (item) {
                    item.valid = true;
                    validFeeTypes[item.label] = item.value;
                    return item;
                  });
                }
                response(data);
              },
            });
          },
          minLength: 1,
          delay: 500,
          select: function (event, ui) {
            // تحقق مما إذا كان العنصر الذي تم اختياره هو "لا نتائج" أو غير صالح
            if (ui.item.valid) {
              $(this).val(ui.item.label); // تعيين القيمة في الحقل الحالي
              const formGroup = $(this).closest(".form-group");
              const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
              const feeBankAmountField = formGroup.find('input[name="bank_deduction[]"]');
                // كنت عند تجربة تعطيل الداتا لتجربة الفاليو بدل منها
              feeAmountField.data("price", ui.item.amount);
              feeBankAmountField.data("bank", ui.item.bank); // // تعيين القيمة الابتدائية للخصم البنكي
              feeBankAmountField.data("bank_final", 0); // تعيين القيمة الابتدائية لاجمالي الخصم البنكي
              feeBankAmountField.val(0); // تعيين القيمة الابتدائية لاجمالي الخصم البنكي
              
              updateFeeAmount(this);
            } else {
              $(this).val(""); // إفراغ الحقل إذا لم يكن العنصر صالحًا
            }
            return false;
          },
          change: function (event, ui) {
            // تحقق مما إذا كان العنصر الذي تم اختياره هو "لا نتائج" أو غير صالح
            if (ui.item && ui.item.valid) {
              $(this).val(ui.item.label); // تعيين القيمة في الحقل الحالي
            } else {
              $(this).val(""); // إفراغ الحقل إذا لم يكن العنصر صالحًا
            }
          },
        })
        .autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
          .append("<div>" + item.label + "</div>")
          .appendTo(ul);
      };
    });
  

  // عند إزالة حقل
  $(document).on("click", ".btn-remove-fee", function () {
    $(this).closest(".form-group").remove();
    updateTotal();
  });

  // حساب المجموع الأولي عند تحميل الصفحة
  updateTotal();

  // معالجة زر التعديل
  $("#services_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var oldBankAmount = $(this).data("old_bank_amount");
    var url = new URL(window.location.href);
    url.searchParams.set("service-uid", id);
    url.searchParams.set("old-bank-amount", oldBankAmount);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_service.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        $("#driver_name").val(data.driver_name);
        $("#vehicle_number").val(data.vehicle_number);
        $("#nov").val(data.nov);
        $("#notes").val(data.notes);
        $("#service_date").val(data.service_date);
        $("#payment_status").val(data.payment_status);
        $("#phone_number").val(data.phone_number);

        $("#insert-service-btn").hide();
        $("#update-service-btn").attr("data-id", id).show();
        $("#cancel-service-btn").show();

        // جلب مصاريف الرحلة
        $.ajax({
          url: "./api/get_service_fees.php",
          type: "GET",
          data: { id: id },
          success: async function (response) {
            // تحويل الاستجابة JSON إلى كائن JavaScript
            const fees = JSON.parse(response);
            // إفراغ الحاوية قبل إضافة الحقول
            $("#feesContainer").empty();

            // إنشاء الحقول بناءً على البيانات المستلمة
            fees.forEach((fee) => {
              var fee_type_data = fee.service_fee_type_id + "- " + fee.fee_type_name;
              if (fee.fee_type_amount != 0.0) {
                fee_type_data += " | " + fee.fee_type_amount;
              }
              if (fee.fee_type_bank_deduction != 0.0) {
                fee_type_data += " | (-" + fee.fee_type_bank_deduction + ")";
              }
              $("#feesContainer").append(`
                                <div class="form-group" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center;">
                                  <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap; width: 35%">
                                    <label for="fee_type" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("type", lang)}</label>
                                    <input type="text" class="form-control trip_fee_type_id" name="fee_type[]" value="${fee_type_data}" required placeholder="${translate("type_to_search", lang)}...">
                                    <input type="hidden" class="form-control" name="fee_ids[]" value="${fee.id}">
                                    <input type="hidden" class="form-control" name="bank_deduction[]" value="${fee.bank_deduction_amount}" data-bank_final="${fee.bank_deduction_amount}">
                                </div>    
                                <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                    <label for="quantity" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("quantity", lang)}</label>
                                    <input type="number" min="1" class="form-control" name="quantity[]" value="${fee.quantity}" required>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                    <label for="fee_amount" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("price", lang)}</label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="fee_amount[]" value="${fee.amount}" required>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                    <label for="description" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("additional_description", lang)} ${translate("optional", lang)}</label>
                                    <input type="text" class="form-control" name="description[]" value="${fee.description}">
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                    <label for="" style="margin-right: 0.5rem; margin-left: 0.5rem;"><br></label>
                                    <button type="button" class="btn btn-danger btn-remove-fee"><i class="far fa-times-circle"></i></button>
                                </div>
                            </div>
                        `);

              // تحديث قيمة المبلغ بناءً على الكمية والسعر لكل حقل
              const formGroup = $("#feesContainer").find(".form-group").last(); // الحصول على آخر حقل مضاف
              const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
              const feeBankAmountField = formGroup.find('input[name="bank_deduction[]"]');
              feeAmountField.data("price", fee.fee_type_amount);
              feeBankAmountField.data("bank", fee.fee_type_bank_deduction);
              feeBankAmountField.data("bank_final", fee.bank_deduction_amount);
              feeBankAmountField.val(fee.bank_deduction_amount);

            });
            // ديث المجموع بعد إضافة الحقول الجديدة
            // updateTotal();
            await fetchAndCalculateTotal();
            Swal.close();
          },
          error: function (xhr, status, error) {
            Swal.fire({
              icon: "error",
              title: translate("error", lang),
              text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
            });
          },
        });
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });

  async function fetchAndCalculateTotal() {
    await new Promise((resolve) => setTimeout(resolve, 100));

    let total = 0;
    let totalBank = 0;

    $("#feesContainer")
      .find(".form-group")
      .each(function () {
        const feeAmount = $(this).find('input[name="fee_amount[]"]').val();
        total += parseFloat(feeAmount) || 0;
        
        const bankValue = $(this).find('input[name="bank_deduction[]"]').data("bank_final");
        totalBank += parseFloat(bankValue) || 0;
      });

    $("#total_sum").val(total.toFixed(2));
    $("#total_bank_sum").val(totalBank.toFixed(2));
  }

  // عند تغيير قيمة حقل الكمية
  $(document).on("input", 'input[name="quantity[]"]', function () {
    updateFeeAmount(this);
  });

  // عند تغيير قيمة حقل السعر مباشرة (إذا كان هناك نوع من التعديل اليدوي)
  $(document).on("input", 'input[name="fee_amount[]"]', function () {
    updateTotal();
  });
});
}

//---------------- التأكيد على حذف خدمة-------------------
$("#services_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  var oldBankAmount = $(this).data("old_bank_amount");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./services.php?service-did=${id}&old-bank-amount=${oldBankAmount}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ------- البحث عن نوع مصروف------------
$(document).ready(function () {
  var validExpenseTypeIds = {};
  var oldValue;

  $("#expense_type_id").on("focus", function () {
    oldValue = $(this).val();
    $(this)
      .autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "./api/search_for_expenses_types.php",
            type: "GET",
            dataType: "json",
            data: {
              term: request.term,
            },
            success: function (data) {
              if (data.length === 0) {
                response([
                  {
                    label: translate("no_results", lang),
                    value: "",
                    valid: false,
                  },
                ]);
              } else {
                data = data.map(function (item) {
                  item.valid = true;
                  validExpenseTypeIds[item.label] = item.value;
                  return item;
                });
                response(data);
              }
            },
          });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
          if (ui.item.valid) {
            $("#expense_type_id").val(ui.item.label);
            $("#amount").val(ui.item.amount);
          } else {
            $("#expense_type_id").val("");
            $("#amount").val("");
          }
          return false;
        },
      })
      .autocomplete("instance")._renderItem = function (ul, item) {
      return $("<li>")
        .append("<div>" + item.label + "</div>")
        .appendTo(ul);
    };
  });

  $("#expense_type_id").on("blur", function () {
    var newValue = $(this).val();
    if (!validExpenseTypeIds.hasOwnProperty(newValue)) {
      $(this).val(oldValue);
    }
  });

  // التحقق عند إرسال النموذج
  $("form").on("submit", function () {
    var value = $("#expense_type_id").val();
    if (!validExpenseTypeIds.hasOwnProperty(value)) {
      $("#expense_type_id").val("");
    }
  });
});

//---------------- التأكيد على حذف نوع مصروف-------------------
$("#expenses_types_table1").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./expenses.php?expense-type-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ---------------تجهيز نوع مصروف للتعديل------------
$(document).ready(function () {
  $("#expenses_types_table1").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("expense-type-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_expense_type.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        $("#expense_type_name").val(data.name);
        $("#expense_type_amount").val(data.amount);
        $("#expense_type_notes").val(data.notes);

        $("#insert-expense-type-btn").hide();
        $("#update-expense-type-btn").attr("data-id", id).show();
        $("#cancel-expense-type-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});

//---------------- التأكيد على حذف مصروف-------------------
$("#expenses_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  var old_expense_amount = $(this).data("old_expense_amount");
  var bank_deduction = $(this).data("bank_deduction");
  var facilities_account = $(this).data("facilities_account");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./expenses.php?expense-did=${id}&old-expense-amount=${old_expense_amount}&facilities-account=${facilities_account}&bank-deduction=${bank_deduction}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ---------------تجهيز مصروف للتعديل------------
$(document).ready(function () {
  $("#expenses_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("expense-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_expense.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        var expense_type_id = data.expense_type_id + "- " + data.etName;
        if (data.etAmount != 0.0) {
          expense_type_id += " | " + data.etAmount;
        }
        $("#expense_type_id").val(expense_type_id);
        $("#hidden_expense_type_id").val(data.expense_type_id);
        $("#amount").val(data.amount);
        $("#old_expense_amount").val(data.amount);
        $("#description").val(data.description);
        $("#expense_date").val(data.expense_date);
        $("#notes").val(data.notes);

        var newE = null;
        if (data.bank_deduction == 1) {
          newE = `
                        <input type="text" class="form-control" id="bank_deduction" name="bank_deduction" disabled value="${translate("none", lang)}">
                        <input type="hidden" id="hidden_bank_deduction" name="hidden_bank_deduction" value="${data.bank_deduction}">
                    `;
        } else if (data.bank_deduction == 2) {
          newE = `
                        <input type="text" class="form-control" id="bank_deduction" name="bank_deduction" disabled value="${translate("deposit", lang)} (+)" style="color: #00c800 !important">
                        <input type="hidden" id="hidden_bank_deduction" name="hidden_bank_deduction" value="${data.bank_deduction}">
                    `;
        } else if (data.bank_deduction == 3) {
          newE = `
                        <input type="text" class="form-control" id="bank_deduction" name="bank_deduction" disabled value="${translate("debit", lang)} (-)" style="color: red !important">
                        <input type="hidden" id="hidden_bank_deduction" name="hidden_bank_deduction" value="${data.bank_deduction}">
                  `;
        }

        var newF = null;
        if (data.facilities_account == 1) {
          newF = `
                        <input type="text" class="form-control" id="facilities_account" name="facilities_account" disabled value="${translate("none", lang)}">
                        <input type="hidden" id="hidden_facilities_account" name="hidden_facilities_account" value="${data.facilities_account}">
                    `;
        } else if (data.facilities_account == 2) {
          newF = `
                        <input type="text" class="form-control" id="facilities_account" name="facilities_account" disabled value="${translate("deposit", lang)} (+)" style="color: #00c800 !important">
                        <input type="hidden" id="hidden_facilities_account" name="hidden_facilities_account" value="${data.facilities_account}">
                    `;
        } else if (data.facilities_account == 3) {
          newF = `
                        <input type="text" class="form-control" id="facilities_account" name="facilities_account" disabled value="${translate("debit", lang)} (-)" style="color: red !important">
                        <input type="hidden" id="hidden_facilities_account" name="hidden_facilities_account" value="${data.facilities_account}">
                  `;
        }

        $("#bank_deduction").replaceWith(newE);
        $("#facilities_account").replaceWith(newF);

        $("#insert-expense-btn").hide();
        $("#update-expense-btn").attr("data-id", id).show();
        $("#cancel-expense-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});

function updateDates() {
  var paramType = document.getElementById("paramType").value;
  var startDateInput = document.getElementById("startDate");
  var endDateInput = document.getElementById("endDate");
  startDateInput.disabled = true;
  endDateInput.disabled = true;
  if (paramType === "period") {
    startDateInput.disabled = false;
    endDateInput.disabled = false;
  }
}

if (document.getElementById("dashboardForm")) {
  document.getElementById("dashboardForm").addEventListener("submit", function (event) {
    var paramType = document.getElementById("paramType").value;
    var startDate = document.getElementById("startDate").value;
    var endDate = document.getElementById("endDate").value;

    if (paramType === "period" && (!startDate || !endDate)) {
      Swal.fire({
        icon: "error",
        title: translate("error", lang),
        text: translate("dates_are_required", lang),
      });
      event.preventDefault();
    }
  });
  updateDates();
}

//----------------نوع رسوم فاتورة التأكيد على حذف -------------------
$("#invoice_fees_types_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./invoices.php?type-of-invoice-fee-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ---------------تجهيز نوع رسوم فاتورة للتعديل------------
$(document).ready(function () {
  $("#invoice_fees_types_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("type-of-invoice-fee-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_type_of_invoice_fee.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        $("#description").val(data.description);
        $("#bank_deduction").val(data.bank_deduction);
        $("#amount").val(data.amount);

        $("#insert-type-of-invoice-fee-btn").hide();
        $("#update-type-of-invoice-fee-btn").attr("data-id", id).show();
        $("#cancel-type-of-invoice-fee-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});

// // ---------------تجهيز فاتورة للتعديل------------
if (staticPageName == "invoices") {
$(document).ready(function () {
  var validFeeTypes = {};

  // وظيفة لحساب وتحديث قيمة السعر بناءً على الكمية والسعر
  function updateFeeAmount(input) {
        console.log("444444");

    const formGroup = $(input).closest(".form-group");
    // const quantity = formGroup.find('input[name="quantity[]"]').val();
    const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
    // const feeAmount = feeAmountField.data("price");
    const feeBankAmountField = formGroup.find('input[name="bank_deduction[]"]');
    const feeBankAmount = feeBankAmountField.data("bank");

    // const newAmount = (parseFloat(feeAmount) || 0);
    const newBankAmount = parseFloat(feeAmountField.val()) || 0;
    // feeAmountField.val(newAmount.toFixed(2));
    if (feeBankAmount) {
      feeBankAmountField.val(newBankAmount.toFixed(2));
    }
    // تحديث المجموع بعد تعديل السعر
    updateTotal();
  }

  // وظيفة لحساب المجموع الإجمالي
  function updateTotal() {
    let total = 0;
    let totalBank = 0;

    // جمع القيم من حقول السعر
    $("#feesContainer")
      .find(".form-group")
      .each(function () {
        const feeAmount = $(this).find('input[name="fee_amount[]"]').val();
        const feeBankAmount = $(this).find('input[name="bank_deduction[]"]').data("bank");
        // const quantity = $(this).find('input[name="quantity[]"]').val();
        const newBankAmount = parseFloat(feeAmount) || 0;
        total += parseFloat(feeAmount) || 0;
        if (feeBankAmount) {
          totalBank += parseFloat(newBankAmount) || 0;
        }
      });

    // تحديث حقل المجموع
    $("#total_sum").val(total.toFixed(2));
    $("#total_bank_sum").val(totalBank.toFixed(2));
  }

  if (staticPageName == "invoices") {
    // البحث عن نوع رسوم الخدمة
    $(document).on("focus", ".trip_fee_type_id", function () {
      $(this)
        .autocomplete({
          source: function (request, response) {
            var loadingMessage = {
              label: translate("loading", lang),
              value: "",
              valid: false,
            };
            response([loadingMessage]);
            $.ajax({
              url: "./api/search_for_invoice_fees_types.php",
              type: "GET",
              dataType: "json",
              data: {
                term: request.term,
              },
              success: function (data) {
                if (data.length === 0) {
                  data = [
                    {
                      label: translate("no_results", lang),
                      value: "",
                      valid: false,
                    },
                  ];
                } else {
                  data = data.map(function (item) {
                    item.valid = true;
                    validFeeTypes[item.label] = item.value;
                    return item;
                  });
                }
                response(data);
              },
            });
          },
          minLength: 1,
          delay: 500,
          select: function (event, ui) {
            // تحقق مما إذا كان العنصر الذي تم اختياره هو "لا نتائج" أو غير صالح
            if (ui.item.valid) {
              $(this).val(ui.item.label); // تعيين القيمة في الحقل الحالي
              const formGroup = $(this).closest(".form-group");
              const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
              const feeBankAmountField = formGroup.find('input[name="bank_deduction[]"]');
              feeAmountField.val(ui.item.amount);
              feeAmountField.data("price", ui.item.amount);
              feeBankAmountField.data("bank", ui.item.bank);
              updateFeeAmount(this);
            } else {
              $(this).val(""); // إفراغ الحقل إذا لم يكن العنصر صالحًا
            }
            return false;
          },
          change: function (event, ui) {
            // تحقق مما إذا كان العنصر الذي تم اختياره هو "لا نتائج" أو غير صالح
            if (ui.item && ui.item.valid) {
              $(this).val(ui.item.label); // تعيين القيمة في الحقل الحالي
            } else {
              $(this).val(""); // إفراغ الحقل إذا لم يكن العنصر صالحًا
            }
          },
        })
        .autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li>")
          .append("<div>" + item.label + "</div>")
          .appendTo(ul);
      };
    });
  }

  // عند إزالة حقل
  $(document).on("click", ".btn-remove-fee", function () {
    $(this).closest(".form-group").remove();
    updateTotal();
  });

  // حساب المجموع الأولي عند تحميل الصفحة
  updateTotal();

  // التحقق من الغاء الفاتورة
  $("#update-invoice-btn").on("click", function (e) {
    e.preventDefault(); // منع الفورم من الإرسال الفوري

    var selectedOption = $("#status").val(); // الحصول على الخيار المختار
    if (selectedOption === "Cancelled") {
      Swal.fire({
        icon: "warning",
        title: translate("are_you_sure", lang),
        text: translate("you_have_selected_cancelled_do_you_want_to_proceed", lang),
        showConfirmButton: true,
        showCancelButton: true,
        confirmButtonText: translate("yes_update_it", lang),
        cancelButtonText: translate("no_cancel", lang),
      }).then((result) => {
        if (result.isConfirmed) {
          // إضافة حقل مخفي يحتوي على name="update-invoice"
          $("<input>")
            .attr({
              type: "hidden",
              name: "update-invoice", // حقل update-invoice
              value: "1", // يمكن أن تعطيه أي قيمة حسب الحاجة
            })
            .appendTo("#invoiceForm");

          // إرسال النموذج بعد التأكيد
          $("#invoiceForm").submit(); // إرسال النموذج
        } else if (result.isDismissed) {
          Swal.fire({
            icon: "info",
            title: translate("canceled", lang),
            text: translate("the_item_is_safe", lang),
            showConfirmButton: false,
            timer: 1000,
          });
        }
      });
    } else {
      // إضافة حقل مخفي يحتوي على name="update-invoice"
      $("<input>")
        .attr({
          type: "hidden",
          name: "update-invoice", // حقل update-invoice
          value: "1", // يمكن أن تعطيه أي قيمة حسب الحاجة
        })
        .appendTo("#invoiceForm");
      // إذا لم يكن الخيار هو "Cancelled"، قم بإرسال النموذج مباشرة
      $("#invoiceForm").submit(); // إرسال النموذج
    }
  });

  // معالجة زر التعديل
  $("#invoices_table").on("click", ".edit-button", function () {
    if (!$('#status option[value="Cancelled"]').length) {
      // Create and append the option
      const option = $("<option>", {
        value: "Cancelled",
        text: translate("cancelled", lang), // Replace with your translation function if necessary
      });
      $("#status").append(option);
    }
    var id = $(this).data("id");
    var oldBankAmount = $(this).data("old_bank_amount");
    var url = new URL(window.location.href);
    url.searchParams.set("invoice-uid", id);
    url.searchParams.set("old-bank-amount", oldBankAmount);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_invoice.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        const rawDate = data.invoice_date; // مثال: "2024-11-27 07:29:26"
        const [datePart, timePart] = rawDate.split(" "); // ["2024-11-27", "07:29:26"]
        const [hours, minutes] = timePart.split(":"); // ["07", "29"]
        const formattedDate = `${datePart}T${hours}:${minutes}`;
        $("#invoice_date").val(formattedDate);
        $("#port").val(data.port);
        $("#status").val(data.status);
        $("#customer_id").val(data.customer_id); //
        $("#hidden_customer_id").val(data.customer_id);
        $("#customer_name").val(data.customer_name);
        $("#exporter_importer_name").val(data.exporter_importer_name);
        $("#driver_name").val(data.driver_name);
        $("#destination_country").val(data.destination_country);
        $("#declaration_number").val(data.declaration_number);
        $("#vehicle_plate_number").val(data.vehicle_plate_number);
        $("#declaration_count").val(data.declaration_count);
        $("#vehicle_count").val(data.vehicle_count);
        $("#customer_invoice_number").val(data.customer_invoice_number);
        $("#goods_description").val(data.goods_description);
        $("#notes").val(data.notes);
        $("#returned_amount").val(data.returned_amount);

        $("#insert-invoice-btn").hide();
        $("#update-invoice-btn").attr("data-id", id).show();
        $("#cancel-invoice-btn").show();

        // جلب مصاريف الرحلة
        $.ajax({
          url: "./api/get_invoice_fees.php",
          type: "GET",
          data: { id: id },
          success: async function (response) {
            // تحويل الاستجابة JSON إلى كائن JavaScript
            const fees = JSON.parse(response);
            // إفراغ الحاوية قبل إضافة الحقول
            $("#feesContainer").empty();

            // إنشاء الحقول بناءً على البيانات المستلمة
            fees.forEach((fee) => {
              var fee_type_data = fee.fee_type_id + "- " + fee.fee_type_name;
              if (fee.fee_type_amount != 0.0) {
                fee_type_data += " | " + fee.fee_type_amount;
              }
              if (fee.fee_type_bank_deduction == true) {
                fee_type_data += " | (T)";
              }
              $("#feesContainer").append(`
                                <div class="form-group" style="display: flex; flex-wrap: wrap; gap: 1rem; justify-content: space-between; align-items: center;">
                                  <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap; width: 35%">
                                    <label for="fee_type" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("type", lang)}</label>
                                    <input type="text" class="form-control trip_fee_type_id" name="fee_type[]" value="${fee_type_data}" required placeholder="${translate("type_to_search", lang)}...">
                                    <input type="hidden" class="form-control" name="fee_ids[]" value="${fee.id}">
                                    <input type="hidden" class="form-control" name="bank_deduction[]" value="${fee.fee_type_bank_deduction == true ? fee.amount : 0.0}">
                                </div>    
                                <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                    <label for="fee_amount" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("price", lang)}</label>
                                    <input type="number" min="0" step="0.01" class="form-control" name="fee_amount[]" value="${fee.amount}" required>
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                    <label for="description" style="margin-right: 0.5rem; margin-left: 0.5rem;">${translate("additional_description", lang)} ${translate("optional", lang)}</label>
                                    <input type="text" class="form-control" name="description[]" value="${fee.description}">
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: flex-start; white-space: nowrap;">
                                    <label for="" style="margin-right: 0.5rem; margin-left: 0.5rem;"><br></label>
                                    <button type="button" class="btn btn-danger btn-remove-fee"><i class="far fa-times-circle"></i></button>
                                </div>
                            </div>
                        `);

              // تحديث قيمة المبلغ بناءً على الكمية والسعر لكل حقل
              const formGroup = $("#feesContainer").find(".form-group").last(); // الحصول على آخر حقل مضاف
              const feeAmountField = formGroup.find('input[name="fee_amount[]"]');
              const feeBankAmountField = formGroup.find('input[name="bank_deduction[]"]');
              feeAmountField.data("price", fee.fee_type_amount);
              feeBankAmountField.data("bank", fee.fee_type_bank_deduction);
            });
            // ديث المجموع بعد إضافة الحقول الجديدة
            // updateTotal();
            await fetchAndCalculateTotal();
            Swal.close();
          },
          error: function (xhr, status, error) {
            Swal.fire({
              icon: "error",
              title: translate("error", lang),
              text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
            });
          },
        });
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });

  async function fetchAndCalculateTotal() {
    await new Promise((resolve) => setTimeout(resolve, 100));

    let total = 0;
    let totalBank = 0;

    $("#feesContainer")
      .find(".form-group")
      .each(function () {
        const feeAmount = $(this).find('input[name="fee_amount[]"]').val();
        const feeBankAmount = $(this).find('input[name="bank_deduction[]"]').data("bank");

        // const quantity = $(this).find('input[name="quantity[]"]').val();
        const newBankAmount = parseFloat(feeAmount) || 0;
        total += parseFloat(feeAmount) || 0;
        if (feeBankAmount) {
          totalBank += parseFloat(newBankAmount) || 0;
        }
      });

    $("#total_sum").val(total.toFixed(2));
    $("#total_bank_sum").val(totalBank.toFixed(2));
  }

  // استدعاء updateTotal عند حدوث تغييرات في الحقول ذات الصلة
  $(document).on("input", 'input[name="fee_amount[]"]', function () {
    updateFeeAmount(this);

    // updateTotal();
  });
  // عند تغيير قيمة حقل الكمية
  // $(document).on("input", 'input[name="quantity[]"]', function () {
  //   updateFeeAmount(this);
  // });
  // // عند تغيير قيمة حقل السعر مباشرة (إذا كان هناك نوع من التعديل اليدوي)
  // $(document).on("input", 'input[name="fee_amount[]"]', function () {
  //   updateTotal();
  // });
});
}
//---------------- التأكيد على حذف فاتورة-------------------
$("#invoices_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  var oldBankAmount = $(this).data("old_bank_amount");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./invoices.php?invoice-did=${id}&old-bank-amount=${oldBankAmount}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ------- البحث عن عميل------------
$(document).ready(function () {
  var validDriverIds = {};
  var oldValue;

  $("#customer_id").on("focus", function () {
    oldValue = $(this).val();
    $(this)
      .autocomplete({
        source: function (request, response) {
          $.ajax({
            url: "./api/search_for_customer.php",
            type: "GET",
            dataType: "json",
            data: {
              term: request.term,
            },
            success: function (data) {
              if (data.length === 0) {
                response([
                  {
                    label: translate("no_results", lang),
                    value: "",
                    valid: false,
                  },
                ]);
              } else {
                data = data.map(function (item) {
                  item.valid = true;
                  validDriverIds[item.label] = item.value;
                  return item;
                });
                response(data);
              }
            },
          });
        },
        minLength: 1,
        delay: 500,
        select: function (event, ui) {
          if (ui.item.valid) {
            $("#customer_id").val(ui.item.label);
            $("#customer_name").val(ui.item.name);
            // addSelectedItem(ui.item.label);
            // alert info data
            // $("#pop_exit_returns").text(ui.item.exit_returns);
            // $("#pop_entry_returns").text(ui.item.entry_returns);
            if (ui.item.exit_clearance != 0) {
              $("#pop_exit_clearance").text(translate("exit_clearance", lang) + ": " + ui.item.exit_clearance + " | ");
            }

            if (ui.item.entry_clearance != 0) {
              $("#pop_entry_clearance").text(translate("entry_clearance", lang) + ": " + ui.item.entry_clearance + " | ");
            }

            if (ui.item.customer_notes) {
              $("#pop_customer_notes").text(translate("notes", lang) + ": " + ui.item.customer_notes);
            }
            if (ui.item.exit_clearance != 0 || ui.item.entry_clearance != 0 || ui.item.customer_notes) {
              $(".alert-info").show();
            }
          } else {
            $("#customer_id").val("");
            $("#customer_name").val("");

            $("#pop_exit_clearance").text("");
            $("#pop_exit_returns").text("");
            $("#pop_entry_clearance").text("");
            $("#pop_entry_returns").text("");
            $("#pop_customer_notes").text("");
            // $(".alert-info").hide();
          }
          return false;
        },
      })
      .autocomplete("instance")._renderItem = function (ul, item) {
      return $("<li>")
        .append("<div>" + item.label + "</div>")
        .appendTo(ul);
    };
  });

  $("#customer_id").on("blur", function () {
    var newValue = $(this).val();
    if (!validDriverIds.hasOwnProperty(newValue)) {
      $(this).val(oldValue);
    }
  });

  // التحقق عند إرسال النموذج
  $("form").on("submit", function () {
    var value = $("#customer_id").val();
    if (!validDriverIds.hasOwnProperty(value)) {
      $("#customer_id").val("");
    }
  });
});

//---------------- التأكيد على دفع فاتورة-------------------
//---------------- التأكيد على حذف فاتورة-------------------
$("#invoices_table").on("click", ".pay-invoice-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_pay_this_invoice", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_pay_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./invoices.php?invoice-pid=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

//----------------عميل التأكيد على حذف -------------------
$("#customers_table").on("click", ".delete-button", function () {
  var id = $(this).data("id");
  Swal.fire({
    icon: "question",
    title: translate("are_you_sure", lang),
    text: translate("do_you_want_to_delete_this_item", lang),
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: translate("yes_delete_it", lang),
    cancelButtonText: translate("no_cancel", lang),
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = `./invoices.php?customer-did=${id}`;
    } else if (result.isDismissed) {
      Swal.fire({
        icon: "info",
        title: translate("canceled", lang),
        text: translate("the_item_is_safe", lang),
        showConfirmButton: false,
        timer: 1000,
      });
    }
  });
});

// ---------------تجهيز عميل للتعديل------------
$(document).ready(function () {
  $("#customers_table").on("click", ".edit-button", function () {
    var id = $(this).data("id");
    var url = new URL(window.location.href);
    url.searchParams.set("customer-uid", id);
    window.history.replaceState({}, document.title, url.toString());
    Swal.fire({
      title: translate("loading", lang),
      text: translate("please_wait_while_we_fetch_the_data", lang),
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });
    $.ajax({
      url: "./api/get_customer.php",
      type: "GET",
      data: { id: id },
      dataType: "json",
      success: function (data) {
        // تعبئة الحقول بالبيانات المسترجعة
        $("#customer_name").val(data.customer_name);
        $("#exit_clearance").val(data.exit_clearance);
        $("#exit_returns").val(data.exit_returns);
        $("#entry_clearance").val(data.entry_clearance);
        $("#entry_returns").val(data.entry_returns);
        $("#customer_notes").val(data.customer_notes);

        $("#insert-customer-btn").hide();
        $("#update-customer-btn").attr("data-id", id).show();
        $("#cancel-customer-btn").show();
        Swal.close();
      },
      error: function (xhr, status, error) {
        Swal.fire({
          icon: "error",
          title: translate("error", lang),
          text: translate("an_error_occurred_while_fetching_the_data_please_try_again_later", lang),
        });
      },
    });
  });
});
