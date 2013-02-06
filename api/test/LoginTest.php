<?php
require 'PHPUnit/Autoload.php';
include_once 'controller/Token.php';
include_once 'util/TestCommons.php';

class LoginTest extends PHPUnit_Framework_TestCase
{
	protected $_common;

	/**
	 * @return void
	 */
	protected function setUp()
	{
		$this->_common = new TestCommons();
		ob_start();
	}

	/**
	 * Test login with a valid user.
	 * @return void
	 */
	public function testLoginValidUser()
	{
		//login with "pechodelata" user
		$_REQUEST["email"] = "dlafuente@tuitravel-ad.com";
		$_REQUEST["password"] = "dlafuente";
		
		//perform a valid login
		$token = $this->_common->validLogin();

		//clean the buffer for next test
		ob_clean();
	}

	/**
	 * Test login with an invalid email.
	 * @return void
	 */
	/*public function testLoginInValidEmail()
	{
		//login with wrong "pechodelata" user
		$_REQUEST["email"] = "pechodelata@kimmmia.es";
		$_REQUEST["password"] = "pechodelata";
		
		$this->_common->invalidLogin();
	}*/

	/**
	 * Test login with an invalid password.
	 * @return void
	 */
	/*public function testLoginInValidPassword()
	{
		//login with wrong "pechodelata" user
		$_REQUEST["email"] = "pechodelata@kimia.es";
		$_REQUEST["password"] = "pechodelatas";

		$this->_common->invalidLogin();
	}*/

	/**
	 * @return void
	 */
	protected function tearDown()
	{
		ob_end_clean();
	}
}
