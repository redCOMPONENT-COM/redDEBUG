<?php
if (empty($data))
{
	return;
}
?>
<span style="
	display: block;
	background: red;
	color: white;
	font-weight: bold;
">
	<span class="reddebug-label">
		<?php echo $sum = array_sum($data), $sum > 1 ? ' errors' : ' error' ?>
	</span>
</span>
