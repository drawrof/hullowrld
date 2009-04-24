<?php defined('ROOT') or die ('Restricted Access');
 
// This is the root route.
$config[''] = array(
	'controller' => 'home',
	'name' => 'root'
);

// Standard "MVC" routing
// The action "index" is automatically used if 
// no action parameter is found in the route
$config[':controller'] = array();
$config[':controller/:action'] = array();
$config[':controller/:action/:id'] = array();


?>