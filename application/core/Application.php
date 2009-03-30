<?php

class App {
	
	static $output = null;
			
	function Init()
	{	
		// Register the autoloader
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

		// Transfer control to the Controller
		$action = $params['action'];
		$controller =& Controller::Instance();
		$controller->$action($params);
		self::$output = $controller->render();
		
		// Send it to the browser
		echo self::$output;		
		
		// Start the wind-down process
		$controller->destroy();
 	}

	static function autoload($fullname)
	{				
		// Controller
		if (FALSE !== strpos($fullname,'Controller')) {

			$type = "Controller";
			$file = self::uglify(substr($fullname,0,-10));
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
			$path = self::check_path(self::uglify($fullname),MODEL_DIR);
			
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
	
	/**
	 * Converts an "ugly" string to a pretty one. 
	 * e.g. "home_controller" is converted to "HomeController"
	 * Optionally inflectifies it if passed a string for the second arg.
	 * 
	 * @param string
	 * @param string $option 
	 *
	 * @return string
	 **/
	static function beautify($string,$option = false)
	{
		// Convert dashes and underscores to spaces
		$string = str_replace('_',' ',$string);
		$string = str_replace('-',' ',$string);
		
		// Inflectify
		if ($option === 'singularize') {
			$string = inflector::singularize($string);
		} else if ($option === 'pluralize') {
			$string = inflector::pluralize($string);
		}
		
		// Uppercase and remove spaces
		if ($option === true) {
			$string = ucwords($string);
		} else {
			$string = str_replace(' ','',ucwords($string));
		}

		return $string;
	}

	/**
	 * Converts a pretty string to an ugly one. 
	 * e.g. "HomeController" is converted to "home_controller"
	 * Optionally inflectifies it if passed a string for the second arg.
	 * 
	 * @param string
	 * @param string $option 
	 *
	 * @return string
	 **/
	static function uglify($string,$inflector = false)
	{
		// This Regex splits apart a string at each capital letter
		$string =  preg_replace('/(\B[A-Z])(?=[a-z])|(?<=[a-z])([A-Z])/sm', ' $1$2', $string);
		
		// Convert dashes to spaces
		$string = str_replace('-',' ',$string);
		
		// Inflectify
		if ($inflector === 'singularize') {
			$string = inflector::singularize($string);
		} else if ($inflector === 'pluralize') {
			$string = inflector::pluralize($string);
		}
		
		// Lowercase it and convert spaces to underscores
		$string =  strtolower(str_replace(' ','_',$string));
		$string = preg_replace('(_{2,})','_',$string);
		
		// Finally, remove duplicate adjacent underscores
		return $string;
	}
	
	static function debug($var,$heading = 'Debug')
	{
		$html = "<pre style='font-size: 12px; padding: 0px 0px;'>";
		
		if ($heading) {
			$html .= "<h2 style='margin: 10px 0; padding: 0; font-size: 14px'>$heading</h2>";
		}
		
		ob_start();
		var_dump($var);
		$html .= ob_get_clean();
			
		$html .=  "</pre>";
	}
}
