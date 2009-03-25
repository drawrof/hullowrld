<?php

class SessionNativeDriver 
{
	var $flashed = array();
	var $to_flash = array();
	var $config = array();
	
	function __construct($config = null)
	{
		$this->config = $config;
		$this->get_flash();
		$this->expire_flash();
	}
	
	public function Regenerate() 
	{
		session_regenerate_id(true);
		$_SESSION['_session_id'] = session_id();
	}
	
	public function Flash($key,$value = null)
	{
		if ($value === null) {
			if (!empty($this->flashed[$key])) {
				return $this->flashed[$key];
			} else {
				return null;
			}
		} else {
			$this->to_flash[$key] = $value;
			return true;
		}
	}
	
	private function get_flash()
	{
		if (!empty($_SESSION['_flash'])) {
			$this->flashed = unserialize($_SESSION['_flash']);
		}
	}
	
	private function expire_flash()
	{
		if (!empty($_SESSION['_flash'])) {
			unset($_SESSION['_flash']);
		}
	}
	
	private function write_flash()
	{
		if (!empty($this->to_flash)) {
			$_SESSION['_flash'] = serialize($this->to_flash);
		}
	}
	
	function __destruct()
	{
		$this->write_flash();
	}
}