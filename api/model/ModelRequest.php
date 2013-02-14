<?php

include_once 'util/UtilCommons.php';
include_once 'error/error.php';

/**
 * Variables in the request. Test mode is set adding a "test" variable.
 */
class ModelRequest extends UtilCommons
{
	var $variables;
	var $test = false;

	/**
	 * Store the given set of request variables.
	 */
	function __construct($request_variables)
	{
		$this->variables = $request_variables;
	}

	/**
	 * Read a variable from the request, show error if not present and it is
	 * mandatory. Remove the key from the request after reading it.
	 * Returns the variable read, or $default if not present.
	 */
	function read($key, $mandatory = true, $default = null)
	{
		if (!$this->has($key, $mandatory))
		{
			return $default;
		}
		$result = $this->variables[$key];
		unset($this->variables[$key]);
		if (!$result)
		{
			return $default;
		}
		return $result;
	}

	/**
	 * Peek into a value from the request, without removing the key.
	 */
	function peek($key)
	{
		if (!$this->has($key, false))
		{
			return null;
		}
		return $this->variables[$key];
	}

	/**
	 * Find out if the request contains the given key, returns true or false.
	 * Shows an error if the $key is not present but is $mandatory.
	 */
	function has($key, $mandatory = false)
	{
		if (!isset($this->variables[$key]))
		{
			if ($mandatory)
			{
				// no key in request; error
				page_error('Empty ' . $key);
			}
			return false;
		}
		return true;
	}

	/**
	 * Write a variable to the request.
	 */
	function write($key, $value)
	{
		$this->variables[$key] = $value;
	}

	/**
	 * Get the keys for all request variables.
	 */
	function get_keys()
	{
		return array_keys($this->variables);
	}

	/**
	 * Return if we are in test mode.
	 */
	function testing()
	{
		return $this->test;
	}
}

// create a global request object with all _REQUEST variables
$REQUEST = new ModelRequest($_REQUEST);
