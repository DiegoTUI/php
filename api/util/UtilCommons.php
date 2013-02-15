<?php

include_once 'UtilLogging.php';
include_once 'UtilConstants.php';
include_once 'UtilKernel.php';
include_once 'UtilExceptions.php';

class UtilCommons
{
	public static function debug($message)
	{
		UtilLogging::getInstance()->debug($message);
	}
}



?>