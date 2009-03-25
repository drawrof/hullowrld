<?php

defined('ROOT') or die ('Restricted Access');

class valid {
	
	static function required(&$item)
	{
		if (empty($item)) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function match(&$item,&$extra)
	{
		if (self::required($extra) == FALSE) return FALSE;
		if ($item != $extra) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	function pattern($item,$extra)
	{
		if (preg_match('/^(['.$extra.'])+$/i',$this->data[$item])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function numeric($item) {
		if (is_numeric($this->data[$item])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function boolean($item) {
		if (strtolower($this->data[$item]) == 'true' ||
			strtolower($this->data[$item]) == 'false' ||
			$this->data[$item] == '1' ||
			$this->data[$item] == '0') 
		{
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function string($item) {
		if (is_string($this->data[$item])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function valid_email($item)
	{
		if (preg_match("/^(.+)@([^\(\);:,<>]+\.[a-zA-Z]{2,4})/", $POST[$item]) != false) {
		    return TRUE;
		} else {
			return FALSE;
		}
	}

	function valid_url($item)
	{
		if (preg_match("/^(http|https|ftp|svn):\/\/([^\(\);:,<>]+\.[a-zA-Z]{2,4})/", $this->data[$items]) != false) {
		    return TRUE;
		} else {
			return FALSE;
		}
	}

	function strip($item,$extra)
	{
		if (preg_replace('/['.$extra.']/','',$this->data[$item])) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}

?>