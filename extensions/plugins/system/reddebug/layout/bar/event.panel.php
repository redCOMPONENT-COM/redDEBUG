<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * @var   array  $data  Data
 */
?>
<h1>Event</h1>
<div>
	<table class="table">
		<thead>
		<tr>
			<td width="10%">Event Type</td>
			<td width="10%">Event</td>
			<td width="40%">Plugins</td>
		</tr>
		</thead>
		<tbody>

		<?php foreach ($data AS $type => $events): ?>
			<?php foreach ($events AS $event => $info): ?>
				<tr>
					<td>
						<?php echo $type;?>
					</td>
					<td>
						<?php echo $event;?>
					</td>
					<td>
						<table class="table">
							<thead>
								<tr>
									<td>plugin</td>
									<td>Args</td>
									<td>Values</td>
									<td>Time</td>
									<td>Memory</td>
								</tr>
							</thead>
							<?php foreach ($info AS $key => $row): ?>
								<?php $marks = $row->profile->getMarks(); ?>
								<tr>
									<td>
										<?php echo $row->plugin; ?>
									</td>
									<td>
										<?php
										if (count($row->args) > 0):
											$data = RedDebugHelper::multiArrayToSingleArray($row->args, '$args');
										?>
											<table class="table table-bordered table-args">
											<?php foreach ($data AS $key2 => $val): if (empty($val)): continue;
											endif; ?>
												<tr>
													<td><?php echo $key2; ?></td>
													<td title="<?php echo htmlentities($val);?>"><?php echo htmlentities(substr($val, 0, 50)); ?></td>
												</tr>
											<?php endforeach; ?>
											</table>
										<?php endif; ?>
									</td>
									<td>
										<?php
										if (count($row->value) > 0):
											$data = RedDebugHelper::multiArrayToSingleArray($row->value, 'Array');
											?>
											<table class="table table-bordered table-args">
												<?php foreach ($data AS $key3 => $val): if (empty($val)): continue;
												endif; ?>
													<tr>
														<td><?php echo $key3; ?></td>
														<td
															title="<?php echo htmlentities($val);?>"><?php echo htmlentities(substr($val, 0, 50)); ?>
														</td>
													</tr>
												<?php endforeach; ?>
											</table>
										<?php endif; ?>
									</td>
									<td>
										<?php echo number_format(($marks[1]->totalTime - $marks[0]->totalTime) * 1000, 1, '.', ''); ?>
									</td>
									<td>
										<?php echo number_format(($marks[1]->totalMemory - $marks[0]->totalMemory) * 1000000, 1, '.', ''); ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</table>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
