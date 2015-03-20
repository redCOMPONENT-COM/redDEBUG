<?php
if (empty($data))
{
	return;
}
?>
<span class="reddebug-errorbox">
	<span class="reddebug-label">
		<?php echo $sum = array_sum($data), $sum > 1 ? ' errors' : ' error' ?>
	</span>
</span>
