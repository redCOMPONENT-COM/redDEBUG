<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

if (empty($data))
{
	return;
}

$sum = array_sum($data)

?>
<span class="reddebug-errorbox">
	<span class="reddebug-label">
		<?php echo $sum > 1 ? ' errors' : ' error' ?>
	</span>
</span>
