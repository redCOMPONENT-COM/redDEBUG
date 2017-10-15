<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$this->time = microtime(true) - RedDebugDebugger::getInstance()->getTime();
?>
<span title="Execution time">
	<span class="label"><?php echo str_replace(' ', ' ', number_format($this->time * 1000, 1, '.', ' ')) ?> ms</span>
</span>
