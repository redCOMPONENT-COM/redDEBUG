<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

JLoader::import('reddebug.library');

/**
 * Class JFormFieldIp
 *
 * @since  1
 */
class JFormFieldIp extends JFormField
{
	protected $type = 'Ip';

	/**
	 * getInput
	 *
	 * @return void
	 */
	public function getInput()
	{
		JHtml::_('jquery.framework');
		JFactory::getDocument()->addScript(JUri::root() . 'plugins/system/reddebug/form/fields/ip.js');
		JFactory::getDocument()->addStyleSheet(JUri::root() . 'plugins/system/reddebug/form/fields/ip.css');

		$html = '<ul id="ip_fields" class="unstyled">';

		if (empty($this->value))
		{
			$this->value = array('');
		}

		$html .= '<ul class="list unstyled">';

		foreach ($this->value AS $key => $val)
		{
			$html .= '<li class="ip-row">';
			$html .= '<input type="text" name="' . $this->name . '" value="' . $val . '"/>';
			$html .= '&nbsp;<a class="reddebug_ip_remove" href="#">' . (JText::_($this->element['remove_label']->__toString())) . '</a>';
			$html .= '</li>';
		}

		$html .= '</ul>';
		$html .= '<li>';
		$html .= '<a class="reddebug_ip_add" href="#">' . JText::_($this->element['add_label']->__toString()) . '</a>';
		$html .= '</li>';
		$html .= '</ul>';

		return $html;
	}
}