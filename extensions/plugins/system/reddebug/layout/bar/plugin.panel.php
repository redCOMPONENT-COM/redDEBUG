<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
?>

<h1>
	Plugin
</h1>
<table class="table">
	<?php foreach ($data AS $type => $plugins): ?>
		<tr>
			<td colspan="4"><h1 class="text-center"><?php echo $type;?></h1></td>
		</tr>
		<?php foreach ($plugins AS $pluginname => $row): ?>
			<tr id="view">
				<td style="width: 20%"><?php echo $type;?></td>
				<td style="width: 50%">
					<?php echo $pluginname; ?>
					<br />
					<?php echo RedDebugHelper::findJoomlaClassFile($pluginname); ?>
				</td>
				<td style="text-align: right" nowrap="nowrap">
					<table class="table" style="width: 450px">
						<thead>
						<tr>
							<td nowrap style="width: 150px;">Method</td>
							<td style="width: 100px;">Count</td>
							<td style="width: 100px;">time</td>
							<td style="width: 100px;">memory</td>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($row AS $name => $info): ?>
							<tr>
								<td><?php echo $name; ?></td>
								<td><?php echo $info['count']; ?></td>
								<td><?php echo number_format($info['time'] * 1000, 1, '.', ' '); ?></td>
								<td><?php echo number_format($info['memory'] * 1000000, 1, '.', ' '); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endforeach; ?>
</table>
