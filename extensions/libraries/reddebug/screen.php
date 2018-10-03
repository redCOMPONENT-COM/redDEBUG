<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Class RedDebugScreen
 *
 * @since  1.0.0
 */
class RedDebugScreen
{
	/**
	 * @var    array
	 * @since  1.0.0
	 */
	public $info = array();

	/**
	 * @var    array
	 * @since  1.0.0
	 */
	private $panels = array();

	/**
	 * @var    string
	 * @since  1.0.0
	 */
	private $directory = null;

	/**
	 * @var    boolean
	 * @since  1.0.0
	 */
	public $jQuery = false;

	/**
	 * addDirectory
	 *
	 * @param   string  $dir  Directory
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function addDirectory($dir)
	{
		$this->directory = $dir;
	}

	/**
	 * addPanel
	 *
	 * @param   RedDebugPanelInterface  $panel  Panel
	 *
	 * @return  RedDebugScreen
	 * @since   1.0.0
	 */
	public function addPanel($panel)
	{
		if (!in_array($panel, $this->panels, true))
		{
			$this->panels[] = $panel;
		}

		return $this;
	}

	/**
	 * render
	 *
	 * @param   mixed  $exception  Error | Exception
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function render($exception)
	{
		$panels = $this->panels;
		$info   = array_filter($this->info);
		$source = JUri::base();
		$title  = $exception instanceof ErrorException ? RedDebugHelper::errorTypeToString($exception->getSeverity()) : get_class($exception);

		require $this->directory . '/screen/screen.php';
	}
}
