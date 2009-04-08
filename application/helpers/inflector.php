<?php
// Thanks to http://www.eval.ca/articles/php-pluralize (MIT license)
//		   http://dev.rubyonrails.org/browser/trunk/activesupport/lib/active_support/inflections.rb (MIT license)
//		   http://www.fortunecity.com/bally/durrus/153/gramch13.html
//		   http://www2.gsu.edu/~wwwesl/egw/crump.htm
//
// Changes (12/17/07)
//   Major changes
//   --
//   Fixed irregular noun algorithm to use regular expressions just like the original Ruby source.
//	   (this allows for things like fireman -> firemen
//   Fixed the order of the singular array, which was backwards.
//
//   Minor changes
//   --
//   Removed incorrect pluralization rule for /([^aeiouy]|qu)ies$/ => $1y
//   Expanded on the list of exceptions for *o -> *oes, and removed rule for buffalo -> buffaloes
//   Removed dangerous singularization rule for /([^f])ves$/ => $1fe
//   Added more specific rules for singularizing lives, wives, knives, sheaves, loaves, and leaves and thieves
//   Added exception to /(us)es$/ => $1 rule for houses => house and blouses => blouse
//   Added excpetions for feet, geese and teeth
//   Added rule for deer -> deer

// Changes:
//   Removed rule for virus -> viri
//   Added rule for potato -> potatoes
//   Added rule for *us -> *uses

// Added by Me (Nov 24, 2008)
//   Cleaned up code to match my preferred style
//   Added caching

class inflector {
	
	static $plural = array(
		'/(quiz)$/i'			   => "$1zes",
		'/^(ox)$/i'				=> "$1en",
		'/([m|l])ouse$/i'		  => "$1ice",
		'/(matr|vert|ind)ix|ex$/i' => "$1ices",
		'/(x|ch|ss|sh)$/i'		 => "$1es",
		'/([^aeiouy]|qu)y$/i'	  => "$1ies",
		'/(hive)$/i'			   => "$1s",
		'/(?:([^f])fe|([lr])f)$/i' => "$1$2ves",
		'/(shea|lea|loa|thie)f$/i' => "$1ves",
		'/sis$/i'				  => "ses",
		'/([ti])um$/i'			 => "$1a",
		'/(tomat|potat|ech|her|vet)o$/i'=> "$1oes",
		'/(bu)s$/i'				=> "$1ses",
		'/(alias)$/i'			  => "$1es",
		'/(octop)us$/i'			=> "$1i",
		'/(ax|test)is$/i'		  => "$1es",
		'/(us)$/i'				 => "$1es",
		'/s$/i'					=> "s",
		'/$/'					  => "s"
	);

	static $singular = array(
		'/(quiz)zes$/i'			 => "$1",
		'/(matr)ices$/i'			=> "$1ix",
		'/(vert|ind)ices$/i'		=> "$1ex",
		'/^(ox)en$/i'			   => "$1",
		'/(alias)es$/i'			 => "$1",
		'/(octop|vir)i$/i'		  => "$1us",
		'/(cris|ax|test)es$/i'	  => "$1is",
		'/(shoe)s$/i'			   => "$1",
		'/(o)es$/i'				 => "$1",
		'/(bus)es$/i'			   => "$1",
		'/([m|l])ice$/i'			=> "$1ouse",
		'/(x|ch|ss|sh)es$/i'		=> "$1",
		'/(m)ovies$/i'			  => "$1ovie",
		'/(s)eries$/i'			  => "$1eries",
		'/([^aeiouy]|qu)ies$/i'	 => "$1y",
		'/([lr])ves$/i'			 => "$1f",
		'/(tive)s$/i'			   => "$1",
		'/(hive)s$/i'			   => "$1",
		'/(li|wi|kni)ves$/i'		=> "$1fe",
		'/(shea|loa|lea|thie)ves$/i'=> "$1f",
		'/(^analy)ses$/i'		   => "$1sis",
		'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i'  => "$1$2sis",
		'/([ti])a$/i'			   => "$1um",
		'/(n)ews$/i'				=> "$1ews",
		'/(h|bl)ouses$/i'		   => "$1ouse",
		'/(corpse)s$/i'			 => "$1",
		'/(us)es$/i'				=> "$1",
		'/s$/i'					 => ""
	);

	static $irregular = array(
		'move'   => 'moves',
		'foot'   => 'feet',
		'goose'  => 'geese',
		'sex'	=> 'sexes',
		'child'  => 'children',
		'man'	=> 'men',
		'tooth'  => 'teeth',
		'person' => 'people'
	);

	static $uncountable = array(
		'sheep' => true,
		'fish' => true,
		'deer' => true,
		'series' => true,
		'species' => true,
		'money' => true,
		'rice' => true,
		'information' => true,
		'equipment' => true
	);

