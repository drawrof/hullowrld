<?php defined('ROOT') or die ('Restricted Access');

class url
{
	static $js_folder = '/javascripts/';
	static $css_folder = '/stylesheets/';
	static $img_folder = '/images/';
	
	static private function link($file,$extension,$path,$append_last_modified = true)
	{
		// Strip folder seperators
		$file = trim($file,'/');
		
		// Ensure there is an extension
		if (FALSE === strrpos($file,'.')) {
			$file = $file.'.'.$extension;
		}
		
		// Local Path
		$local_path = DOCROOT.$path.$file;
			
		// Find the last modification time
		if ($append_last_modified) {
			$full_path = ROOT.$path.$file;
			$last_modified = date('mdyGis',@filemtime($full_path));
			$local_path = $local_path."?".$last_modified;
		}
		
		return $local_path;
	}
	
	static function javascript($file)
	{
		return self::link($file,'js',self::$js_folder);
	}
	
	static function stylesheet($file)
	{
		return self::link($file,'css',self::$css_folder);
	}
	
	static function image($file,$extension = 'jpg')
	{
		return self::link($file,$extension,self::$img_folder,false);
	}
	
	static function named($name, $params = array())
	{
		if (isset(Router::$named_routes[(string)$name])) {
						
			$route = Router::$named_routes[(string)$name];
			
			// Parameters to fill?
			foreach ($params as $key => $value) {
				$route = str_replace(':'.$key, $value, $route);
			}
			
			return DOCROOT.trim($route,'/');
				
		} else {
			throw new RouterException('unknown_named_route',array('name' => $name));
		}
	}
}