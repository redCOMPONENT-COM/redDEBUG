<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Class RedDebugHelper
 *
 * @since  1.0.0
 */
class RedDebugHelper
{
	/**
	 * findTrace
	 *
	 * @param   array    $trace   Trace
	 * @param   string   $method  Method
	 * @param   integer  $index   Index
	 *
	 * @return mixed
	 * @since  1.0.0
	 */
	public static function findTrace(array $trace, $method, &$index = null)
	{
		$parts = explode('::', $method);

		foreach ($trace as $i => $item)
		{
			$check = isset($item['function']) && $item['function'] === end($parts);
			$check = $check && isset($item['class']) === isset($parts[1]);
			$check = $check &&
				(!isset($item['class']) || $item['class'] === $parts[0] || $parts[0] === '*' || is_subclass_of($item['class'], $parts[0]));

			if ($check)
			{
				$index = $i;

				return $item;
			}
		}

		return null;
	}

	/**
	 * fixEncoding
	 *
	 * @param   string  $string  String
	 *
	 * @return  string
	 * @since   1.0.0
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
	 * @return  string
	 * @since   1.0.0
	 */
	public static function errorTypeToString($code)
	{
		/**
		 * Error Types get text from joomla language files
		 */
		$errorTypes = array(
			E_ERROR				=> JText::_('PLG_SYSTEM_REDDEBUG_E_ERROR'),
			E_USER_ERROR		=> JText::_('PLG_SYSTEM_REDDEBUG_E_USER_ERROR'),
			E_RECOVERABLE_ERROR => JText::_('PLG_SYSTEM_REDDEBUG_E_RECOVERABLE_ERROR'),
			E_CORE_ERROR		=> JText::_('PLG_SYSTEM_REDDEBUG_E_CORE_ERROR'),
			E_COMPILE_ERROR		=> JText::_('PLG_SYSTEM_REDDEBUG_E_COMPILE_ERROR'),
			E_PARSE				=> JText::_('PLG_SYSTEM_REDDEBUG_E_PARSE'),
			E_WARNING			=> JText::_('PLG_SYSTEM_REDDEBUG_E_WARNING'),
			E_CORE_WARNING		=> JText::_('PLG_SYSTEM_REDDEBUG_E_CORE_WARNING'),
			E_COMPILE_WARNING	=> JText::_('PLG_SYSTEM_REDDEBUG_E_COMPILE_WARNING'),
			E_USER_WARNING		=> JText::_('PLG_SYSTEM_REDDEBUG_E_USER_WARNING'),
			E_NOTICE			=> JText::_('PLG_SYSTEM_REDDEBUG_E_NOTICE'),
			E_USER_NOTICE		=> JText::_('PLG_SYSTEM_REDDEBUG_E_USER_NOTICE'),
			E_STRICT			=> JText::_('PLG_SYSTEM_REDDEBUG_E_STRICT'),
			E_DEPRECATED		=> JText::_('PLG_SYSTEM_REDDEBUG_E_DEPRECATED'),
			E_USER_DEPRECATED	=> JText::_('PLG_SYSTEM_REDDEBUG_E_USER_DEPRECATED'),
		);

		return isset($errorTypes[$code]) ? $errorTypes[$code] : JText::_('E_UDEFINE');
	}

	/**
	 * findJoomlaClassFile
	 *
	 * @param   string  $class          ClassName
	 * @param   mixed   $default        DefaultValue
	 * @param   mixed   $extensionName  Extension Name
	 *
	 * @return  null|string
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public static function findJoomlaClassFile($class, $default = null, &$extensionName = false)
	{
		// Autoload class
		if (class_exists($class, true))
		{
			$class         = new ReflectionClass($class);
			$extensionName = $class->getExtensionName();
			$filename      = $class->getFileName();

			return empty($filename) ? $default : $filename;
		}

		return $default;
	}

	/**
	 * checkDebugMode
	 *
	 * @param   array  $list  List of ips
	 *
	 * @return  boolean|integer
	 * @since   1.0.0
	 */
	public static function checkDebugMode(array $list)
	{
		if (count($list) == 0)
		{
			return true;
		}

		// Localhost
		$list[] = '127.0.0.1';
		$list[] = '::1';
		$list   = array_map('trim', $list);

		$check = implode('|', $list);
		$ip    = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : php_uname('n');
		$ipx   = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : '';

		$found = preg_match('#^(' . $check . ')$#', $ip);

		if ($found == 0 && $ipx)
		{
			$found = preg_match('#^(' . $check . ')$#', $ipx);
		}

		return $found;
	}

