<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

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
