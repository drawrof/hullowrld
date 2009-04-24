<?php defined('ROOT') or die ('Restricted Access');

class AppException extends Exception 
{
	// Error Messages
	static $config;
	
	// Headers to be sent based on error code	
	static $headers = array(
		300 => 'HTTP/1.1 301 Moved Permanently',
		403 => 'HTTP/1.1 403 Forbidden',
		404 => 'HTTP/1.0 404 Not Found',
		500 => 'HTTP/1.1 500 Internal Server Error',
	);
	
	// Each subclass defines a group from where it loads its configuration
	public $group = 'app';
	
	// Useful data
	public $error;
	public $data;
	public $error_code;
	public $error_message;
	 
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
		
		// Ensure the configuration has been loaded
		$this->load_config();
				
		// Determine the error we're working with
		if (!empty(self::$config[$this->group][$error])) {
			$error_code = self::$config[$this->group][$error][0];
			$error_message = self::$config[$this->group][$error][1];
		} else {
			exit('Unable to find configuration settings for the key "'.$error.'" in the group "'.$this->group.'"');
		}
		
		// Set up
		$this->error = $error;
		$this->data = $data;
		$this->error_code = $error_code;
		$this->error_message  = $error_message;
		
		// A few initialization methods
		$this->send_headers();
		$this->process_message();
		$this->clean_output_buffer();

		// Allows mapping to a different, less exposing error view in production
		(IN_PRODUCTION) ? $this->display_production() : $this->display_development();
		exit;
	}
	
	/**
	 * Loads configuration independent of the Config Class
	 *
	 * @return void
	 **/
	private function load_config()
	{
		if (self::$config == null) {
			if (file_exists(CONFIG_DIR.'exception'.EXT)) {
				include CONFIG_DIR.'exception'.EXT;
				self::$config = $config;
			} else {
				exit('Unable to load Exception configuration from "'.CONFIG_DIR.'exception'.EXT.'".');
			}
		}
	}
	
	/**
	 * Sends headers based on the error code of the Exception
	 *
	 * @return void
	 **/
	private function send_headers()
	{
		if (!empty(self::$headers[$this->error_code]) && !headers_sent()) {
			 header(self::$headers[$this->error_code]);
		}		
	}
	
	/**
	 * Allows for a simple markup system within error messages. 
	 * Something like {var_name} is treated as a variable. Anything in 
	 * double quotes is treated as code and wrapped in a span with the
	 * class of 'code'.
	 *
	 * @return void
	 **/
	private function process_message()
	{
		// Convert {vars} to the data provided
		foreach($this->data as $key => $value) {
			$this->error_message = str_replace("{".$key."}",$value,$this->error_message);
		}
		
		// Convert "foo" to a nicer HTML equivalent
		$match = "/\"([^\"]+)\"/i";
		$replace = "&#8220;<span class='code'>$1</span>&#8221;";
		$this->error_message = preg_replace($match,$replace,$this->error_message);
	}
	
	/**
	 * Cleans the output buffer. This allows errors to override 
	 * anything that's already been outputted to the browser.
	 *
	 * @return void
	 **/
	function clean_output_buffer()
	{	
		for($i=0; $i < ob_get_level(); $i++) {
			ob_end_clean();
		}
	}
	
	/**
	 * Includes a file in ERROR_DIR based on the error code that occurred.
	 * This only happends in production mode.
	 *
	 * @return void
	 **/
	private function display_production()
	{
		if (file_exists(ERROR_DIR.$this->error_code.EXT)) {
			include ERROR_DIR.$this->error_code.EXT;
		} else {
			exit('Unable to load "'.ERROR_DIR.$this->error_code.EXT.'"');
		}
	}
	
	/**
	 * Makes detailed error messages, traces and other information available
	 * to an error template, which it then includes and processes.
	 *
	 * @return void
	 **/
	private function display_development()
	{
		// Make these variables available to the template
		$trace 				=	$this->getTrace();
		$formatted_trace	= 	$this->format_trace($trace);
		$line				= 	$this->getLine();
		$class 				= 	$trace[0]['class'];
		$type				= 	$trace[0]['type'];
		$method 			= 	$trace[0]['function'];
		$error 				= 	ucfirst(str_replace('_',' ',$this->error));
		$message 			= 	$this->error_message;
		$code 				= 	$this->error_code;
		
		// Include this file, which will have access 
		// to all of the important variables above.
		include ERROR_DIR.'system_template'.EXT;
	}
	
	/**
	 * Formates a backtrace into a nice ordered list. 
	 * Took it from Kohana.
	 *
	 * @return void
	 **/
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
				$temp .= "<strong>File:</strong> ".str_replace(APP_DIR,'',$entry['file']);
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
					if (is_string($arg) AND is_file($arg)) {
						// Remove docroot from filename
						$arg = preg_replace('!^'.preg_quote(APP_DIR).'!', '', $arg);
					}
					
					if (is_array($arg)) {
						
						$copy = '{';
						
						foreach($arg as $arg_key => $arg_val) {
							
							// Don't want to trigger __toString();
							if (is_object($arg_val)) {
								$arg_val = '&'.get_class($arg_val);
							}
							
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

class ControllerException extends AppException
{
	public $group = 'controller';
}

class ViewException extends AppException
{
	public $group = 'view';
}

class DatabaseException extends AppException
{
	public $group = 'database';
}

class RouterException extends AppException
{
	public $group = 'router';
}

class ConfigException extends AppException
{
	public $group = 'config';
}