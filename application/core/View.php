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

	function __construct($file = null,$data = array(),$partial = false)
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
	
	public function render()
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
			return ob_get_clean();
			
		} else {
			
			// Throw an error if the view is missing
			throw new Error('missing_view', array('view_path' => $this->path));
		}
	}
}

function render($file = null,$data = array())
{
	// If the file is null, we'll assume it's a call for the
	// default view to be rendered
	if ($file == null) {
		$file = Router::Instance()->params['action'];
		$data = Controller::$data['local'];
	}
	
	// Instantiate the view
	$view = new View($file,$data,false);
	
	// Render it immediately
	return $view->render();
}

function render_partial($file = null,$data = array())
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
	$view = new View($file,$data,true);
	
	// Render it immediately
	return $view->render();
}

function render_collection($file = null,$data = array())
{
	// Determine the variable name that is passed to the partial
	$data_name = basename($file);
	$data_name = ltrim($data_name,'_');
	
	// Counter to be passed to the collection
	$i = 0;
	
	// Final rendered content
	$collection = '';
	
	// Loop through each item in the array, rendering a partial and 
	// passing some data to the file
	foreach ($data as $key => $value) {
		
		$data_key = $data_name.'_key';
		$data_counter = $data_name.'_counter';
		
		// Data to be passed
		$partial_data = array(
			$data_name 		=> $value,
			$data_key 		=> $key,
			$data_counter	=> $i,
		);
		
		$collection .= render_partial($file,$partial_data)."\n";
	}
	
	return $collection;
}

?>