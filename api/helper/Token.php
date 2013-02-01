<?php

class Controller_Rest_Helper_Token extends Kimia_Profile_Mixed
{
	/**
	 * @var \Controller_Rest_Model_Token
	 */
	private $_model;

	/**
	 * Constructor
	 */
	public function __construct($email, $password)
	{
		self::debug('init');
		parent::__construct();
		$this->_model = new Controller_Rest_Model_Token($email, $password);
	}

	/**
	 * Create a new token reading the parameters from the message body.
	 * @return void
	 */
	public function login()
	{
		self::debug('init');

		//search the email in table users of mySQL
		$sql = "select * from users where email ='" . $this->_model->email . "'";
		self::debug('Querying users for email: [' . $sql . ']');
		$row = $this->_database->getInstance()->_db->fetchOneRow($sql);

		if (!$row)	//user is not in the database
		{
			throw new UnauthorizedException("Invalid email or password");
		}

		//user exists, now check password
		if (!(Kimia_Model_Crypto::checkPasswordHash($this->_model->password, $row->iterations, $row->salt, $row->passwordHash))) //password incorrect
		{
			throw new UnauthorizedException("Invalid email or password");
		}

		//user exists and password correct. Save token in DB
		$sql = "insert into tokens(token, userid) value('" .
			   $this->_model->token . "','" .
			   $row->userId . "')";

		self::debug('Inserting token: [' . $sql . ']');

		try 
		{
			$this->_database->getInstance()->_db->insert($sql);
		}
		catch (Exception $e) 
		{
    		throw new KimiaException("Error inserting in DB: " . $e->getMessage());
		}

		//Return successful login reply
		return json_encode(array('userId' => $row->userId,
									'userName' => $row->userName,
									'email' => $row->email,
									'roleId' => $row->roleId,
									'token' => $this->_model->token));
	}

	/**
	 * Generates a code and sends an email with a passwrod reminder
	 * @return void
	 */
	public function sendPasswordReminder()
	{
		self::debug('init');

		//search the email in table users of mySQL
		$sql = "select * from users where email ='" . $this->_model->email . "'";
		self::debug('Querying users for email: [' . $sql . ']');
		$row = $this->_database->getInstance()->_db->fetchOneRow($sql);

		if (!$row)	//user is not in the database
		{
			throw new NotFoundException("There is no user registered with this email");
		}

		//user exists. Send reminder.
		$from = "no-reply@kimia.mobi";
		$nameFrom = "Kimia App Traking";
		$to = $this->_model->email;
		$subject = "[KimiaAppTracking] Password Reset";
		$link = "http://apptracking.kimia.es/admin/resetpassword.html?code=" . $this->_model->token . "&email=" . urlencode($to);
		$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<title>Untitled Document</title>
				</head>

				<body>
				<p>Hi,</p>
				<p>We have received a password reset request for this KimiaAppTracking account. Please click on the following link to reset your password:</p>
				<p><a href=' . $link . '>' . $link . '</a></p>
				<p><strong>Warning</strong>: this link will be valid for one day. If you didn\'t request a password reset just ignore this message.</p>
				<p>Best,</p>
				<p>Kimia App Tracking.</p>
				</body>
				</html>';

		$this->authSendEmail($from, $nameFrom, $to, $subject, $body);

		//save code and email in Redis
		$codeId = Kimia_Util_Redis::generatePasswordRecoveryKey($this->_model->token);
		$params = array(REDIS_FIELD_USER_ID => $row->userId,
						REDIS_FIELD_EMAIL => $this->_model->email,
						REDIS_FIELD_TIMESTAMP => time());
		$this->_redis->hmset(Kimia_Redis::WRITE, $codeId, $params);

		//return code
		return $this->_model->token;
	}

