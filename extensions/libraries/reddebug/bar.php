<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Class RedDebugBar
 *
 * @since  1.0.0
 */
class RedDebugBar
{
	/**
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $hasRun = false;

	/**
	 * @var    array
	 * @since  1.0.0
	 */
	protected $panel = array();

	/**
	 * @var    string
	 * @since  1.0.0
	 */
	protected $directory = null;

	/**
	 * render
	 *
	 * @return  void
	 * @since  1.0.0
	 */
	public function render()
	{
		$obLevel = ob_get_level();
		$panels  = array();

		if ($this->hasRun)
		{
			return;
		}

		$this->hasRun = true;

		foreach ($this->panel as $id => $panel)
		{
			/**
			 * fix id to htmlID
			 */
			$idHtml = preg_replace('#[^a-z0-9]+#i', '-', $id);
			$class  = get_class($panel);

			try
			{
				$panel->directory = $this->directory;
				$tab              = (string) $panel->getTab();
				$panelHtml        = $tab ? (string) $panel->getPanel() : null;
				$panels[]         = array(
					'id' => $idHtml,
					'tab' => $tab,
					'class' => $class,
					'panel' => $panelHtml
				);
			}

			// If exception so give us some report into debugbar
			catch (Exception $e)
			{
				$out = '<h1>Error: ' . $id . '</h1><div class="inner">' .
					nl2br(htmlspecialchars($e, ENT_IGNORE, 'UTF-8')) .
					'</div>';
				/**
				 * Add error to panel
				 */
				$panels[] = array(
					'id' => "error-$idHtml",
					'tab' => "Error in $id",
					'class' => $class,
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
		 * so to it we using php session
		 */
		if (session_id() === '')
		{
			session_start();
		}

		$sessionDebug = & $_SESSION['__REDDEBUG__']['debuggerbar'];

		/**
		 * if Location in header so we save data so we can see errors
		 */
		if (preg_match('#^Location:#im', implode("\n", headers_list())))
		{
			$sessionDebug[] = $panels;

			return;
		}

		/**
		 * Takes input in array and returns a new array with the order of the elements reversed.
		 */
		$list = array_reverse((array) $sessionDebug);

		/**
		 * run foreach to
		 */
		foreach ($list as $id => $oldPanels)
		{
			$panels[] = array(
				'tab' => '<span title="before redirect">previous</span>',
				'panel' => null,
				'class' => null,
				'previous' => null,
			);

			/**
			 * run all olds panels
			 */
			foreach ($oldPanels as $panel)
			{
				$panel['id'] .= '-' . $id;
				$panels[]     = $panel;
			}
		}

		$sessionDebug = null;

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
	 * @return  RedDebugPanelDefault
	 * @since   1.0.0
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
	 * @since  1.0.0
	 */
	public function addPanel(RedDebugPanelInterface $instance, $name = null)
	{
		$this->panel[$name] = $instance;

		return $this->panel[$name];
	}

	/**
	 * addDirectory
	 *
	 * @param   string  $dir  Dir
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function addDirectory($dir)
	{
		$this->directory = $dir;
	}
}
