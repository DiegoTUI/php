<?php
/**
 * TicketAvailRequest.
 * (C) 2013 Tui Innovation.
 */
 
include_once 'model/ModelEntity.php';
include_once 'model/ModelAttribute.php';

/**
 * Ticket Availability Request.
 */
$TICKET_AVAIL_RQ = new ModelEntity('TicketAvailRQ', array(
	new Mandatory('echoToken'),
	new Mandatory('sessionId'),
	new Mandatory('xmlns', null, null, 'http://www.hotelbeds.com/schemas/2005/06/messages'),
	new Mandatory('xmlns:xsi', null, null, 'http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.hotelbeds.com/schemas/2005/06/messages TicketAvailRQ.xsd'),
	new Mandatory('Language'),
	new Mandatory('User', 'Credentials'),
	new Mandatory('Password', 'Credentials'),
	new Mandatory('itemsPerPage', 'PaginationData'),
	new Mandatory('pageNumber', 'PaginationData'),
	new Mandatory('AdultCount', 'ServiceOccupancy'),
	new Mandatory('ChildCount', 'ServiceOccupancy'),
	new Mandatory('code', 'Destination'),
	new Optional('Value', 'Destination'),	//REMOVE - only for test purposes
	new Mandatory('type', 'Destination', null, 'SIMPLE'),
	new Mandatory('date', 'DateFrom'),
	new Mandatory('date', 'DateTo')
));
