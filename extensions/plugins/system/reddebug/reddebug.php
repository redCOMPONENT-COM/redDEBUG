<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;
define('REDDEBUG', 0);

JLoader::import('reddebug.library');

/**
 * Class PlgSystemRedDebug
 *
 * @since  1.0
 */
class PlgSystemRedDebug extends JPlugin
{
	/**
	 * @var bool
	 */
	protected $autoloadLanguage = true;

	/**
	 * @var array
	 */
	public static $logPlugin;

	/**
	 * @var bool
	 */
	public static $afterRespond = false;

	/**
	 * @var bool
	 */
	protected static $reset = false;

	/**
	 * @var string
	 */
	protected static $ModuleHelperName = 'default';

	/**
	 * @var bool
	 */
	protected static $checkIp = false;

	/**
	 * __construct
	 *
	 * @param   object  &$subject  Subject
	 * @param   array   $config    Config
	 *
	 * @throws Exception
	 */
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		// Check debug mode for this page
		$arrayValidIPs = array();
		$validIPs = trim($this->params->get('ip'));

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

		$app        = JFactory::getApplication();
		$session    = JFactory::getSession();
		$classes    = $session->get('joomlaClasses', array(), 'redDebug');
		$dispatcher = RedDebugJoomlaDispatcher::getInstance();

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
	}

	/**
	 * Does the plugin need to be loaded?
	 *
	 * @return  boolean
	 *
	 * @since   1.0.1
	 */
	protected function isActive()
	{
		$inAdmin = $this->params->get('in_admin');

		if (!JFactory::getApplication()->isSite() && $inAdmin == 0)
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

		return true;
	}

	/**
	 * Detect if current request is using AJAX
	 *
	 * @return  boolean
	 *
	 * @since   1.0.1
	 */
	protected function isAjaxRequest()
	{
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

	/**
	 * onAfterInitialise
	 *
	 * @return void
	 */
	public function onAfterInitialise()
	{
		if (!$this->isActive())
		{
			return false;
		}

		$jVersion   = new JVersion;
		$version    = (int) $jVersion->RELEASE;

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
	 * @return void
	 */
	public function onAfterRender()
	{
		if (!$this->isActive())
		{
			return false;
		}

		$app            = JFactory::getApplication();
		$session        = JFactory::getSession();
		$classes        = $session->get('joomlaClasses', array(), 'redDebug');

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
	 * @return void
	 */
	public function onAfterJoomla()
	{
		if (!$this->isActive())
		{
			return false;
		}

		if (static::$afterRespond)
		{
			return;
		}

		static::$afterRespond = true;

		$methods = RedDebugJoomlaModule::getLog();

		$app            = JFactory::getApplication();
		$session        = JFactory::getSession();

		$classes        = $session->get('joomlaClasses', array(), 'redDebug');

		$plugins = RedDebugJoomlaDispatcher::$logger;
		$event_count = 0;
		$plg = array();
		$evt = array();

		foreach ($plugins AS $plugin => $events)
		{
			foreach ($events AS $event => $info)
			{
				$plg[($info[0]->type)][$plugin][$event] = array();
				$plg[($info[0]->type)][$plugin][$event]['count']	= count($info);
				$plg[($info[0]->type)][$plugin][$event]['time']		= 0;
				$plg[($info[0]->type)][$plugin][$event]['memory']	= 0;

				foreach ($info AS $key => $row)
				{
					$mark = $row->profile->getMarks();

					if (count($mark) == 2)
					{
						$time = $mark[1]->totalTime - $mark[0]->totalTime;
						$memory = $mark[1]->totalMemory - $mark[0]->totalMemory;

						$plg[($info[0]->type)][$plugin][$event]['time'] = $time;
						$plg[($info[0]->type)][$plugin][$event]['memory'] = $memory;
					}
				}

				if (!isset($eve[($info[0]->type)][$event]))
				{
					$event_count++;
					$eve[($info[0]->type)][$event] = array();
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

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_PLUGINS_LABEL'),
				$plg,
				count($plugins),
				'plugin'
			),
			'plugin'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_EVENT_LABEL'),
				$eve,
				$event_count,
				'event'
			),
			'event'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_MODULES_LABEL'),
				$methods,
				count($methods),
				'module'
			),
			'modules'
		);

		$parms = json_decode(JFactory::getApplication()->getTemplate(true)->params);
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_TEMPLATE_LABEL'),
				$parms,
				null,
				'default'
			),
			'template'
		);

		$config = (array) new JConfig;
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

		$jUser  = JFactory::getUser();
		$user   = get_object_vars($jUser);
		$user	= RedDebugHelper::MultiArrayToSingleArray((object) $user, 'JUser');

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
			$class = new ReflectionClass('JControllerLegacy');
			$propsStatic = $class->getStaticProperties();
			$data = RedDebugHelper::MultiArrayToSingleArray(RedDebugHelper::removeRecursion($propsStatic));
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

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_JOOMLA_VERSION_LABEL'),
				(array) new JVersion,
				null,
				'default'
			),
			'joomlainfo'
		);

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

		/**
		 * Get Includes class
		 */
		$declared_classes = get_declared_classes();
		$declared_tmp = array();

		foreach ($declared_classes AS $key => $class)
		{
			if (isset($classes[$class]))
			{
				$declared_tmp[$class] = $classes[$class];
			}
			else
			{
				$key = RedDebugHelper::findJoomlaClassFile($class, null, $extension_name);

				if ($key == null)
				{
					$key = JText::sprintf('PLG_SYSTEM_REDDEBUG_DEFAULT_PHP_CLASS_PATH', $extension_name);
				}

				$declared_tmp[$class] = $key;
			}
		}

		$declared_classes = $declared_tmp;

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_CLASSES_LABEL'),
				$declared_classes,
				count($declared_classes),
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

		$session_vars = RedDebugHelper::MultiArrayToSingleArray($_SESSION, '$_SESSION');
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				JText::_('PLG_SYSTEM_REDDEBUG_SESSION_LABEL'),
				$session_vars,
				count($session_vars),
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

		return;
	}
}