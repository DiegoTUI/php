<?php

class Controller_Rest_Model_Token extends Kimia_Commons
{
	/**
	 * @var
	 */
	public $token;
	/**
	 * @var
	 */
	public $email;
	/**
	 * @var
	 */
	public $password;

	/**
	 * Create a token
	 */
	public function __construct($email, $password)
	{
		self::debug('init');
		$this->token = Kimia_Model_Crypto::createRandom36(KIMIA_TOKEN_LENGTH);
		$this->email = $email;
		$this->password = $password;
		self::debug('Model constructed with parameters: ' . $this->token . ', ' . $this->email . '.');
	}
}

