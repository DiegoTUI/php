<?php

include_once 'model/Token.php';
include_once 'util/Crypto.php';
include_once 'util/MongoDB.php';

class HelperToken extends Commons
{
	/**
	 * @var \ModelToken
	 */
	private $_model;
	
	/**
	 * @var  \collection of users
	 */
	private $_usersCollection;
	
	/**
	 * @var  \collection of tokens
	 */
	private $_tokensCollection;

	/**
	 * Constructor
	 */
	public function __construct($email, $password)
	{
		self::debug('init');
		parent::__construct();
		$this->_model = new ModelToken($email, $password);
		$this->_usersCollection = UtilMongoDB::getInstance()->getCollection('users');
		$this->_tokensCollection = UtilMongoDB::getInstance()->getCollection('tokens');
	}

	/**
	 * Create a new token reading the parameters from the message body.
	 * @return void
	 */
	public function login()
	{
		self::debug('init');

		//search the email in table users of MongoDB
		$user = $this->_usersCollection->findOne(array("email" => $this->_model->email));
		self::debug('Querying users for email: [' . $this->_model->email . ']');

		if (!$user)	//user is not in the database
		{
			throw new UnauthorizedException("Invalid email or password");
		}

		//user exists, now check password
		if (!(UtilCrypto::checkPasswordHash($this->_model->password, $row->iterations, $row->salt, $row->passwordHash))) //password incorrect
		{
			throw new UnauthorizedException("Invalid email or password");
		}

		//user exists and password correct. Save token in DB
		self::debug('Inserting token: [' . $this->_model->token . ']');
		try 
		{
			$this->_tokensCollection->insert(array("token" => $this->_model->token,
													"userId" => $user['userId'],
													"created" => $this->_model->created));
		}
		catch (Exception $e) 
		{
    		throw new TuiException("Error inserting in DB: " . $e->getMessage());
		}

		//Return successful login reply
		return json_encode(array('userId' => $user['userId'],
									'userName' => $user['userName'],
									'email' => $user['email'],
									'roleId' => $user['roleId'],
									'token' => $this->_model->token));
	}

}
