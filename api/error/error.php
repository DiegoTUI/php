<?php

include_once 'util/UtilConfig.php';
include_once 'util/UtilExceptions.php';

$ERROR_MESSAGE = '';

function send_page($header, $page, $message = null)
{
	global $CONFIG;
	global $ERROR_MESSAGE;
	if ($CONFIG["test"])
		throw new TuiException ("page error");
	header($header);
	$ERROR_MESSAGE = $message;
	include_once('error/' . $page);
	exit();
}

// send to HTTP 401: unauthorized
function unauthorized($message)
{
	send_page('HTTP/1.0 401 Unauthorized', './401.php', $message);
}

// send to HTTP 403: forbidden
function forbidden()
{
	send_page('HTTP/1.0 403 Forbidden', './403.php');
}

/**
 * Send to HTTP 404: not found.
 */
function not_found($message)
{
	send_page('HTTP/1.0 404 Not found', './404.php', $message);
}

/**
 * Send to HTTP 500: page error.
 */
function page_error($message)
{
	send_page('HTTP/1.0 500 ' . $message, './500.php', $message);
}

