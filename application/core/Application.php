<?php defined('ROOT') or die ('Restricted Access');

class App {
	
	static $output = null;
			
	function Init()
	{	
		spl_autoload_register('App::autoload');
		
		// These will be loaded every request anyway, 
		// so there's no point letting the __autoload catch them.
		include CORE_DIR.'Router'.EXT;
		include CORE_DIR.'Config'.EXT;
		include CORE_DIR.'View'.EXT;
		include CORE_DIR.'Controller'.EXT;
		include CORE_DIR.'Model'.EXT;
		
		// Include the inflector as well. The autoload is unfortunately rather slow.
		include HELPER_DIR.'inflector'.EXT;
				
		// Instantiate a couple of Core Classes
		$config =& Config::Instance();
		$params = Router::Setup();

		// Instantiate the controller and call the requested action
		$action = $params['action'];
		$controller = $params['Controller'];
		$controller = new $controller;
		$controller->$action($params);
		
		// Send the Headers.
		View::send_headers($params['format']);
		
		// Save this for caching and the like
		self::$output = (string)$controller;		

		// Send it to the browser
		echo self::$output;

		// Start the wind-down process
		// Cache the current route, since we had
		// a successful request
		Router::cache();
 	}

	static function autoload($fullname)
	{				
		// Controller
		if (substr($fullname,-10) === 'Controller') {

			$type = "Controller";
			$file = inflector::underscore(substr($fullname,0,-10));
			$path = CONTROLLER_DIR.$file.EXT;
			
		// Helper
		} else if ($fullname[0] !== ucfirst($fullname[0])) {
			
			$type = "Helper";
			$path = HELPER_DIR.$fullname.EXT;
		
		// Libraries	
		} else if (substr($fullname,-7) === 'Library') {
			
			$type = "Library";
			$path = LIB_DIR.substr($fullname,0,-7).EXT;
	
		// Drivers
		} else if (substr($fullname,-6) === 'Driver') {
			
			// Pop off the last segment (== '_Driver')
			$file = substr($fullname,0,-7);
			
			// Convert underscores to slashes
			$file = str_replace('_','/',$file);
			
			$type = "Driver";
			$path = DRIVER_DIR.$file.EXT;
		
		// Error	
		} else if (substr($fullname,-9) === 'Exception') {
			
			require CORE_DIR.'Exception'.EXT;
			return;
			
		// Models
		} else {
			
			$type = "Model";
			$path = MODEL_DIR.inflector::underscore($fullname).EXT;

		}
		
		// Throw different errors for different types
		if ($type === 'Controller') {
			$exception = 'ControllerException';
			$error = 'missing_controller';
		} else {
			$exception = 'AppException';
			$error = 'missing_class';
		}
		
		// Attempt to include the file				
		if (file_exists($path)) {
			include $path;
			
			// Ensure that the class exists
			if (!class_exists($fullname,false)) {
				throw new $exception(
					$error,
					array(
						'class' => $fullname,
						'class_path' => $path,
						'class_type' => $type,
					)
				);
			}
			
		// Too bad
		} else {
			throw new $exception(
				$error,
				array(
					'class' => $fullname,
					'class_path' => $path,
					'class_type' => $type,
				)
			);
		}
	}
}
