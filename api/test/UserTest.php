<?php

include_once 'test/util/TestUtilCommons.php';

class UserTest extends PHPUnit_Framework_TestCase
{

	protected $_common;
	private $_usersCollection;

	/**
	 * @return void
	 */
	protected function setUp()
	{
		$this->_common = new TestUtilCommons();
		$this->_common->createTestUser();
		$this->_usersCollection = TestUtilMongo::getInstance()->getCollection('users');

		ob_start();
	}

	/**
	 * Test adding a user, log in with it, and then removing it
	 * @return void
	 */
	public function testAddRemoveUser()
	{
		//Create a user with admin token
		$this->_common->addUserWithAdminToken();

		//retrieve userId
		$_REQUEST["userId"] = $this->_common->getUserIdForCurrentUser();

		//check that a userId is returned
		$this->assertTrue($_REQUEST["userId"] != null, "returned no userId when making a valid query");

		//perform a valid login
		$token = $this->_common->validLogin();

		//remove user with admin token
		$this->_common->removeUserWithAdminToken();

		//clean buffer
		ob_clean();
	}

	/**
	 * Test adding a user twice. And removing it twice.
	 * @return void
	 */
	public function testAddUserTwice()
	{
		//Create a user with admin token
		$this->_common->addUserWithAdminToken();

		//retrieve userId
		$_REQUEST["userId"] = $this->_common->getUserIdForCurrentUser();

		//check that a userId is returned
		$this->assertTrue($_REQUEST["userId"] != null, "returned no userId when making a valid query");

		//try to add it again
		try 
		{
			ControllerUser::addUser(TUI_TEST_ADMIN_TOKEN);
			$this->fail("It added a user twice without triggering an exception");
		}
		catch (TuiException $e) 
		{
			//OK
		}

		//clean buffer
		ob_clean();

		//now remove the user
		$this->_common->removeUserWithAdminToken();

		//clean buffer
		ob_clean();
	}
	
	/**
	 * Test adding a user with the user token
	 * @return void
	 */
	public function testAddUserWithUserToken()
	{
		//add user with user token
		try 
		{
			ControllerUser::addUser(TUI_TEST_USER_TOKEN);
			$this->fail("It added a user using the user token without triggering an exception");
		}
		catch (UnauthorizedException $e) 
		{
			//OK
		}
		
		//clean the buffer for next test
		ob_clean();
	}

	/**
	 * Test removing a user with the user token
	 * @return void
	 */
	public function testRemoveUserWithUserToken()
	{
		//first add the user with the admin token
		$this->_common->addUserWithAdminToken();

		//retrieve userId
		$_REQUEST["userId"] = $this->_common->getUserIdForCurrentUser();

		//check that a userId is returned
		$this->assertTrue($_REQUEST["userId"] != null, "returned no userId when making a valid query");

		//remove user with user token
		try 
		{
			ControllerUser::removeUser(TUI_TEST_USER_TOKEN);
			$this->fail("It removed a user using the user token without triggering an exception");
		}
		catch (UnauthorizedException $e) 
		{
			//OK
		}
		
		//clean the buffer for next test
		ob_clean();

		//remove the user for the next test
		$this->_common->removeUserWithAdminToken();
	}

	/**
	 * Test modifying a user
	 * @return void
	 */
	public function testModifyUser()
	{
		//Create a user with admin token
		$this->_common->addUserWithAdminToken();
		//retrieve userId
		$userId = $this->_common->getUserIdForCurrentUser();
		$_REQUEST["userId"] = $userId;
		//check that a userId is returned
		$this->assertTrue($_REQUEST["userId"] != null, "returned no userId when making a valid query");
		//perform a valid login
		$token = $this->_common->validLogin();

		//try to modify the user with an empty _REQUEST
		$this->_common->resetPost();
		//call modify user with admin token, but nothing in it
		try
		{
			ControllerUser::modifyUser(TUI_TEST_ADMIN_TOKEN);
			$this->fail("It modified a user with no userId provided without triggering an exception");
		}
		catch (TuiException $e) 
		{
			//OK
		}

		//now insert just userId
		$_REQUEST["userId"] = $userId;

		//call modify user with admin token
		try
		{
			ControllerUser::modifyUser(TUI_TEST_ADMIN_TOKEN);
		}
		catch (TuiException $e) 
		{
			$this->fail("Unable to modify user with admin token and correct userId: " . $e->getMessage());
		}

		//change e-mail and password of the created user
		$newEmail = "tordo@test";
		$newPassword = "zorrilla";
		//get the new parameters into POST
		$_REQUEST["email"] = $newEmail;
		$_REQUEST["password"] = $newPassword;

		//reset userId field in POST
		$_REQUEST["userId"] = null;

		//call modify user with admin token, but no userId
		try
		{
			ControllerUser::modifyUser(TUI_TEST_ADMIN_TOKEN);
			$this->fail("It modified a user with no userId provided without triggering an exception");
		}
		catch (TuiException $e) 
		{
			//OK
		}

		//enter userId into POST
		$_REQUEST["userId"] = $userId;

		//call modify user with user token
		try
		{
			ControllerUser::modifyUser(TUI_TEST_USER_TOKEN);
			$this->fail("It modified a user with user token without triggering an exception");
		}
		catch (UnauthorizedException $e) 
		{
			//OK
		}

		//call modify user with admin token
		try
		{
			ControllerUser::modifyUser(TUI_TEST_ADMIN_TOKEN);
		}
		catch (TuiException $e) 
		{
			$this->fail("Unable to modify user with admin token: " . $e->getMessage());
		}

		//check that the user was written in the db
		$user = $this->_usersCollection->findOne(array("userId" => $_REQUEST["userId"]));
		TestUtilLogging::getInstance()->debug('Querying users for userId: [' . $_REQUEST["userId"] . ']');
		$this->assertTrue($user != NULL, "user not saved in the database");
	
		//check that the email in the db is correct
		$this->assertEquals($_REQUEST["email"],$user['email']);

		//clean buffer
		ob_clean();

		//perform a valid login
		$token = $this->_common->validLogin();

		//remove user with admin token
		$this->_common->removeUserWithAdminToken();

		//set the test user as it was
		$this->_common->createTestUser();

		//clean buffer
		ob_clean();
	}

