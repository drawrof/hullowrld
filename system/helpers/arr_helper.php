<?php

defined('ROOT') or die ('Restricted Access');

class arr {

	// http://ca.php.net/is_array#85324
	function assoc($_array) {
	    if ( !is_array($_array) || empty($_array) ) {
	        return -1;
	    }
	    foreach (array_keys($_array) as $k => $v) {
	        if ($k !== $v) {
	            return true;
	        }
	    }
	    return false;
	}
	
	function search($string, $array = array ())
    {     
	  	if (FALSE !== ($key = array_search($string,$array))) {
			return $array[$key];
		} 
		
		return false;
    }

}

?>