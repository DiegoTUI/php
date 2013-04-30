<?php
/**
 * TuiInnovation API.
 * Get a list of the available xml services.
 * (C) 2013 TuiInnovation.
 */

$urls = array(
	'ATLAS' => 'http://212.170.239.71/appservices/http/FrontendService?xml_request=$xml_request$',
);

echo json_encode($urls);
?>
