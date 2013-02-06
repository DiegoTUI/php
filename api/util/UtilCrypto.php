<?php

class UtilCrypto
{
	/**
	 * Create a random binary string with the given length in bytes.
	 * @param $length of the resulting string (in bytes).
	 */
	public static function createRandomBytes($length)
	{
		return openssl_random_pseudo_bytes($length);
	}

	/**
	 * Create a random hex number with the given length.
	 * @param $length of the resulting string (not in bytes).
	 */
	public static function createRandomHex($length)
	{
		$bin = self::createRandomBytes($length / 2 + 1);
		return substr(bin2hex($bin), 0, $length);
	}

	/**
	 * Create a random number with the given bytes, return as base 64.
	 * @param $length of the resulting string in bytes.
	 */
	public static function createRandom64($length)
	{
		return base64_encode(self::createRandomBytes($length));
	}

	/**
	 * Create a random identifier in base 36, with the given length.
	 * @param $length of the resulting string (not in bytes).
	 */
	public static function createRandom36($length)
	{
		$result = '';
		while ($length > 8)
		{
			$result .= self::createRandom36Piece(8);
			$length -= 8;
		}
		return $result . self::createRandom36Piece($length);
	}

	/**
	 * Create a piece of a random base 36 identifier, 8 characters max.
	 */
	private static function createRandom36Piece($length)
	{
		if ($length > 8)
		{
			throw new KimiaException('Cannot generate base 36 piece > 8');
		}
		$hex = self::createRandomHex($length * 1.40 + 1);
		$full = substr(base_convert($hex, 16, 36), -$length);
		return str_pad($full, $length, '0', STR_PAD_LEFT);
	}

	/**
	 * Compute a salted stretched password hash.
	 * The password is salted and stretched for a number of iterations.
	 * Returns the computed hash.
	 */
	public static function computePasswordHash($password, $salt, $iterations)
	{
		$hash = hash('sha256', $password . '+' . $salt);
		for ($i = 0; $i < $iterations; $i++)
		{
			$hash = hash('sha256', $hash . '+' . $password . '+' . $salt);
		}
		return $hash;
	}

	/**
	 * Create a new password hash. Chooses a random salt, and uses the default
	 * number of iterations.
	 * Returns a hash object that stores: hash, salt and iterations.
	 */
	public static function createPasswordHash($password, $iterations)
	{
		$salt = self::createRandomHex(32);
		$hash = self::computePasswordHash($password, $salt, $iterations);
		return array(
			'hash' => $hash,
			'salt' => $salt,
			'iterations' => $iterations,
		);
	}

	/**
	 * Check a hash object: redo the hash computations and see if it matches.
	 * Returns true or false.
	 */
	public static function checkPasswordHash($password, $iterations, $salt, $hash)
	{
		$hashCalculated = self::computePasswordHash($password, $salt, $iterations);
		return ($hashCalculated === $hash);
	}

        /**
         * Encrypt the given value with the key, return as base64.
	 * @param key64 the key in base64.
         */
	public static function encrypt64($value, $key64)
        {
		$key = base64_decode($key64);
                $size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, 'ctr');
                $iv = mcrypt_create_iv($size, MCRYPT_DEV_URANDOM);
                $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $value, 'ctr', $iv);
                return base64_encode($iv . $encrypted);
        }

        /**
         * Decrypt the given value as base64 with the key, also encoded as base64.
         */
	public static function decrypt64($value64, $key64)
        {
		$decoded = base64_decode($value64);
		$key = base64_decode($key64);
		$iv = substr($decoded, 0, 16);
		$encrypted = substr($decoded, 16);
		return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $encrypted, 'ctr', $iv);
        }
}

