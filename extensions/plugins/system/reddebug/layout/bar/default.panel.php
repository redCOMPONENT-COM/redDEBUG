<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
?>
<h1>
	<?php echo $this->title; ?>
</h1>
<table class="table default-panel table-bordered">
	<?php foreach($data AS $key => $val): ?>
		<tr>
			<td>
				<?php echo htmlspecialchars($key, null, 'UTF-8'); ?>
			</td>
			<td nowrap>
				<?php echo htmlspecialchars($val, null, 'UTF-8'); ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>