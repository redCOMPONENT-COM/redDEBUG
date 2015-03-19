<?php
/**
 * @copyright  Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

/**
 * Class RedDebugJoomlaModule
 *
 * here we made mvc change for debug will working on this level
 * @since  1
 */
class RedDebugJoomlaModule
{
	static private $logger;

	/**
	 * changeJoomlaCode
	 *
	 * To change joomla core for fixed this
	 *
	 * @return void
	 */
	public static function changeJoomlaCode()
	{
		$code = file_get_contents(JPATH_LIBRARIES . '/cms/module/helper.php');

		$code = strtr(
			$code,
			array(
				'abstract class JModuleHelper' => 'class JModuleHelper extends RedDebugJoomlaModule',
				'JDEBUG' => 'self::debugger($module)',
				'<?php' => '',
			)
		);
		eval($code);

	}

	/**
	 * debugger
	 *
	 * @param   object  $module  Module
	 *
	 * @return false
	 */
	public static function debugger($module)
	{
		// Check that $module is a valid module object
		if (!is_object($module) || !isset($module->module) || !isset($module->params))
		{
			return false;
		}

		if (!isset(self::$logger[$module->id]))
		{
			/**
			 * if we not have in array so it before
			 */
			$module->start_time         = microtime(true);
			$module->close_time         = 0;
			$module->start_memory       = memory_get_usage() / 1048576;
			self::$logger[$module->id]  = $module;
		}
		else
		{
			/**
			 * We have run it before and add some new data
			 */
			self::$logger[$module->id]->content         = $module->content;
			self::$logger[$module->id]->close_time      = microtime(true);
			self::$logger[$module->id]->closed_memory   = memory_get_usage() / 1048576;
			self::$logger[$module->id]->change          = $module;
		}

		return false;
	}

	/**
	 * getLog
	 *
	 * @return array
	 */
	public static function getLog()
	{
		return self::$logger;
	}
}