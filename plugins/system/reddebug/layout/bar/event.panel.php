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

		<?php foreach($data AS $type => $events): ?>
			<?php foreach($events AS $event => $info): ?>
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
							<?php foreach($info AS $key => $row): ?>
								<?php $marks = $row->profile->getMarks(); ?>
								<tr>
									<td>
										<?php echo $row->plugin; ?>
									</td>
									<td>
										<textarea style="height: 80px; width: 100%; resize: none;"><?php print_r($row->args); ?></textarea>
									</td>
									<td>
										<?php echo $row->value; ?>
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