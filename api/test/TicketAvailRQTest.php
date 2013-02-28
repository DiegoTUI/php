<?php

include_once 'model/TicketAvailRQ.php';
include_once 'model/ModelRequest.php';
include_once 'test/util/TestUtilCommons.php';
include_once 'util/UtilConfig.php';
include_once 'util/UtilExceptions.php';
include_once 'util/UtilKernel.php';

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
		
		//leave everything clean for the next test
		$this->_common->resetRequest();
		$this->_common->createTicketAvailRQ();
		ob_clean();
	}
	
	/**
	 * Test getting the xml_json
	 * @return void
	 */
	public function testXmlJson()
	{
		$sizeRequest = count($_REQUEST);
		//Create the request in the global variable $REQUEST
		global $REQUEST;
		$REQUEST = new ModelRequest($_REQUEST);
		//Check that it was created OK
		$this->assertEquals(count($REQUEST->variables),$sizeRequest);
		//Read all the attributes from the request
		global $TICKET_AVAIL_RQ;
		$TICKET_AVAIL_RQ->read_set_all();
		//Check that there is one attribute left in the request
		$this->assertEquals(count($REQUEST->variables), 1);
		//produce the xml_json
		$xml_json = $TICKET_AVAIL_RQ->get_xml_json();
		$this->_common->debug('xml_json after reading: ' . prettyPrintJSON(json_encode($xml_json)));
		/*var_dump($xml_json);
		$stringOutput = ob_get_contents();
		$this->_common->debug('xml_json after reading: ' . $stringOutput);
		ob_clean();*/
		//check that everything has been translated OK
		foreach ($TICKET_AVAIL_RQ->attributes as $attribute)
		{
			$this->_common->check_xml_json_attribute($attribute, $xml_json);
		}
	}
	
	/**
	 * Test getting the xml
	 * @return void
	 */
	public function testGetXml()
	{
		$sizeRequest = count($_REQUEST);
		//Create the request in the global variable $REQUEST
		global $REQUEST;
		$REQUEST = new ModelRequest($_REQUEST);
		//Check that it was created OK
		$this->assertEquals(count($REQUEST->variables),$sizeRequest);
		//Read all the attributes from the request
		global $TICKET_AVAIL_RQ;
		$TICKET_AVAIL_RQ->read_set_all();
		$xml = $TICKET_AVAIL_RQ->get_xml();
		$this->_common->debug ("Xml produced: " . $xml);
		$trimmed_xml = trimXML($xml, $TICKET_AVAIL_RQ->name);
		$this->_common->debug ("TrimmedXml produced: " . $trimmed_xml);
		$this->_common->checkXMLWithEntity ($trimmed_xml , $TICKET_AVAIL_RQ);
		ob_clean();
	}
	
	/**
	 * Test calling the server and getting the results of the query
	 * @return void
	 */
	public function testCallAtlas()
	{
		$sizeRequest = count($_REQUEST);
		//Create the request in the global variable $REQUEST
		global $REQUEST;
		$REQUEST = new ModelRequest($_REQUEST);
		//Check that it was created OK
		$this->assertEquals(count($REQUEST->variables),$sizeRequest);
		//Read all the attributes from the request
		global $TICKET_AVAIL_RQ;
		$TICKET_AVAIL_RQ->read_set_all();
		$xml = $TICKET_AVAIL_RQ->get_xml();
		//launch the request
		global $CONFIG;
		$url = $CONFIG['url_test_http'];
		$request = new HTTPRequest($url, HTTP_METH_POST);
		$this->_common->debug("POST_body :" . $xml . "\n");
		$request->setBody($xml);
		$request->setHeaders(array("Accept" => "text/html,application/xhtml+xml,application/xml;q=0.9,;q=0.8",
									"Accept-Encoding" => "gzip, deflate",
									"Content-Type" => "text/plain; charset=UTF-8"));
		$request->send();
		$response = $request->getResponseBody();
		$this->_common->debug("ATLAS response :" . $response . "\n");
		//Create a simpleXML with the result
		$ticketAvailRS = new SimpleXMLElement ($response);
		//check Audit data
		$this->_common->checkElementsNotNull($ticketAvailRS->AuditData, array("ProcessTime", "Timestamp", "RequestHost", "ServerName", "ServerId", "SchemaRelease", "HydraCoreRelease", "HydraEnumerationsRelease", "MerlinRelease"));
		//check first serviceTicket
		$serviceTicket = $ticketAvailRS->ServiceTicket[0];
		foreach ($serviceTicket->children() as $child)
			$this->_common->debug("Child of ServiceTicket :" . $child->getName() . "\n");
		$this->_common->checkElementsNotNull($ticketAvailRS, array("DateFrom", "DateTo", "Currency", "TicketInfo", "AvailableModality", "Paxees"));
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
