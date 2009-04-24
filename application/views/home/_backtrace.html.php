<?php defined('ROOT') or die ('Restricted Access'); ?>
<li>
	<span>
		<strong>File:</strong> <?=isset($backtrace['file']) ? $backtrace['file'] : 'None' ?><br />
		<span class="code">
			<span>&#8618;</span> 
			<? if (isset($backtrace['class'])): ?>
			<span class='class'><?=$backtrace['class']?></span><span class='type'><?=$backtrace['type']?></span><span class='method'><?=$backtrace['function']?></span>()
			<? else: ?>
			<span class='method'><?=$backtrace['function']?></span>()
			<? endif ?>
		</span>
	</span>
</li>