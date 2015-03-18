<?php
/**
 * @copyright  Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

/**
 * Class RedDebugPanelList
 *
 * @since  1
 */
class RedDebugPanelList implements RedDebugPanelInterface
{
	/**
	 * @var string
	 */
	private $title;

	/**
	 * @var $data
	 */
	private $data;

	/**
	 * @var $directory
	 */
	public $directory;

	/**
	 * @var $directory
	 */
	public $layout;

	/**
	 * @var int
	 */
	public $count;

	/**
	 * __construct
	 *
	 * @param   string  $title   Title
	 * @param   mixed   $data    Data
	 * @param   int     $count   Count
	 * @param   string  $layout  Layout
	 */
	public function __construct($title, $data=null, $count = null, $layout=null)
	{
		$this->title	= $title;
		$this->count	= $count;
		$this->data		= $data;
		$this->layout	= $layout;
	}

	/**
	 * get
	 *
	 * @param   mixed  $key      Key
	 * @param   mixed  $default  Default Value
	 *
	 * @return RedDebugPanelList
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
	 * getTab
	 *
	 * @return string
	 */
	public function getTab()
	{
		return "{$this->title}" . ($this->count == null ? '' : " ({$this->count})");
	}

	/**
	 * Renders HTML code for custom panel.
	 *
	 * @return string
	 */
	public function getPanel()
	{
		ob_start();

		if (is_file($this->directory . "/bar/{$this->layout}.panel.php"))
		{
			$data = $this->data;
			require $this->directory . "/bar/{$this->layout}.panel.php";
		}

		return ob_get_clean();
	}
}
