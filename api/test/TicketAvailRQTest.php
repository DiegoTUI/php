<?php

include_once 'model/TicketAvailRQ.php';
include_once 'test/util/TestUtilCommons.php';
include_once 'util/UtilConfig.php';
include_once 'util/UtilExceptions.php';

class TicketAvailRQTest extends PHPUnit_Framework_TestCase
{

	protected $_common;

	/**
	 * @return void
	 */
	protected function setUp()
	{
		$this->_common = new TestUtilCommons();
		$this->_common->createTicketAvailRQ();
		global $CONFIG;
		$CONFIG['test'] = true;
		ob_start();
	}

	/**
	 * Test adding creating the request, reading, peeking and writing into the request
	 * @return void
	 */
	public function testRequest()
	{
		/*$sizeRequest = count($_REQUEST);
		//Create the request
		$request = new ModelRequest($_REQUEST);
		//Check that it was created OK
		$this->assertEquals(count($request->variables),$sizeRequest);
		//Look for a non existing key
		$key = "nonExistingKey";
		$this->assertEquals($request->read($key, false), null);
		$this->assertEquals($request->peek($key), null);
		try
		{
			$request->read($key, true);
			$this->fail('did not throw an exception when reading a mandatory non-existing key');
		}
		catch (TuiException $e)
		{
		//OK
		}
		//Look for an existing key
		$key = "password";
		$result = $request->read($key);
		$this->assertTrue($result != null, "did not return a correct result when reading a valid key");
		$this->assertEquals($result,$_REQUEST[$key]);
		$this->assertEquals(count($request->variables),$sizeRequest-1);
		//Look for the same key again
		$this->assertEquals($request->read($key, false), null);
		$this->assertEquals($request->peek($key), null);
		//peek for an existing key
		$key = "userName";
		$result = $request->peek($key);
		$this->assertTrue($result != null, "did not return a correct result when peeking a valid key");
		$this->assertEquals($result,$_REQUEST[$key]);
		$this->assertEquals(count($request->variables),$sizeRequest-1);
		//read the same key
		$result = $request->read($key, false);
		$this->assertTrue($result != null, "did not return a correct result when reading a valid key");
		$this->assertEquals($result,$_REQUEST[$key]);
		$this->assertEquals(count($request->variables),$sizeRequest-2);
		//write a new key and read it
		$key = "newKey";
		$value = "newValue";
		$request->write($key, $value);
		$result = $request->read($key);
		$this->assertTrue($result != null, "did not return a correct result when reading a valid key");
		$this->assertEquals($result, $value);
		$this->assertEquals(count($request->variables),$sizeRequest-2);*/
	}
	
	/**
	 * @return void
	 */
	protected function tearDown()
	{
		global $CONFIG;
		$CONFIG['test'] = false;
		ob_end_clean();
	}
}
