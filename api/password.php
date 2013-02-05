<?php

include_once './util/Crypto.php';

$password = $_REQUEST["password"];

$arrayPassword = UtilCrypto::createPasswordHash($password,10000);

echo "hash: " . $arrayPassword['hash'];
echo "salt: " . $arrayPassword['salt'];
echo "iterations: " . $arrayPassword['iterations'];

?>
