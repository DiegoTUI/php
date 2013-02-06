<?php

include_once 'util/Commons.php';

class ModelToken extends Commons
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
	 * @var
	 */
	public $created;

	/**
	 * Create a token
	 */
	public function __construct($email, $password)
	{
		self::debug('init');
		$this->token = UtilCrypto::createRandom36(KIMIA_TOKEN_LENGTH);
		$this->email = $email;
		$this->password = $password;
		$this->created = new MongoDate();
		self::debug('Model constructed with parameters: ' . $this->token . ', ' . $this->email . '.');
	}
}

