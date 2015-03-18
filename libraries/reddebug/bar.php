<?php
/**
 * @copyright  Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

/**
 * Class RedDebugBar
 *
 * @since  1
 */
class RedDebugBar
{
	/**
	 * @var array
	 */
	protected $panel = array();

	/**
	 * @var null
	 */
	protected $directory = null;

	/**
	 * render
	 *
	 * @return  void
	 */
	public function render()
	{
		$obLevel = ob_get_level();
		$panels = array();

		foreach ($this->panel as $id => $panel)
		{
			/**
			 * fix id to htmlID
			 */
			$idHtml = preg_replace('#[^a-z0-9]+#i', '-', $id);

			try
			{
				$panel->directory	= $this->directory;
				$tab				= (string) $panel->getTab();
				$panelHtml			= $tab ? (string) $panel->getPanel() : null;
				$panels[]			= array('id' => $idHtml, 'tab' => $tab, 'panel' => $panelHtml);
			}
			// If exception so give us some report into debugbar
			catch (Exception $e)
			{
				$out = '<h1>Error: ' . $id . '</h1><div class="inner">' .
					nl2br(htmlSpecialChars($e, ENT_IGNORE, 'UTF-8')) .
					'</div>';
				/**
				 * Add error to panel
				 */
				$panels[] = array(
					'id' => "error-$idHtml",
					'tab' => "Error in $id",
					'panel' => $out
				);

				/**
				 * restore ob-level if broken
				 */
				while (ob_get_level() > $obLevel)
				{
					ob_end_clean();
				}
			}
		}

		/**
		 * session
		 *
		 * why not using joomla session.
		 * on some version of joomla or config joomla session is closed here in shutdown function.
		 * so for fixed it we using php session
		 */
		@session_start();
		$session_debug = & $_SESSION['__REDDEBUG__']['debuggerbar'];

		/**
		 * if Location in header so we save data so we can see errors
		 */
		if (preg_match('#^Location:#im', implode("\n", headers_list())))
		{
			$session_debug[] = $panels;

			return;
		}

		/**
		 * Takes input in array and returns a new array with the order of the elements reversed.
		 */
		$list = array_reverse((array) $session_debug);

		/**
		 * run foreach to
		 */
		foreach ($list as $id => $old_panels)
		{
			$panels[] = array(
				'tab' => '<span title="Previous request before redirect">previous</span>',
				'panel' => null,
				'previous' => null,
			);

			/**
			 * run all olds panels
			 */
			foreach ($old_panels as $panel)
			{
				$panel['id'] .= '-' . $id;
				$panels[] = $panel;
			}
		}

		$session_debug = null;

		/**
		 * Include file
		 */
		include $this->directory . '/bar/bar.php';
	}

	/**
	 * getPanel
	 *
	 * @param   string  $name  Name
	 *
	 * @return RedDebugPanel
	 */
	public function getPanel($name)
	{
		return $this->panel[$name];
	}

	/**
	 * addPanel
	 *
	 * @param   RedDebugPanelInterface  $instance  RedDebugPanel
	 * @param   string                  $name      Name
	 *
	 * @return RedDebugPanelDefault
	 */
	public function addPanel(RedDebugPanelInterface $instance, $name = null)
	{
		return $this->panel[$name] = $instance;
	}

	/**
	 * addDirectory
	 *
	 * @param   string  $dir  Dir
	 *
	 * @return  void
	 */
	public function addDirectory($dir)
	{
		$this->directory = $dir;
	}
}
