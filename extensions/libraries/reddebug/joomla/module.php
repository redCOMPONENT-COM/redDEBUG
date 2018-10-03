<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Class RedDebugJoomlaModule
 * Here we made mvc change for debug will working on this level
 *
 * @since  1.0.0
 */
class RedDebugJoomlaModule
{
	/**
	 * @var    array
	 * @since  1.0.0
	 */
	static private $logger;

	/**
	 * changeJoomlaCode
	 *
	 * To change joomla core for fixed this
	 *
	 * @param   string  $filename  FileName
	 *
	 * @return void
	 * @since  1.0.0
	 * @throws \Exception
	 */
	public static function changeJoomlaCode($filename)
	{
		$code = file_get_contents($filename);

		$moduleHelperReplace = [
			'class ModuleHelper' => 'class ModuleHelper extends \RedDebugJoomlaModule'
		];

		if (file_exists(JPATH_SITE . '/libraries/cms/module/helper.php'))
		{
			$moduleHelperReplace = [
				'abstract class JModuleHelper' => 'class JModuleHelper extends \RedDebugJoomlaModule'
			];
		}

		$code = strtr(
			$code,
			array_merge(
				$moduleHelperReplace,
				[ 'renderModule(' => 'xRenderModule(' ]
			)
		);

		$code = strtr(
			$code,
			array(
				'JDEBUG' => 'REDDEBUG',
				'<?php' => '',
			)
		);

		$updateTime = filemtime($filename);
		$codeFile   = JPATH_CACHE . '/module_helper.php';
		$codeUpdate = file_exists($codeFile) ? filemtime($codeFile) : 0;

		if ($updateTime > $codeUpdate)
		{
			file_put_contents($codeFile, "<?php \n" . $code);
		}

		include_once $codeFile;
	}

	/**
	 * debugger
	 *
	 * @param   object  $module   Module
	 * @param   string  $when     When it's executed (before/after)
	 * @param   string  $content  Resulting content (after)
	 *
	 * @return  false
	 * @since   1.0.0
	 */
	public static function debugger($module, $when = '', $content = '')
	{
		// Check that $module is a valid module object
		if (!is_object($module) || !isset($module->module) || !isset($module->params))
		{
			// Not using time to save this
			return false;
		}

		if (!isset(self::$logger[$module->id]))
		{
			/**
			 * if we not have in array so it before
			 */
			$module->start_time        = microtime(true);
			$module->close_time        = 0;
			$module->start_memory      = memory_get_usage() / 1048576;
			self::$logger[$module->id] = $module;
		}
		else
		{
			/**
			 * We have run it before and add some new data
			 */
			self::$logger[$module->id]->content       = $module->content;
			self::$logger[$module->id]->close_time    = microtime(true);
			self::$logger[$module->id]->closed_memory = memory_get_usage() / 1048576;
			self::$logger[$module->id]->change        = $module;
		}

		return false;
	}

	/**
	 * getLog
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function getLog()
	{
		return self::$logger;
	}

	/**
	 * renderModule
	 *
	 * @param   object  $module   Module
	 * @param   array   $attribs  Module attributes
	 *
	 * @return array
	 * @since  1.0.0
	 */
	public static function renderModule($module, $attribs = array())
	{
		self::debugger($module, 'before');
		$content         = JModuleHelper::xRenderModule($module, $attribs);
		$module->content = $content;
		self::debugger($module, 'after', $content);

		return $content;
	}
}
