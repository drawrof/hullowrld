<?php defined('ROOT') or die ('Restricted Access'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>pikonik - a very nice MVC framework</title>
		<style type='text/css'>
		
			body {
				padding: 0;
				margin: 0;
				font-family: Georgia, serif;
				background: #F6F6F6;
				font-size: 1.2em;
			}
			
			.container 
			{
				font-size: 1em;
			}
			
			.icon 
			{
				font-size: 1.1em;
				color: red;
				line-height: 1em;
				position: relative;
				top: 0.05em;
			}

			h1 
			{
				font-size: 1.7em;
				font-weight: normal;
				letter-spacing: -0.07em;
				line-height: 2em;
				color: #333;
				background: #F6F6F6 none repeat scroll 0 0;
				padding: 0.2em 0.7em 0.14em 0.7em;
				margin: 0;

				
			}
			
			h2
			{
				padding: 0.8em 1.2em;
				font-weight: normal;
				color: #333;
				margin: 0;
				font-size: 1.6em;

			}
			
			h2 .icon
			{
				font-size: 0.8em;
				top: -0.05em;
			} 
			
			.content
			{
				padding: 2em 2.8em;
				border: 1px solid #EAEAEA;
				background: white;
			}
			
			.code
			{
				font-family: Monaco,Monospace,Courier;
				color: #666;
				font-size: 0.9em;
			}
			
			.file
			{
				font-family: Monaco,Monospace,Courier;
				color: #666;
				font-size: 0.8em;
			}
			
			.caps 
			{
				text-transform: uppercase;
			}
			
			#trace
			{
				background: #F6F6F6;
				margin: 0px;
				color:#999;
				font-size:0.8em;
				line-height: 1.5em;
				border: 1px solid #EAEAEA;
				border-top: 1px solid #f0f0f0;
				border-bottom: 1px solid #f0f0f0;
			}
						
			#backtrace
			{
				padding: 2em 2em 2em 3.4em;
				margin: 0;
				background: white;
				border-top: 1px solid #EAEAEA;
				border-bottom: 1px solid #EAEAEA;
			}
			
			
			#backtrace li
			{
				margin-bottom: 5px;
				padding-left: 1px;
			}
			
			#backtrace span
			{
				color: #333;
			}
			
			#backtrace span span
			{
				font-family:Monaco,Courier,fixed-width !important;
				color: #666;
			}
			
			#backtrace span span span
			{
				color: #999;
			}
			
			#backtrace span span span.method
			{
				color: #356AA0;
			}
			
			#backtrace span span span.type
			{
				color: #999;
			}
			
			#backtrace span span span.class
			{
				color: #479647;
			}
			
			#backtrace span span span.arg
			{
				color: #C79810;
			}
			
			
		</style>
	</head>
	<body>
		<div class='container'>
			<h1>
				<span class='icon'>&times;</span> the page cannot be found
			</h1>
			<div class='content'>
				<p>
				The page you requested, &#8220;<span class='code'>/<?=Router::$uri?></span>&#8221;, was not found.
				</p>
			</div>
		</div>
	</body>
</html>
