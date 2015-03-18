<h1>Plugin</h1>
<table class="table">
	<?php foreach($data AS $type => $plugins): ?>
		<tr>
			<td colspan="4"><h1 class="text-center"><?php echo $type;?></h1></td>
		</tr>
		<?php foreach($plugins AS $pluginname => $row): ?>
			<tr id="view">
				<td><?php echo $type;?></td>
				<td><?php echo $pluginname; ?></td>
				<td><?php echo $row['name']; ?></td>
				<td>
					<table class="table">
						<thead>
						<tr>
							<td style="width: 120px;">Method</td>
							<td style="width: 20px;">Count</td>
						</tr>
						</thead>
						<tbody>
						<?php foreach($row['method'] AS $name => $info): ?>
							<tr>
								<td><?php echo $name; ?></td>
								<td><?php echo $info['count']; ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endforeach; ?>
</table>