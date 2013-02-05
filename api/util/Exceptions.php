<?php

class TuiException extends Exception
{
	/**
	 * Redefine the exception so message isn't optional.
	 */
	public function __construct($message, $code = 0, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}

/**
 * Unauthorized; show a 401 page.
 */
class UnauthorizedException extends TuiException
{
}

/**
 * Object not found; show a 404 page.
 */
class NotFoundException extends TuiException
{
}

