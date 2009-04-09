<?php

// For the most part, nothing needs to be changed for this file.
// It basically contains human-readable messages for 
// various Exceptions that can occur throughout the system
// 
// The number at the beginning of the array determines the
// HTTP Status Header that is sent, as well as the view name
// that is displayed when in production mode.

// Controller Group
$config['controller'] = array(
	'missing_controller' => array(
		404, 'The {class_type} "{class}" could not be found and instantiated. It was expected in "{class_path}". If you are sure this file exists, ensure that the class is properly named.'),
	'missing_action' => array(
		404, 'The action "{action}" in the controller "{controller}" does not exist. If this particular page doesn&rsquo;t require an action, create a view in "{view_path}" and the system will automatically render it.'),
	'private_action' => array(
		404, 'The action "{action}" in the controller "{controller}" is private and cannot be called.'),
	'invalid_format' => array(
		404, '"{controller}->{action}()" is not configured to respond to "{format}" requests.'),
);

// View Group
$config['view'] = array(
	'missing_view' => array(
		500, 'The view "{view_path}" does not exist.'),
	'variable_collision' => array(
		500, 'A variable collision occurred in {view_path}.'),
	'unsupported_format' => array(
		500, 'Views are not currently configured to support the "{format}" format.'),
);

// Database Group
$config['database'] = array(
	'database_error' => array(
		500, 'An error occurred with the database after issuing the query "{db_query}". The database returned the following error: "{db_error}".'),
);

// Router Group
$config['router'] = array(
	'no_route_matched' => array(
		404, 'The incoming URI "{uri}" did not match any of the configured routes.'),
	'missing_parameter' => array(
		500, 'The Router did not find the necessary "{parameter}" parameter in the route "{route}".'),
	'unknown_named_route' => array(
		500, 'The route named "{name}" could not be found. Ensure that it has been mapped to the Router.'),
	'found_leading_underscore' => array(
		404, 'The router determined that the incoming route&rsquo;s "{parameter}" parameter began with an underscore. Route segments cannot begin with an underscore because of certain security vulnerabilities.'),
		
);

// Configuration Group
$config['config'] = array(
	'missing_required' => array(
		500, 'A required configuration file, expected in "{path}" was not found.'),
	'unreadable_directory' => array(
		500, 'The configuration directory "{path}" does not appear to be readable.'),
);

// Some generic app ones
$config['app'] = array(
	'missing_class' => array(
		500, 'The {class_type} "{class}" could not be found and instantiated. It was expected in "{class_path}".'),
	'missing_file' => array(
		500, 'The file "{file}" could not be found for use by the "{class}" class.'),
	'unreadable_file' => array(
		500, 'The file "{file}" is not readable by the application.'),
	'unknown_filetype' => array(
		500, 'The file "{file}" does not appear to be a filetype that can be processed by the "{class}" class.'),
	'missing_gd_extension' => array(
		500, 'The GD extension must be installed to use the image processing functions.'),
	
);