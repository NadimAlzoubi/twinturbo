<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/error403.css">
    <title>Document</title>
</head>

<body>
    <h1>403</h1>
    <div>
        <p>> <span>ERROR CODE</span>: "<i>HTTP 403 Forbidden</i>"</p>
        <p>> <span>ERROR DESCRIPTION</span>: "<i>Access Denied. You Do Not Have The Permission To Access This Page On
                This Server</i>"</p>
        <p>> <span>ACTION</span>: [<a href="./index.php">Click Here To Return To The Home Page</a>...]</p>
        <p>> <span>HAVE A NICE DAY SIR :-)</span></p>
    </div>
</body>
<script>
var str = document.getElementsByTagName('div')[0].innerHTML.toString();
var i = 0;
document.getElementsByTagName('div')[0].innerHTML = "";

setTimeout(function() {
    var se = setInterval(function() {
        i++;
        document.getElementsByTagName('div')[0].innerHTML = str.slice(0, i) + "|";
        if (i == str.length) {
            clearInterval(se);
            document.getElementsByTagName('div')[0].innerHTML = str;
        }
    }, 10);
}, 0);
</script>

</html>