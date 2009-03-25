<?php

class Controller
{
	static $instance = null;
	static $cache_loaded = false;
	static $data = array(
		'local' => array(),
		'global' => array(),
	);
	
	var $layout = null;
	var $responds_to = array('html');

	function __construct()
	{		
		// Set the instance
		self::$instance = $this;
		
		// Abstract away the router
		$this->Params =& Router::Instance()->params;
		
		// Verify a proper response exists
		$this->responds_to($this->Params['action'],$this->Params['format']);
		
		// Ensure the action is a public action
		$this->is_public($this->Params['action']);
		
		// Attempt to instantiate a model with the same
		// name is the controller
		Model::Factory(false);
		
		// Load template data
		$this->load_template_data($this->Params['controller'],$this->Params['action']);
	}

	function __set($name,$value)
	{
		// Mimic default functionality...
		$this->$name = $value;
						
		// But capture it for use in the view
		if ($name[0] === ucfirst($name[0])) {
			$location = 'global';
		} else {
			$location = 'local';
		}	
		
		if (empty(self::$data[$location][$name])) {
			self::$data[$location][$name] =& $this->$name;
		}
	}
	
	function __call($action,$args)
	{
		// Ensure that, at the very least, the view isn't a partial
		$action = ltrim($action,'_');
		$view = VIEW_DIR.'/'.str_replace('_controller','',$this->Params['controller']).'/'.$action.'.'.$this->Params['format'].'.php';
		
		// Check for an actionless view, if doesn't exist 
		// we throw. Otherwise, we let the view render it.
		if (!file_exists($view)) {
			throw new Error(
				'missing_action',
				array(
					'action' => $action,
					'controller' => $this->Params['Controller'],
					'view_path' => str_replace(APP_DIR,'',$view)
				)
			);
		}
	}
	
	static function &Instance()
	{
		if (self::$instance === null) {	
			// Instantiate the Controller
			$controller = Router::Instance()->params['Controller'];
			new $controller;
		}
		return self::$instance;
	}
	
	private function load_template_data($controller,$action)
	{
		$config = Config::Instance()->template;
		if (empty($config)) return;
		
		$controller = str_replace('_controller','',$controller);
		$route = $controller.'/'.$action;
		
		// Loop through each possible key
		$keys = array('_default',$controller,$route);
			
		foreach ($keys as $key) {
			if (!empty($config[strtolower($key)])) {
				foreach ($config[$key] as $template_key => $template_val) {
					$this->$template_key = $template_val;
				} 
			}
		}
	}
	
	private function responds_to($action,$format)
	{
		$unknown = false;
		
		// Search on a per-action basis
		if (!empty($this->responds_to[$action])) {
			if (!in_array($format,$this->responds_to[$action])) {
				$unknown = true;
			}
		} else if (!in_array($format,$this->responds_to)) {
			$unknown = true;
		}
		
		if ($unknown) {
			throw new Error(
				'invalid_format', 
				array(
					'format' => $format,
					'action' => $action,
					'controller' => $this->Params['Controller'], 
				)
			);
		}
	}
	
	function is_public($action)
	{
		try {
			$reflection = new ReflectionMethod($this,$action);
			if (!$reflection->isPublic()) {
				throw new Error(
					'private_action',
					array(
						'action' => $action,
						'controller' => $this->Params['Controller'],
					)
				);
			}
		} catch (Exception $e) {
			// Exception will be caught when the method doesn't exist at all.
			// Just return false and __call will pick up the missing action to generate
			// the proper error.
			return false;
		}
	}
	
	public function render()
	{
		if (!empty($this->layout)) {
			return View::Factory('/layouts/'.$this->layout,self::$data['local']);
		} else {
			return View::Factory($this->Params['action'],self::$data['local']);
		}
	}
	
	public function destroy()
	{
		Router::Instance()->cache();
	}
}

?>