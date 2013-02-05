<?php

/**
 * Check if a string is equal to another.
 */
function equals($first, $second)
{
	if (!$first && !$second)
	{
		return true;
	}
	if (!$first || !$second)
	{
		return false;
	}
	if ($first == "" && $second == "")
	{
		return true;
	}
	if ($first == "" || $second == "")
	{
		return false;
	}
	if (strcmp($first, $second) == 0)
	{
		return true;
	}
	return false;
}

/**
 * Check if a string contains another one.
 */
function contains($haystack, $needle)
{
	if (!$haystack || !$needle)
	{
		return false;
	}
	if ($haystack == "" || $needle == "")
	{
		return false;
	}
	return strpos($haystack, $needle) !== false;
}

/**
 * Compare two strings in a fuzzy way: if one is contained in the other,
 * removing punctuation signs, ignoring case, etcetera.
 */
function compareStrings($first, $second)
{
	$first = convertString($first);
	$second = convertString($second);
	if (!$first && !$second)
	{
		return true;
	}
	if (!$first || !$second)
	{
		return false;
	}
	if ($first == "" && $second == "")
	{
		return true;
	}
	if ($first == "" || $second == "")
	{
		return false;
	}
	if (equals($first, $second))
	{
		return true;
	}
	if (strlen($first) > strlen($second) && contains($first, $second))
	{
		return true;
	}
	if (strlen($first) < strlen($second) && contains($second, $first))
	{
		return true;
	}
	return false;
}

/**
 * Convert a string to lower case, remove punctuation, etcetera.
 */
function convertString($input)
{
	$replaced = str_replace( array( '\'', '"', ',' , '.', '-', ';', '<', '>' ), '', $input);
	return strtolower($replaced);
}

/**
 * Check that a string starts with another one.
 */
function startsWith($haystack, $needle)
{
	return strpos($haystack, $needle) === 0;
}

/**
 * Return the $haystack substring that goes up to (but excluding) the $needle.
 */
function substringUpTo($haystack, $needle)
{
	$arr = explode($needle, $haystack, 2);
	return $arr[0];
}

/**
 * Return the $haystack substring that starts at the $needle. If no $needle is found return null.
 */
function substringFrom($haystack, $needle)
{
	$arr = explode($needle, $haystack, 2);
	if (count($arr) < 2)
	{
		return null;
	}
	return $arr[1];
}

/**
 * Insert an element into an array at the given position.
 */
function arrayInsert(&$haystack, $position, $needle)
{
	$toInsert = array($needle);
	array_splice($haystack, $position, 0, $toInsert);
}

/**
 * Convert an ISO date to a printable date.
 */
function getPrintableDate($date)
{
	$timestamp = strtotime($date);
	return date('M d, Y', $timestamp);
}

/**
 * Header control. Used for fine-grained control and to mock tests.
 */
class Headers
{
	/**
	 * Test mode.
	 */
	private static $test = false;

	/**
	 * Store cookies in tests.
	 */
	private static $cookies = array();

	/**
	 * Latest redirection.
	 */
	private static $redirection = null;

	/**
	 * Set test mode.
	 */
	public static function setTestMode()
	{
		Headers::$test = true;
		Headers::$cookies = array();
		Headers::$redirection = null;
	}

	/**
	 * Set a cookie.
	 */
	public static function setCookie($name, $value)
	{
		if (Headers::$test)
		{
			Headers::$cookies[$name] = $value;
			return true;
		}
		return setcookie($name, $value, time() + KIMIA_COOKIE_EXPIRATION, KIMIA_COOKIE_PATH, KIMIA_COOKIE_DOMAIN);
	}

	/**
	 * Find out if the cookie was set.
	 */
	public static function hasCookie($name)
	{
		if (Headers::$test)
		{
			return isset(Headers::$cookies[$name]);
		}
		return isset($_COOKIE[$name]);
	}

	/**
	 * Get the value of a cookie.
	 */
	public static function getCookie($name)
	{
		if (Headers::$test)
		{
			return Headers::$cookies[$name];
		}
		return $_COOKIE[$name];
	}

	/**
	 * Set a redirection.
	 */
	public static function redirect($url)
	{
		if (Headers::$test)
		{
			Headers::$redirection = $url;
			return;
		}
                header('HTTP/1.1 301 Moved Permanently');
                header('Location: ' . $url);
		echo "\r\n\r\n\r\n";
	}

	/**
	 * Get the latest redirection.
	 */
	public static function getRedirection()
	{
		return Headers::$redirection;
	}
}

