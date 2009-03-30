<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?=$title?></title>
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
				color:red;
				font-size:1.1em;
				line-height:1em;
				position:relative;
				top:0.05em;
			}

			h1 
			{
				font-size: 1.7em;
				font-weight: normal;
				letter-spacing: -0.07em;
				line-height: 2em;
				color: #333;
				background: #F6F6F6 none repeat scroll 0 0;
				padding: 0.2em 0.8em;
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
			
			h3
			{
				color:#999999;
				font-family:Arial,Helvetica;
				font-size:0.6em;
				margin-top:1em;
				text-transform:uppercase;
				margin-bottom:-1em;
			}
			
			.content
			{
				padding: 1em 2.5em;
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
			
			.memory
			{
				color:#999;
				font-family:Arial;
				font-size:0.6em;
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
			
			.method
			{
				color: #356AA0 !important;
			}
			
			.type
			{
				color: #999 !important;
			}
			
			.class
			{
				color: #479647 !important;
			}
			
			.arg
			{
				color: #C79810 !important;
			}
				
			.array-key
			{
				color: #D15600 !important;
				font-style: oblique !important;
			}
			
			.array-val
			{
				color: #C79810 !important;
			}
			
			p
			{
				line-height:1.8em;
				margin-bottom:1.5em;
			}
			
			#profile
			{
				padding: 0.5em 1.5em 0.5em 2.7em
			}
			
		</style>
	</head>
	<body>
		<div class='container'>
			<h1><span class='icon'>*</span> hellowrld</h1>
			<?=render()?>
	</body>
</html>