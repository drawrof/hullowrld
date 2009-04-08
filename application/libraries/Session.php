<?php
defined('ROOT') or die ('Restricted Access');

class SessionLibrary {
	
	static $instance = false;
	static $config = array(
		'driver' => 'native',
		'session_name' => false,
		'lifetime' => 0,
		'path' => '/',
		'domain' => false,
		'secure' => false,
		'httponly' => false,	
	);
	
	static $Driver = false;
	
	/*--------------------| Initialization & Setup Methods |--------------------*/
	function __construct()
	{	
		// Load Configuration
		self::$config =& array_merge(self::$config,Config::Instance()->session);
		
		// Set the Session name if it is specified
		if (!empty(self::$config['name'])) {
			session_name(self::$config['name']);
		}
		
		// Set the cookie parameters
		session_set_cookie_params(
			self::$config['lifetime'], 
			self::$config['path'], 
			self::$config['domain'], 
			self::$config['domain'], 
			self::$config['httponly']
		);
		
		// Start the session
		session_start();
		
		// Set a few default session paramaters
		$_SESSION['_session_id'] = session_id();
		
		// Set the full driver class name
		$driver = "Session_".inflector::camelize(self::$config['driver'])."_Driver";
	
		// Load the driver
		self::$Driver = new $driver(self::$config);
		
		// We only need to instantiate a driver for non-native session handling
		if (self::$config['driver'] !== 'native') {
				
			// Set it as the default $_SESSION save handler to the loaded driver
			session_set_save_handler( 
				array(self::$Driver,'Open'), 
				array(self::$Driver,'Close'),
				array(self::$Driver,'Read'), 
				array(self::$Driver,'Write'), 
				array(self::$Driver,'Destroy'), 
				array(self::$Driver,'GarbageCollector')  
			);
		}
		
		// Set the instance
		self::$instance =& self::$Driver;
	}
		
	static function &Instance() {
		if (empty(self::$instance)) {
			new SessionLibrary;
		}
		return self::$instance;
	}
}
