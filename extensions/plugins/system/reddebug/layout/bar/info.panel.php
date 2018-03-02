<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$cIT = count(get_declared_classes()) . ' + ' . count(get_declared_interfaces()) . ' + '
	. (PHP_VERSION_ID >= 50400 ? count(get_declared_traits()) : '0');

$info = array_filter(
	array_merge(
		array(
			'Execution time'                => str_replace(' ', ' ', number_format($this->time * 1000, 1, '.', ' ')) . ' ms',
			'Peak of allocated memory'      => str_replace(' ', ' ', number_format(memory_get_peak_usage() / 1000000, 2, '.', ' ')) . ' MB',
			'Included files'                => count(get_included_files()),
			'Classes + interfaces + traits' => $cIT,
			'Your IP'                       => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
			'Server IP'                     => isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : null,
			'PHP'                           => PHP_VERSION,
			'Server'                        => isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : null
		),
		(array) $this->data
	)
);
?>
<h1>System info</h1>
<div id="redDebug-info">
	<table class="table table-bordered">
		<?php foreach ($info as $key => $val): ?>
			<tr>
				<td><?php echo htmlspecialchars($key, null, 'UTF-8') ?></td>
				<td nowrap><?php echo htmlspecialchars($val, null, 'UTF-8') ?></td>
			</tr>
		<?php endforeach ?>
		<tr>
			<td>
				Copyright
			</td>
			<td>
				Copyright (C) 2015 - 2018 <a target="_blank" href="http://redcomponent.com">redcomponent.com</a>. All rights reserved.
			</td>
		</tr>
	</table>
</div>
