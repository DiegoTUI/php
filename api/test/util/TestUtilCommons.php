<?php

include_once 'test/util/TestUtilMongo.php';
include_once 'test/util/TestUtilLogging.php';
include_once 'util/UtilConstants.php';
include_once 'util/UtilKernel.php';
include_once 'util/UtilConfig.php';

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
		TestUtilLogging::getInstance()->debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that a token is returned
		$this->assertTrue(array_key_exists('token', $stringArray), "returned no token when making a valid login");

		//check that the token is valid
		$token = $stringArray["token"];
		$this->assertTrue(strlen($token) == TUI_TOKEN_LENGTH, "invalid token length. It is " . strlen($token) . " and should be " . TUI_TOKEN_LENGTH);

		//check that the token was written in the db
		$tokenObject = $this->_tokensCollection->findOne(array('token' => $token));
		TestUtilLogging::getInstance()->debug('Querying tokens for token: [' . $token . ']');
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
		TestUtilLogging::getInstance()->debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that a userId is returned
		$this->assertTrue(array_key_exists('userId', $stringArray), "returned no userId when making a valid add_user");

		//check that the userId is valid
		$userId = $stringArray["userId"];
		$this->assertTrue(strlen($userId) == TUI_ADMIN_LENGTH, "invalid userId length. It is " . strlen($userId) . " and should be " . TUI_ADMIN_LENGTH);

		//check that the user was written in the db
		$user = $this->_usersCollection->findOne(array("userId" => $userId));
		TestUtilLogging::getInstance()->debug('Querying users for userId: [' . $userId . ']');
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
		catch (TuiException $e) 
		{
			$this->fail("Unable to remove user with admin token: " . $e->getMessage());
		}
		
		//Controller_Rest_User::removeUser(KIMIA_TEST_ADMIN_TOKEN);
		$stringOutput = ob_get_contents();
		TestUtilLogging::getInstance()->debug('stringOutput: ' . $stringOutput);
		$stringArray = json_decode($stringOutput, true);

		//check that a userId is returned
		$this->assertTrue(array_key_exists('userId', $stringArray), "returned no userId when making a valid remove_user");

		//check that the userId is valid
		$userId = $stringArray["userId"];
		$this->assertTrue(strlen($userId) == TUI_ADMIN_LENGTH, "invalid userId length. It is " . strlen($userId) . " and should be " . TUI_ADMIN_LENGTH);

		//check that the user was deleted from the db
		$user = $this->_usersCollection->findOne(array("userId" => $userId));
		TestUtilLogging::getInstance()->debug('Querying users for userId: [' . $userId . ']');
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

		TestUtilLogging::getInstance()->debug('Querying users for email: [' . $_REQUEST["email"] . ']');

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
	 * creates test ticket avail rq
	 * @return void
	 */
	public function createTicketAvailRQ()
	{
		global $CONFIG;
		$_REQUEST["echoToken"] = "DummyEchoToken";
		$_REQUEST["sessionId"] = "DummySessionId";
		$_REQUEST["Language"] = "ENG";
		$_REQUEST["Credentials_User"] = $CONFIG["user"];
		$_REQUEST["Credentials_Password"] = $CONFIG["password"];
		$_REQUEST["PaginationData_itemsPerPage"] = "25";
		$_REQUEST["PaginationData_pageNumber"] = "1";
		$_REQUEST["ServiceOccupancy_AdultCount"] = "1";
		$_REQUEST["ServiceOccupancy_ChildCount"] = "0";
		$_REQUEST["Destination_code"] = "PMI";
		//$_REQUEST["Destination_Value"] = "Jaaarrrl";
		$_REQUEST["Destination_Name_Fake"] = "Fake";
		$_REQUEST["DateFrom_date"] = $this->tomorrow();
		$_REQUEST["DateTo_date"] = $this->day_after_tomorrow();
	}
	
	/**
	 * creates test ticket avail rq
	 * @return void
	 */
	public function createHotelListRQ()
	{
		global $CONFIG;
		$_REQUEST["echoToken"] = "DummyEchoToken";
		$_REQUEST["Language"] = "ENG";
		$_REQUEST["Credentials_User"] = $CONFIG["user"];
		$_REQUEST["Credentials_Password"] = $CONFIG["password"];
		$_REQUEST["Destination_code"] = "PMI";
		$_REQUEST["Destination_Name_Fake"] = "Fake";
	}
	
	/**
	 * reset REQUEST variable
	 * @return void
	 */
	public function resetRequest()
	{
		foreach ($_REQUEST as $key=>$value)
		{
			unset($_REQUEST[$key]);
		}
	}
	
	/**
	 * compare the value of an attribute with what's in the $_REQUEST global
	 * @return void
	 */
	public function checkAttribute($attribute, $value)
	{
		TestUtilLogging::getInstance()->debug('Attribute about to check: ' . $attribute->id . " against value: " . $value);
		if (isset($_REQUEST[$attribute->id]))
		{
			$this->assertTrue(equals($_REQUEST[$attribute->id], $value), "wrong value for attribute: " . $attribute->id . " - it is " . $attribute->value . " and should be: " . $value);
		}
		ob_clean();
	}
	
	/**
	 * checks that all the attributes in the entity are included in the xml
	 * @return void
	 */
	public function checkXMLWithEntity ($xml, $entity)
	{
		$sxe = new SimpleXMLElement ($xml);
		var_dump (get_object_vars($sxe));
		$stringOutput = ob_get_contents();
		TestUtilLogging::getInstance()->debug('simpleXML: ' . $stringOutput);
		ob_clean();
		foreach ($entity->attributes as $attribute)
		{
			$this->checkAttributeInSimpleXML($attribute, $sxe);
		}
	}
	
	/**
	 * checks that a certain attribute included in the provided SimpleXML
	 * @return void
	 */
	 public function checkAttributeInSimpleXML($attribute, $simpleXML)
	 {
		if (!$attribute->mandatory) return;
		
		$cursor = $simpleXML;
		//check that the full path exists
		foreach ($attribute->path as $node_name)
		{
			if (!equals($node_name,"attribute"))
			{
				$this->assertTrue(isset($cursor->$node_name), "checkAttributeInSimpleXML - Node: " . $node_name . " does not exist in xml provided");
				$cursor = $cursor->$node_name;
			}
		}
		//check that the value is correct
		$name = $attribute->name;
		if(equals($attribute->type, "attribute") && $attribute->checkable)
		{
			$this->assertEquals($attribute->value, $cursor[$attribute->name]);
		}
		else if(equals($attribute->type, "element"))
		{
			if (equals($name, "Value"))
			{
				$this->assertEquals($attribute->value, (string)$cursor);
			}
			else
				$this->assertEquals($attribute->value, $cursor->$name);
		}
	 }
	
	/**
	 * check if an attribute was correctly translated to xml_json
	 * @return void
	 */
	public function check_xml_json_attribute($attribute, $xml_json)
	{
		$piece = &$xml_json;
		foreach ($attribute->path as $node_name)
		{
			$this->assertTrue (isset($piece[$node_name]), "Level " . $node_name . " not set in xml_json for attribute: " . $attribute->id);
			$piece = &$piece[$node_name];
		}
		if (equals($attribute->type,"list"))
		{
			$this->assertTrue (equals(json_encode($piece[$attribute->name]), $attribute->value), "Value for attribute: " . $attribute->id . " not set properly in xml_json. It is " . json_encode($piece[$attribute->name]) . " and should be " . $attribute->value . "for name " . $attribute->name);
		}
		else
		{
			$this->assertTrue (equals($piece[$attribute->name], $attribute->value), "Value for attribute: " . $attribute->id . " not set properly in xml_json. It is " . $piece[$attribute->name] . " and should be " . $attribute->value);
		}
	}
	 
	 /**
	 * checks if an array of elements exist individually in a certain simpleXML
	 * @return void
	 */
	public function checkElementsNotNull($simpleXML, $elements)
	{
		foreach ($elements as $element)
		{
			TestUtilLogging::getInstance()->debug('Processing element: ' . $element . " of value " . $simpleXML->$element);
			$this->assertTrue (isset($simpleXML->$element), "Element " . $element . " does not exixt within XML " . $simpleXML->getName());
		}
	}
	/**
	 * returns today's date in format "yyyymmdd"
	 * @return void
	 */
	public function today()
	{
		return date("Ymd");
	}
	
	/**
	 * returns tomorrow's date in format "yyyymmdd"
	 * @return void
	 */
	public function tomorrow()
	{
		return date("Ymd", time() + 86400);
	}
	
	/**
	 * returns the day after tomorrow's date in format "yyyymmdd"
	 * @return void
	 */
	public function day_after_tomorrow()
	{
		return date("Ymd", time() + 172800);
	}
}

