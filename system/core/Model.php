<?php

class Model
{
	// This holds instances of each model
	static $instances;
	
	// Holds the database Object
	var $Db = 'default';
	
	// Whether the default Model has been loaded
	static $loaded_default = false;
	
	function __construct()
	{
		$name = App::beautify(get_class($this));
		
		// Pass the Model to the controller
		self::$instances[$name] = $this;
		
		// Add the model to the controller
		Controller::Instance()->$name = $this;
		
		// Instantiate the Database
		$this->Db = DatabaseLibrary::Instance($this->Db);
		
		// Set the first table
		$this->Db->from(App::uglify(get_class($this),'pluralize'));
	}
	
	static function Factory($name = false,$id = null)
	{
		// Set a default Model. It needs to be manually included,
		// Since the autoload will throw an error if it doesn't
		// exist in the filesystem.
		if ($name === false && self::$loaded_default == false) {
			
			$Router = Router::Instance();
			$name = $Router->params['Model'];
			$path = App::check_path($Router->params['model'],MODEL_DIR);
			
			if (file_exists($path)) {
				include_once $path;
			} else {
				return;
			}
		}
		
		$name = App::beautify($name,'singularize');
		
		if (!isset(self::$instances[$name])) {
			new $name($id);
		}
						
		return self::$instances[$name];
	}

}

?>