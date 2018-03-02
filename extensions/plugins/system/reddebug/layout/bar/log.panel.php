<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
?>
<div>
	<table class="table">
		<?php foreach($data as $category => $entries): ?>
			<?php if (empty($entries)): ?>
				<?php continue; ?>
			<?php endif; ?>
			<tr>
				<td colspan="4"><h1 class="text-center"><?php echo $category ?> (<?php echo count($entries) ?>)</h1></td>
			</tr>
			<?php foreach ($entries as $entry): ?>
				<?php
				$log = $entry['entry'];
				$debugs = array_reverse($entry['debug']);
				?>
			<tr>
				<td width="10%">
					<?php echo $log->date ?>
				</td>
				<td width="10%">
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
				<td width="30%">
					<?php echo $log->message ?>
				</td>
				<td width="50%">
					<table class="table">
						<thead>
							<th>#</th>
							<th>Line</th>
							<th>Function</th>
						</thead>
						<tbody>
						<?php foreach ($debugs as $i => $debug): ?>
							<?php if (!isset($debug['line'])): ?>
								<?php continue; ?>
							<?php elseif ($debug['class'] == 'JLog'): ?>
								<?php break; ?>
							<?php else: ?>
							<tr>
								<td width="10px"><?php echo $i + 1; ?></td>
								<td width="30%"><?php echo '<strong>' . $debug['class'] . '</strong>' . $debug['type'] . $debug['function'] ?>()</td>
								<td width="auto"><?php echo $debug['file'] ?>:<?php echo $debug['line'] ?></td>
							</tr>
							<?php endif; ?>
						<?php endforeach; ?>
						</tbody>
					</table>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
	</table>
</div>
