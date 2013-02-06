<?php

include_once 'helper/Token.php';

class Token extends Commons
{
	/**
	 * @static
	 * @return void
	 */
	public static function login()
	{
		self::debug('init');		
		$helper = new HelperToken($_REQUEST["email"], $_REQUEST["password"]);
		echo $helper->login();
		self::debug('done');
	}
}

?>
