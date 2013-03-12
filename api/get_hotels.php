<?php

include_once './util/UtilMongo.php';

$query = array ("location.code" => "PMI",
				"category" => "5EST",
				"location.latitude" => array('$ne'=>null),
				"location.longitude" => array('$ne'=>null));
$cursor = UtilMongo::getInstance()->getCollection("hotels")->find($query);
$return = array();

while ($cursor->hasNext())
{
	$result = $cursor->getNext();
	array_push($return, $result);
}

echo json_encode($return);

?>
