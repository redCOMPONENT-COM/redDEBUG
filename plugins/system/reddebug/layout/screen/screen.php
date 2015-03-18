<?php ob_start(); ?>
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
			<a href="https://www.google.com/search?sourceid=tracy&amp;q=<?php echo urlencode($title . ' ' . preg_replace('#\'.*\'|".*"#Us', '', $exception->getMessage())) ?>">search</a>
		</p>
	</div>
</div>
<?php $output = ob_get_clean(); ?>
<script type="text/javascript">
	window.addEventListener('load', function() {
		document.getElementsByTagName('body')[0].innerHTML = <?php echo json_encode($output);?>;
		document.getElementsByTagName('body')[0].className = '';
		document.getElementsByTagName('body')[0].id = 'redDebugBody';
	}
</script>