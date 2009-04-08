<?php

class HomeController extends ApplicationController 
{
	var $layout = 'home';
	
	public function index() 
	{
		$this->content->memory_used = round(memory_get_peak_usage()/1024/1024,2);
		$this->content->backtrace = debug_backtrace();			
		// Don't like calling view::render* inside your views or need 
		// more control over the data that you pass to it?
		// Simply use $this->content->foo = new View('foo');
		// Then pass data to foo using $this->content->foo->bar = 'Hello World';
		// Then, simple echo $foo in your view file. 
		// Much cleaner for complex nesting and data passing.
	}
}
