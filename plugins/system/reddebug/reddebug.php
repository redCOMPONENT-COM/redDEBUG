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
 * @since  1
 */
class PlgSystemRedDebug extends JPlugin
{
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
		self::$checkIp = RedDebugHelper::checkDebugMode($this->params->get('ip', ''));

		if (!self::$checkIp)
		{
			return false;
		}

		$app        = JFactory::getApplication();
		$session    = JFactory::getSession();
		$classes    = $session->get('joomlaClasses', array(), 'redDebug');

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
	 * onAfterInitialise
	 *
	 * @return void
	 */
	public function onAfterInitialise()
	{
		if (!self::$checkIp)
		{
			return false;
		}

		$jVersion   = new JVersion;
		$version    = (int) $jVersion->RELEASE;

		if ($version == 2)
		{
			/**
			 * In old joomla we not have this event.
			 * but maybe we need to move change this onAfterRespond
			 * so we get last events from plugin
			 */
			register_shutdown_function(
				array(
					$this,
					'onAfterRespond'
				)
			);
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

		$debugger = RedDebugDebugger::getInstance();
		$debugger->enable();
		$debugger->directory = __DIR__ . DIRECTORY_SEPARATOR . 'layout';

		$dispatcher = RedDebugJoomlaDispatcher::getInstance();
		list($type, $names) = $this->_subject->getInfo(__FUNCTION__);

		self::$logPlugin[] = array(
			'type' => $type,
			'method' => __FUNCTION__,
			'names' => $names,
			'args' => func_get_args()
		);

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
		if (!self::$checkIp)
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
	 * onAfterRespond
	 *
	 * @return void
	 */
	public function onAfterRespond()
	{
		if (!self::$checkIp)
		{
			return false;
		}

		if (static::$afterRespond)
		{
			return;
		}

		static::$afterRespond = true;

		$methods = RedDebugJoomlaModule::getLog();

		$plugin = array();
		$event = array();
		$plg = array();
		$evt = array();

		foreach (self::$logPlugin AS $row)
		{
			$type = $row['type'];
			$method = $row['method'];

			foreach ($row['names'] AS $keyname => $name)
			{
				if (!isset($plugin[$type][$keyname]))
				{
					$plugin[$type][$keyname] = array(
						'name' => $name,
						'method' => array()
					);
				}

				if (!isset($plugin[$type][$keyname]['method'][$method]))
				{
					$plugin[$type][$keyname]['method'][$method] = array(
						'args' => array(),
						'count' => 0
					);
				}

				/**
				 * Plugin list
				 */
				$plugin[$type][$keyname]['method'][$method]['count']++;
				$plugin[$type][$keyname]['method'][$method]['args'][] = is_array($row['args']) ? $row['args'] : array();

				$plg[$keyname] = 1;
				$evt[$method] = 1;

				/**
				 * Help to event list
				 */
				$event[$type][$method]['args'] = is_array($row['args']) ? $row['args'] : array();
				$event[$type][$method]['class'][$keyname] = $plugin[$type][$keyname]['method'][$method]['count'];
			}
		}

		$debug = RedDebugDebugger::getInstance();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Plugin',
				$plugin,
				count($plg),
				'plugin'
			),
			'plugin'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Modules',
				$methods,
				count($methods),
				'module'
			),
			'modules'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Events',
				$event,
				count($evt),
				'event'
			),
			'event'
		);

		$request = array_merge(array('template' => JFactory::getApplication()->getTemplate()), $_REQUEST);
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Request',
				$request,
				count($request),
				'default'
			),
			'request'
		);

		$parms = json_decode(JFactory::getApplication()->getTemplate(true)->params);
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Template parameters',
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
				'Config',
				$config,
				null,
				'default'
			),
			'config'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'User',
				JFactory::getUser(),
				null,
				'default'
			),
			'user'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Info',
				(array) new JVersion,
				null,
				'default'
			),
			'joomlainfo'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'PHP',
				null,
				null,
				null
			),
			'php'
		);

		$includes = get_included_files();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'files',
				$includes,
				count($includes),
				'default'
			),
			'includes'
		);

		$declared_classes = get_declared_classes();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'classes:',
				$declared_classes,
				count($declared_classes),
				'default'
			),
			'classes'
		);

		$constants = get_defined_constants();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'constants',
				$constants,
				count($constants),
				'default'
			),
			'constants'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Server',
				$_SERVER,
				count($_SERVER),
				'default'
			),
			'server'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Session',
				$_SESSION,
				count($_SESSION),
				'ini'
			),
			'session'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Cookie',
				$_COOKIE,
				count($_COOKIE),
				'default'
			),
			'cookie'
		);

		$configs = ini_get_all();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'INI',
				$configs,
				count($configs),
				'ini'
			),
			'php_ini'
		);

		return;
	}
}
