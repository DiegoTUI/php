<?php

class TestMongoDB {

	private static $db;
	
	//Singleton constructor
	public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new MongoDB();
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