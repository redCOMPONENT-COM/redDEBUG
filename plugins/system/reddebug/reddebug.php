<?php
/**
 * @copyright  Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('_JEXEC') or die;

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
	 * onAfterInitialise
	 *
	 * @return void
	 */
	public function onAfterInitialise()
	{
		$jVersion = new JVersion;
		$version = (int) $jVersion->RELEASE;

		if ($version == 2)
		{
			/**
			 * in old joomla we not have this event.
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
	 * onAfterRespond
	 *
	 * @return void
	 */
	public function onAfterRespond()
	{
		if (static::$afterRespond)
		{
			return;
		}

		static::$afterRespond = true;

		$app = JFactory::getApplication();
		$method = new ReflectionMethod('JModuleHelper', 'load');
		$method->setAccessible(true);
		$methods = $method->invoke(null);

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
		unset($config['password'], $config['ftp_pass'], $config['smtppass']);

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
				'PHP:',
				null,
				null,
				null
			),
			'php'
		);

		$includes = get_included_files();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'files:',
				$includes,
				count($includes),
				'default'
			),
			'includes'
		);

		$declared_classes = get_declared_classes();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'files:',
				$declared_classes,
				count($declared_classes),
				'default'
			),
			'classes'
		);

		$constants = get_defined_constants();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'files:',
				$constants,
				count($constants),
				'default'
			),
			'constants'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Server:',
				$_SERVER,
				count($_SERVER),
				'default'
			),
			'server'
		);

		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'Session:',
				$_SESSION,
				count($_SESSION),
				'default'
			),
			'session'
		);

		$configs = ini_get_all();
		$debug->getBar()->addPanel(
			new RedDebugPanelList(
				'INI:',
				$configs,
				count($configs),
				'ini'
			),
			'php_ini'
		);

		return;
	}
}
