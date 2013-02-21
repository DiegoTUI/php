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
 * Checks if a string starts with Upper Case.
 */
function startsWithUpper($string)
{
	$chr = mb_substr ($string, 0, 1, "UTF-8");
    return mb_strtolower($chr, "UTF-8") != $chr;
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
 * Checks if an array is an associative array.
 */
 function isAssociative ($array)
 {
	return (bool)count(array_filter(array_keys($array), 'is_string'));
 }

 /**
  * Trims an xml and returns the content between the provided node (including the node.
  */
 function trimXML ($xml, $node)
 {
	$array1 = explode ("<" . $node, $xml);
	$array2 = explode ($node . ">", $array1[1]);
	return "<" . $node . $array2[0] . $node . ">";
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
 * Pretty prints a JSON.
 */
function prettyPrintJSON( $json )
{
	$result = '';
	$level = 0;
	$prev_char = '';
	$in_quotes = false;
	$ends_line_level = NULL;
	$json_length = strlen( $json );

	for( $i = 0; $i < $json_length; $i++ ) {
		$char = $json[$i];
		$new_line_level = NULL;
		$post = "";
		if( $ends_line_level !== NULL ) {
			$new_line_level = $ends_line_level;
			$ends_line_level = NULL;
		}
		if( $char === '"' && $prev_char != '\\' ) {
			$in_quotes = !$in_quotes;
		} else if( ! $in_quotes ) {
			switch( $char ) {
				case '}': case ']':
					$level--;
					$ends_line_level = NULL;
					$new_line_level = $level;
					break;

				case '{': case '[':
					$level++;
				case ',':
					$ends_line_level = $level;
					break;

				case ':':
					$post = " ";
					break;

				case " ": case "\t": case "\n": case "\r":
					$char = "";
					$ends_line_level = $new_line_level;
					$new_line_level = NULL;
					break;
			}
		}
		if( $new_line_level !== NULL ) {
			$result .= "\n".str_repeat( "\t", $new_line_level );
		}
		$result .= $char.$post;
		$prev_char = $char;
	}

	return $result;
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

