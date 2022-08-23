<?php

include_once "managerFunctions.php";
$hashCode = $_GET['myCode'];
if (!isset($hashCode) or empty($hashCode) or (mysqli_num_rows(mysqli_query($connectToDB, "SELECT * FROM Users WHERE Hash = '$hashCode' LIMIT 1")) != 1)) {
    header("Location: Account.php");
} else {
    activateNewAccount($hashCode);
}
