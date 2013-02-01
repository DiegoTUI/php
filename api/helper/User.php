<?php
class Controller_Rest_Helper_User extends Kimia_Profile_Database
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
	 * Constructor
	 */
	public function __construct($token)
	{
		self::debug('init');
		parent::__construct();
		$this->_token = $token;
		$this->_model = new Controller_Rest_Model_User();
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
		//search the userId in table users of mySQL
		$sql = "select * from users where userId ='" . $userId . "'";
		self::debug('Querying users for userId: [' . $sql . ']');
		$row = $this->_database->getInstance()->_db->fetchOneRow($sql);

		if (!$row)
		{
			throw new NotFoundException("User not found");
		}
		else
		{
			$this->_model->createFullUser ($userId, $row->userName, $row->email, $row->passwordHash, $row->salt, $row->iterations, $row->roleId);
		}
	}

	/**
	 * Extra constructor for modifyUser
	 */
	public function prepareModifyUser($userId, $userName, $email, $password, $roleId)
	{
		//search the userId in table users of mySQL
		$sql = "select * from users where userId ='" . $userId . "'";
		self::debug('Querying users for userId: [' . $sql . ']');
		$row = $this->_database->getInstance()->_db->fetchOneRow($sql);

		if (!$row)
		{
			throw new NotFoundException("User not found");
		}
		else
		{
			//create the right user
			$modifiedUserName = (($userName==null) ? $row->userName : $userName);
			$modifiedEmail = (($email==null) ? $row->email : $email);
			$modifiedRoleId = (($roleId==null) ? $row->roleId : $roleId);

			if ($password != null)
			{
				$this->_model->generateUserWithUserId ($userId, $modifiedUserName, $modifiedEmail, $password, $modifiedRoleId);
			}
			else
			{
				$this->_model->createFullUser ($userId, $modifiedUserName, $modifiedEmail, $row->passwordHash, $row->salt, $row->iterations, $modifiedRoleId);
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
		Kimia_Service_Auth::getInstance()->checkServicePermissions($this->_token, "add_user");
	
		//save user in mySQL database, including iterations and salt
		$sql = "insert into users(userId, userName, email, passwordHash, salt, iterations, roleId) value('" .
			   $this->_model->userId . "','" .
			   $this->_model->userName . "','" .
			   $this->_model->email . "','" .
			   $this->_model->passwordHash . "','" .
			   $this->_model->salt . "','" .
			   $this->_model->iterations . "','" .
			   $this->_model->roleId . "')";

		self::debug('Inserting user: [' . $sql . ']');

		try 
		{
			$this->_database->getInstance()->_db->insert($sql);
		}
		catch (Exception $e) 
		{
			throw new KimiaException("Error inserting in DB: " . $e->getMessage());
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
		Kimia_Service_Auth::getInstance()->checkServicePermissions($this->_token, "remove_user");

		//remove the user
		$sql = "delete from users where userId = '" . $this->_model->userId . "'";

		self::debug('Deleting user: [' . $sql . ']');

		try 
		{
			$this->_database->getInstance()->_db->delete($sql);
		}
		catch (Exception $e) 
		{
			throw new KimiaException("Error deleting from DB");
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
		Kimia_Service_Auth::getInstance()->checkServicePermissions($this->_token, "modify_user");

		//update user in mySQL database, including iterations and salt
		$sql = "update users set userName='" . $this->_model->userName .
									"', email='" . $this->_model->email .
									"', passwordHash='" . $this->_model->passwordHash .
									"', salt='" . $this->_model->salt .
									"', iterations='" . $this->_model->iterations .
									"', roleId='" . $this->_model->roleId .
									"' where userId='" . $this->_model->userId . "'";

		self::debug('Updating user: [' . $sql . ']');

		try 
		{
			$this->_database->getInstance()->_db->update($sql);
		}
		catch (Exception $e) 
		{
			throw new KimiaException("Error updating DB: " . $e->getMessage());
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
		Kimia_Service_Auth::getInstance()->checkServicePermissions($this->_token, "read_user");

		if (!Kimia_Service_Auth::getInstance()->isAdmin($this->_token)) //is not an admin
		{
			//check that the token is owner of the userId
			Kimia_Service_Auth::getInstance()->checkUserIdEqualsToken($this->_model->userId, $this->_token);
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
		Kimia_Service_Auth::getInstance()->checkServicePermissions($this->_token, "list_users");

		//list all the users whose name includes $userName from the database
		$sql = "select userId,userName,email,roleId from users where userName like '%" . $userName . 
									"%' and email like '%" . $email .
									"%' and roleId like '%" . $roleId. "%'";

		self::debug('Querying users for *: [' . $sql . ']');

		$array = $this->_database->getInstance()->_db->fetchAll($sql);

		return json_encode($array);
	}
}
