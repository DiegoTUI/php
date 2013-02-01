<?php

/**
 * Singleton Log class:
 * - contains lfile, lwrite and lclose public methods
 * - lfile sets path and name of log file
 * - lwrite writes message to the log file (and implicitly opens log file)
 * - lclose closes log file
 * - first call of lwrite method will open log file implicitly
 * - message is written with the following format: [d/M/Y:H:i:s] (script name) message
 */
class Log {

	// declare log file and file pointer as private properties
	private static $log_file = "/var/tuiinnovation/log/php.log";
    private $log_file, $fp;
	
	//Singleton constructor
	public static function getInstance()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new UserFactory();
        }
        return $inst;
    }
	
    //Private ctor so nobody else can instance it
    private function __construct()
    {

    }
	
	//destructor (closes the file)
	public function __destruct()
	{
		if (is_resource($this->fp)) {
            $this->lclose();
        }
	}
	
	//log debug trace
	public function debug ($message)
	{
		$this->lwrite("[DEBUG] " . $message);
	}
	
    // write message to the log file
    private function lwrite($message) {
        // if file pointer doesn't exist, then open log file
        if (!is_resource($this->fp)) {
            $this->lopen();
        }
        // define script name
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        // define current time and suppress E_WARNING if using the system TZ settings
        // (don't forget to set the INI setting date.timezone)
        $time = @date('[d/M/Y:H:i:s]');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time ($script_name) $message" . PHP_EOL);
    }
    // close log file (it's always a good idea to close a file when you're done with it)
    private function lclose() {
        fclose($this->fp);
    }
    // open log file (private method)
    private function lopen() {
        // in case of Windows set default log file
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $log_file_default = 'c:/php/logfile.txt';
        }
        // set default log file for Linux and other systems
        else {
            $log_file_default = '/tmp/logfile.txt';
        }
        // define log file from lfile method or use previously set default
        $lfile = $this->log_file ? $this->log_file : $log_file_default;
        // open log file for writing only and place file pointer at the end of the file
        // (if the file does not exist, try to create it)
        $this->fp = fopen($lfile, 'a') or exit("Can't open $lfile!");
    }
}

?>