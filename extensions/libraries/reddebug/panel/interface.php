<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Interface RedDebugPanelInterface
 *
 * @since  1.0.0
 */
interface RedDebugPanelInterface
{
	/**
	 * get
	 *
	 * @param   mixed  $key      Key
	 * @param   mixed  $default  Default Value
	 *
	 * @return null
	 */
	public function get($key, $default = null);

	/**
	 * set
	 *
	 * @param   mixed  $key    Key to object
	 * @param   mixed  $value  Key to object
	 *
	 * @return void
	 */
	public function set($key, $value);

	/**
	 * getPanel
	 *
	 * @return string
	 */
	public function getPanel();

	/**
	 * getTab
	 *
	 * @return string
	 */
	public function getTab();
}
