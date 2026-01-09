<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Site Maintenance</title>
  <link rel="icon" href="./vendor/warning.svg" type="image/x-icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
  <style>
    html,
    body {
      padding: 0;
      margin: 0;
      width: 100%;
      height: 100%;
    }

    * {
      box-sizing: border-box;
    }

    body {
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #f4f4f9;
      font-family: "Cairo", sans-serif;
    }

    .container{
      display: flex;
      flex-direction: column;
      width: 80%;
      max-width: 1200px;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      text-align: center;
    }

    .container .cn {
      display: flex;
      background: #fff;
      overflow: hidden;
    }
    .column {
      flex: 1;
      padding: 30px;
      text-align: center;
    }

    .column.left {
      border-right: 1px solid #e0e0e0;
    }

    .column img {
      width: 60px;
      margin-top: 1em;
    }

    .column h1 {
      font-size: 36px;
      font-weight: 400;
      margin: 20px 0;
    }

    .column p {
      font-size: 18px;
      line-height: 1.6;
      color: #333;
    }

    .column a {
      color: #007bff;
      text-decoration: none;
    }

    .column a:hover {
      text-decoration: underline;
    }

    .rtl {
      direction: rtl;
      text-align: right;
    }

    .ltr {
      direction: ltr;
      text-align: left;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="cn">
      <div class="column ltr">
        <img src="./vendor/warning.svg" alt="Maintenance Icon">
        <h1>We&rsquo;ll be back soon!</h1>
        <p>We apologize for the inconvenience.<br>
          We are currently undergoing maintenance to enhance our service. If you require assistance,
          please feel free to reach out to us via <a href="mailto:nadim.alzoubi.99@gmail.com">Email</a> for updates. Otherwise, we will be back online shortly!</p>
      </div>
      <div class="column rtl">
        <img src="./vendor/warning.svg" alt="Maintenance Icon">
        <h1>سنعود قريباً!</h1>
        <p>نعتذر عن الإزعاج.<br> نحن حالياً في عملية صيانة لتحسين خدمتنا. إذا كنت بحاجة للمساعدة،
          يمكنك دائمًا التواصل معنا عبر <a href="mailto:nadim.alzoubi.99@gmail.com">البريد الإلكتروني</a> للحصول على التحديثات، وإلا سنعود قريبًا!</p>
      </div>
    </div>
    <h5>Nadim Alzoubi | <a target="_blank" href="https://nadim.pro">www.Nadim.pro</a> | نديم الزعبي</h5>
  </div>
</body>

<script>
  function check() {
    fetch('check_maintenance_status.php')
      .then(response => response.json())
      .then(data => {
        if (!data.underMaintenance) {
          window.location.href = 'index.php';
        }
      });
  }
  check();
  setInterval(check, 60000);
</script>

</html>