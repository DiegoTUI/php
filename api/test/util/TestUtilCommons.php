<?php

include_once 'test/util/TestUtilMongo.php';
include_once 'test/util/TestUtilLogging.php';
include_once 'util/UtilConstants.php';
include_once 'controller/ControllerToken.php';
include_once 'controller/ControllerUser.php';

class TestUtilCommons extends PHPUnit_Framework_TestCase
{
	private $_usersCollection;
	private $_tokensCollection;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_usersCollection = TestUtilMongo::getInstance()->getCollection('users');
		$this->_tokensCollection = TestUtilMongo::getInstance()->getCollection('tokens');
	}
	
	public static function debug($message)
	{
		TestUtilLogging::getInstance()->debug($message);
	}

	/**
	 * Perform a valid login with whatever is in POST
	 * @return token
	 */
	public function validLogin()
	{
		try
		{
			ControllerToken::login();
		}
		catch (TuiException $e) 
		{
			$this->fail("Valid login failed with exception: " . $e->getMessage());
		}
		
		$stringOutput = ob_get_contents();
		self::debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that a token is returned
		$this->assertTrue(array_key_exists('token', $stringArray), "returned no token when making a valid login");

		//check that the token is valid
		$token = $stringArray["token"];
		$this->assertTrue(strlen($token) == TUI_TOKEN_LENGTH, "invalid token length. It is " . strlen($token) . " and should be " . TUI_TOKEN_LENGTH);

		//check that the token was written in the db
		$tokenObject = $this->_tokensCollection->findOne(array('token' => $token));
		self::debug('Querying tokens for token: [' . $token . ']');
		$this->assertTrue($tokenObject != NULL, "token not saved in the database");
		
		//clean the buffer for next test
		ob_clean();

		return $token;		
	}

	/**
	 * Perform a invalid login with whatever is in POST
	 * @return token
	 */
	public function invalidLogin ()
	{
		try
		{
			ControllerToken::login();
			$this->fail("Invalid login did not throw any exception");
		}
		catch (TuiException $e) 
		{
			//OK
		}

		//clean the buffer for next test
		ob_clean();
	}

	/**
	 * tries to add a user with the admin token and returns the output of the operation
	 * @return jsonarray
	 */
	public function addUserWithAdminToken()
	{
		ControllerUser::addUser(TUI_TEST_ADMIN_TOKEN);
		$stringOutput = ob_get_contents();
		self::debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that a userId is returned
		$this->assertTrue(array_key_exists('userId', $stringArray), "returned no userId when making a valid add_user");

		//check that the userId is valid
		$userId = $stringArray["userId"];
		$this->assertTrue(strlen($userId) == TUI_ADMIN_LENGTH, "invalid userId length. It is " . strlen($userId) . " and should be " . TUI_ADMIN_LENGTH);

		//check that the user was written in the db
		$user = $this->_usersCollection->findOne(array("userId" => $userId));
		self::debug('Querying users for userId: [' . $userId . ']');
		$this->assertTrue($user != NULL, "user not saved in the database");
		
		//check that the email in the db is correct
		//$this->assertTrue(strcmp($_REQUEST["email"],$user['email']) == 0, "incorrect e-mail stored in database");	
		$this->assertEquals($_REQUEST["email"], $user['email']);
		
		//clean the buffer for next test
		ob_clean();
	}

	/**
	 * tries to remove a user with the admin token and returns the output of the operation
	 * @return jsonarray
	 */
	public function removeUserWithAdminToken()
	{
		//remove user with admin token
		try 
		{
			ControllerUser::removeUser(TUI_TEST_ADMIN_TOKEN);
		}
		catch (KimiaException $e) 
		{
			$this->fail("Unable to remove user with admin token: " . $e->getMessage());
		}
		
		//Controller_Rest_User::removeUser(KIMIA_TEST_ADMIN_TOKEN);
		$stringOutput = ob_get_contents();
		self::debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that a userId is returned
		$this->assertTrue(array_key_exists('userId', $stringArray), "returned no userId when making a valid remove_user");

		//check that the userId is valid
		$userId = $stringArray["userId"];
		$this->assertTrue(strlen($userId) == TUI_ADMIN_LENGTH, "invalid userId length. It is " . strlen($userId) . " and should be " . TUI_ADMIN_LENGTH);

		//check that the user was deleted from the db
		$user = $this->_usersCollection->findOne(array("userId" => $userId));
		self::debug('Querying users for userId: [' . $userId . ']');
		$this->assertTrue($user == NULL, "user not properly deleted from the database");
		
		//clean the buffer for next test
		ob_clean();
	}

	/**
	 * returns the current userId for the POST user
	 * @return userID, null if it does not exist in the db
	 */
	public function getUserIdForCurrentUser()
	{
		$userId = null;
		
		$user = $this->_usersCollection->findOne(array("email" => $_REQUEST["email"]));

		self::debug('Querying users for email: [' . $_REQUEST["email"] . ']');

		if ($user)
			$userId = $user['userId'];	

		return $userId;
	}

	/**
	 * creates test user and inserts it in the POST global
	 * @return void
	 */
	public function createTestUser()
	{
		$_REQUEST["userName"] = "zurullo";
		$_REQUEST["email"] = "zurullo@test";
		$_REQUEST["password"] = "perrilla";
		$_REQUEST["roleId"] = "user";
	}

	/**
	 * reset POST variable
	 * @return void
	 */
	public function resetPost()
	{
		$_REQUEST["userId"] = NULL;
		$_REQUEST["userName"] = NULL;
		$_REQUEST["email"] = NULL;
		$_REQUEST["password"] = NULL;
		$_REQUEST["roleId"] = NULL;
	}
	
	/**
	 * reset GET variable
	 * @return void
	 */
	public function resetGet()
	{
		$_REQUEST["userId"] = NULL;
		$_REQUEST["appId"] = NULL;
		$_REQUEST["appName"] = NULL;
		$_REQUEST["storeLink"] = NULL;
		$_REQUEST["os"] = NULL;
		$_REQUEST["url"] = NULL;
		$_REQUEST["webHookId"] = NULL;
		$_REQUEST["type"] = NULL;
		$_REQUEST["notifyWebHooks"] = NULL;
		$_REQUEST["eventTypeId"] = NULL;
		$_REQUEST["notifyKimia"] = NULL;
		$_REQUEST["price"] = NULL;
		$_REQUEST["kimiaAccess"] = NULL;
	}
}
