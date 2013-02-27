<?php
/**
 * Configuration variables.
 * (C) 2013 Tui Innovation.
 */

include_once 'util/UtilLogging.php';
 
const VERSION = '0.0.9';

/**
 * Configuration object.
 */
$CONFIG = array(
	// Used to know if we are testing or not
	// Tests will set this to true on setUp and to false again in TearDown
	'test' => false,
	
	// URL to make the xml calls
	'url' => 'http://212.170.239.18/appservices/http/FrontendService',
	
	//User and password authorized to make the calls
	'user' => 'BDS',
	'password' => 'BDS',
	
	//name of the parameter in the POST call
	'parameter_name' => 'xml_request',
	
	//xml headers and footers
	'xml_headers' => array(
		'TicketAvailRQ' => "<soapenv:Envelope" . "\n" . 
			'soapenv:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"' ."\n" . 
			'xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">' . "\n" .
			'<soapenv:Body>' . "\n" . 
			'<hb:getTicketAvail xmlns:hb="http://axis.frontend.hydra.hotelbeds.com" xsi:type="xsd:string">' . "\n",
			
		'TicketAvailRQ_noSOAP' => '<hb:getTicketAvail xmlns:hb="http://axis.frontend.hydra.hotelbeds.com" xsi:type="xsd:string">' . "\n"),
							
	'xml_footers' => array (
		'TicketAvailRQ' => '</hb:getTicketAvail>' . "\n" . 
			'</soapenv:Body>' . "\n" . 
			'</soapenv:Envelope>',
			
		'TicketAvailRQ_noSOAP' => '</hb:getTicketAvail>')
);

date_default_timezone_set('UTC');
