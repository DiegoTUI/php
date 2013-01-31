<?php

$m = new Mongo();
$db = $m->test;
$collection_objects = $db->objects;

$elements = $collection_objects->find();

//phpinfo();

$count = 0;

foreach ($elements as $element)
{
	$string = '<b>Element ' . $count . '</b>:</br>field1: ' . $element["field1"] . '</br>field2: ' . $element["field2"] . '</br>';
	echo $string;
	$count++;
}

?>
