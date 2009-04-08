<?php

class View 
{	
	// Headers for particular extensions
	static $content_types = array(
		'html'	=> 	'text/html',
		'js'	=> 	'application/javascript',
		'css'	=> 	'text/css',
		'xml'	=> 	'text/xml',
		'json'	=>	'application/json',
		'zip'	=> 	'application/zip',
		'jpg'	=> 	'image/jpeg',
		'png'	=> 	'image/png',
		'gif'	=> 	'image/gif'
	);
	
	// Format all views will be rendered as
	static public $format = null;
	
	// Global data from the controller
	static $global_data = array();
	
	// Data for each instance
	public $data = array();
	
	// Filename
	public $file;
	
	// Final path to be rendered
	public $path = null;
	
	// Final Rendered View
	public $view = null;

	/**
	 * Sets up a view to be processed and rendered. Gathers full path information
	 * and data to be used by the view.
	 * 
	 * @param string
	 * @param array
	 *
	 **/
	function __construct($file = null,$data = array())
	{												
		// Emulate an absolute/relative path system, with VIEW_DIR being the root;
		// If there is a leading slash, VIEW_DIR is the working directory.
		if ($file[0] === '/') {
			$file = trim($file,'/');
			
		// Otherwise, VIEW_DIR/current_controller/ will be the working directory.
		} else {
			$file = Router::Instance()->params['controller'].'/'.$file;
		}
		
		// Set a few variables. I just like the way comments break up code.
		$this->file = $file;
		$this->data = $data;
	}
	
	/**
	 * Gets a property from the data.
	 * 
	 * @param string
	 * @return mixed
	 **/
	private function &__get($property)
	{
		if (!isset($this->data[$property])) {
			$this->data[$property] = array();
		}
		return $this->data[$property];	
	}
	
	/**
	 * Sets a property in the View data.
	 * 
	 * @param string
	 * @param mixed
	 * @return mixed
	 **/
	private function __set($property,$value)
	{
		if (substr($property,0,7) === 'global_') {
			self::$global_data[substr($property,7)] = $value;
		} else {
			$this->data[$property] = $value;	
		}
		
	}
	
	/**
	 * Allows views to be rendered as an object
	 *
	 * @return string
	 **/
	public function __toString()
	{
		// Ensure the view has been processed
		if ($this->view === null) {
			$this->process();
		}
		
		// Return the data
		return $this->view;
	}
	
	/**
	 * Initializes a few global items.
	 *
	 * @return void
	 **/
	static function send_headers($format)
	{
		// All views will use this format for rendering views
		self::$format = $format;
		
		// Set the headers
		if (isset(self::$content_types[self::$format])) {
			header('Content-type: '.self::$content_types[self::$format]);
		} else {
			// Throw an error if the view is missing
			throw new Error('unsupported_format', array('format' => self::$format));
		}
	}
	
	public function locate_file($file,$format)
	{
		// Add the file extension?
		if (FALSE === strrpos($file,'.')) {
			$file = $file.'.'.$format.EXT;
		}

		// Create the full path
		$path = VIEW_DIR.'/'.$file;
		
		// Ensure it exists
		if (!file_exists($path)) {
			// Throw an error if the view is missing
			throw new Error('missing_view', array('view_path' => $path));
		}
		
		return $path;
	}
		
	/**
	 * Renders a view.
	 *
	 * @return string
	 **/
	public function process()
	{		
		// Make sure there is a path to work with
		if ($this->path == null) { 
			$this->path = $this->locate_file($this->file,self::$format);
		}
		
		// Merge local and global data
		$this->data = array_merge((array)self::$global_data,$this->data);
		
		// Extract the data.	
		if (is_array($this->data)) {
			extract($this->data,EXTR_SKIP);
		}

		// Buffer the output
		ob_start();
		include $this->path;
		$this->view = ob_get_clean();
		
		// Return the string
		return $this->view;
	}
	
	/**
	 * Factory for rendering Views. If no arguments are passed to it, it renders
	 * a file based on the name of the action for this request. Note that this, and all of
	 * the static render* functions return a string, so that the view must be explicitly sent
	 * to the browser, however, you can also process it further if you wish.
	 * 
	 * @param string
	 * @param array
	 *
	 * @return string
	 **/
	static function render($file = null,$data = array())
	{
		// Instantiate the view
		$view = new View($file,$data);

		// Render it immediately
		return $view->process();
	}
	
