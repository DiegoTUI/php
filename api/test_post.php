<?php

$field1 = $_GET["field1"];
$field2 = $_GET["field2"];

$post_body = @file_get_contents(STDIN);

echo "Post received - field1: " . $field1 . " - field2: " . $field2 . " - post body: " . $post_body;

?>
