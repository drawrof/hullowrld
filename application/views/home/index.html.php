<div class='content'>
	<p><?=render_partial('partial') ?></p>
	<p class='memory'>Peak Memory Usage: <?=$memory_used?>MB</p>
</div>
<div id='trace'>
	<h2><span class='icon'>&#8634;</span> Backtrace</h2>
	<ol id="backtrace">
	<?=render_collection('collection',$backtrace)?>		
	</ol>
</div>