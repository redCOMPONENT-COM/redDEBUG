<h1><?php echo $this->title; ?></h1>
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