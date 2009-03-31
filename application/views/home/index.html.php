<div class='content'>
	<h3>Views</h3>
	<p>
		The page you are viewing is located in &#8220;<span class='code'>/views/home/index.html.php</span>&#8221;. By default, the system renders a view with a 
		filename in the format of &#8220;<span class='code'>/views/controller/action.format.php</span>&#8221;. 
	</p>
	
	<h3>Passing Data to the View</h3>
	<p>
		Data is passed to the view by simply adding an instance variable to the controller. For example, the action that controls this page defines an instance variable <span class='code'><span class='class'>$this</span><span class='type'>-></span><span class='method'>memory_used</span></span>. This variable is then available to the view via the variable <span class='method'>$memory_used</span>. If you would like a variable available to all views, layouts, partials, and collections make the first letter of the variable uppercase.
	</p>
	
	<h3>Layouts</h3>
	<p>
		This page is wrapped in a special layout view in located in &#8220;<span class='code'>/views/layouts/home.html.php</span>&#8221;. A call to 
		&#8220;<span class='code'><span class='class'>view</span><span class='type'>::</span><span class='method'>render</span>()</span>&#8221; within that layout specifies where you would like your page specific view to go. Setting the instance variable <span class='code'><span class='class'>$this</span><span class='type'>-></span><span class='method'>layout</span></span> equal to the name of the layout file in your controller lets the system know that you'd like the page rendered with a layout.
	</p>
	
	<h3>Partials</h3>
	<p><?=view::render_partial('partial') ?></p>
	
	<h3>Collections</h3>
	<p>The backtrace below was rendered with a call to 
		&#8220;<span class='code'><span class='class'>view</span><span class='type'>::</span><span class='method'>render_collection</span>(<span class='arg'>'backtrace'</span>,<span class='arg'>$backtrace</span>)</span>&#8221;
		which looped through the data passed to it, rendering the partial located in &#8220;<span class='code'>/views/home/_backtrace.html.php</span>&#8221; 
		for each element in the array.
	</p>
</div>
<div id='trace'>
	<h2><span class='icon'>&#8634;</span> Backtrace</h2>
	<ol id="backtrace">
	<?=view::render_collection('backtrace',$backtrace)?>		
	</ol>
</div>
<div id='profile'>
	<p class='memory'>Rendered in <?=round((microtime(true) - APP_START) * 1000)?>ms using <?=$memory_used?>MB of memory</p>
</div>