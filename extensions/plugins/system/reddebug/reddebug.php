<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
define('REDDEBUG', 0);

JLoader::import('reddebug.library');

/**
 * Class PlgSystemRedDebug
 *
 * @since  1.0.0
 */
class PlgSystemRedDebug extends JPlugin
{
	/**
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * @var    array
	 * @since  1.0.0
	 */
	public static $logPlugin;

	/**
	 * @var    boolean
	 * @since  1.0.0
	 */
	public static $afterRespond = false;

	/**
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected static $reset = false;

	/**
	 * @var    string
	 * @since  1.0.0
	 */
	protected static $ModuleHelperName = 'default';

	/**
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected static $checkIp = false;

	/**
	 * @var    array
	 * @since  1.0.0
	 */
	protected static $logs = [];

	/**
	 * @var    integer
	 * @since  1.0.0
	 */
	protected static $logsCount = 0;

	/**
	 * __construct
	 *
	 * @param   object  $subject   Subject
	 * @param   array   $config    Config
	 *
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function __construct(&$subject, $config = [])
	{
		parent::__construct($subject, $config);

		// Check debug mode for this page
		$arrayValidIPs = [];
		$validIPs      = trim($this->params->get('ip'));

		if ($validIPs != '')
		{
			$ips = explode(chr(13), $validIPs);

			if ($ips && count($ips))
			{
				foreach ($ips as $ip)
				{
					$arrayValidIPs[] = trim($ip);
				}
			}
		}

		self::$checkIp = RedDebugHelper::checkDebugMode($arrayValidIPs);

		if (version_compare(JVERSION, '3.4', '<='))
		{
			RedDebugJoomlaView::getInstance();
		}

		if (!$this->isActive())
		{
			return false;
		}

		JFactory::getConfig()->set('gzip', 0);

		$app     = JFactory::getApplication();
		$session = JFactory::getSession();
		$classes = $session->get('joomlaClasses', [], 'redDebug');
		RedDebugJoomlaDispatcher::getInstance();

		static::$reset = $app->input->get('reset_class_files', !isset($classes['JModuleHelper']));

		if (isset($classes['JModuleHelper']))
		{
			$location = dirname($classes['JModuleHelper']);
			$location = explode(DIRECTORY_SEPARATOR, $location);
			$location = end($location);

			self::$ModuleHelperName = ($location == 'module' ? 'Default' : $location);
		}

		if (static::$reset != 1)
		{
			RedDebugJoomlaModule::changeJoomlaCode($classes['JModuleHelper']);
		}

		if ($this->params->get('show_jlog', 1))
		{
			// Register logger
			JLog::addLogger(array('logger' => 'callback', 'callback' => array($this, 'addLog')), JLog::ALL);
		}

		return true;
	}

	/**
	 * Does the plugin need to be loaded?
	 *
	 * @return  boolean
	 * @since   1.0.1
	 * @throws  \Exception
	 */
	protected function isActive()
	{
		$app = JFactory::getApplication();

		$inAdmin = $this->params->get('in_admin');

		if (!$app->isSite() && $inAdmin == 0)
		{
			return false;
		}

		if (!self::$checkIp)
		{
			return false;
		}

		if (JFactory::getDocument()->getType() !== 'html' || $this->isAjaxRequest())
		{
			return false;
		}

		// JCE loads with wrong headers so let's exclude it
		$option = $app->input->get('option');

		if ($option == 'com_jce')
		{
			return false;
		}

		return true;
	}

	/**
	 * Detect if current request is using AJAX
	 *
	 * @return  boolean
	 * @since   1.0.1
	 * @throws  \Exception
	 */
	protected function isAjaxRequest()
	{
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	/**
	 * onAfterInitialise
	 *
	 * @return  void
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function onAfterInitialise()
	{
		if (!$this->isActive())
		{
			return;
		}

		/**
		 * So we in debug mode can work on offline page.
		 * and can test no login pages..
		 *
		 * @todo maybe we only have this function in pro version
		 */
		if ($this->params->get('offline', false))
		{
			JFactory::getConfig()->set('offline', 0);
		}

		/**
		 * So we in debug mode easy can use not sef url in debug mode
		 *
		 * @todo maybe we only have this function in pro version
		 */
		if ($this->params->get('sef', false))
		{
			JFactory::getConfig()->set('sef', 0);
		}

		/**
		 * why. so we before we add debug bar and panel
		 * run this.
		 */
		register_shutdown_function(
			array(
				$this,
				'onAfterJoomla'
			)
		);

		$debugger = RedDebugDebugger::getInstance();
		$debugger->enable();
		$debugger->directory = __DIR__ . DIRECTORY_SEPARATOR . 'layout';

		/**
		 * register_shutdown_function
		 */
		static::$afterRespond = false;
	}

