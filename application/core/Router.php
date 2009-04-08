<?php
defined('ROOT') or die ('Restricted Access');

class Router {
	
	static $instance = false;
	
	// Some various configuration options
	static $config = array(
		
		// Default parameters
		'params' => array(
			'controller' => null,
			'action' => 'index',
			'id' => null,
			'format' => 'html',
			'is_ajax' => false,
			'name' => null,
		),
		
		// Regex to filter data with
		'regex' => '/[^a-zA-Z0-9\-\_\.\/\+]/',
		
		// Possible routes wildcards
		'wildcards' => array(
			':controller' => '([a-zA-Z\-\_]+)',
			':action' => '([a-zA-Z0-9\-\_]+)',
			':year' => '([12][0-9]{3})',
			':month' => '(0[1-9]|1[012])',
			':day' => '(0[1-9]|[12][0-9]|3[01])',
			':id' => '([0-9]+)',
		),
		
		// parameters that must exist
		'validate' => array(
			'controller',
			'action'
		)
	);
	
	// Routes
	static $routes = array();
	static $named_routes = array();

	// The original input
	var $original = false;
	
	// After initial processing
	var $string = false;
				
	// Found Paramaters
	var $params = array();
	
	// Whether the routes need to be cached
	var $cache_routes = false;

	/**
	 * Singleton access for the Router
	 *
	 * @return object
	 *
	 **/
	static function Instance()
	{
		if (empty(self::$instance)) {
			new Router;
		}
		
		return self::$instance;
	}
	
	/**
	 * Router Constructor. Merges Config and begins processing.
	 *
	 * @return void
	 *
	 **/
	function __construct()
	{			
		// Set the instance
		self::$instance =& $this;

		// Merge Config
		$config = Config::Instance();
		self::$config = array_merge(self::$config,$config->router);

		// Grab the routes...
		self::$routes = $config->routes;
		
		// ...and map them if they're not coming from the cache
		if (!$config->is_cached) {
						
			array_walk(self::$routes,array($this,'map'));
			
			// Flag the cache
			$this->cache_routes = true;
		}

		// Begin URI Processing
		$this->process_uri();
		$this->process_routes();
	}
	
	/**
	 * Sends various parts of the routing system to the Config
	 * Class for caching in production mode
	 *
	 * @return void
	 *
	 **/
	public function cache()
	{
		$config = Config::Instance();
				
		// Cache the routes
		if ($this->cache_routes) {
			$config->routes = self::$routes;
			$config->named_routes = self::$named_routes;
			$config->regenerate();
		}
		
		// Cache the route
		if (!isset($config->routes[(string)$this->params['Route']]['cached_route'])) {
			$this->params['cached_route'] = true;
			$config->routes[(string)$this->params['Route']] = $this->params;
			$config->regenerate();
		}
	}
	
	/**
	 * Connects a route — either standard or named — to the router
	 * 
	 * @param $args string 
	 * @return void
	 *
	 **/
	private function map($args,$route)
	{	
		self::$routes[(string)$route] = $args;
		
		// Add an entry to our named routes
		if (isset($args['name'])) {
			self::$named_routes[$args['name']] = (string)$route;
		}

	}
			
	/**
	 * Returns a Regex string based on the name of the incoming parameter
	 *
	 * @param $key string
	 * @return string
	 *
	 **/	
	private function process_wildcard($key)
	{
		// Search for a predefined wildcard
		if (!empty(self::$config['wildcards'][$key])) {
			return self::$config['wildcards'][$key];
			
		// Auto-find foreign key IDs
		} else if (FALSE !== strpos($key,'_id')) {
			return self::$key['wildcards'][':id'];
			
		// Catch all
		} else {
			return '(.+)';
		}
	}
	
