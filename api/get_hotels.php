<?php

include_once './util/UtilMongo.php';

$params = $_REQUEST["params"];

if (!$params) return json_encode(array("message"=>"ERROR: no params provided"));
$params = json_decode($params, true);

if (!array_key_exists("latitude", $params)) return json_encode(array(message=>"ERROR: no latitude provided"));
if (!array_key_exists("longitude", $params)) return json_encode(array(message=>"ERROR: no longitude provided"));

$longitude = (double)($params["longitude"]);
$latitude = (double)($params["latitude"]);
$londelta = array_key_exists("longitudeDelta", $params)? (double)($params["longitudeDelta"]) : 0.1;
$latdelta = array_key_exists("latitudeDelta", $params) ? (double)($params["latitudeDelta"]) : 0.1;
$maxnumber = array_key_exists("maxNumber", $params) ? (double)($params["maxNumber"]) : 50;
$maxdistance = $londelta > $latdelta ? $londelta : $latdelta;

$bottomleft = array($longitude - $londelta, $latitude - $latdelta);
$topright = array($longitude + $londelta, $latitude + $latdelta);

$querybox = array ("loc" => 
					array('$within' => 
						array('$box' => 
							array($bottomleft , $topright ))));
$querygeonear = array('geoNear'=>'hotels', 'near'=>array($longitude, $latitude), 'num'=>$maxnumber, 'maxDistance'=>$maxdistance);
							
//$cursor = UtilMongo::getInstance()->getCollection("hotels")->find($query);
$reply = UtilMongo::getInstance()->gatDb()->command($querygeonear);
$return = array("message" => "OK", "response" => array());

foreach ($reply["results"] as $item){
	array_push($return["response"], $item["obj"]);
}

/*while ($cursor->hasNext())
{
	$result = $cursor->getNext();
	array_push($return["response"], $result);
}*/

echo json_encode($return);

?>
