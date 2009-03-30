<?php

class Config {
	
	static $instance;
	
	// Container for all of the configuration files
	private $cache = array();
	
	// Flag the cache for regeneration on the next request
	private $regenerate = false;
	
	// Whether the configuration came from the cache or is freshly loaded
	public $is_cached = false;
	
	// Config Directory
	public $config_dir;
	
	// Cache Directory
	public $cache_file;
	
	// App Environment

	/**
	 * Loads the configuration
	 *
	 * @return void
	 **/
	function __construct()
	{
		// Set the Instance
		self::$instance = $this;
		
		// Set a few required properties
		$this->config_dir = CONFIG_DIR;
		$this->cache_file = CACHE_DIR.'/config.cache';
		
		// Load the cached configuration object if it exits
		if ($this->cache_file && file_exists($this->cache_file) && APP_ENV === 'production') {

			$this->cache = unserialize(file_get_contents($this->cache_file));
			$this->is_cached = true;
			
		// Otherwise we load the config directory and process each file
		} else {

			// Open the directory
			if ($handle = opendir($this->config_dir)) {
				
				// This is an array of files we don't want to add to our return value
				// Mostly system files that are normally of little importance
				$disallow = array(
								'.',
								'..',
								'.DS_Store',
								'.svn',
								'Thumbs.db',
								'filesystem.php'
							);
				
				// Read the directory
			    while (false !== ($file = readdir($handle))) {
				
					// Make sure the file does not match the disallowed list
			        if (in_array($file,$disallow)) continue;
			
					// Each file's array of configuration directives
					// is held by a single variable whose name is
					// equal to the filename minus '.php'
					$key = strtolower(str_replace('.php','',$file));

					// Process the configuration
					include $this->config_dir.'/'.$file;

					// Add $config to the cache
					if (isset($config)) {
						$this->cache[$key] = $config;
						unset($config);
					}
			    }
			
				// Finished here
				closedir($handle);
				
			} else {
				throw new Error('unreadable_config_directory', array('path' => $this->config_dir));
			}
			
			// Flag to regenerate the cache
			$this->regenerate();
		}
	}

	/**
	 * Gets a property from the configuration.
	 * 
	 * @param string
	 * @return mixed
	 **/
	private function &__get($property)
	{
		if (!isset($this->cache[$property])) {
			$this->cache[$property] = array();
		}
		return $this->cache[$property];	
	}
	
	/**
	 * Sets a property in the configuration. Use 
	 * Config::Instance()->Regenerate() to cache it
	 * for future requests.
	 * 
	 * @param string
	 * @param mixed
	 * @return mixed
	 **/
	private function __set($property,$value)
	{
		$this->cache[$property] = $value;
	}
	
	/**
	 * Performs caching of configuration properties
	 *
	 * @return void
	 **/
	function __destruct()
	{								
		if ($this->regenerate === true && APP_ENV === 'production') {
			file_put_contents($this->cache_file,serialize($this->cache));
		}
	}
	
	/**
	 * Singleton access
	 *
	 * @return void
	 **/
	static function &Instance()
	{
		if (self::$instance == null) {
			new Config;
		}
		return self::$instance;
	}
	
	/**
	 * Flags cache for regeneration.
	 *
	 * @return void
	 **/
	public function Regenerate()
	{
		if ($this->regenerate == false) {
			$this->regenerate = true;
		}
	}
}

?>