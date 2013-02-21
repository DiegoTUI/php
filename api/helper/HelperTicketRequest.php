<?php

include_once 'model/ModelRequest.php';
include_once 'model/TicketAvailRQ.php';
include_once 'util/UtilConfig.php';

class HelperTicketRequest
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		UtilLogging::getInstance->debug('init');
	}

	/**
	 * Make a request to atlas and echo the resulting xml
	 * @return void
	 */
	public function ticketAvailRQ()
	{
		UtilLogging::getInstance->debug('init');
		global $CONFIG;
		global $TICKET_AVAIL_RQ;
		$TICKET_AVAIL_RQ->read_set_all();
		$request = new HTTPRequest($CONFIG->url, HTTP_METH_POST);
		$request->setBody($TICKET_AVAIL_RQ->get_xml());
		$request->send();
		$response = $request->getResponseBody();
		echo $response;
	}
}
