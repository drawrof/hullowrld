<?php

// Root Directory, containing this file
define('ROOT',realpath(dirname(__FILE__)));

// Document Root, useful for outputting URLs
define('DOCROOT',rtrim(str_replace(realpath($_SERVER['DOCUMENT_ROOT']),'',realpath(ROOT)),'/'));

// Core Directory
define('CORE_DIR',APP_DIR.'/core');

// Houses various libraries
define('LIB_DIR',APP_DIR.'/libraries');

// Houses various helpers for application development
define('HELPER_DIR',APP_DIR.'/helpers');

// Houses various drivers for libraries
define('DRIVER_DIR',LIB_DIR.'/drivers');

// Location of application controllers
define('CONTROLLER_DIR',APP_DIR.'/controllers');

// Location of application models
define('MODEL_DIR',APP_DIR.'/models');

// Location of application views
define('VIEW_DIR',APP_DIR.'/views');

// Location of error views
define('ERROR_DIR',APP_DIR.'/errors');

// Location of application models
define('APP_HELPER_DIR',APP_DIR.'/helpers');

// Contains the file based caching system
define('CACHE_DIR',APP_DIR.'/cache');

// Contains the pages system
define('PAGE_DIR',VIEW_DIR.'/pages');

// Configuration file DIR
define('CONFIG_DIR',APP_DIR.'/config');