<?php

class App {
	
	static $output = null;
			
	function Init()
	{	
		spl_autoload_register('App::autoload');
		
		// These will be loaded every request anyway, 
		// so there's no point letting the __autoload catch them.
		include CORE_DIR.'/Router.php';
		include CORE_DIR.'/Config.php';
		include CORE_DIR.'/View.php';
		include CORE_DIR.'/Controller.php';
		include CORE_DIR.'/Model.php';
				
		// Instantiate a couple of Core Classes
		$config =& Config::Instance();
		$router =& Router::Instance();
		$params = $router->params;

		// Instantiate the controller and call the requested action
		$action = $params['action'];
		$controller =& $params['Controller'];
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
		Router::Instance()->cache();
 	}

	static function autoload($fullname)
	{				
		// Controller
		if (FALSE !== strpos($fullname,'Controller')) {

			$type = "Controller";
			$file = inflector::uglify(substr($fullname,0,-10));
			$path = self::check_path($file,CONTROLLER_DIR);
			
		// Helper
		} else if ($fullname[0] !== ucfirst($fullname[0])) {
			
			$type = "Helper";
			$path = self::check_path($fullname,HELPER_DIR);
		
		// Libraries	
		} else if (substr($fullname,-7) === 'Library') {
			
			$type = "Library";
			$path = self::check_path(substr($fullname,0,-7),LIB_DIR);
	
		// Drivers
		} else if (substr($fullname,-6) === 'Driver') {
			
			// Pop off the last segment (== '_Driver')
			$file = substr($fullname,0,-7);
			
			// Convert underscores to slashes
			$file = str_replace('_','/',$file);
			
			$type = "Driver";
			$path = self::check_path($file,DRIVER_DIR);
		
		// Error	
		} else if ($fullname == 'Error') {
			
			require CORE_DIR.'/Error.php';
			return;
			
		// Models
		} else {
			
			$type = "Model";
			$path = self::check_path(inflector::uglify($fullname),MODEL_DIR);
			
		}
		
		// Potentially throw different errors for different types
		if ($type === 'Controller') {
			$error = 'missing_controller';
		} else {
			$error = 'missing_class';
		}
		
		// Attempt to include the file				
		if (file_exists($path)) {
			include $path;
			
			// Ensure that the class exists
			if (!class_exists($fullname,false)) {
				throw new Error(
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
			throw new Error(
				$error,
				array(
					'class' => $fullname,
					'class_path' => $path,
					'class_type' => $type,
				)
			);
		}
	}
	
	/**
	 * Appends a file and extension to a particular system path.
	 *
	 * @param string
	 * @param string
	 * @param mixed
	 * 
	 * @return string
	 **/
	static function check_path($file, $path, $ext = 'php') 
	{
		// Is there an extension? If not, we append one
		if (strrpos($file,'.'.$ext) === false && $ext != false){ 
			$file = $file.".".$ext; 
		}
		
		// Is there a leading slash? If not, we append an absolute path
		if (substr($file,0,1) != '/' && $path != false) {
			$file = rtrim($path,'/')."/".$file;	
		}
			
		return $file;
	}
}
