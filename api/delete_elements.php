<?php

$m = new Mongo();
$db = $m->test;
$collection_objects = $db->objects;

$collection_objects->remove();

echo "All objects removed from the DB";

?>
