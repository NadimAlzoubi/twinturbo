<?php 
include_once('../inc/connect.php');

function getBalance(){
    global $connection;
    $query = "SELECT account_amount, facilities_amount FROM bank_account";
    $result = mysqli_query($connection, $query);
    return mysqli_fetch_assoc($result);
}

if (isset($_GET['id'])) {
    $balance = getBalance();
    echo json_encode($balance);
}
?>
