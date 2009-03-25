<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>hullowrld - a very nice MVC framework</title>
		<style type='text/css'>
					
			.red 
			{
				color: red;
			}
			
			.mvc 
			{
				float: right;
				font-weight: normal;
				font-size: 0.8em;
				letter-spacing: 0;
				margin-top: 2.0em;
				margin-right: 3.4em;
				letter-spacing: -0.05em;
				color: #666;
			}
			
			h1 
			{
				font-size: 2em;
				font-weight: normal;
				letter-spacing: -0.07em;
				line-height: 1em;
				color: #333;
				background: #F6F6F6 none repeat scroll 0 0;
				padding: 0em 0em 0.7em 0.7em;
				margin: 0;
			}

			.memory 
			{
				font-size: 0.8em;
				color: #666;
			}
		
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
				border-bottom: 1px solid #F0F0F0;
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
				font-size: 2em;
				font-weight: normal;
				letter-spacing: -0.07em;
				line-height: 2em;
				color: #333;
				background: #F6F6F6 none repeat scroll 0 0;
				padding: 0em 0.9em 0.14em 1.4em;
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
				padding: 1em 2.8em 1.4em;
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

			span.method
			{
				color: #356AA0;
			}

			span.type
			{
				color: #999;
			}

			span.class
			{
				color: #479647;
			}

			span.arg
			{
				color: #C79810;
			}

		</style>
	</head>
	<body>
		<div class='container'>
			<span class='mvc'>a simple PHP framework</span>
			<h1>hull<span class='red'>o</span>world</h1>
			<div class='content'>
				<?=render(); ?>
			</div>
		</div>
	</body>
</html>