	/**
	 * highlightFile
	 *
	 * @param   string  $file   File
	 * @param   int     $line   line number to show
	 * @param   int     $lines  number of lines
	 *
	 * @return  string|null
	 * @since   1.0.0
	 */
	public static function highlightFile($file = null, $line = null, $lines = null)
	{
		if (!file_exists($file))
		{
			return null;
		}

		// Get code in file
		$source = file_get_contents($file);

		if (empty($source))
		{
			return null;
		}

		$source = strtr(
			$source,
			array(
				"\r\n"  => "\n",
				"\r"    => "\n"
			)
		);
		$source = highlight_string($source, true);

		// Highlight_string return "<code><span style="color: #000000">" in start of code. to remove this we do this
		$source = explode("\n", $source);
		$before = $source[0];

		$source = strtr(
			$source[1],
			array(
				"<br />" => "\n"
			)
		);

		// Line fixed
		$source = strtr(
			$source,
			array(
				"\r\n" => "\n"
			)
		);

		// Add line break
		$source = "\n" . $source;

		// Explode on line breaks
		$source = explode("\n", $source);
		$code   = '';
		$spans  = 1;

		$lines = is_null($lines) ? count($source) : $lines;

		// Get Start line number
		$counter = max(1, ($line - floor($lines * 2 / 3)));
		$start   = $counter;

		while (--$counter >= 1)
		{
			if (preg_match('#.*(</?span[^>]*>)#', $source[$counter], $matches))
			{
				if ($matches[1] !== '</span>')
				{
					$spans++;
					$code .= $matches[1];
				}

				break;
			}
		}

		$source = array_slice($source, $start, $lines, true);
		$maxLen = strlen(count($source));

		foreach ($source as $line => $content)
		{
			$spans += substr_count($content, '<span') - substr_count($content, '</span');
			preg_match_all('#<[^>]+>#', $content, $tags);

			$class = ($line == $start) ? 'line line-start' : 'line';

			if ($line == $line)
			{
				$code .= sprintf(
					"<span class='%s highlight'>%s:</span><span class='highlight'>%s\n</span>%s",
					$class,
					str_pad($line, $maxLen, "0", STR_PAD_LEFT),
					strip_tags($content),
					implode('', $tags[0])
				);
			}
			else
			{
				$code .= sprintf(
					"<span class='%s'>%s:</span>%s\n",
					$class,
					str_pad($line, $maxLen, "0", STR_PAD_LEFT),
					$content
				);
			}
		}

		$code .= str_repeat('</span>', $spans) . '</code>';

		return ($before . '' . $code);
	}

	/**
	 * removeRecursion
	 *
	 * @param   object  $object  Object
	 * @param   array   $stack   Stack
	 *
	 * @return string
	 * @since  1.0.0
	 */
	public static function removeRecursion(&$object, &$stack = array())
	{
		if ((is_object($object) || is_array($object)) && $object)
		{
			if (!in_array($object, $stack, true))
			{
				$stack[] = $object;

				foreach ($object as &$subobject)
				{
					self::removeRecursion($subobject, $stack);
				}
			}
			else
			{
				$object = gettype($object) == 'object' ? '(Recursion)' . " " . gettype($object) : '*Recursion*';
			}
		}

		return $object;
	}

	/**
	 * multiArrayToSingleArray
	 *
	 * @param   array|object  $array      Array / Object
	 * @param   string        $namespace  Namespace
	 * @param   array         $newArray   New Output array
	 *
	 * @return  array
	 * @since   1.0.0
	 */
	static public function multiArrayToSingleArray($array, $namespace = '$this', &$newArray = [])
	{
		$temp = "%s->%s";

		if (is_array($array))
		{
			$temp = "%s[%s]";
		}

		foreach ($array AS $key => $val)
		{
			$newKey = sprintf($temp, $namespace, $key);

			if (is_array($val) || is_object($val))
			{
				self::multiArrayToSingleArray($val, $newKey, $newArray);
			}
			else
			{
				$newArray[$newKey] = $val;
			}
		}

		return $newArray;
	}
}
