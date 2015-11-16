<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
?>
<div>
	<table class="table">
		<?php foreach($data as $category => $logs): ?>
			<?php if (empty($logs)): ?>
				<?php continue; ?>
			<?php endif; ?>
			<tr>
				<td colspan="3"><h1 class="text-center"><?php echo $category ?> (<?php echo count($logs) ?>)</h1></td>
			</tr>
			<?php foreach ($logs as $log): ?>
			<tr>
				<td width="15%">
					<?php echo $log->date ?>
				</td>
				<td width="15%">
					<?php
					switch ($log->priority):
						case JLog::EMERGENCY:
							?>
							<p class="text-error">Emergency</p>
							<?php
							break;

						case JLog::ALERT:
							?>
							<p class="text-error">Alert</p>
							<?php
							break;

						case JLog::CRITICAL:
							?>
							<p class="text-error">Critical</p>
							<?php
							break;

						case JLog::ERROR:
							?>
							<p class="text-error">Error</p>
							<?php
							break;

						case JLog::WARNING:
							?>
							<p class="text-warning">Warning</p>
							<?php
							break;

						case JLog::NOTICE:
							?>
							<p class="text-warning">Notice</p>
							<?php
							break;

						case JLog::INFO:
							?>
							<p class="text-infor">Information</p>
							<?php
							break;

						default:
						case JLog::DEBUG:
							?>
							<p class="text-muted">Debug</p>
							<?php
							break;
					endswitch;
					?>
				</td>
				<td width="70%">
					<?php echo $log->message ?>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</table>
</div>