<h1>Errors</h1>
<div class="reddebug-inner">
	<table>
	<?php foreach ($data as $item => $count): ?>
		<?php
			list($file, $line, $message) = explode('|', $item, 3);
		?>
		<tr>
			<td class="reddebug-right">
				<?php echo $count ? "$count\xC3\x97" : '' ?>
			</td>
			<td>
				<pre>
					<?php
						echo htmlspecialchars(
							$message,
							ENT_IGNORE,
							'UTF-8'
						);
						echo ' in ' . $file . ' line ' . $line;
					?>
				</pre>
			</td>
		</tr>
	<?php endforeach ?>
	</table>
</div>
