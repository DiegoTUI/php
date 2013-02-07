<?php

include_once 'helper/HelperUser.php';

class ControllerUser extends UtilCommons
{
	/**
	 * @static
	 * @param $token
	 * @return void
	 */
	public static function addUser($token)
	{
		self::debug('init');
	
		$helper = new HelperUser($token);
		$helper->prepareAddUser($_REQUEST["userName"],$_REQUEST["email"], $_REQUEST["password"], $_REQUEST["roleId"]);
		echo $helper->addUser();
		self::debug('done');
	}
	
	/**
	 * @static
	 * @return void
	 */
	public static function removeUser($token)
	{
		self::debug('init');
		
		$helper = new HelperUser($token);
		
		UtilAuth::getInstance()->checkRequestKey("userId");
		$userId = $_REQUEST["userId"];
		$helper->prepareRemoveReadUser($userId);   //there is no user to delete in the database
		echo $helper->removeUser();
		self::debug('done');
	}

	/**
	 * @static
	 * @return void
	 */
	public static function modifyUser($token)
	{
		self::debug('init');
		
		UtilAuth::getInstance()->checkRequestKey("userId");
		//now build the parameters of the helper
		$userName = (isset($_REQUEST["userName"]) ? $_REQUEST["userName"] : null);
		$email = (isset($_REQUEST["email"]) ? $_REQUEST["email"] : null);
		$password = (isset($_REQUEST["password"]) ? $_REQUEST["password"] : null);
		$roleId = (isset($_REQUEST["roleId"]) ? $_REQUEST["roleId"] : null);

		//build helper and trigger function
		$helper = new Controller_Rest_Helper_User($token);
		$helper->prepareModifyUser($_REQUEST["userId"], $userName, $email, $password, $roleId);
		echo $helper->modifyUser();
		self::debug('done');
	}
	
	public static function readUser($token)
	{
		self::debug('init');

		$helper = new Controller_Rest_Helper_User($token);
		UtilAuth::getInstance()->checkRequestKey("userId");
		$userId = $_REQUEST["userId"];
		$helper->prepareRemoveReadUser($userId);   //there is no user to read in the database
		echo $helper->readUser();
		self::debug('done');
	}

	public static function listUsers($token)
	{
		self::debug('init');
		
		$helper = new Controller_Rest_Helper_User($token);
		$userName = (isset($_REQUEST["userName"]) ? $_REQUEST["userName"] : "");
		$email = (isset($_REQUEST["email"]) ? $_REQUEST["email"] : "");
		$roleId = (isset($_REQUEST["roleId"]) ? $_REQUEST["roleId"] : "");
		echo $helper->listUsers($userName, $email, $roleId);
		self::debug('done');
	}
}
