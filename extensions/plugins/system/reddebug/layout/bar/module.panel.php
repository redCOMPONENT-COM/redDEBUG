<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
?>
<h1>
	Modules
</h1>
<div>
	<table class="table">
		<thead>
			<tr>
				<td width="5%">ID</td>
				<td width="10%">Title</td>
				<td width="5%">position</td>
				<td width="5%">module</td>
				<td width="20%">content</td>
				<td width="20%">params</td>
			</tr>
		</thead>
		<?php foreach($data AS $type => $row): ?>
			<tr>
				<td><?php echo $row->id;?></td>
				<td><?php echo $row->title;?></td>
				<td><?php echo empty($row->position) ? 'none' : $row->position;?></td>
				<td>
					<?php echo $row->module;?>
					<table>
						<tr>
							<td>Time:</td>
							<td><?php echo number_format(($row->close_time - $row->start_time) * 100, 2); ?> ms</td>
						</tr>
						<tr>
							<td>memory:</td>
							<td><?php echo number_format(($row->start_memory - $row->close_memory) * 100, 2); ?> KB</td>
						</tr>
					</table>
				</td>
				<td>
					<textarea style="width: 200px; resize: none;"><?php echo htmlentities($row->content); ?></textarea>
				</td>
				<td>
					<?php
					if(count($row->params) > 0)
					{
						echo '<table class="table table-bordered table-params">';

						$data = RedDebugHelper::MultiArrayToSingleArray(json_decode($row->params), '$params');

						foreach ($data AS $key => $val)
						{
							echo '<tr><td>' . $key .'</td><td>' . htmlentities($val) . '</td></tr>';
						}

						echo '</table>';
					}

					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>