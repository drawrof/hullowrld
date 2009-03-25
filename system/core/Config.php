<?php

class Config {
	
	static $instance;
	
	private $cache = array();
	private $regenerate = false;
	private $is_cached = false;
	
	function __construct()
	{
		// Set the Instance
		self::$instance = $this;
		
		// Load the cached configuration object if it exits
		if (file_exists(($_path = CACHE_DIR.'/config.cache')) && APP_ENV === 'production') {
			$this->cache = unserialize(file_get_contents($_path));
			$this->is_cached = true;
			
		// Otherwise we load the config directory and process
		} else {
			
			$_files = filesystem::read(CONFIG_DIR,array('filesystem.php'));
			foreach ($_files as $_file => $_path) {
						
				// Each file's array of configuration directives
				// is held by a single variable whose name is
				// equal to the filename minus '.php'
				$_var = strtolower(str_replace('.php','',$_file));
				
				// Process the configuration
				include $_path;
				
				if (isset($config)) {
					$this->cache[$_var] = $config;
				}
							
				unset($config);
			}
			$this->regenerate();
		}
	}

	private function &__get($property)
	{
		if (!isset($this->cache[$property])) {
			$this->cache[$property] = array();
		}
		return $this->cache[$property];	
	}
	
	private function __set($property,$value)
	{
		$this->cache[$property] = $value;
	}
	
	function __destruct()
	{								
		if ($this->regenerate === true && APP_ENV === 'production') {
			filesystem::write(CACHE_DIR.'/config.cache',serialize($this->cache));
		}
	}
	
	static function &Instance()
	{
		if (self::$instance == null) {
			new Config;
		}
		return self::$instance;
	}
	
	public function Regenerate()
	{
		if ($this->regenerate == false) {
			$this->regenerate = true;
		}
	}
	
	public function is_cached()
	{
		return $this->is_cached;
	}
}

?>