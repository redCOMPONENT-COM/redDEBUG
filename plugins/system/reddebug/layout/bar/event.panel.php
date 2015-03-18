<h1>Event</h1>
<div>
	<table class="table">
		<thead>
		<tr>
			<td width="10%">Event Type</td>
			<td width="10%">Event</td>
			<td width="40%">Plugins</td>
			<td width="40%">params</td>
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
								<td style="width: 150px;">Plugin</td>
								<td style="width: 20px;">Count</td>
							</tr>
							</thead>
							<?php foreach($info['class'] AS $class => $count): ?>
								<tr>
									<td><?php echo $class;?></td>
									<td><?php echo $count;?></td>
								</tr>
							<?php endforeach; ?>
						</table>
					</td>
					<td>
						<textarea style="width: 400px; resize: none;"><?php print_r($info['args']); ?></textarea>
					</td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>