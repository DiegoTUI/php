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
	'url_test_http' => 'http://212.170.239.71/appservices/http/FrontendService',
	'url_live_http' => 'http://212.170.239.18/appservices/http/FrontendService',
	'url_test_ws' => 'http://212.170.239.71/appservices/ws/FrontendService',
	'url_live_ws' => 'http://212.170.239.18/appservices/ws/FrontendService',
	
	//User and password authorized to make the calls
	'user' => 'BDS',
	'password' => 'BDS'
);

date_default_timezone_set('UTC');
