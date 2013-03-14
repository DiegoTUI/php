<?php

include_once './util/UtilMongo.php';

$params = $_REQUEST["params"];

if (!$params) return json_encode(array("message"=>"ERROR: no params provided"));
$params = json_decode($params, true);

if (!array_key_exists("latitude", $params)) return json_encode(array(message=>"ERROR: no latitude provided"));
if (!array_key_exists("longitude", $params)) return json_encode(array(message=>"ERROR: no longitude provided"));

$longitude = (double)($params["longitude"]);
$latitude = (double)($params["latitude"]);
$londelta = array_key_exists("longitudeDelta", $params)? (double)($params["longitudeDelta"] : 0.1;
$latdelta = array_key_exists("latitudeDelta", $params) ? (double)($params["latitudeDelta"]) : 0.1;

$bottomleft = array($longitude - $londelta, $latitude - $latdelta);
$topright = array($longitude + $londelta, $latitude + $latdelta);

var_dump($bottomleft);
var_dump($topright);

$query = array ("loc" => 
					array('$within' => 
						array('$box' => 
							array($bottomleft , $topright ))));
							
$cursor = UtilMongo::getInstance()->getCollection("hotels")->find($query);
echo "count: " . $cursor->count();
$return = array("message" => "OK", "response" => array());

while ($cursor->hasNext())
{
	$result = $cursor->getNext();
	array_push($return["response"], $result);
}

echo json_encode($return);

?>
