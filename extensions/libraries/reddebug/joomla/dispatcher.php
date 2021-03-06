<?php
/**
 * @copyright  Copyright (C) 2015 - 2018 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */
defined('_JEXEC') or die;

/**
 * Class RedRedubDispatcher
 *
 * @since  1.0.0
 */
class RedDebugJoomlaDispatcher extends JEventDispatcher
{
	/**
	 * @var    array
	 * @since  1.0.0
	 */
	static public $logger = array();
	/**
	 * getInstance
	 *
	 * @return self
	 * @since  1.0.0
	 */
	static public function getInstance()
	{
		if (get_class(self::$instance) == 'JEventDispatcher')
		{
			$instance       = self::$instance;
			self::$instance = new self;
			self::$instance->fixed(clone $instance);
		}

		foreach (self::$instance->_observers AS $key => $row)
		{
			if (is_object($row))
			{
				$subject = self::$instance->_observers[$key]->get('_subject', null);

				if ($subject != null && (get_class($subject) == 'JEventDispatcher'))
				{
					self::$instance->_observers[$key]->set('_subject', self::$instance);
				}
			}
		}

		JFactory::$application->loadDispatcher(self::$instance);

		return self::$instance;
	}

	/**
	 * Fixed some issue i have in testing
	 *
	 * @param   JEventDispatcher  $instance  JEventDispatcher
	 *
	 * @return  void
	 * @since   1.0.0
	 */
	public function fixed($instance)
	{
		foreach ($instance AS $key => $val)
		{
			$this->{$key} = $val;
		}
	}

	/**
	 * debugger
	 *
	 * @param   JPlugin  $plugin  Plugin
	 * @param   null     $event   Events
	 * @param   null     $args    Args
	 *
	 * @return object
	 * @since   1.0.0
	 */
	static public function debugger($plugin = null, $event = null, $args = null)
	{
		$jProfile = new JProfiler;

		$class = 'none';

		if (is_object($plugin))
		{
			$class = get_class($plugin);
		}
		elseif (is_array($plugin))
		{
			if (is_object($plugin['handler']))
			{
				$class = get_class($plugin['handler']);
			}
			elseif (is_string($plugin['handler']))
			{
				$class = $plugin['handler'] . ' (function)';
			}
			elseif (is_array($plugin['handler']))
			{
				$class = get_class(reset($plugin['handler']));
			}
		}

		if (!isset(self::$logger[$class]))
		{
			self::$logger[$class] = array();
		}

		if (!isset(self::$logger[$class][$event]))
		{
			self::$logger[$class][$event] = array();
		}

		$isJObject = $plugin instanceof JPlugin;
		$result    = (object) array(
			'plugin'	=> $class,
			'args'		=> $args,
			'value'		=> null,
			'profile'	=> $jProfile,
			'type'		=> $isJObject ? $plugin->get('_type', null) : null
		);

		self::$logger[$class][$event][] = $result;

		return $result;
	}

	/**
	 * trigger
	 * here is version 2 of this. here we change core version of plugin trigger
	 *
	 * @param   string  $event  Event name
	 * @param   array   $args   Gets an array of the function's argument list.
	 *
	 *
	 * @return array
	 * @since  1.0.0
	 *
	 * @todo i have some idea to made this better in next version
	 */
	public function trigger($event, $args = array())
	{
		$result = array();

		/*
		 * If no arguments were passed, we still need to pass an empty array to
		 * the call_user_func_array function.
		 */
		$args = (array) $args;

		$event = strtolower($event);

		// Check if any plugins are attached to the event.
		if (!isset($this->_methods[$event]) || empty($this->_methods[$event]))
		{
			// No Plugins Associated To Event!
			return $result;
		}

		// Loop through all plugins having a method matching our event
		foreach ($this->_methods[$event] as $key)
		{
			$value = '';

			// Check if the plugin is present.
			if (!isset($this->_observers[$key]))
			{
				continue;
			}

			$debug = self::debugger($this->_observers[$key], $event, $args);
			$debug->profile->mark('before');

			// Fire the event for an object based observer.
			if (is_object($this->_observers[$key]))
			{
				$args['event'] = $event;
				$value         = $this->_observers[$key]->update($args);
			}
			// Fire the event for a function based observer.
			elseif (is_array($this->_observers[$key]))
			{
				$value = call_user_func_array($this->_observers[$key]['handler'], $args);
			}

			$debug->value      = $value;
			$debug->args_after = $args;
			$debug->profile->mark('after');

			if (isset($value))
			{
				$result[] = $value;
			}
		}

		return $result;
	}
}
