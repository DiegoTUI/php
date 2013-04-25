<?php

$field1 = $_POST["field1"];
$field2 = $_POST["field2"];

$post_body = @file_get_contents('php://input');

$headers = apache_request_headers();

var_dump($headers);

echo "Post received - field1: " . $field1 . " - field2: " . $field2 . " - post body: " . $post_body;

?>
