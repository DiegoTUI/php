<?php
/**
 * TuiInnovation API.
 * Get a list of the available xml services.
 * (C) 2013 TuiInnovation.
 */

$xml = array(
	'HotelListRQ' => '<HotelListRQ echoToken="$echoToken$" xmlns="$xmlns$" xmlns:xsi="$xmlns:xsi$" xsi:schemaLocation="$xsi:schemaLocation$"><Language>$Language$</Language><Credentials><User>$Credentials_User$</User><Password>$Credentials_Password$</Password></Credentials><Destination code="$Destination_code$" type="$Destination_type$"></Destination></HotelListRQ>',

	'TicketAvailRQ' => '<TicketAvailRQ echoToken="$echoToken$" sessionId="$sessionId$" xmlns="$xmlns$" xmlns:xsi="$xmlns:xsi$" xsi:schemaLocation="$xsi:schemaLocation$"><Language>$Language$</Language><Credentials><User>$Credentials_User$</User><Password>$Credentials_Password$</Password></Credentials><Destination code="$Destination_code$" type="$Destination_type$"></Destination><PaginationData itemsPerPage="$PaginationData_itemsPerPage$" pageNumber="$PaginationData_pageNumber$"/><ServiceOccupancy><AdultCount>$ServiceOccupancy_AdultCount$</AdultCount><ChildCount>$ServiceOccupancy_ChildCount$</ChildCount></ServiceOccupancy><DateFrom date="$DateFrom_date$"/><DateTo date="$DateTo_date$"/></TicketAvailRQ>'
);

//In the future this can be read from a mongoDB
$json = array(
	'HotelListRQ' => array(
		'HotelListRQ' => array(
			'@echoToken' => '$echoToken$',
			'@xmlns' => '$xmlns$',
			'@xmlns:xsi' => '$xmlns:xsi$',
			'@xsi:schemaLocation' => '$xsi:schemaLocation$',
			'Language' => array('#' => '$Language$'),
			'Credentials' => array(
				'User' => array('#' => '$Credentials_User$'),
				'Password' => array('#' => '$Credentials_Password$')),
			'Destination' => array(
				'@code' => '$Destination_code$',
				'@type' => '$Destination_type$')
		)
	),
	'TicketAvailRQ' => array(
		'TicketAvailRQ' => array(
			'@echoToken' => '$echoToken$',
			'@sessionId' => '$sessionId$',
			'@xmlns' => '$xmlns$',
			'@xmlns:xsi' => '$xmlns:xsi$',
			'@xsi:schemaLocation' => '$xsi:schemaLocation$',
			'Language' => array('#' => '$Language$'),
			'Credentials' => array(
				'User' => array('#' => '$Credentials_User$'),
				'Password' => array('#' => '$Credentials_Password$')),
			'Destination' => array(
				'@code' => '$Destination_code$',
				'@type' => '$Destination_type$'),
			'PaginationData' => array(
				'@itemsPerPage' => '$PaginationData_itemsPerPage$',
				'@pageNumber' => '$PaginationData_pageNumber$'),
			'ServiceOccupancy' => array(
				'AdultCount' => array('#' => '$ServiceOccupancy_AdultCount$'),
				'ChildCount' => array('#' => '$ServiceOccupancy_ChildCount$')),
			'DateFrom' => array('@date' => '$DateFrom_date$'),
			'DateTo' => array('@date' => '$DateTo_date$')
		)
	)
);

$result = array('usage' => 'format parameter has to have one of these two values: json or xml');
if (array_key_exists('format', $_REQUEST)) {
	if ($_REQUEST['format'] == 'json') {
		$result = $json;
	} else if ($_REQUEST['format'] == 'xml') {
		$result = $xml;
	}
}

echo json_encode($result);
?>
