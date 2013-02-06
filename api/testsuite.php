<?php

require_once 'PHPUnit/Autoload.php';
require_once 'test/LoginTest.php';
//require_once 'PHPUnit.php';

$suite  = new PHPUnit_TestSuite("TestSuite");
$result = PHPUnit::run($suite);

echo $result -> toString();

?>
