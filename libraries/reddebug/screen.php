<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

/**
 * Class RedDebugScreen
 *
 * @since  1
 */
class RedDebugScreen
{
	public $info = array();

	private $panels = array();

	private $directory = null;

	public $jQuery = false;

	/**
	 * addDirectory
	 *
	 * @param   string  $dir  Directory
	 *
	 * @return void
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
	 * @return RedDebugScreen
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
	 * @param   Exception  $exception  Exception
	 *
	 * @return void
	 */
	public function render(Exception $exception)
	{
		$panels = $this->panels;
		$info = array_filter($this->info);
		$source = JUri::base();
		$title = $exception instanceof ErrorException ? RedDebugHelper::errorTypeToString($exception->getSeverity()) : get_class($exception);

		require $this->directory . '/screen/screen.php';
	}
}
