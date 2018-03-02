<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
?>
<h1>
	<?php echo $this->title; ?>
</h1>
<table class="table default-panel">
	<?php foreach($data AS $key => $row): ?>
		<tr>
			<td><?php echo $key; ?></td>
			<td>
				<table style="width: 500px;">
					<?php foreach($row AS $key => $val): ?>
						<tr>
							<td><?php echo $key; ?></td>
							<td><?php echo $val; ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</td>
		</tr>
	<?php endforeach; ?>
</table>