	/**
	 * Test reading a user
	 * @return void
	 */
	public function testReadUser()
	{
		//Create a user with admin token
		$this->_common->addUserWithAdminToken();
		//retrieve userId
		$_REQUEST["userId"] = $this->_common->getUserIdForCurrentUser();
		//check that a userId is returned
		$this->assertTrue($_REQUEST["userId"] != null, "returned no userId when making a valid query");
		//perform a valid login
		$token = $this->_common->validLogin();

		//Try to read the user with a token that has an invalid userId
		try 
		{
			ControllerUser::readUser(TUI_TEST_USER_TOKEN);
			$this->fail("Read the user with invalid userId without triggering an exception");
		}
		catch (UnauthorizedException $e) 
		{
			//OK
		}

		//clean the buffer for next test
		ob_clean();

		//Try to read the user with his own token 
		try 
		{
			ControllerUser::readUser($token);
		}
		catch (TuiException $e) 
		{
			$this->fail("Unable to read a user with his own token: " . $e->getMessage());;
		}

		$stringOutput = ob_get_contents();
		TestUtilLogging::getInstance()->debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that the email returned is correct
		$this->assertEquals($stringArray["email"],$_REQUEST["email"]);
		
		//clean the buffer for next test
		ob_clean();

		//now try with an admin token
		try 
		{
			ControllerUser::readUser(TUI_TEST_ADMIN_TOKEN);
		}
		catch (TuiException $e) 
		{
			$this->fail("Unable to read a user with admin token: " . $e->getMessage());;
		}

		$stringOutput = ob_get_contents();
		TestUtilLogging::getInstance()->debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that the email returned is correct
		$this->assertEquals($stringArray["email"],$_REQUEST["email"]);

		//clean the buffer for next test
		ob_clean();

		//remove user with admin token
		$this->_common->removeUserWithAdminToken();
		
		//clean the buffer for next test
		ob_clean();
	}

	/**
	 * Test listing all the users
	 * @return void
	 */
	public function testListUsers()
	{
		//Try to list the users with a user token
		try 
		{
			ControllerUser::listUsers(TUI_TEST_USER_TOKEN);
			$this->fail("It listed users using the user token without triggering an exception");
		}
		catch (UnauthorizedException $e) 
		{
			//OK
		}
		
		//clean the buffer for next test
		ob_clean();

		$_REQUEST["userName"] = null;
		$_REQUEST["email"] = null;
		$_REQUEST["roleId"] = null;
		
		TestUtilLogging::getInstance()->debug('about to list users:". "\nuserName: ' . $_REQUEST["userName"] . "\nemail: " . $_REQUEST["email"] . "\nroleId: " . $_REQUEST["roleId"]);

		//now try with an admin token
		ControllerUser::listUsers(TUI_TEST_ADMIN_TOKEN);

		$stringOutput = ob_get_contents();
		TestUtilLogging::getInstance()->debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that it returned a proper list
		$this->assertTrue(count($stringArray) >= 2, "didnt list the users properly");

		//clean the buffer for next test
		ob_clean();

		//Now list all the users whose username contains "chodela" and email contains "kimia"
		$_REQUEST["userName"] = "ufos";
		$_REQUEST["email"] = "ente";
		$_REQUEST['roleId'] = null;
		ControllerUser::listUsers(TUI_TEST_ADMIN_TOKEN);

		$stringOutput = ob_get_contents();
		TestUtilLogging::getInstance()->debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that it returned a proper list. I assume that only one user is listed: pechodelata
		$this->assertTrue(count($stringArray) == 1, "didnt list the users properly");

		//clean the buffer for next test
		ob_clean();
	}

	/**
	 * @return void
	 */
	protected function tearDown()
	{
		ob_end_clean();
	}
}
