<?php

class Controller
{
	// Controller Singleton
	public static $instance = null;
	
	// Controller Data that is passed to the view
	public static $data = array(
		'local' => array(),
		'global' => array(),
	);
	
	// Default layout to use
	public $layout = null;
	
	// Formats that the controller responds to
	public $responds_to = array('html');

	public function __construct()
	{		
		// Set the instance
		self::$instance = $this;
		
		// Abstract away the router
		$this->Params =& Router::Instance()->params;
		
		// Verify a proper response exists
		$this->responds_to($this->Params['action'],$this->Params['format']);
		
		// Ensure the action is a public action
		$this->is_public($this->Params['action']);
		
		// Attempt to instantiate a model with the same name is the controller
		$model = $this->Params['Model'];
		$this->$model = Model::Factory($this->Params['Model'],$this->Params['id'],true);
		
		// Load template data
		$this->load_template_data($this->Params['controller'],$this->Params['action']);
	}

	/**
	 * Captures all instance variables set within the controller, which are then made available to the views
	 * Variables beginning with a capital letter are "Global", in that all views have access to them. Lowercase
	 * variables are only made available to the layout and default view; Partials cannot touch them.
	 *
	 * @return void
	 **/
	public function __set($name,$value)
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
	
	/**
	 * Throws an error if the action that we attempted to call was not found
	 *
	 * @return void
	 **/
	public function __call($action,$args)
	{
		// Ensure that, at the very least, the view isn't a partial
		$action = ltrim($action,'_');
		$view = VIEW_DIR.'/'.$this->Params['controller'].'/'.$action.'.'.$this->Params['format'].'.php';
		
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
	
	/**
	 * Singleton access for the Controller
	 *
	 * @return Object
	 **/
	static public function &Instance()
	{
		if (self::$instance === null) {	
			// Instantiate the Controller
			$controller = Router::Instance()->params['Controller'];
			new $controller;
		}
		return self::$instance;
	}
	
	/**
	 * If 'template.php' exists in the config directory. It is loaded.
	 * It is expected to contain an array of template data for views, which is
	 * useful for things like page titles and descriptions that don't really make sense
	 * to be set in either the view or the controller.
	 * 
	 * This searches for three keys, first '_default' which contains "default" template data.
	 * Then, it searches for a key in the format of 'controller/action', and finally for a key
	 * that matches exactly the incoming URI, i.e. 'blog/show/3'.
	 * 
	 * Then, every key in these arrays are made available to the views via the magic of the __set method.
	 * They are simply looped through and added to the controller. Uppercase keys are global, lowercase are
	 * only available to the main layout and the view.
	 * 
	 * Check the config/template.php for a sample.
	 *
	 * @return void
	 **/
	private function load_template_data($controller,$action)
	{
		// Load the template if it exists
		$config = Config::Instance()->template;
		if (empty($config)) return;
		
		// Set the array key to be searched
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
	
	/**
	 * Ensures that the action that is about to be called responds to the format
	 * that the Router determined was requested.
	 *
	 * @return void
	 **/
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
	
	/**
	 * Ensures that a particular action is public
	 *
	 * @return void
	 **/
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
			return;
		}
	}
	
	/**
	 * Renders the default view for the action
	 *
	 * @return string
	 **/
	public function render()
	{
		// Load a layout if one is set by the controller
		if (!empty($this->layout)) {
			$file = '/layouts/'.$this->layout;
			
		// Otherwise load a view based on the name of action
		} else {
			$file = $this->Params['action'];
		}
		
		// Set the data
		$data = self::$data['local'];
		
		// Instantiate the View, bypassing the higher-level 
		// static rendering methods like View::render();
		return new View($file,$data);
	}
	
	/**
	 * Indicates the Router that the route was successful and it should be cached.
	 *
	 * @return void
	 **/
	public function destroy()
	{
		Router::Instance()->cache();
	}
}

?>