	/**
	 * onAfterRender
	 *
	 * @return  void
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function onAfterRender()
	{
		if (!$this->isActive())
		{
			return;
		}

		$app     = JFactory::getApplication();
		$session = JFactory::getSession();
		$classes = $session->get('joomlaClasses', [], 'redDebug');

		// So we can se class we need to load
		if (count($classes) == 0 || static::$reset)
		{
			$classes['JModuleHelper'] = RedDebugHelper::findJoomlaClassFile('JModuleHelper', null);
			$session->set('joomlaClasses', $classes, 'redDebug');
			$app->redirect(isset($_SERVER['REQUEST_URI']) ? strtr($_SERVER['REQUEST_URI'], array('reset_class_files' => 'x')) : JUri::root());
		}
	}

	/**
	 * onAfterJoomla
	 *
	 * @return  void
	 * @since   1.0.0
	 * @throws  \Exception
	 */
	public function onAfterJoomla()
	{
		if (!$this->isActive())
		{
			return;
		}

		if (static::$afterRespond)
		{
			return;
		}

		static::$afterRespond = true;

		$methods = RedDebugJoomlaModule::getLog();

		$session = JFactory::getSession();

		$classes = $session->get('joomlaClasses', [], 'redDebug');

		$plugins    = RedDebugJoomlaDispatcher::$logger;
		$eventCount = 0;
		$plg        = [];
		$eve        = [];

		foreach ($plugins AS $plugin => $events)
		{
			foreach ($events AS $event => $info)
			{
				$plg[($info[0]->type)][$plugin][$event]           = [];
				$plg[($info[0]->type)][$plugin][$event]['count']  = count($info);
				$plg[($info[0]->type)][$plugin][$event]['time']   = 0;
				$plg[($info[0]->type)][$plugin][$event]['memory'] = 0;

				foreach ($info AS $key => $row)
				{
					$mark = $row->profile->getMarks();

					if (count($mark) == 2)
					{
						$time   = $mark[1]->totalTime - $mark[0]->totalTime;
						$memory = $mark[1]->totalMemory - $mark[0]->totalMemory;

						$plg[($info[0]->type)][$plugin][$event]['time']   = $time;
						$plg[($info[0]->type)][$plugin][$event]['memory'] = $memory;
					}
				}

				if (!isset($eve[($info[0]->type)][$event]))
				{
					$eventCount++;
					$eve[($info[0]->type)][$event] = [];
				}

				$eve[($info[0]->type)][$event] = array_merge($eve[($info[0]->type)][$event], $info);
			}
		}

		$debug = RedDebugDebugger::getInstance();

		// Joomla information

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'<strong>Joomla:</strong>',
				null,
				null,
				null
			),
			'joomla'
		);

		if ($this->params->get('show_event', 1))
		{
			$debug->getBar()->addPanel(
				new RedDebugPanelList(
					JText::_('PLG_SYSTEM_REDDEBUG_EVENT_LABEL'),
					$eve,
					$eventCount,
					'event'
				),
				'event'
			);
		}

		if ($this->params->get('show_plugin', 1))
		{
			$debug->getBar()->addPanel(
				new RedDebugPanelList(
					JText::_('PLG_SYSTEM_REDDEBUG_PLUGINS_LABEL'),
					$plg,
					count($plugins),
					'plugin'
				),
				'plugin'
			);
		}

		if ($this->params->get('show_module', 1))
		{
			$debug->getBar()->addPanel(
				new RedDebugPanelList(
					JText::_('PLG_SYSTEM_REDDEBUG_MODULES_LABEL'),
					$methods,
					count($methods),
					'module'
				),
				'modules'
			);
		}

		if ($this->params->get('show_component', 1))
		{
			/**
			 * If you using default joomla system and display as default "parent::display" it will working
			 */
			if (version_compare(JVERSION, '3.4', '<='))
			{
				$data = (object) RedDebugJoomlaView::getInstance()->getView();
				unset($data->document);
				$data = RedDebugHelper::MultiArrayToSingleArray(RedDebugHelper::removeRecursion($data));
			}
			else
			{
				$class       = new ReflectionClass('JControllerLegacy');
				$propsStatic = $class->getStaticProperties();
				$data        = RedDebugHelper::MultiArrayToSingleArray(RedDebugHelper::removeRecursion($propsStatic));
			}

			if (count($data) > 0)
			{
				$debug->getBar()->addPanel(
					new RedDebugPanelList(
						JText::_('PLG_SYSTEM_REDDEBUG_COMPONENT_LABEL'),
						$data,
						count($data),
						'default'
					),
					'component'
				);
			}
		}

		if ($this->params->get('show_jlog', 1))
		{
			$debug->getBar()->addPanel(
				new RedDebugPanelList(
					'JLog',
					self::$logs,
					self::$logsCount,
					'log'
				),
				'log'
			);
		}

		if ($this->params->get('show_template_params', 1))
		{
			$params = json_decode(JFactory::getApplication()->getTemplate(true)->params);
			$debug->getBar()->addPanel(
				new RedDebugPanelList(
					JText::_('PLG_SYSTEM_REDDEBUG_TEMPLATE_LABEL'),
					$params,
					null,
					'default'
				),
				'template'
			);
		}

		if ($this->params->get('show_joomla_config', 1))
		{
			$config = JFactory::getConfig();
			unset($config['password'], $config['ftp_pass'], $config['smtppass'], $config['secret']);

			$debug->getBar()->addPanel(
				new RedDebugPanelList(
					JText::_('PLG_SYSTEM_REDDEBUG_CONFIG_LABEL'),
					$config,
					null,
					'default'
				),
				'config'
			);
		}

		if ($this->params->get('show_joomla_infor', 1))
		{
			$debug->getBar()->addPanel(
				new RedDebugPanelList(
					JText::_('PLG_SYSTEM_REDDEBUG_JOOMLA_VERSION_LABEL'),
					(array) new JVersion,
					null,
					'default'
				),
				'joomlainfo'
			);
		}

		if ($this->params->get('show_user_infor', 1))
		{
			$jUser = JFactory::getUser();
			$user  = get_object_vars($jUser);
			$user  = RedDebugHelper::MultiArrayToSingleArray((object) $user, 'JUser');

			unset($user['password'], $user['password_clear']);

			$debug->getBar()->addPanel(
				new RedDebugPanelList(
					JText::_('PLG_SYSTEM_REDDEBUG_USER_LABEL'),
					$user,
					null,
					'default'
				),
				'user'
			);
		}

		// PHP Information
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'<strong>PHP:</strong>',
				null,
				null,
				null
			),
			'php'
		);

		$request = array_merge(array('template' => JFactory::getApplication()->getTemplate()), $_REQUEST);
		$request = RedDebugHelper::MultiArrayToSingleArray($request, '$_REQUEST');
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_REQUEST_LABEL'),
				$request,
				count($request),
				'default'
			),
			'request'
		);

		$includes = get_included_files();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_FILES_LABEL'),
				$includes,
				count($includes),
				'includes_files'
			),
			'includes'
		);

		// Get Includes class
		$declaredClasses = get_declared_classes();
		$declaredTmp     = [];

		foreach ($declaredClasses AS $key => $class)
		{
			if (isset($classes[$class]))
			{
				$declaredTmp[$class] = $classes[$class];
			}
			else
			{
				$key = RedDebugHelper::findJoomlaClassFile($class, null, $extensionName);

				if ($key == null)
				{
					$key = JText::sprintf('PLG_SYSTEM_REDDEBUG_DEFAULT_PHP_CLASS_PATH', $extensionName);
				}

				$declaredTmp[$class] = $key;
			}
		}

		$declaredClasses = $declaredTmp;

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_CLASSES_LABEL'),
				$declaredClasses,
				count($declaredClasses),
				'default'
			),
			'classes'
		);

		$constants = get_defined_constants();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_CONSTANTS_LABEL'),
				$constants,
				count($constants),
				'default'
			),
			'constants'
		);

		$server = RedDebugHelper::MultiArrayToSingleArray($_SERVER, '$_SERVER');
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_SERVER_LABEL'),
				$server,
				count($server),
				'default'
			),
			'server'
		);

		$sessionVars = RedDebugHelper::MultiArrayToSingleArray($_SESSION, '$_SESSION');
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_SESSION_LABEL'),
				$sessionVars,
				count($sessionVars),
				'default'
			),
			'session'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_COOKIE_LABEL'),
				RedDebugHelper::MultiArrayToSingleArray($_COOKIE, '$_COOKIE'),
				count($_COOKIE),
				'default'
			),
			'cookie'
		);

		$configs = ini_get_all();
		$configs = RedDebugHelper::MultiArrayToSingleArray($configs, 'INI');

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_INI_LABEL'),
				$configs,
				count($configs),
				'default'
			),
			'php_ini'
		);
	}

	/**
	 * Store log messages so they can be displayed later.
	 * This function is passed log entries by JLogLoggerCallback.
	 *
	 * @param   JLogEntry  $entry  A log entry.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	public function addLog(JLogEntry $entry)
	{
		$category = $entry->category;

		if (!isset(self::$logs[$category]))
		{
			self::$logs[$category] = [];
		}

		self::$logs[$category][] = array(
			'entry' => $entry,
			'debug' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
		);
		self::$logsCount++;
	}
}
