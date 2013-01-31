<?php
$m = new Mongo();
$db = $m->test;

$collection_objects = $db->objects;

$field1 = $_REQUEST["field1"];
$field2 = $_REQUEST["field2"];

$new_object = array("field1" => $field1, "field2" => $field2);

$collection_objects->insert($new_object);

echo "Element created - " . json_encode($new_object);

?>
