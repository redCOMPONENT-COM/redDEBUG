<?php
//JViewLegacy
class RedDebugJoomlaView extends JObject
{
	static protected $instance = null;

	static protected $view;

	/**
	 * getInstance
	 *
	 * @return RedDebugJoomlaView
	 */
	static public function getInstance()
	{
		if (is_null(self::$instance))
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

	public function __construct()
	{
		$code = file_get_contents(JPATH_LIBRARIES . '/legacy/view/legacy.php');
		$code = strtr($code, array(
			'extends JObject' => 'extends RedDebugJoomlaView',
			'public function display' => 'protected function xDisplay',
		));

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
				throw new Exception('PHP');
			}

			eval($code);
		}
		catch (Exception $e)
		{
			$update_time = filemtime(JPATH_LIBRARIES . '/cms/module/helper.php');
			$code_file = JPATH_CACHE . '/view_helper.php';
			$code_update = file_exists($code_file) ? filemtime($code_file) : 0;

			if ($update_time > $code_update)
			{
				file_put_contents($code_file, "<?php \n" . $code);
			}

			include_once $code_file;
		}
	}

	/**
	 * display
	 *
	 * @param   null  $tpl  Tpl
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->xDisplay($tpl);
		self::$view = json_decode(json_encode(get_object_vars($this)));
	}

	/**
	 * getView
	 *
	 * @return mixed
	 */
	public function getView()
	{
		return self::$view;
	}
}
