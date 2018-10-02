<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Class RedDebugJoomlaView
 *
 * @since  1.0.0
 */
class RedDebugJoomlaView extends JObject
{
	/**
	 * @var     self
	 * @since   1.0.0
	 */
	static protected $instance = null;

	/**
	 * @var     array
	 * @since   1.0.0
	 */
	static protected $view;

	/**
	 * getInstance
	 *
	 * @return  RedDebugJoomlaView
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	static public function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 *
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function __construct()
	{
		parent::__construct();

		$code = file_get_contents(JPATH_LIBRARIES . '/legacy/view/legacy.php');

		$code = strtr(
			$code,
			array(
				'extends JObject' => 'extends RedDebugJoomlaView',
				'public function display' => 'protected function xDisplay',
			)
		);

		$code = strtr(
			$code,
			array(
				'JDEBUG' => 'REDDEBUG',
				'<?php' => '',
			)
		);

		try
		{
			if (!@eval("return true;"))
			{
				throw new \Exception('PHP');
			}

			eval($code);
		}
		catch (\Exception $e)
		{
			$updateTime = filemtime(JPATH_LIBRARIES . '/cms/module/helper.php');
			$codeFile   = JPATH_CACHE . '/view_helper.php';
			$codeUpdate = file_exists($codeFile) ? filemtime($codeFile) : 0;

			if ($updateTime > $codeUpdate)
			{
				file_put_contents($codeFile, "<?php \n" . $code);
			}

			include_once $codeFile;
		}
	}

	/**
	 * display
	 *
	 * @param   null  $tpl  Tpl
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function display($tpl = null)
	{
		$this->xDisplay($tpl);
		self::$view = get_object_vars($this);
	}

	/**
	 * getView
	 *
	 * @return  mixed
	 * @since   1.0.0
	 */
	public function getView()
	{
		return self::$view;
	}
}
