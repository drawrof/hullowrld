<?php defined('ROOT') or die ('Restricted Access');

$config['default'] = array(
	'persistent' => FALSE,
	'connection'=> array(
		'type'     => 'mysql',
		'user'     => '',
		'pass'     => '',
		'host'     => 'localhost',
		'port'     => FALSE,
		'socket'   => FALSE,
		'database' => 'test'
	),
	'character_set' => 'utf8',
	'table_prefix'  => '',
	'object'        => TRUE,
	'cache'         => FALSE,
	'escape'        => TRUE
);
	
?>