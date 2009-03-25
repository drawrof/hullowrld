<?php
defined('ROOT') or die ('Restricted Access');

class filesystem {
		
	static function create($path,$permissions = 0777)
	{		
		// Is this a directory or a file? If we can't find a '.' we're
		// going to assume it's a directory.
		if (strrpos($path,'.') === FALSE) {
			return mkdir($path,$permissions,true);
			
		// Otherwise it's a file
		} else {
			
			// The containing directory doesn't exist. Attempt to create it.
			if (!is_dir(dirname($path))) {
				if (false === mkdir(dirname($path),$permissions,true)) {
					return false;
				}
			}
			
			// Create the file. We won't return the handler.
			if (($h = fopen($path, 'w')) !== false) {
				
				// Close the file, we don't need it anymore
				if (!fclose($h)) {
					return false;
				}
				
				return chmod($path,$permissions);
				
			} else {
				return false;
			}
		}
	}
	
	static function delete($path) 
	{
		// We're dealing with a directory...
	    if (is_dir($path) && !is_link($path)) {
			// Open it...
	        if ($dh = opendir($path)) {
				// Read it...
	            while (($sf = readdir($dh)) !== false) {

					// Skip over unecessary "files"...
	                if ($sf == '.' || $sf == '..') {
	                    continue;
	                }

					// Remove its contents by recursively calling this function...
	                if (self::delete($path.'/'.$sf)) {
	                    return false;
	                }

	            }
				// We've deleted everything in this directory.
	            closedir($dh);
	        }
			// The directory is empty, we can remove it.
	        return rmdir($path);
	    }
		// We're dealing with a file. Remove it.
	    return unlink($path);
	}
	
	static function write($file,$contents,$flags = LOCK_EX)
	{
		// Attempt to create the file if it doesn't exist.
		if (!file_exists($file)) {
			if (!self::create($file)) {
				return false;
			}
		}
		
		// Attempt to write
		return file_put_contents($file,$contents,$flags);
	}
	
	static function read($path,$options = array())
	{
		// Seems that we're dealing with a directory
		if (is_dir($path)) {
			
			// This is an array of files we don't want to add to our return value
			// Mostly system files that are normally of little importance
			$disallow = array(
							'.',
							'..',
							'.DS_Store',
							'.svn',
							'Thumbs.db'
						);
			
			// Merge $disallow with $options for greater control
			if (!empty($options)) {
				$disallow = array_merge($disallow,$options);
			}

			// Open the directory
			if ($handle = opendir($path)) {
				
				// Read the directory
			    while (false !== ($file = readdir($handle))) {
				
					// Make sure the file does not match the disallowed list
			        if (!in_array($file,$disallow)) {
			            $files[$file] = rtrim($path,'/')."/".$file;
			        }
			    }
			
			    closedir($handle);
			
			// Couldn't open the directory
			} else {
				return false;
			}
			
			return $files;
			
		// Seems we're dealing with a file
		} else {
			// ...pretty straightforward
			return file_get_contents($path);
		}
	}
	
	static function rename($source,$destination)
	{
		return rename($source,$destination);
	}
	
	static function chmod($path,$chmod = false)
	{
		if (!$chmod) return fileperms($path);
		return chmod($path,$chmod);
	}
	
	static function chown($path,$owner = false,$group = false)
	{
		// Return an array of the user, group, and user:group
		// if $owner and $group are false
		if (!$owner && !$group) {
			$ch = fileowner($path);
			$user = posix_getpwuid($ch);
			$group = posix_getgrgid($user['gid']);
			
			return array(
				'user:group' => $user['name'].':'.$group['name'],
				'user' => $user['name'],
				'group' => $group['name']
			);
		
		// Attempt to change the group of the file
		} else if (!$owner) {
			return chgrp($path,$group);
		
		// Attempt to change the owner of the file
		} else if (!$group) {
			return chown($path,$owner);
		}
		
		return false;
	}
	
	static function is_writable($file) 
	{
		// This method is safe for windows and unix
		if (strtolower(substr(PHP_OS,0,3)) == 'win') {
			if (!file_exists($file)) {
				return false;
			}

			if (is_file($file)) {
				$tmpfh = @fopen($file,'ab');
				if ($tmpfh == false) {
					return false;
				} else {
					fclose($tmpfh);
					return true;
				}
			} else if (is_dir($file)) {
				$tmpnam = time().md5(uniqid('iswritable'));
				if (touch($file.'/'.$tmpnam)) {
					unlink($file.'/'.$tmpnam);
					return true;
				} else {
					return false;
				}
			}
		} else {
			return is_writable($file);
		}
	}

}

?>