<?php

// Comment this line to default to your server's error reporting level
error_reporting(E_ALL);

// Starting point in time
define('APP_START',microtime(true));

// development: Verbose errors, Disabled Caching
// production: Safe errors, Enabled Caching
define('APP_ENV','development');

// Set a string inside the realpath function to the application directory
// relative to this document. Absolute paths are acceptable as well.
define('APP_DIR',rtrim(realpath('../application'),'/').'/');

// Extension of files. 99% of the time this will be fine as .php.
define('EXT','.php');

// -----------------------------------------------------
// You shouldn't need to change anything below this line
// -----------------------------------------------------

// Include all of the directory layout constants
require APP_DIR.'config/filesystem'.EXT;

// Main Initialization class
require CORE_DIR.'Application'.EXT;

// Call the Init Function and we're off
App::Init();