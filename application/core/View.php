<?php

class View 
{	
	// Headers for particular extensions
	static $content_type = array(
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
	
	// Data for each instance
	private $data = array();
	
	// File to be rendered
	private $file;
	
	// Location of the file
	private $path;
	
	// File type
	private $format;
	
	// Final Rendered View
	private $view = null;

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
		
		$this->file = $file;
		$this->format = Router::Instance()->params['format'];
		$this->path = VIEW_DIR.'/'.$this->file.'.'.$this->format.'.php';
		$this->data = array_merge((array)Controller::$data['global'],(array)$data);
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
		$this->data[$property] = $value;
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
	 * Renders a view.
	 *
	 * @return string
	 **/
	public function process()
	{				
		if (file_exists($this->path) && !empty(self::$content_type[$this->format])) {
			// Extract the data. A manual extract, using foreach,
			// while capable of much more detailed error-checking
			// is significantly slower than extract()	
			if (is_array($this->data)) {
				extract($this->data,EXTR_SKIP);
			}
			
			// Set the headers
			header('Content-type: '.self::$content_type[$this->format]);

			// Buffer the output
			ob_start();
			include $this->path;
			$this->view = ob_get_clean();
			
			// Return the string
			return $this->view;
			
		} else {
			
			// Throw an error if the view is missing
			throw new Error('missing_view', array('view_path' => $this->path));
		}
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
		// If the file is null, we'll assume it's a call for the
		// default view to be rendered
		if ($file == null) {
			$file = Router::Instance()->params['action'];
			$data = Controller::$data['local'];
		}

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
		// Add an underscore to the partial name if necessary
		$filename = basename($file);
		$directory = dirname($file);

		// Get rid of dirname's '.' return value when there is no dirname
		$directory = ($directory == '.') ? '' : $directory.'/';

		// Add an underscore if it's necessary	
		if ($file != null && substr($filename,0,1) != '_') {
			$filename = '_'.$filename;
		} 

		// Add the full path back together
		$file = $directory.$filename;

		// Instantiate the view
		$view = new View($file,$data);

		// Render it immediately
		return $view->process();
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
		// Determine the variable name that is passed to the partial
		$data_name = basename($file);
		$data_name = App::uglify(ltrim($data_name,'_'),'singularize');

		// Counter to be passed to the collection
		$i = 0;
		
		// Total number of items - 1, since $i is zero-based
		$total = count($data) - 1;

		// A few array keys
		$data_key = $data_name.'_key';
		$data_counter = $data_name.'_counter';
		$data_first = 'first_'.$data_name;
		$data_last = 'last_'.$data_name;

		// Final rendered content
		$collection = '';

		// Loop through each item in the array, rendering a partial and 
		// passing some data to the file
		foreach ($data as $key => $value) {

			// Data to be passed
			$partial_data = array(
				$data_first 	=> ($i === 0) ? true : false,
				$data_name 		=> $value,
				$data_key 		=> $key,
				$data_counter	=> $i,
				$data_last 		=> ($i === $total) ? true : false,
			);

			// Render it
			$collection .= self::render_partial($file,$partial_data)."\n";

			// Increment the counter
			$i++;
		}

		return $collection;
	}
}

?>