	public static function pluralize($string)
	{
		static $cache = array();
		if (!empty($cache[$string])) return $cache[$string];

		// Save the original for caching	
		$original = $string; 
		
		// save some time in the case that singular and plural are the same
		if (!empty(self::$uncountable[strtolower($string)])) {
			$cache[$original] = $string;
			return $string;
		}

		// check for irregular singular forms
		foreach (self::$irregular as $pattern => $result) {
			$pattern = '/'.$pattern.'$/i';

			if (preg_match($pattern,$string)) {
				$cache[$original] = preg_replace($pattern,$result,$string);
				return $cache[$original];
			}	 
		}

		// check for matches using regular expressions
		foreach (self::$plural as $pattern => $result) {
			if (preg_match($pattern,$string)) {
				$cache[$original] = preg_replace($pattern,$result,$string);
				return $cache[$original];
			}
				
		}
		return $original;
	}

	public static function singularize($string)
	{
		static $cache = array();
		if (!empty($cache[$string])) return $cache[$string];
		
		// Set the original for caching	
		$original = $string; 
		
		// save some time in the case that singular and plural are the same
		if (!empty(self::$uncountable[strtolower($string)])) {
			$cache[$original] = $string;
			return $string;
		}
			
		// check for irregular plural forms
		foreach (self::$irregular as $result => $pattern) {
			$pattern = '/' . $pattern . '$/i';

			if (preg_match($pattern,$string)) {
				$cache[$original] = preg_replace($pattern,$result,$string);
				return $cache[$original];
			}
				
		}

		// check for matches using regular expressions
		foreach (self::$singular as $pattern => $result) {
			if (preg_match($pattern,$string)) {
				$cache[$original] = preg_replace($pattern,$result,$string);
				return $cache[$original];
			}   
		}
		return $original;
	}

	/**
	 * Converts a string to its camelized version.
	 * e.g. "home_controller" is converted to "HomeController"
	 * 
	 * @param string
	 * @param string $option 
	 *
	 * @return string
	 **/
	static function camelize($word)
	{
		return str_replace(' ','',ucwords(preg_replace('/[^A-Z^a-z^0-9]+/',' ',$word)));
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
	static function underscore($word)
	{
		static $cache;
        if(!isset($cache[$word])){
            $cache[$word] = strtolower(preg_replace(
            array('/[^A-Z^a-z^0-9^\/]+/','/([a-z\d])([A-Z])/','/([A-Z]+)([A-Z][a-z])/'),
            array('_','\1_\2','\1_\2'), $word));
        }
        return $cache[$word];
	}
	
	/**
	* Converts a class name to its table name according to rails
	* naming conventions.
	* 
	* Converts "Person" to "people"
	* 
	* @access public
	* @static
	* @see classify
	* @param	string	$class_name	Class name for getting related table_name.
	* @return string plural_table_name
	*/
	static function tableize($class_name)
	{
		return self::pluralize(self::underscore($class_name));
	}

	/**
	* Converts a table name to its class name according to rails
	* naming conventions.
	* 
	* Converts "people" to "Person"
	* 
	* @access public
	* @static
	* @see tableize
	* @param	string	$table_name	Table name for getting related ClassName.
	* @return string SingularClassName
	*/
	static function classify($table_name)
	{
		return self::camelize(self::singularize($table_name));
	}
	
	
	/**
    * Returns a human-readable string from $word
    * 
    * Returns a human-readable string from $word, by replacing
    * underscores with a space, and by upper-casing the initial
    * character by default.
    * 
    * If you need to uppercase all the words you just have to
    * pass 'all' as a second parameter.
    * 
    * @access public
    * @static
    * @param    string    $word    String to "humanize"
    * @param    string    $uppercase    If set to 'all' it will uppercase all the words
    * instead of just the first one.
    * @return string Human-readable word
    */
    function humanize($word, $uppercase = '')
    {
        $uppercase = $uppercase == 'all' ? 'ucwords' : 'ucfirst';
        return $uppercase(str_replace('_',' ',preg_replace('/_id$/', '',$word)));
    }

	/**
	* Converts number to its ordinal English form.
	* 
	* This method converts 13 to 13th, 2 to 2nd ...
	* 
	* @access public
	* @static
	* @param	integer	$number	Number to get its ordinal value
	* @return string Ordinal representation of given string.
	*/
	static function ordinalize($number)
	{
		if (in_array(($number % 100),range(11,13))) {
			return $number.'th';
		} else {
			switch (($number % 10)) {
				case 1:
				return $number.'st';
				break;
				case 2:
				return $number.'nd';
				break;
				case 3:
				return $number.'rd';
				default:
				return $number.'th';
				break;
			}
		}
	}
}
