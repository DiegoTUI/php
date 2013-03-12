<?php

include_once './util/UtilMongo.php';

$query = array ("location.code" => "PMI",
				"category" => "5EST");
$cursor = UtilMongo::getInstance()->getCollection("hotels")->find($query);
$return = array();

while ($cursor->hasNext())
{
	array_push($return, $cursor->getNext());
}
var_dump ($return);
return json_encode($return);

?>
