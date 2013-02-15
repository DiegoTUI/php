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
);

UtilLogging::getInstance()->debug("Redefined CONFIG to: " . $CONFIG["test"]);
