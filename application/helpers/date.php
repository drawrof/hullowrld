<?php defined('ROOT') or die ('Restricted Access');

class date
{
	/**
	 * Converts a date to various formats. The first argument accepts a unix timestamp
	 * or a string that can be converted to a unix timestamp with the php function strtotime
	 * (a list of formats is available in the documentation for that function). The second argument
	 * accepts a string. Possible values include 'datetime' or 'timestamp' (the mysql standard, YYYY-MM-DD HH:MM:SS), 
	 * 'time' (HH:MM:SS), 'year' (YYYY), 'date' (YYYY-MM-DD), 'unix' (time in seconds since the epoch), or any string
	 * that PHP's date function accepts.
	 * 
	 * @param $date
	 * @param $format
	 * @return void
	 **/
	static function convert($date,$format)
	{
		// Attempt conversion to a unix timestamp
		if (!is_numeric($date)) {
			if (FALSE === ($date = strtotime($date))) {
				return false;
			}
		}
		
		// Could it already be a timestamp?
		if (is_numeric($date)) {
			switch (strtolower($format)) {
				case 'mysql':
				case "datetime":
				case 'timestamp':
					return date("Y-m-d H:i:s", $date);
					break;
				
				case "time":
					return date('g:i:s', $date);
					break;
					
				case "year":
					return date("Y", $date);
					break;
				
				case "date":
					return date("Y-m-d", $date);
					break;
				
				case 'unix':
					return $date;
				
				// Accept arbitrary formats	
				default:
					return date($format,$date);
					break;
			}
		}
	}
}