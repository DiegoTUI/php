<?php

include_once 'UtilCommons.php';
include_once 'UtilMongo.php';

class UtilAuth extends UtilCommons
{
	/**
	 * Singleton instance for this class.
	 */
	private static $_instance;
	
	/**
	 * @var  \collection of users
	 */
	private static $_usersCollection;
	
	/**
	 * @var  \collection of roles
	 */
	private static $_rolesCollection;
	
	/**
	 * @var  \collection of tokens
	 */
	private static $_tokensCollection;

	/**
	 *
	 * Method that creates a unique instance of the class.
	 *
	 * @return UtilAuth
	 */
	public static function getInstance()
	{
		if (self::$_instance == NULL) 
		{
			self::$_instance = new self();
			self::$_usersCollection = UtilMongo::getInstance()->getCollection('users');
			self::$_rolesCollection = UtilMongo::getInstance()->getCollection('roles');
			self::$_tokensCollection = UtilMongo::getInstance()->getCollection('tokens');
		}

		return self::$_instance;
	}

	/**
	 * checks if a certain user is allowed to use a service.
	 * @param string $token
	 * @param string $serviceName
	 * @throws unauthorizedException
	 */
	public function checkServicePermissions($token, $serviceName)
	{
		self::debug('init');
		if ($this->getValidPermission($token, $serviceName))
		{
			return;
		}
		throw new UnauthorizedException("Invalid token");
	}

	/**
	 * Return true only if there is a valid permission, false otherwise.
	 */
	private function getValidPermission($token, $serviceName)
	{
		$userId = $this->getUserIdFromToken($token);
		if (!$userId)
		{
			return false;
		}
		//get the role for the user
		$user = self::$_usersCollection->findOne(array('userId' => $userId));
		self::debug('Querying users for userId: [' . $userId . ']');
		if (!$user)
		{
			return false;
		}
		//now check if the roleId retrieved has permissions to use the service
		$role = self::$_rolesCollection->findOne(array('roleId' => $user['roleId']));
		self::debug('Querying roles for roleId: [' . $user['roleId'] . ']');
		if (!$role)
		{
			return false;
		}
		foreach ($role['authServices'] as $authService)
		{
			if (equals($authService, $serviceName))
				return true;
		}
		
		return false;
	}

	/**
	 * checks if a certain token is admin.
	 * @param string $token
	 * @return bool
	 */
	public function isAdmin ($token)
	{
		self::debug('init');

		$userId = $this->getUserIdFromToken($token);

		if ($userId)	//token is in the database
		{
			//retrieve the roleId from the database
			$user = self::$_usersCollection->findOne(array('userId' => $userId));
			
			self::debug('Querying users for userId: [' . $userId . ']');

			if ($user)  //we got a match
			{
				if (equals("admin", $user['roleId']))
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * retrieves the userId for a certain token.
	 * @param string $token
	 * @return userId. Null if not found.
	 */
	public function getUserIdFromToken($token)
	{
		//search the token in table tokens of MongoDB
		$tokenObject = self::$_tokensCollection->findOne(array('token' => $token));
		if (!$tokenObject)	//token is not in the database
		{
			return null;
		}
		return $tokenObject['userId'];
	}

	/**
	 * Checks request for a certain key. Throws exception if it doesn't find it.
	 * @param string $key
	 * @throws TuiException if not found.
	 */
	public function checkRequestKey($key)
	{
		if (!isset($_REQUEST[$key]))
		{
			throw new TuiException("Key " . $key . " not provided in request");
		}
	}

	/**
	 * Check that the given key exists, and return it.
	 */
	public function getRequestKey($key)
	{
		$this->checkRequestkey($key);
		return $_REQUEST[$key];
	}

	/**
	 * checks that a certain userId corresponds to a certain token
	 * @param string $userId
	 * @param string $token
	 * @throws KimiaException if not found.
	 */
	public function checkUserIdEqualsToken($userId, $token) 
	{
		$retrievedUserId = $this->getUserIdFromToken($token);
		if (!equals($retrievedUserId, $userId)) //is not the same user
		{
				throw new UnauthorizedException("Unauthorized user");
		}
	}
}

