<?php

// Comment this line to default to your server's error reporting level
error_reporting(E_ALL);

// development: Verbose errors, Disabled Caching
// production: Safe errors, Enabled Caching
define('APP_ENV','development');

// Simple way to locate the application directory
// If you want a non-standard directory structure, use the constants in config/filesystem.php
// Set a string inside the realpath function to the appropriate directory
// relative to this document. Absolute paths are acceptable as well.
define('APP_DIR',realpath('../application'));

// -----------------------------------------------------
// You shouldn't need to change anything below this line
// -----------------------------------------------------

// Include all of the directory layout constants
require APP_DIR.'/config/filesystem.php';

// Main Initialization class
require CORE_DIR.'/Application.php';

// Call the Init Function and we're off
App::Init();