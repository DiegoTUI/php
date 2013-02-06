<?php

class UtilMongoDB {

	private static $db;
	
	//Singleton constructor
	public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new self();
			$m = new Mongo();
			self::$db = $m->tuiinnovation;
        }
        return $inst;
    }
	
    //Private ctor so nobody else can instance it
    private function __construct()
    {

    }
	
	//Return a certain collection
	public function getCollection ($collection)
	{
		return self::$db->$collection;
	}
	
}
?>