	/**
	 * Factory for rendering Partials. Essentially, it is an alias
	 * of View::render, except it adds an underscore to the filename
	 * ala Rails.
	 * 
	 * @param string
	 * @param array
	 *
	 * @return string
	 **/
	static function render_partial($file = null,$data = array())
	{		
		// Instantiate the Partial
		$partial = new Partial($file,$data);

		// Render it immediately
		return $partial->process();
	}
	
	/**
	 * Factory for rendering Collections. Loops through each item in the data
	 * Rendering a partial for each one and returning the concatenated values of all of them.
	 * It makes five variables available to each item (plus global variables), where 
	 * "filename" is equal to the name of the file: 
	 * 
	 * $filename = the current array value
	 * $filename_key = the current array key
	 * $filename_counter = a zero-based index of the array
	 * $first_filename = true if it's the first item in the array
	 * $last_filename = true if it's the last item in the array
	 * 
	 * Note that $filename is singularized. For example, with this call:
	 * 
	 * View::render_collection('posts',array('first','second'));
	 * 
	 * The variables made available to the view are as follows:
	 * $post, $post_key, $post_counter, $first_post, $last_post
 	 * 
	 * @param string
	 * @param array
	 *
	 * @return string
	 **/
	static function render_collection($file = null,$data = array())
	{
		// Instantiate the Partial
		$collection = new Collection($file,$data);

		// Render it immediately
		return $collection->process();
	}
}

class Layout extends View
{
	/**
	 * Sets up a partial to be processed and rendered.
	 * 
	 * @param string
	 * @param array
	 *
	 **/
	function __construct($file = null,$data = array())
	{														
		if (substr($file,0,9) != '/layouts/') {
			$file = '/layouts/'.trim($file,'/');
		}

		parent::__construct($file,$data);
	}
}

class Partial extends View
{
	/**
	 * Sets up a partial to be processed and rendered.
	 * 
	 * @param string
	 * @param array
	 *
	 **/
	function __construct($file = null,$data = array())
	{														
		// Ensure there is an underscore in the filename
		$filename = basename($file);
		
		if ($file != null && substr($filename,0,1) !== '_') {
			
			$filename = '_'.$filename;
			$directory = dirname($file);

			// Get rid of dirname's '.' return value when there is no dirname
			$directory = ($directory == '.') ? '' : $directory.'/';
			
			$file = $directory.$filename;
		}
		
		parent::__construct($file,$data);
	}
}

class Collection extends Partial
{
	// Counter
	public $i = 0;
	
	// Total number of items
	public $total = 0;
	
	// Variable names passed to each collection
	public $name = null;
	public $key = null;
	public $counter = null;
	public $first = null;
	public $last = null;
	
	// Full Collection Data
	public $collection_data = array();
	
	function __construct($file = null,$data = array()) 
	{			
		// Determine the variable name that is passed to the partial
		$this->name = basename($file);
		$this->name = inflector::underscore(ltrim($this->name,'_'));

		// Total number of items - 1, since $i is zero-based
		$this->total = count($data) - 1;

		// A few array keys
		$this->key = $this->name.'_key';
		$this->counter = $this->name.'_counter';
		$this->first = 'first_'.$this->name;
		$this->last = 'last_'.$this->name;
		
		// Save the collection data
		$this->collection_data = $data;
		
		// Good to go. Don't pass on the data because it doesn't need to be.
		parent::__construct($file);
		
	}
	
	public function process()
	{
		// Final rendered content is stored here
		$collection = '';
		
		// Loop through each item in the array, rendering a partial and 
		// passing some data to the file
		foreach ($this->collection_data as $key => $value) {

			// Data to be passed
			$data = array(
				$this->first 	=> ($this->i === 0) ? true : false,
				$this->name 	=> $value,
				$this->key 		=> $key,
				$this->counter	=> $this->i,
				$this->last		=> ($this->i === $this->total) ? true : false,
			);

			// Reset the data
			$this->data = $data;

			// Increment the counter
			$this->i++;
			
			// Call the parent processing function
			$collection .= parent::process();
		}
		
		$this->view = $collection;
		return $collection;
	}
}


?>