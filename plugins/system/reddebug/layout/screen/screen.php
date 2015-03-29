<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

$trace = $exception->getTrace();

$buffer = JFactory::getApplication()->getBody(false);
ob_start();
?>
<style type="text/css">
	<?php echo file_get_contents(__DIR__ . '/screen.css'); ?>
</style>
<div id="redscreen">
	<div id="header">
		<h1>
			<?php echo htmlspecialchars($title), ($exception->getCode() ? ' #' . $exception->getCode() : '') ?>
		</h1>
		<p>
			<?php echo htmlspecialchars($exception->getMessage()) ?>
			<a href="https://www.google.com/search?q=<?php
				echo urlencode($title . ' ' . preg_replace('#\'.*\'|".*"#Us', '', $exception->getMessage()))
			?>">search</a>
		</p>
	</div>
	<div id="redContent">
		<div class="row">
			<div class="col-xs-2">
				<a class="reddebug-action">
					<h2><?php echo JText::_('PLG_SYSTEM_REDDEBUG_SOURCE_FILE'); ?></h2>
				</a>
			</div>
			<div class="col-xs-10 info">
				<table class="table table-bordered">
					<tr>
						<th><h2><?php echo JText::sprintf('PLG_SYSTEM_REDDEBUG_FILE_PATH', $exception->getFile(), $exception->getLine()); ?></h2></th>
					</tr>
					<tr>
						<td>
							<div class="redDebugCodeBox">
								<?php echo RedDebugHelper::highlightFile($exception->getFile(), $exception->getLine(), 30); ?>
							</div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-2">
				<a class="reddebug-action">
					<h2><?php echo JText::_('PLG_SYSTEM_REDDEBUG_STACK_TRACE'); ?></h2>
				</a>
			</div>
			<div class="col-xs-10 info" style="display: none;">
				<table class="table table-bordered">
					<tr>
						<th>
							<?php echo JText::_('PLG_SYSTEM_REDDEBUG_STACK_TRACE_NO'); ?>
						</th>
						<th>
							<?php echo JText::_('PLG_SYSTEM_REDDEBUG_STACK_TRACE_FILE'); ?>
						</th>
						<th>
							<?php echo JText::_('PLG_SYSTEM_REDDEBUG_STACK_TRACE_LINE'); ?>
						</th>
						<th>
							<?php echo JText::_('PLG_SYSTEM_REDDEBUG_STACK_TRACE_FUNCTION'); ?>
						</th>
					</tr>
				<?php foreach ($trace as $i => $t) : ?>
					<tr>
						<td>
							<?php echo $i ?>
						</td>
						<td>
							<?php echo $t['file'] ?>
						</td>
						<td>
							<?php echo $t['line'] ?>
						</td>
						<td>
							<?php echo $t['function'] ?>
						</td>
					</tr>
					<tr>
						<td colspan="4">
							<div class="redDebugCodeBox">
								<?php echo RedDebugHelper::highlightFile($t['file'], $t['line'], 10); ?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
</div>
<?php
$output = ob_get_clean();

if (empty($buffer))
{
		$buffer = <<<HTML
	<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr">
		<html>
			<head>
				<title></title>
			</head>
			<body>
			</body>
		</html>
HTML;
	echo $buffer;
}

if (!stripos($buffer, 'jquery'))
{
	echo '<script type="text/javascript" src="' . JUri::root() . '/media/jui/js/jquery.min.js"></script>';
	$this->jQuery = true;
}
?>
<script type="text/javascript">
	jQuery('document').ready(function(){
		jQuery('body').html(<?php echo json_encode($output);?>);
		jQuery('body')[0].className= 'redDebug';
		jQuery('body').attr('id', 'redDebugBody');

		jQuery('#redContent .reddebug-action').click(function(){
			jQuery(this).parents('.row').find('>.info').toggle();
		});
	});
</script>
