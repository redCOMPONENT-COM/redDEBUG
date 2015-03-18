<h1>Modules</h1>
<div>
	<table class="table">
		<thead>
			<tr>
				<td width="5%">Show Title</td>
				<td width="10%">Title</td>
				<td width="5%">position</td>
				<td width="5%">module</td>
				<td width="20%">content</td>
				<td width="20%">params</td>
			</tr>
		</thead>
		<?php foreach($data AS $type => $row): ?>
			<tr>
				<td><?php echo $row->showtitle;?></td>
				<td><?php echo $row->title;?></td>
				<td><?php echo empty($row->position) ? 'none' : $row->position;?></td>
				<td><?php echo $row->module;?></td>
				<td>
					<textarea style="width: 200px; resize: none;"><?php echo htmlentities($row->content); ?></textarea>
				</td>
				<td>
					<textarea style="width: 200px; resize: none;"><?php print_r(json_decode($row->params)); ?></textarea>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>