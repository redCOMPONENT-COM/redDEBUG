<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;
?>
<table class="table">
	<thead>
		<tr>
			<th><?php echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_INDEX');?></th>
			<th><?php echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_PATH');?></th>
			<th><?php echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE');?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="3">&nbsp;</td>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach ($data AS $index => $file): ?>
			<tr>
				<td style="width: 50px;">
					<?php echo $index; ?>
				</td>
				<td nowrap>
					<?php echo $file; ?>
				</td>
				<td style="width: 100px;">
					<?php
					if (stripos($file, realpath(JPATH_LIBRARIES)) === 0):
						echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE_LIBRARIES');
					elseif (stripos($file, realpath(JPATH_ROOT . '/components')) === 0):
							echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE_COMPONENTS');
					elseif (stripos($file, realpath(JPATH_ROOT . '/administrator/components')) === 0):
							echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE_ADMIN_COMPONENTS');
					elseif (stripos($file, realpath(JPATH_BASE . '/templates')) === 0):
							echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE_TEMPLATES');
					elseif (stripos($file, realpath(JPATH_BASE . '/modules')) === 0):
							echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE_MODULES');
					elseif (stripos($file, realpath(JPATH_ROOT . '/plugins')) === 0):
							echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE_PLUGINS');
					elseif (stripos($file, realpath(JPATH_BASE . '/includes')) === 0):
							echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE_INCLUDES');
					elseif (stripos($file, realpath(JPATH_BASE . '/language')) === 0):
							echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE_LANGUAGE');
					else:
							echo JText::_('PLG_SYSTEM_REDDEBUG_INCLUDES_FILES_TYPE_OTHER');
					endif;
					?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
