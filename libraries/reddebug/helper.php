<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

/**
 * Class RedDebugHelper
 *
 * @since  1
 */
class RedDebugHelper
{
	/**
	 * findTrace
	 *
	 * @param   array   $trace   Trace
	 * @param   string  $method  Method
	 * @param   int     &$index  Index
	 *
	 * @return mixed
	 */
	public static function findTrace(array $trace, $method, &$index = null)
	{
		$m = explode('::', $method);

		foreach ($trace as $i => $item)
		{
			$check = isset($item['function']) && $item['function'] === end($m);
			$check = $check && isset($item['class']) === isset($m[1]);
			$check = $check && (!isset($item['class']) || $item['class'] === $m[0] || $m[0] === '*' || is_subclass_of($item['class'], $m[0]));

			if ($check)
			{
				$index = $i;

				return $item;
			}
		}

		return;
	}

	/**
	 * fixEncoding
	 *
	 * @param   string  $string  String
	 *
	 * @return string
	 */
	public static function fixEncoding($string)
	{
		if (PHP_VERSION_ID < 50400)
		{
			return @iconv('UTF-16', 'UTF-8//IGNORE', iconv('UTF-8', 'UTF-16//IGNORE', $string));
		}
		else
		{
			return htmlspecialchars_decode(htmlspecialchars($string, ENT_NOQUOTES | ENT_IGNORE, 'UTF-8'), ENT_NOQUOTES);
		}
	}

	/**
	 * errorTypeToString
	 *
	 * @param   int  $code  Error Code
	 *
	 * @return string
	 */
	public static function errorTypeToString($code)
	{
		/**
		 * Error Types get text from joomla language files
		 */
		$ErrorTypes = array(
			E_ERROR				=> JText::_('E_ERROR'),
			E_USER_ERROR		=> JText::_('E_USER_ERROR'),
			E_RECOVERABLE_ERROR => JText::_('E_RECOVERABLE_ERROR'),
			E_CORE_ERROR		=> JText::_('E_CORE_ERROR'),
			E_COMPILE_ERROR		=> JText::_('E_COMPILE_ERROR'),
			E_PARSE				=> JText::_('E_PARSE'),
			E_WARNING			=> JText::_('E_WARNING'),
			E_CORE_WARNING		=> JText::_('E_CORE_WARNING'),
			E_COMPILE_WARNING	=> JText::_('E_COMPILE_WARNING'),
			E_USER_WARNING		=> JText::_('E_USER_WARNING'),
			E_NOTICE			=> JText::_('E_NOTICE'),
			E_USER_NOTICE		=> JText::_('E_USER_NOTICE'),
			E_STRICT			=> JText::_('E_STRICT'),
			E_DEPRECATED		=> JText::_('E_DEPRECATED'),
			E_USER_DEPRECATED	=> JText::_('E_USER_DEPRECATED'),
		);

		return isset($ErrorTypes[$code]) ? $ErrorTypes[$code] : JText::_('E_UDEFINE');
	}
}
