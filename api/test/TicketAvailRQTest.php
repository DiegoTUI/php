<?php

include_once 'model/TicketAvailRQ.php';
include_once 'model/ModelRequest.php';
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
		$this->_common->resetRequest();
		$this->_common->createTicketAvailRQ();
		global $CONFIG;
		$CONFIG['test'] = true;
		ob_start();
	}

	/**
	 * Test reading the request in an object
	 * @return void
	 */
	public function testReadRequest()
	{
		$sizeRequest = count($_REQUEST);
		//Create the request in the global variable $REQUEST
		global $REQUEST;
		$REQUEST = new ModelRequest($_REQUEST);
		//Check that it was created OK
		$this->assertEquals(count($REQUEST->variables),$sizeRequest);
		var_dump($REQUEST);
		$stringOutput = ob_get_contents();
		$this->_common->debug('REQUEST after reading: ' . $stringOutput);
		ob_clean();
		//Read all the attributes from the request
		global $TICKET_AVAIL_RQ;
		$TICKET_AVAIL_RQ->read_set_all();
		//Check that there is one attribute left in the request
		$this->assertEquals(count($REQUEST->variables), 1);
		//check that the attributes were properly imported
		foreach ($TICKET_AVAIL_RQ->attributes as $attribute)
		{
			$this->_common->debug ("About to check attribute: " . $attribute->id . " with value: " . $attribute->value);
			$this->_common->checkAttribute ($attribute, $attribute->value);
		}
		
		ob_clean();
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
