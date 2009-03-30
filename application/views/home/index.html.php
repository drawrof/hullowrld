<div class='content'>
	<h3>Partials</h3>
	<p><?=render_partial('partial') ?></p>
	<h3>Collections</h3>
	<p>The backtrace below was rendered with a call to &#8220;<span class='code'><span class='method'>render_collection</span>(<span class='arg'>'backtrace'</span>,<span class='arg'>debug_backtrace()</span>)</span>&#8221; which looped through the data passed to it, rendering the partial located in &#8220;<span class='code'>/views/home/_backtrace.html.php</span>&#8221; for each element in the array.</p>
</div>
<div id='trace'>
	<h2><span class='icon'>&#8634;</span> Backtrace</h2>
	<ol id="backtrace">
	<?=render_collection('backtrace',debug_backtrace())?>		
	</ol>
</div>
<div id='profile'>
	<p class='memory'>Rendered in <?=round((microtime(true) - APP_START) * 1000)?>ms using <?=$memory_used?>MB of memory</p>
</div>