<?php

include_once 'Logging.php';
include_once 'Constants.php';
include_once 'Kernel.php';
include_once 'Exceptions.php';

class Commons
{
	public static function debug($message)
	{
		Logging::getInstance()->debug($message);
	}
}

?>