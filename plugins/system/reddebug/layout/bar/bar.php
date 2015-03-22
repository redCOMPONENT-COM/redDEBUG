<?php ob_start(); ?>
<style id="redDebug-style" class="redDebug">
	<?php echo file_get_contents(__DIR__ . '/../bootstrap.css'); ?>
	<?php echo file_get_contents(__DIR__ . '/bar.css'); ?>
</style>

<script id="redDebug-script">
	<?php echo file_get_contents(__DIR__ . '/../modal.js'); ?>
	<?php echo file_get_contents(__DIR__ . '/bar.js'); ?>
</script>
<?php
/**
 *  Panel
 */
?>
<div id="redDebug-panel">
	<?php foreach ($panels as $panel): if (!empty($panel['previous'])) continue; ?>
		<?php if(!empty($panel['panel']) && $panel['panel'] != null): ?>
			<div aria-labelledby="" aria-hidden="true" class="modal reddebug-panel <?php echo trim($panel['class']) ?>" id="redDebug-panel-<?php echo $panel['id'] ?>">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
						<h4 class="modal-title" id="modal-title"><?php echo trim($panel['tab']) ?>
							<a class="anchorjs-link" href="#modal-title">
								<span class="anchorjs-icon"></span>
							</a>
						</h4>
					</div>
					<div class="modal-body">
						<?php if ($panel['panel']): echo $panel['panel']; endif; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach ?>
</div>
<?php
/**
 * Menu bar
 */
?>
<div id="redDebug-bar">
	<ul>
		<li>
			<a href="http://redcomponent.com" title="redcomponent" target="_blank">
				<img src="data:image/png;base64,<?php echo base64_encode(file_get_contents(__DIR__ . '/../reddebug_65x20.png')); ?>" alt="*Logo*" />
			</a>
		</li>
		<?php foreach ($panels as $panel): if (!$panel['tab']) continue; ?>
			<?php if (!empty($panel['previous'])): echo '</ul><ul class="reddebug-previous">'; endif; ?>
			<li>
				<?php if ($panel['panel']): ?>
					<a href="#" rel="<?php echo $panel['id'] ?>">
						<?php echo trim($panel['tab']) ?>
					</a>
				<?php else: ?>
					<span>
						<?php echo trim($panel['tab']) ?>
					</span>
				<?php endif; ?>
			</li>
		<?php endforeach ?>
	</ul>
</div>
<?php
$output = ob_get_clean();
$buffer = JFactory::getApplication()->getBody(false);

// So we get jQuery
if (!stripos($buffer, 'jquery') && RedDebugDebugger::getInstance()->getRedScreen()->jQuery == false)
{
	echo '<script type="text/javascript" src="' . JUri::root() . '/media/jui/js/jquery.min.js"></script>';
}

/**
 * Add data to javascript
 */
?>
<script type="text/javascript">
	(function() {
		window.addEventListener('load', function() {
			var debug = document.body.appendChild(document.createElement('div'));
			debug.id = 'redDebug';
			debug.className = 'redDebug';
			debug.innerHTML = <?php echo json_encode(RedDebugHelper::fixEncoding($output)) ?>;

			for (var i = 0, scripts = debug.getElementsByTagName('script'); i < scripts.length; i++) {
				(window.execScript || function(data) {
					window['eval'].call(window, data);
				})(scripts[i].innerHTML);
			}

			debug.style.display = 'block';
		});
	})();
</script>
