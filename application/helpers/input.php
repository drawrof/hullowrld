<?php defined('ROOT') or die ('Restricted Access');

class input {
	
	// From: http://talks.php.net/show/php-best-practices/26
	static function fix_magic_quotes()
	{
		if (get_magic_quotes_gpc()) {
			$in = array(&$_GET, &$_POST, &$_COOKIE);
			
			while (list($k,$v) = each($in)) {
				foreach ($v as $key => $val) {
					if (!is_array($val)) {
						$in[$k][$key] = stripslashes($val);
						continue;
					}
					
					$in[] =& $in[$k][$key];
				}
			}
			
			unset($in);
		}
	}
}

?>