<?php

include_once "helper/HelperTicketRequest.php";

class ControllerTicketRequest
{
	/**
	 * @static
	 * @return void
	 */
	public static function ticketAvailRQ()
	{
		UtilLogging::getInstance->debug('init');		
		$helper = new HelperTicketRequest();
		echo $helper->ticketAvailRQ();
		UtilLogging::getInstance->debug('done');
	}
}

?>