	/**
	 * Sets an "is_ajax" key in $this->params if the incoming request is an AJAX request
	 *
	 * @return void
	 *
	 **/
	private function is_ajax()
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
			AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
				$this->params['is_ajax'] = true;
			}
	}
	
	/**
	 * Adds a few useful extra parameters to $this->params
	 *
	 * @return void
	 *
	 **/
	private function populate_params($params)
	{
		$extra = array(
			'Controller' => inflector::camelize($this->params['controller']."Controller"),
			'controller' => inflector::underscore($this->params['controller']),
			
			'Model' => inflector::classify($this->params['controller']),
			'model' => inflector::tableize($this->params['controller']),

			'action' => inflector::underscore($this->params['action']),
			
			'URI' => $this->original,
			'Route' => $this->string,
		);
		
		$this->params = array_merge($params,$extra);
	}
	
	/**
	 * Throws an exception if $this->params does not contain 
	 * all of the necessary parameters
	 *
	 * @return void
	 * @throws Error
	 *
	 **/
	private function validate_route($params)
	{
		// Did we even find anything?
		if (empty($params)) {
			throw new RouterException('no_route_matched',array('uri' => "/".$this->string));
		}
		
		// Validate specific params
		foreach(self::$config['validate'] as $param) {
			if (empty($params[$param])) {
				throw new RouterException(
					'missing_parameter',
					array(
						'parameter' => $param,
						'route' => "/".$this->string,
					)
				);	
			}
		}
		
		// Although the controller prevents private actions from being called,
		// the constructor must be public. The best way to get around this
		// is to not allow actions starting with underscores to be recognized.
		if (substr($params['action'],0,1) === '_') {
			throw new RouterException(
				'found_leading_underscore',
				array(
					'parameter' => 'action',
					'action' => $params['action']
				)
			);
		}
		
	}
	
	/**
	 * Attempts to find a usable URI based on various
	 * server-defined variables
	 *
	 * @return void
	 *
	 **/
	private function process_uri()
	{
		// Find the route from the query string
		// The PATH_INFO method is preferred, since
		// it is less restrictive about certain characters
		// (periods on not allowed in GET keys)
		if (isset($_SERVER['PATH_INFO'])) {
			$string = $_SERVER['PATH_INFO'];
		} else if (isset($_SERVER['ORIG_PATH_INFO'])) {
			$string = $_SERVER['ORIG_PATH_INFO'];
		} else if (current($_GET) === '') {
			$string = key($_GET);
			unset($_GET[$string]);
		} else {
			$string = false;
		}
		
		// Make it safe!
		$string = preg_replace(self::$config['regex'],'',$string);
		
		// Trim the leading and trailing slashes and all but the last period
		$string = trim(preg_replace('/\.(?=.*\..*$)/s','',$string),'/');
		
		// Set the original URI for nostalgic purposes
		$this->original = $string;

		// Set the new URI
		$this->string = (!empty($string)) ? $string : '';
	}
	
	static function link_to_named($name,$params = array())
	{
		if (isset(self::$named_routes[(string)$name])) {
						
			$route = self::$named_routes[(string)$name];
			
			// Parameters to fill?
			foreach ($params as $key => $value) {
				$route = str_replace(':'.$key, $value, $route);
			}
			
			return '/'.trim($route,'/');
				
		} else {
			throw new RouterException('unknown_named_route',array('name' => $name));
		}
	}
	
	/**
	 * Loops through all of the routes, comparing them to the URI. Wildcards are 
	 * taken into consideration and re-populated if a match is found. This function
	 * first searches for a direct match, then splits apart the URI and route, matching
	 * each segment. It also ensures that both have the same number of segments. If a match
	 * is found, it goes through a couple validation and param-population routines.
	 *
	 * @return void
	 *
	 **/
	private function process_routes()
	{
		// Empty Parameters
		$params = array();
		
		// Routes references
		$routes =& self::$routes;
		
		// URI String
		$string = $this->string;

		// Direct match?
		if (isset($routes[(string)$string])) {
			
			$this->params = array_merge(self::$config['params'],$routes[(string)$string]);
						
			// It's a cached route, no need to continue
			if (isset($this->params['cached_route'])) {
				return;
			}

		// Route away
		} else {

			// Cut off the extension, if it exists
			if (FALSE !== ($strrpos = strrpos($string,'.'))) {
				$format = preg_replace('/[^a-z]/','',substr($string,$strrpos + 1));
				$string = substr($string,0,$strrpos);
			} else {
				$format = 'html';
			}
			
			$uri = explode('/',$string);
			$string = $string.".".$format;

			// Loop through each route
			foreach($routes as $route => $route_params) {

				// Explode the route, to search for parameters
				$route = explode('/',$route);
				$i = 0;
						
				// No reason to compare if they're not the same number of segments
				if (count($route) !== count($uri)) {
					continue;
				}
			
				// Loop through each segment, this will allow regex 
				// back-references to be created in the proper order
				foreach ($route as $key => $value) {
					
					// If there's a wildcard match, it is given 
					// a proper regex, and a numerical index for a match
					if (substr($value,0,1) === ':') {					
						// Build the string with actual regex patterns
						$regex = $this->process_wildcard($value);

						if (preg_match('#^'.$regex.'$#',$uri[$key])) {
							$params[substr($value,1)] = $uri[$key];						
							continue;
						} else {
							$params = array();
							break;
						}
					
					// Not a wildcard, but still a match.
					} else if ($uri[$key] == $route[$key]) {
						$params = array_merge($route_params,$params);
						continue;
					
					// This route isn't gonna work.
					} else {
						$params = array();
						break;
					}
				
					$i++;
				}
			
				// We've found something
				if (!empty($params)) {
					$this->params = array_merge(self::$config['params'],array('format' => $format),$uri,$route_params,$params);
					break;
				}
			}
		}

		// Verify Route
		$this->validate_route($this->params);
		
		// Is it an ajax request?
		$this->is_ajax();
		
		// Populate extra parameters
		$this->populate_params($this->params);
	}
}

?>