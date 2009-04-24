<?php defined('ROOT') or die ('Restricted Access'); ?>
<div class='content'>
	<p>
		The page you are viewing is located in &#8220;<span class='code'>/views/home/index.html.php</span>&#8221;. By default, the system renders a view with a 
		filename in the format of &#8220;<span class='code'>/views/controller/action.format.php</span>&#8221;. 
	</p>
</div>
<div id='trace'>
	<h2><span class='icon'>&#8634;</span> Backtrace</h2>
	<ol id="backtrace">
	<?=view::render_collection('backtrace',$backtrace)?>		
	</ol>
</div>
<div id='profile'>
	<p class='memory'>Rendered in <?=round((microtime(true) - $_SERVER['REQUEST_TIME']) * 1000)?>ms using <?=$memory_used?>MB of memory</p>
</div>