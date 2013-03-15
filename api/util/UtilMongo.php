<?php

class UtilMongo {

	private static $db;
	
	/**
	 * Singleton instance for this class.
	 */
	private static $_instance;
	
	//Singleton constructor
	public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
			$m = new Mongo();
			self::$db = $m->tuiinnovation;
        }
        return self::$_instance;
    }
	
    //Private ctor so nobody else can instance it
    private function __construct()
    {

    }
	
	//Return current database
	public function getDb ()
	{
		return self::$db;
	}
	
	//Return a certain collection
	public function getCollection ($collection)
	{
		return self::$db->$collection;
	}
	
	//Return last error
	public function getLastError()
	{
		$arrayError = self::$db->lastError();
		return $arrayError['err'];
	}
	
}
?>