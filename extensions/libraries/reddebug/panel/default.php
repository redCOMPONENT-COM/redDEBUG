<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Class RedDebugPanelDefault
 *
 * @since  1
 */
class RedDebugPanelDefault implements RedDebugPanelInterface
{
	/**
	 * @var $id
	 */
	private $id;

	/**
	 * @var $data
	 */
	private $data;

	/**
	 * @var $directory
	 */
	public $directory;

	/**
	 * __construct
	 *
	 * @param   mixed  $id  ID for panel
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * get
	 *
	 * @param   mixed  $key      Key
	 * @param   mixed  $default  Default Value
	 *
	 * @return null
	 */
	final public function get($key, $default = null)
	{
		return isset($this->data[$key]) ? $this->data[$key] : $default;
	}

	/**
	 * set
	 *
	 * @param   mixed  $key    Key to object
	 * @param   mixed  $value  Key to object
	 *
	 * @return void
	 */
	final public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * getPanel
	 *
	 * @return string
	 */
	public function getPanel()
	{
		ob_start();

		if (is_file($this->directory . "/bar/{$this->id}.panel.php"))
		{
			$data = $this->data;
			require $this->directory . "/bar/{$this->id}.panel.php";
		}

		return ob_get_clean();
	}

	/**
	 * getTab
	 *
	 * @return string
	 */
	public function getTab()
	{
		ob_start();

		if (is_file($this->directory . "/bar/{$this->id}.tab.php"))
		{
			$data = $this->data;
			require $this->directory . "/bar/{$this->id}.tab.php";
		}

		return ob_get_clean();
	}
}
