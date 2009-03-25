<?php

defined('ROOT') or die ('Restricted Access');

class url {
	
	static $breadcrumb_cache = false;
	static $relative_cache = false;
	
	static function absolute($path)
	{
		return rtrim(rtrim(ROOT,'/').$path,'/');
	}
	
	static function relative($path) 
	{
		// Check the cache
		if (!empty(self::$relative_cache[$path])) return self::$relative_cache[$path];
		
		$original_path = $path;
		
		//remove the leading forward-slash if it's there
		if (substr($path,0,1) == '/') $path = substr($path,1);
		
		// Get the original URI, before processing or routing,
		// and trim off leading slashes
		$uri = ltrim(URILibrary::Original(),'/');
		
		// Strip everything but slashes and count the number
		$segments = strlen(preg_replace('/[^\/]/','',$uri));

		//Loop-De-Loop
		for ($i = 0; $i < $segments; $i++) {
			$path = "../".$path;
		}
		
		// Set the cache and return
		self::$relative_cache[$original_path] = $path;	
		return $path;
	}
	
	static function breadcrumb($array,$default = 'Welcome',$separator = ' > ')
	{
		$breadcrumb = false;
		$first = false;
		
		if (is_string($array)) {
			$array = explode('/',trim($array,'/'));
		}
		
		if (empty($array)) {
			return $default;
		}
		
		// Loop through our array and build the breadcrumbs
		foreach ($array as $crumb) {
			
			$crumb = App::beautify($crumb,true);
			$crumb = text::limit_words($crumb,4);
			
			// Don't append the separator the first time around
			if ($first === true) $breadcrumb .= $separator;
			if ($first === false) $first = true;
			
			$breadcrumb .= $crumb;
		}
		
		return $breadcrumb;
	}

}

?>