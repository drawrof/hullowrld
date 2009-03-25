<?php

class date
{
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
				case "datetime":
					return date("Y-m-d H:i:s", $date);
					break;
				
				case "time":
					return date('g:i:s', $date);
					break;
					
				case "timestamp":
					return date("Y-m-d H:i:s", $date);
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