<?php

//include_once(__DIR__ . '/../../common/frameworkInclude.php');

class Test_Util_Common extends PHPUnit_Framework_TestCase
{
	protected $_database;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->_database = new Test_Util_Database();
	}

	/**
	 * Perform a valid login with whatever is in POST
	 * @return token
	 */
	public function validLogin()
	{
		try
		{
			Controller_Rest_Token::login();
		}
		catch (KimiaException $e) 
		{
			$this->fail("Valid login failed with exception: " . $e->getMessage());
		}
		
		$stringOutput = ob_get_contents();
		Test_Util_Log::debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that a token is returned
		$this->assertTrue(array_key_exists('token', $stringArray), "returned no token when making a valid login");

		//check that the token is valid
		$token = $stringArray["token"];
		$this->assertTrue(strlen($token) == KIMIA_TOKEN_LENGTH, "invalid token length. It is " . strlen($token) . " and should be " . KIMIA_TOKEN_LENGTH);

		//check that the token was written in the db
		$sql = "select * from tokens where token ='" . $token . "'";
		Test_Util_Log::debug('Querying tokens for token: [' . $sql . ']');
		$row = $this->_database->selectOne($sql);
		$this->assertTrue($row != NULL, "token not saved in the database");
		
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
			Controller_Rest_Token::login();
			$this->fail("Invalid login did not throw any exception");
		}
		catch (KimiaException $e) 
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
		Controller_Rest_User::addUser(KIMIA_TEST_ADMIN_TOKEN);
		$stringOutput = ob_get_contents();
		Test_Util_Log::debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that a userId is returned
		$this->assertTrue(array_key_exists('userId', $stringArray), "returned no userId when making a valid add_user");

		//check that the userId is valid
		$userId = $stringArray["userId"];
		$this->assertTrue(strlen($userId) == KIMIA_ADMIN_LENGTH, "invalid userId length. It is " . strlen($userId) . " and should be " . KIMIA_ADMIN_LENGTH);

		//check that the user was written in the db
		$sql = "select * from users where userId ='" . $userId . "'";
		Test_Util_Log::debug('Querying users for userId: [' . $sql . ']');
		$row = $this->_database->selectOne($sql);
		$this->assertTrue($row != NULL, "user not saved in the database");
		
		//check that the email in the db is correct
		$this->assertTrue(strcmp($_REQUEST["email"],$row->email) == 0, "incorrect e-mail stored in database");	

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
			Controller_Rest_User::removeUser(KIMIA_TEST_ADMIN_TOKEN);
		}
		catch (KimiaException $e) 
		{
			$this->fail("Unable to remove user with admin token: " . $e->getMessage());
		}
		
		//Controller_Rest_User::removeUser(KIMIA_TEST_ADMIN_TOKEN);
		$stringOutput = ob_get_contents();
		Test_Util_Log::debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that a userId is returned
		$this->assertTrue(array_key_exists('userId', $stringArray), "returned no userId when making a valid remove_user");

		//check that the userId is valid
		$userId = $stringArray["userId"];
		$this->assertTrue(strlen($userId) == KIMIA_ADMIN_LENGTH, "invalid userId length. It is " . strlen($userId) . " and should be " . KIMIA_ADMIN_LENGTH);

		//check that the user was deleted from the db
		$sql = "select * from users where userId ='" . $userId . "'";
		Test_Util_Log::debug('Querying users for userId: [' . $sql . ']');
		$row = $this->_database->selectOne($sql);
		$this->assertTrue($row == NULL, "user not properly deleted from the database");
		
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

		$sql = "select * from users where email ='" . $_REQUEST["email"] . "'";
		Test_Util_Log::debug('Querying users for email: [' . $sql . ']');
		$row = $this->_database->selectOne($sql);
		if ($row)
			$userId = $row->userId;	

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
	 * tries to add an app with the given token and returns the output of the operation
	 * @return appId
	 */
	public function addAppWithToken($token)
	{
		Controller_Rest_App::addApp($token);
		$stringOutput = ob_get_contents();
		Test_Util_Log::debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that an appId is returned
		$this->assertNotNull($stringArray, 'empty response for add_app');
		$this->assertTrue(array_key_exists('appId', $stringArray), "returned no appId when making a valid add_app");

		//check that the appId is valid
		$appId = $stringArray["appId"];
		$this->assertTrue(strlen($appId) == KIMIA_ADMIN_LENGTH, "invalid appId length. It is " . strlen($appId) . " and should be " . KIMIA_ADMIN_LENGTH);

		//check that the app was written in the db
		$sql = "select * from apps where appId ='" . $appId . "'";
		Test_Util_Log::debug('Querying apps for appId: [' . $sql . ']');
		$row = $this->_database->selectOne($sql);
		$this->assertTrue($row != NULL, "app not saved in the database");

		//clean the buffer for next test
		ob_clean();
		
		//return appId
		return $appId;
	}
	
	/**
	 * tries to remove an app with the given token and returns the output of the operation
	 * @return jsonarray
	 */
	public function removeAppWithToken($token)
	{
		Controller_Rest_App::removeApp($token);

		$stringOutput = ob_get_contents();
		Test_Util_Log::debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that an appId is returned
		$this->assertTrue(array_key_exists('appId', $stringArray), "returned no appId when making a valid remove_app");

		//check that the appId is valid
		$appId = $stringArray["appId"];
		$this->assertTrue(strlen($appId) == KIMIA_ADMIN_LENGTH, "invalid appId length. It is " . strlen($appId) . " and should be " . KIMIA_ADMIN_LENGTH);

		//check that the app was deleted from the db
		$sql = "select * from apps where appId ='" . $appId . "'";
		Test_Util_Log::debug('Querying apps for appId: [' . $sql . ']');
		$row = $this->_database->selectOne($sql);
		$this->assertTrue($row == NULL, "app not properly deleted from the database");
		
		//clean the buffer for next test
		ob_clean();
	}
	
	/**
	 * creates test app and inserts it in the GET global
	 * @return void
	 */
	public function createTestApp()
	{	
		$_REQUEST["appName"] = "zurullapp";
		$_REQUEST["storeLink"] = "zurullapp.itunes.com";
		$_REQUEST["os"] = "ios";
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

	/**
	 * Remove test webhooks for an event type.
	 */
	public function removeTestWebHooks($eventTypeId)
	{
		$sql = "delete from webHooks where eventTypeId = '" . $eventTypeId . "';";
		Test_Util_Log::debug('Removing test webHooks: ' . $sql);
		$this->_database->delete($sql);
	}
}

