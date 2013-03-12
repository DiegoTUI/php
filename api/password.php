<?php

include_once './util/UtilCrypto.php';

$password = $_REQUEST["password"];

$arrayPassword = UtilCrypto::createPasswordHash($password,10000);

echo "hash: " . $arrayPassword['hash'] . "</br>";
echo "salt: " . $arrayPassword['salt']  . "</br>";
echo "iterations: " . $arrayPassword['iterations'] . "</br>";

?>
