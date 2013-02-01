<?php
class Controller_Rest_Model_User extends Kimia_Commons
{
	/**
	 * @var
	 */
	public $userId;
	/**
	 * @var
	 */
	public $userName;
	/**
	 * @var
	 */
	public $email;
	/**
	 * @var
	 */
	public $passwordHash;
	/**
	 * @var
	 */
	public $salt;
	/**
	 * @var
	 */
	public $iterations;
	/**
	 * @var
	 */
	public $roleId;

	/**
	 * Create an empty user
	 */
	public function __construct()
	{
		self::debug('init');
	}

	/**
	 * Create a user and generate userId
	 */
	public function generateUser ($userName, $email, $password, $roleId)
	{
		$this->userId = Kimia_Model_Crypto::createRandom36(KIMIA_ADMIN_LENGTH);
		$this->userName = $userName;
		$this->email = $email;
		$arrayPassword = Kimia_Model_Crypto::createPasswordHash($password,10000);
		$this->passwordHash = $arrayPassword['hash'];
		$this->salt = $arrayPassword['salt'];
		$this->iterations = $arrayPassword['iterations'];
		$this->roleId = $roleId;
		self::debug('Model constructed with parameters: ' . $this->userId . ', ' . $this->userName . ', '. $this->email . ', ' . $this->roleId . '.');
	}

	/**
	 * Create with a set userId and known password
	 */
	public function generateUserWithUserId ($userId, $userName, $email, $password, $roleId)
	{
		$this->userId = $userId;
		$this->userName = $userName;
		$this->email = $email;
		$arrayPassword = Kimia_Model_Crypto::createPasswordHash($password,10000);
		$this->passwordHash = $arrayPassword['hash'];
		$this->salt = $arrayPassword['salt'];
		$this->iterations = $arrayPassword['iterations'];
		$this->roleId = $roleId;
		self::debug('Model constructed with parameters: ' . $this->userId . ', ' . $this->userName . ', '. $this->email . ', ' . $this->roleId . '.');
	}

	/**
	 * Create a user with a set userId and unknown password
	 */
	public function createFullUser ($userId, $userName, $email, $passwordHash, $salt, $iterations, $roleId)
	{
		$this->userId = $userId;
		$this->userName = $userName;
		$this->email = $email;
		$this->passwordHash = $passwordHash;
		$this->salt = $salt;
		$this->iterations = $iterations;
		$this->roleId = $roleId;
		self::debug('Model constructed with parameters: ' . $this->userId . ', ' . $this->userName . ', '. $this->email . ', ' . $this->roleId . '.');
	}
}