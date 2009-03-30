<?php

class Model
{	
	// Holds the database Object
	var $database = 'default';
	
	// Saves the id if it was instantiated with it
	var $id = null;
	
	/**
	 * Prepares the Database for use by the model
	 *
	 * @param int
	 * @return void
	 **/
	function __construct($id = null)
	{		
		// Instantiate the Database
		$this->Db = DatabaseLibrary::Instance($this->Db);
		
		// Set the first table
		$this->Db->from(App::uglify(get_class($this),'pluralize'));
	}
	
	/**
	 * Creates and returns a new Model
	 *
	 * @param string
	 * @param int
	 * @param bool
	 * @return object
	 **/
	static function Factory($name = false,$id = null, $check_existence = false)
	{
		// The controller will set this flag so that the autoload doesn't throw
		// an exception when it attempts to load a non-existent model. 
		// In essence, Models are optional.
		if ($check_existence) {
			
			// Check to see if it exists, and return if it doesn't
			$path = App::check_path(App::uglify($name),MODEL_DIR);
			if (!file_exists($path)) {
				return false;
			}
		}
		
		return new $name($id);
	}

}

?>