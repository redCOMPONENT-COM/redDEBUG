<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
?>
<h1>
	Errors
</h1>
<div class="reddebug-inner">
	<table class="table">
	<?php foreach ($data as $item => $count): ?>
		<?php
			list($file, $line, $message) = explode('|', $item, 3);
		?>
		<tr>
			<td class="reddebug-right">
				<?php echo $count ? "$count\xC3\x97" : '' ?>
			</td>
			<td>
				<?php
					echo htmlspecialchars(
						$message,
						ENT_IGNORE,
						'UTF-8'
					);
					echo ' in ' . $file . ' line ' . $line;
				?>
			</td>
		</tr>
	<?php endforeach ?>
	</table>
</div>
