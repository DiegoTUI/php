<?php
/**
 * HotelListRequest.
 * (C) 2013 Tui Innovation.
 */
 
include_once 'model/ModelEntity.php';
include_once 'model/ModelAttribute.php';

/**
 * Ticket Availability Request.
 */
$HOTEL_LIST_RQ = new ModelEntity('HotelListRQ', array(
	new Mandatory('echoToken'),
	new Mandatory('xmlns', null, null, 'http://www.hotelbeds.com/schemas/2005/06/messages'),
	new Mandatory('xmlns:xsi', null, null, 'http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.hotelbeds.com/schemas/2005/06/messages HotelListRQ.xsd'),
	new Mandatory('Language'),
	new Mandatory('User', 'Credentials'),
	new Mandatory('Password', 'Credentials'),
	new Mandatory('code', 'Destination'),
	new Mandatory('type', 'Destination', null, 'SIMPLE')
));
