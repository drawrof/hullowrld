<?php

class Error extends Exception {
	
	// Yes, this is ugly. But I want to keep this Exception Class self-contained.
	static $config = array(
		// Actions & Controllers
		'missing_controller' => array('The {class_type} "{class}" could not be found and instantiated. It was expected in "{class_path}". If you are sure this file exists, ensure that the class is properly named.', 404),
		'missing_action' => array('The action "{action}" in the controller "{controller}" does not exist. If this particular page doesn&rsquo;t require an action, create a view in "{view_path}" and the system will automatically render it.', 404),
		'private_action' => array('The action "{action}" in the controller "{controller}" is private and cannot be called.', 404),
		'invalid_format' => array('"{controller}->{action}()" is not configured to respond to "{format}" requests.', 404),
		
		// Views
		'missing_view' => array('The view "{view_path}" does not exist.', 500),
		'variable_collision' => array('A variable collision occurred in {view_path}.', 500),
		
		// Router
		'no_route_match' => array('The incoming URI "{uri}" did not match any of the configured routes.',404),
		'missing_parameter' => array('The Router did not find the necessary "{parameter}" parameter in the route "{route}".', 500),
		'unknown_named_route' => array('The route named "{name}" could not be found. Ensure that it has been mapped to the Router.', 500),
		
		// Configuration
		'missing_configuration' => array('A required configuration file, expected in "{path}" was not found.', 500),
		'unreadable_config_directory' => array('The configuration directory "{path}" does not appear to be readable.',500),
		
		// Generic Missing Class
		'missing_class' => array('The {class_type} "{class}" could not be found and instantiated. It was expected in "{class_path}".', 500),
		
		// File specific
		'missing_file' => array('The file "{file}" could not be found for use by the "{class}" class.', 500),
		'unreadable_file' => array('The file "{file}" is not readable by the application.', 500),
		'unknown_filetype' => array('The file "{file}" does not appear to be a filetype that can be processed by the "{class}" class.', 500),
		'missing_gd_extension' => array('The GD extension must be installed to use the image processing functions.', 500),
		
		// Database specific
		'database_error' => array('An error occurred with the database after issuing the query "{db_query}". The database returned the following error: "{db_error}".',500),
	);
		
	// Headers to be sent based on error code	
	static $headers = array(
		300 => 'HTTP/1.1 301 Moved Permanently',
		403 => 'HTTP/1.1 403 Forbidden',
		404 => 'HTTP/1.0 404 Not Found',
		500 => 'HTTP/1.1 500 Internal Server Error',
	);
	 
	/**
	 * Displays a view file based on the error that occurred
	 *
	 * @param string $error The error that occurred
	 * @param array $data Error data that is passed to the view
	 * @return void
	 * 
	 **/
	function __construct($error,$data = array()) 
	{
		parent::__construct();
		
		$error_code = self::$config[$error][1];
					
		// Send the appropriate headers
		$this->send_headers($error_code);
		
		// Allows mapping to a different, less exposing error view in production mode
		if (APP_ENV == 'production') {
			$this->display_production($error_code,$error,$data);
		} else {
			$this->display_development($error,$data);
		}
	}
	
	private function display_production($error_code,$error,$data)
	{
		include ERROR_DIR.'/'.$error_code.'.php';
		exit;
	}
	
	private function display_development($error,$data)
	{
		$trace = $this->getTrace();
		$formatted_trace = $this->format_trace($trace);
		$line = $this->getLine();
		$class = $trace[0]['class'];
		$type = $trace[0]['type'];
		$method = $trace[0]['function'];
		$error_name = ucfirst(str_replace('_',' ',$error));
	
		if (!empty(self::$config[$error][0])) {
			$error_string = self::$config[$error][0];
			
			// Replace keys with data
			foreach($data as $key => $value) {
				$error_string = str_replace("{".$key."}",$value,$error_string);
				// Convert quotes in strings to something nicer
			}
			
			$error_string = preg_replace("/\"([^\"]+)\"/i","&#8220;<span class='code'>$1</span>&#8221;",$error_string);
			
		} else {
			$error_string = false;
		}
		
		// Just include it. We want to bypass as much of the system as
		// possible. Including the View Class.
		for($i=0; $i <= ob_get_level(); $i++) {
			ob_end_clean();
		}
		include ERROR_DIR.'/system_template.php';
		exit;
	}
	
	function __toString()
	{
		return (string) $this->message; 
	}

	private function send_headers($code)
	{
		if (!empty(self::$headers[$code])) {
			 return header(self::$headers[$code]);
		}
		return false;		
	}
	
	public static function format_trace($trace)
	{
		if ( ! is_array($trace))
			return;

		// Final output
		$output = array();

		foreach ($trace as $entry)
		{
			$temp = '<li><span>';

			if (isset($entry['file']))
			{
				$temp .= "<strong>File:</strong> ".str_replace(SUPER_ROOT,'',$entry['file']);
			} else {
				$temp .= "<strong>File:</strong> None";
			}

			$temp .= '<br /><span class="code"><span>&#8618;</span> ';

			if (isset($entry['class']))
			{
				// Add class and call type
				$temp .= "<span class='class'>".$entry['class']."</span><span class='type'>".$entry['type']."</span>";
			}

			// Add function
			$temp .= "<span class='method'>".$entry['function'].'</span>(';

			// Add function args
			if (isset($entry['args']) AND is_array($entry['args']))
			{
				// Separator starts as nothing
				$sep = '';

				while ($arg = array_shift($entry['args']))
				{
					if (is_string($arg) AND is_file($arg))
					{
						// Remove docroot from filename
						$arg = preg_replace('!^'.preg_quote(SUPER_ROOT).'!', '', $arg);
					}
					
					if (is_array($arg)) {
						
						$copy = '{';
						
						foreach($arg as $arg_key => $arg_val) {
							$copy .= "<span class='array-key'>".(string)$arg_key."</span>: <span class='array-val'>".(string)$arg_val."</span>, ";
						}
						
						$arg = substr($copy,0,-2) . "}";
					}
					
					$arg = '<span class="arg">'.$arg.'</span>';
					$temp .= $sep.print_r($arg, TRUE);

					// Change separator to a comma
					$sep = ', ';
				}
			}

			$temp .= ')</span></span></li>';

			$output[] = $temp;
		}

		return '<ol id="backtrace">'.implode("\n", $output).'</ol>';
	}
}

