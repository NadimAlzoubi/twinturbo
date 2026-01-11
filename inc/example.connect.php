<?php
error_reporting(0);
// error_reporting(E_ALL);
$host = "localhost";
$username = "";
$password = "";
$database = "";
$connection = mysqli_connect($host, $username, $password, $database);
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql_defualt_limit = 25;
$s3key = "32 letters long key";