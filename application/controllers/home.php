<?php

class HomeController extends ApplicationController {
	
	var $layout = 'home';
	
	public function index() 
	{
		$this->memory_used = round(memory_get_peak_usage()/1024/1024,2);
		$this->backtrace = debug_backtrace();
		
		// Models
		// _________________________
		// If a model exists that is the same name as your controller, only singularized, will be automatically loaded and instantiated.
		// For a hypothetical controller named PostsController, if a model named "Post" in /application/models/post.php exists then it will be loaded 
		// and instantiated at runtime and available at $this->Post. 
		// 
		// Models automatically have the Database Library available to them at $this->Db. If they subclass "Model" then there are no helper
		// Functions available to it, only database access. If, however, it subclasses ORMLibrary, then it will have all of the ORM Libraries
		// features available to it as well as general Db access. The framework uses a slightly modified version of Kohana's ORM lib. Check their docs for usage.
		
		// Views
		// _________________________
	}
}