	/**
	 * Generates a code and sends an email with a passwrod reminder
	 * @return void
	 */
	public function resetPassword($code)
	{
		self::debug('init');

		$codeId = Kimia_Util_Redis::generatePasswordRecoveryKey($code);

		if (!$this->_redis->exists(Kimia_Redis::READ, $codeId))
		{
			throw new UnauthorizedException("Invalid code provided");
		}

		$record = $this->_redis->hgetall(Kimia_Redis::READ, $codeId);
		if (time() > ($record[REDIS_FIELD_TIMESTAMP] + 86400))
		{
			throw new UnauthorizedException("Expired code provided");
		}
		if (!equals($this->_model->email, $record[REDIS_FIELD_EMAIL]))
		{
			throw new UnauthorizedException("Invalid email provided");
		}

		//search the userId in table users of mySQL
		$sql = "select * from users where userId ='" . $record[REDIS_FIELD_USER_ID] . "'";
		self::debug('Querying users for userId: [' . $sql . ']');
		$row = $this->_database->getInstance()->_db->fetchOneRow($sql);

		if (!$row)
		{
			throw new NotFoundException("User not found");
		}

		$user = new Controller_Rest_Model_User();
		$user->generateUserWithUserId ($row->userId, $row->userName, $row->email, $this->_model->password, $row->roleId);
		$sql = "update users set passwordHash='" . $user->passwordHash .
									"', salt='" . $user->salt .
									"', iterations='" . $user->iterations .
									"' where userId='" . $row->userId . "'";

		self::debug('Updating user: [' . $sql . ']');

		try 
		{
			$this->_database->getInstance()->_db->update($sql);
		}
		catch (Exception $e) 
		{
			throw new KimiaException("Error updating DB: " . $e->getMessage());
		}

		return $this->login();
	}

	private function authSendEmail($from, $namefrom, $to, $subject, $message)  
	{  
		//SMTP + SERVER DETAILS  
		/* * * * CONFIGURATION START * * * */ 
		$smtpServer = "smtp.kimiasol.com";  
		$port = "587";  
		$timeout = "30";  
		$username = "apptest@kimiasol.com";  
		$password = "kimiapp";  
		$localhost = "smtp.kimiasol.com";  
		$newLine = "\r\n";  
		/* * * * CONFIGURATION END * * * * */ 
		 
		//Connect to the host on the specified port  
		$smtpConnect = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);  
		$smtpResponse = fgets($smtpConnect, 515);  
		if(empty($smtpConnect))   
		{  
			throw new KimiaException("Error while sending email: " . $smtpResponse); 
		}  
		 
		//Request Auth Login  
		fputs($smtpConnect,"AUTH LOGIN" . $newLine);  
		$smtpResponse = fgets($smtpConnect, 515);  
		$logArray['authrequest'] = "$smtpResponse";  
		 
		//Send username  
		fputs($smtpConnect, base64_encode($username) . $newLine);  
		$smtpResponse = fgets($smtpConnect, 515);  
		$logArray['authusername'] = "$smtpResponse";  
		 
		//Send password  
		fputs($smtpConnect, base64_encode($password) . $newLine);  
		$smtpResponse = fgets($smtpConnect, 515);  
		$logArray['authpassword'] = "$smtpResponse";  
		 
		//Say Hello to SMTP  
		fputs($smtpConnect, "HELO $localhost" . $newLine);  
		$smtpResponse = fgets($smtpConnect, 515);  
		$logArray['heloresponse'] = "$smtpResponse";  
		 
		//Email From  
		fputs($smtpConnect, "MAIL FROM: $from" . $newLine);  
		$smtpResponse = fgets($smtpConnect, 515);  
		$logArray['mailfromresponse'] = "$smtpResponse";  
		 
		//Email To  
		fputs($smtpConnect, "RCPT TO: $to" . $newLine);  
		$smtpResponse = fgets($smtpConnect, 515);  
		$logArray['mailtoresponse'] = "$smtpResponse";  
		 
		//The Email  
		fputs($smtpConnect, "DATA" . $newLine);  
		$smtpResponse = fgets($smtpConnect, 515);  
		$logArray['data1response'] = "$smtpResponse";  
		 
		//Construct Headers  
		$headers = "MIME-Version: 1.0" . $newLine;  
		$headers .= "Content-type: text/html; charset=UTF-8" . $newLine;  
		//$headers .= "To: $to" . $newLine;  
		//$headers .= "From: $namefrom <$from>" . $newLine;  
		 
		fputs($smtpConnect, "To: $to\nFrom: $from\nSubject: $subject\n$headers\n\n$message\n.\n");  
		$smtpResponse = fgets($smtpConnect, 515);  
		$logArray['data2response'] = "$smtpResponse";  
		 
		// Say Bye to SMTP  
		fputs($smtpConnect,"QUIT" . $newLine);   
		$smtpResponse = fgets($smtpConnect, 515);  
		$logArray['quitresponse'] = "$smtpResponse";

		self::debug('SMTP logArray: ' . json_encode($logArray));
	}
}
