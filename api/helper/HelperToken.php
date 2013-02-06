<?php

include_once 'model/ModelToken.php';
include_once 'util/UtilCrypto.php';
include_once 'util/UtilMongo.php';

class HelperToken extends UtilCommons
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
		$this->_model = new ModelToken($email, $password);
		$this->_usersCollection = UtilMongo::getInstance()->getCollection('users');
		$this->_tokensCollection = UtilMongo::getInstance()->getCollection('tokens');
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
		
		self::debug('User exists, checking password');

		//user exists, now check password
		if (!(UtilCrypto::checkPasswordHash($this->_model->password, $user['iterations'], $user['salt'], $user['passwordHash']))) //password incorrect
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
