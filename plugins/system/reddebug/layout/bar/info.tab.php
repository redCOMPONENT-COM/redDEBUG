<?php

$this->time = microtime(true) - RedDebugDebugger::getInstance()->getTime();
?>
<span title="Execution time">
	<span class="tracy-label"><?php echo str_replace(' ', ' ', number_format($this->time * 1000, 1, '.', ' ')) ?> ms</span>
</span>
