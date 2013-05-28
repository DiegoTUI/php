<?php

$field1 = $_GET["field1"];
$field2 = $_GET["field2"];

$post_body = @file_get_contents('php://input');

$result = array("rf1" => $field1, "rf2" => $field2, "rfb" => $post_body);

echo json_encode($result);

?>
