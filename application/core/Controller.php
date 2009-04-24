<?php defined('ROOT') or die ('Restricted Access');

class Controller
{
	// Controller Singleton
	public static $instance = null;
	
	// Default layout to use
	// Later converted to a View Object
	public $layout = null;
	
	// Final View to be used
	public $content = null;
	
	// Formats that the controller responds to
	public $responds_to = array('html');
	
	public function __construct()
	{		
		// Set the instance
		self::$instance = $this;
		
		// Abstract away the router
		$this->Params =& Router::$params;
		
		// Verify a proper response exists
		$this->responds_to($this->Params['action'],$this->Params['format']);
		
		// Ensure the action is a public action
		$this->is_public($this->Params['action']);
		
		// Attempt to instantiate a model with the same name is the controller
		$model = $this->Params['Model'];
		$this->$model = Model::Factory($this->Params['Model'],$this->Params['id'],true);
						
		// Instantiate the Layout and View Objects so that the controller has access to them
		$this->content = new View($this->Params['action']);
		
		if (!empty($this->layout)) {
			$this->layout = new ViewLayout($this->layout);
			$this->layout->content = $this->content;
		}
		
		// Load template data
		$this->load_view_data($this->Params['controller'],$this->Params['action']);
	}
	
	/**
	 * Throws an error if the action that we attempted to call was not found
	 *
	 * @return void
	 **/
	private function __call($action,$args)
	{
		// Ensure that, at the very least, the view isn't a partial
		$action = ltrim($action,'_');
		$view = VIEW_DIR.$this->Params['controller'].'/'.$action.'.'.$this->Params['format'].EXT;
		
		// Check for an actionless view, if doesn't exist 
		// we throw. Otherwise, the view class will pick it up
		// and render it.
		if (!file_exists($view)) {
			throw new ControllerException(
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
	 * Renders the default view for the action
	 *
	 * @return string
	 **/
	private function __toString()
	{	
		// Process the view first. That way, if it really needs it
		// It can access $this->layout;
		$view = $this->content->process();
		
		if (!empty($this->layout)) {
			$view = $this->layout->process();
		}
		
		return $view;
	}
	
	/**
	 * If 'view_data.php' exists in the config directory. It is loaded.
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
	 * Check the config/view_data.php for a sample.
	 *
	 * @return void
	 **/
	private function load_view_data($controller,$action)
	{
		// Load the template if it exists
		$config = Config::Instance()->view_data;
		if (empty($config)) return;
		
		// Set the array key to be searched
		$route = $controller.'/'.$action;
		
		// All of the keys
		$keys = array(
			'_content' 				=> $this->content,
			'_layout'		 		=> $this->layout,
			$controller.'_content'	=> $this->content,
			$controller.'_layout' 	=> $this->layout,
			$route.'_content'		=> $this->content,
			$route.'_layout'		=> $this->layout,
		);
		
		// Go through each one
		foreach ($keys as $key => $view) {
			
			// Shouldn't continue if it's empty
			if (!empty($config[strtolower($key)])) {
				
				// Load the template data
				foreach ($config[$key] as $template_key => $template_val) {
					$view->$template_key = $template_val;
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
		if (isset($this->responds_to[$action])) {
			if (!in_array($format,$this->responds_to[$action])) {
				$unknown = true;
			}
		} else if (!in_array($format,$this->responds_to)) {
			$unknown = true;
		}
		
		if ($unknown) {
			throw new ControllerException(
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
	private function is_public($action)
	{
		try {
			$reflection = new ReflectionMethod($this,$action);
			if (!$reflection->isPublic()) {
				throw new ControllerException(
					'private_action',
					array(
						'action' => $action,
						'controller' => $this->Params['Controller'],
					)
				);
			}
		} catch (Exception $e) {
			// Exception will be caught when the method doesn't exist at all.
			// Just return and __call will pick up the missing action to generate
			// the proper error.
			return;
		}
	}
}

?>