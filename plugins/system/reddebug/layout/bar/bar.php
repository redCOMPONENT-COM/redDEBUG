<?php ob_start() ?>
<style id="redDebug-style" class="redDebug">
	<?php echo file_get_contents(__DIR__ . '/bar.css') ?>
</style>

<script id="redDebug-script">
	<?php echo file_get_contents(__DIR__ . '/bar.js') ?>
</script>
<?php
/**
 *  Panel
 */
?>
<?php foreach ($panels as $panel): if (!empty($panel['previous'])) continue ?>
	<div class="reddebug-panel" id="redDebug-panel-<?php echo $panel['id'] ?>">
		<?php if ($panel['panel']): echo $panel['panel']; endif; ?>
	</div>
<?php endforeach ?>
<?php
/**
 * Menu bar
 */
?>
<div id="redDebug-bar">
	<ul>
		<li>
			<a title="debug bar">
				RedDebug
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
<?php $output = ob_get_clean(); ?>
<?php
/**
 * Add data to javascript
 */
?>
<script type="text/javascript">
	(function() {
		window.addEventListener('load', function() {
			var debug = document.body.appendChild(document.createElement('div'));
			debug.id = 'redDebug';
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
