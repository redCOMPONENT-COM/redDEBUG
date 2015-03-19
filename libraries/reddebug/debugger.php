<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

/**
 * Class RedDebugDebugger
 *
 * @since  1
 */
class RedDebugDebugger
{
	/**
	 * @var RedDebugDebugger
	 */
	private static $instance;

	/**
	 * @var bool
	 */
	private $enable;

	/**
	 * @var float
	 */
	private $time;

	/**
	 * @var bool
	 */
	private $done;

	/**
	 * @var mixed
	 */
	private $screen;

	/**
	 * @var bar
	 */
	private $bar;

	public static $onFatalError = array();
	/**
	 * getInstance
	 *
	 * @return RedDebugDebugger
	 */
	static public function getInstance()
	{
		return (self::$instance instanceof self ? self::$instance : (self::$instance = new self));
	}

	/**
	 * getTime
	 *
	 * @return float
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * enable
	 *
	 * @return void
	 */
	public function enable()
	{
		$this->time = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
		error_reporting(E_ALL | E_STRICT);

		if (function_exists('ini_set'))
		{
			ini_set('display_errors', 0);
			ini_set('html_errors', false);
			ini_set('log_errors', false);
		}

		if (!$this->enable)
		{
			register_shutdown_function(
				array(
					$this,
					'shutdownHandler'
				)
			);
			set_exception_handler(
				array(
					$this,
					'exceptionHandler'
				)
			);
			set_error_handler(
				array(
					$this,
					'errorHandler'
				)
			);
			$this->enable = true;
		}
	}

	/**
	 * shutdownHandler
	 *
	 * @return void
	 */
	public function shutdownHandler()
	{
		$error = error_get_last();

		if (in_array($error['type'], array(E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE, E_RECOVERABLE_ERROR, E_USER_ERROR), true))
		{
			$this->exceptionHandler(
				new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']),
				false
			);
		}
		elseif (!connection_aborted())
		{
			$this->getBar()->render();
		}
	}

	/**
	 * exceptionHandler
	 *
	 * @param   Exception  $exception  Exception
	 * @param   bool       $exit       Exit
	 *
	 * @return void
	 */
	public function exceptionHandler(\Exception $exception, $exit = true)
	{
		if ($this->done)
		{
			return;
		}

		$this->done = true;

		if (!headers_sent())
		{
			$protocol		= isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
			$code			= isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE ') !== false ? 503 : 500;

			header("$protocol $code", true, $code);
		}

		if (!connection_aborted())
		{
			$this->getRedScreen()->render($exception);
			$this->getBar()->render();
		}

		try
		{
			foreach (self::$onFatalError as $handler)
			{
				call_user_func($handler, $exception);
			}
		}
		catch (\Exception $e)
		{
		}

		if ($exit)
		{
			exit(254);
		}
	}

	/**
	 * errorHandler
	 *
	 * @param   int     $severity  Severity
	 * @param   string  $message   Message
	 * @param   string  $file      File
	 * @param   int     $line      Line
	 * @param   string  $context   Content
	 *
	 * @return  bool
	 *
	 * @throws ErrorException
	 */
	public function errorHandler($severity, $message, $file, $line, $context)
	{
		error_reporting(E_ALL | E_STRICT);
		/**
		 * E_RECOVERABLE_ERROR
		 *
		 * Catchable fatal error.
		 * It indicates that a probably dangerous error occurred,
		 * but did not leave the Engine in an unstable state.
		 *
		 * If the error is not caught by a user defined handle (see also set_error_handler()),
		 * the application aborts as it was an E_ERROR.
		 *
		 * E_USER_ERROR
		 *
		 * User-generated error message.
		 * This is like an E_ERROR, except it is generated in PHP code by using the
		 * PHP function trigger_error()
		 */
		if ($severity === E_RECOVERABLE_ERROR || $severity === E_USER_ERROR)
		{
			/**
			 * http://php.net/manual/en/function.debug-backtrace.php
			 * As of 5.3.6, this parameter is a bitmask for the following options:
			 * DEBUG_BACKTRACE_PROVIDE_OBJECT
			 * DEBUG_BACKTRACE_IGNORE_ARGS
			 */
			$debug = PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : false;

			if (RedDebugHelper::findTrace(debug_backtrace($debug), '*::__toString'))
			{
				$previous	= isset($context['e']) && $context['e'] instanceof Exception ? $context['e'] : null;
				$e			= new ErrorException($message, 0, $severity, $file, $line, $previous);
				$e->context = $context;

				$this->exceptionHandler($e);
			}

			$e = new ErrorException($message, 0, $severity, $file, $line);
			$e->context = $context;

			throw $e;
		}
		elseif (($severity & error_reporting()) !== $severity)
		{
			return false;
		}

		// Add debug to debug panel.
		$message	= 'PHP ' . RedDebugHelper::errorTypeToString($severity) . ": $message";
		$count		= $this->getBar()->getPanel('error')->get("$file|$line|$message", 0);
		$count++;

		// Update count
		$this->getBar()->getPanel('error')->set("$file|$line|$message", $count);

		return null;
	}

	/**
	 * getRedScreen
	 *
	 * @return RedDebugScreen
	 */
	public function getRedScreen()
	{
		if (!$this->screen)
		{
			$this->screen = new RedDebugScreen;
			$this->screen->addDirectory($this->directory);
		}

		return $this->screen;
	}

	/**
	 * getBar
	 *
	 * @return RedDebugBar
	 */
	public function getBar()
	{
		if (!$this->bar)
		{
			$this->bar = new RedDebugBar;
			$this->bar->addDirectory($this->directory);
			$this->bar->addPanel(new RedDebugPanelDefault('info'), 'info');
			$this->bar->addPanel(new RedDebugPanelDefault('error'), 'error');
		}

		return $this->bar;
	}
}
