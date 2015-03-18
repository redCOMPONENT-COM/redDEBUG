<h1><?php echo $this->title; ?></h1>
<table class="table default-panel">
	<?php foreach($data AS $key => $val): ?>
		<tr>
			<td>
				<?php echo htmlspecialchars($key, null, 'UTF-8'); ?>
			</td>
			<td>
				<?php echo htmlspecialchars($val, null, 'UTF-8'); ?>
			</td>
		</tr>
	<?php endforeach; ?>
</table>