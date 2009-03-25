<?php
defined('ROOT') or die ('Restricted Access');

class upload {
	
	static function save($file,$filename = false,$directory = false,$overwrite = true) 
	{
		if (!is_array($file) && !empty($_FILES[$file])) $file = $_FILES[$file];
		
		// Get all of the path information
		if ($filename == false) $filename = $file['name'].strtolower(date('M-d-Y-H-i-s'));
		if ($directory == false) $directory = UPLOAD_PATH;
		$full_path = Core::check_path($filename,$directory,'noextension');
		
		// Make sure we don't overwrite anything that someone doesn't want overwritten
		if ($overwrite == false && file_exists($full_path)) return false;
		
		// Recursively create the directory(ies) if necessary
		if (!is_dir($directory)) {
			if (!mkdir($directory,0777,true)) {
				return false;
			}
		}
		
		// Check if we have write privileges in the directory
		if (!filesystem::is_writable($directory)) return false;
				
		// Save it. An is_uploaded_file check is not necessary as the 
		// move_uploaded_file does this automatically
		if (move_uploaded_file($file['tmp_name'],$full_path)) {
			return $full_path;
		}
		
		// If we're here something went wrong
		return false;
	}
	
	static function valid($file)
	{
		if (!is_array($file) && !empty($_FILES[$file])) $file = $_FILES[$file];

		if (is_uploaded_file($file['tmp_name'])) {
			return true;
		} else {
			return false;
		}
	}
	
	static function extension($file)
	{
		if (!is_array($file) && !empty($_FILES[$file])) $file = $_FILES[$file];
		
		return strtolower(substr($file['tmp_name'],strrpos($file['tmp_name'],'.')));
	}
	
	static function type($file,$allowed)
	{
		if (!is_array($file) && !empty($_FILES[$file])) $file = $_FILES[$file];
		if (is_string($allowed)) $allowed = explode(',',$allowed);
		
		$extension = self::extension($file);

		if (in_array($extension,$allowed)) {
			return true;
		} else {
			return false;
		}
	}
	
	static function image($file,$allowed = false)
	{
		if (!is_array($file) && !empty($_FILES[$file])) $file = $_FILES[$file];
		
		if (function_exists('exif_imagetype')) {
			return exif_imagetype($file['tmp_name']);
		} else {
			if (FALSE != ($image_details = getimagesize($file['tmp_name']))) {
				return $image_details[2];
			} else {
				return false;
			}
		}
		
		return false;
	}
	
}

?>