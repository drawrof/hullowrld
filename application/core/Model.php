<?php defined('ROOT') or die ('Restricted Access');

class Model
{	
	// Holds all model Instances
	static $instance = array();
	
	// Holds the database Object
	var $Database = 'default';
	
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
		// Set our instance
		self::$instances[get_class($this)] = $this;
			
		// Instantiate the Database
		$this->Database = DatabaseLibrary::Instance($this->Database);
		
		// Set the first table
		$this->Database->from(inflector::tableize(get_class($this)));
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
			$path = MODEL_DIR.inflector::underscore($name).EXT;
			if (!file_exists($path)) {
				return false;
			}
		}
		
		// Ensure the name is singular and properly capitalized
		$name = inflector::classify($name);
		
		// Check the isntances
		if (isset(self::$instances[$name])) {
			return self::$instances[$name];
		}
		
		return new $name($id);
	}

}

?>