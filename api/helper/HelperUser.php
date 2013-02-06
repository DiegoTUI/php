<?php

include_once 'model/ModelUser.php';
include_once 'util/UtilAuth.php';
include_once 'util/UtilMongo.php';

class HelperUser extends Commons
{
	/**
	 * @var \Controller_Rest_Model_User
	 */
	private $_model;

	/**
	 * @var \token
	 */
	private $_token;
	
	/**
	 * @var  \collection of users
	 */
	private $_usersCollection;

	/**
	 * Constructor
	 */
	public function __construct($token)
	{
		self::debug('init');
		parent::__construct();
		$this->_token = $token;
		$this->_model = new ModelUser();
		$this->_usersCollection = UtilMongo::getInstance()->getCollection('users');
	}

	/**
	 * Extra constructor for addUser
	 */
	public function prepareAddUser($userName, $email, $password, $roleId)
	{
		$this->_model->generateUser($userName, $email, $password, $roleId);
	}

	/**
	 * Extra constructor for removeUser and readUser
	 */
	public function prepareRemoveReadUser($userId)
	{
		//search the userId in table users of MongoDB
		$user = $this->_usersCollection->findOne(array('userId' => $userId));
		self::debug('Querying users for userId: [' . $userId . ']');

		if (!$user)
		{
			throw new NotFoundException("User not found");
		}
		else
		{
			$this->_model->createFullUser ($userId, $user['userName'], $user['email'], $user['passwordHash'], $user['salt'], $user['iterations'], $user['roleId']);
		}
	}

	/**
	 * Extra constructor for modifyUser
	 */
	public function prepareModifyUser($userId, $userName, $email, $password, $roleId)
	{
		//search the userId in table users of MongoDB
		$user = $this->_usersCollection->findOne(array('userId' => $userId));
		self::debug('Querying users for userId: [' . $userId . ']');

		if (!$user)
		{
			throw new NotFoundException("User not found");
		}
		else
		{
			//create the right user
			$modifiedUserName = (($userName==null) ? $user['userName'] : $userName);
			$modifiedEmail = (($email==null) ? $user['email'] : $email);
			$modifiedRoleId = (($roleId==null) ? $user['roleId'] : $roleId);

			if ($password != null)
			{
				$this->_model->generateUserWithUserId ($userId, $modifiedUserName, $modifiedEmail, $password, $modifiedRoleId);
			}
			else
			{
				$this->_model->createFullUser ($userId, $modifiedUserName, $modifiedEmail, $user['passwordHash'], $user['salt'], $user['iterations'], $modifiedRoleId);
			}
		}
	}

	/**
	 * Create a new user reading the parameters from the message body.
	 * @return void
	 */
	public function addUser()
	{
		self::debug('init');

		//check if this user has permissions for this service
		UtilAuth::getInstance()->checkServicePermissions($this->_token, "add_user");
	
		//save user in MongoDB database, including iterations and salt
		self::debug('Inserting user: [' . $this->_model->userId . ']');
		
		try
		{
			$this->_usersCollection->insert(array('userId' => $this->_model->userId,
												'userName' => $this->_model->userName,
												'email' => $this->_model->email,
												'passwordHash' => $this->_model->passwordHash,
												'salt' => $this->_model->salt,
												'iterations' => $this->_model->iterations,
												'roleId' => $this->_model->roleId,
												'created' => $this->_model->created,
												'lastModified' => $this->_model->created));
		}
		catch (Exception $e) 
		{
			throw new TuiException("Error inserting in DB: " . $e->getMessage());
		}
		
		//return reply
		return json_encode(array('userId' => $this->_model->userId,
									'userName' => $this->_model->userName,
									'email' => $this->_model->email,
									'roleId' => $this->_model->roleId));
	}
	
	/**
	 * Remove a user reading the parameters from the message body.
	 * @return void
	 */
	public function removeUser()
	{
		self::debug('init');

		//check if this user has permissions for this service
		UtilAuth::getInstance()->checkServicePermissions($this->_token, "remove_user");

		//remove the user
		self::debug('Deleting user: [' . $this->_model->userId . ']');

		try 
		{
			$this->_usersCollection->remove(array('userId' => $this->_model->userId));
		}
		catch (Exception $e) 
		{
			throw new TuiException("Error deleting from DB");
		}

		//return reply
		return json_encode(array('userId' => $this->_model->userId,
									'userName' => $this->_model->userName,
									'email' => $this->_model->email,
									'roleId' => $this->_model->roleId));
	}

	/**
	 * Modify a user reading the parameters from the message body.
	 * @return void
	 */
	public function modifyUser()
	{
		self::debug('init');

		//check if this user has permissions for this service
		UtilAuth::getInstance()->checkServicePermissions($this->_token, "modify_user");

		//update user in mongoDB database, including iterations and salt
		try 
		{
			$this->_usersCollection->update(array('userId' => $this->_model->userId),
											array('$set' => array('userName' =>  $this->_model->userName,
																	'email' =>  $this->_model->email,
																	'passwordHash' =>  $this->_model->passwordHash,
																	'salt' =>  $this->_model->salt,
																	'iterations' =>  $this->_model->iterations,
																	'roleId' =>  $this->_model->roleId,
																	'lastModified' => $this->_model->created) ));
		}
		catch (Exception $e) 
		{
			throw new TuiException("Error updating DB: " . $e->getMessage());
		}
		
		//return reply
		return json_encode(array('userId' => $this->_model->userId,
									'userName' => $this->_model->userName,
									'email' => $this->_model->email,
									'roleId' => $this->_model->roleId));
	}

	/**
	 * Read a single user.
	 * @return json
	 */
	public function readUser()
	{
		self::debug('init');

		//check if the user has rights to read this user
		UtilAuth::getInstance()->checkServicePermissions($this->_token, "read_user");

		if (!UtilAuth::getInstance()->isAdmin($this->_token)) //is not an admin
		{
			//check that the token is owner of the userId
			UtilAuth::getInstance()->checkUserIdEqualsToken($this->_model->userId, $this->_token);
		}

		//return reply
		return json_encode(array('userId' => $this->_model->userId,
									'userName' => $this->_model->userName,
									'email' => $this->_model->email,
									'roleId' => $this->_model->roleId));
	}

	/**
	 * List all users.
	 * @return jsonarray
	 */
	public function listUsers($userName, $email, $roleId)
	{
		self::debug('init');

		//check if this user has permissions for this service
		UtilAuth::getInstance()->checkServicePermissions($this->_token, "list_users");

		//list all the users whose name includes $userName from the database
		$users = $this->_usersCollection->find(array('userName' => new MongoRegex('/' . $userName . '/'),
												'email' => new MongoRegex('/' . $email . '/'),
												'roleId' => new MongoRegex('/' . $roleId . '/')));

		self::debug('Querying users for *: [' . $sql . ']');
		
		$array = array ();
		foreach ($users as $user) 
		{
				array_push($array, $user);
		}

		return json_encode($array);
	}
}
