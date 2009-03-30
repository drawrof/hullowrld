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
	$pos = strrpos($file,'/');

	if ($pos !== FALSE) {
		if ($file[($pos+1)] !== '_') {
			$file = substr_replace($file,'/_',$pos,1);
		}
	} else {
		if ($file[0] !== '_') {
			$file = '_'.$file;
		}	
	}
	
	// Instantiate the view
	$view = new View($file,$data,true);
	
	// Render it immediately
	return $view->render();
}

?>