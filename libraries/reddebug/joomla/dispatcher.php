<?php
/**
 * @copyright  Copyright (C) 2012 - 2015 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

/**
 * Class RedRedubDispatcher
 *
 * @since  1
 */
class RedDebugJoomlaDispatcher extends JEventDispatcher
{
	static public $logger;
	/**
	 * getInstance
	 *
	 * @return RedRedubDispatcher
	 */
	static public function getInstance()
	{
		if (get_class(self::$instance) == 'JEventDispatcher')
		{
			$instance = self::$instance;
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
	 * @return void
	 */
	public function fixed($instance)
	{
		foreach ($instance AS $key => $val)
		{
			$this->{$key} = $val;
		}
	}

	/**
	 * getInfo
	 *
	 * @param   string  $event  Joomla Event Info (same version joomla using)
	 *
	 * @return array
	 */
	public function getInfo($event)
	{
		$event = strtolower($event);

		$names = array();
		$class = array();
		$type = 'none';

		if (isset($this->_methods[$event]))
		{
			foreach ($this->_methods[$event] AS $key)
			{
				if (is_object($this->_observers[$key]))
				{
					$names[] = $this->_observers[$key]->get('_name');
					$class[] = get_class($this->_observers[$key]);
					$type = $this->_observers[$key]->get('_type');
				}
			}
		}

		return array($type, array_combine($class, $names));
	}

	/**
	 * Debugger
	 *
	 * @param   null  $plugin  Object where plugin class and more...
	 *
	 * @return void
	 */
	static public function debugger($plugin = null, $args = null, $value = null, $type = 0, $before = true)
	{
		if($type == 1)
		{
			self::$logger[get_class($plugin)][] = $args;
		}

	}

	/**
	 * trigger
	 * here is version 2 of this. here we change core version of plugin trigger
	 *
	 * @param   string  $event  Event name
	 * @param   array   $args   Gets an array of the function's argument list.
	 *
	 * @todo i have some idea to made this better in next version
	 *
	 * @return array
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
			// Check if the plugin is present.
			if (!isset($this->_observers[$key]))
			{
				continue;
			}

			// Fire the event for an object based observer.
			if (is_object($this->_observers[$key]))
			{
				$args['event'] = $event;
				self::debugger($this->_observers[$key], $args, null, 1, true);
				$value = $this->_observers[$key]->update($args);
				self::debugger($this->_observers[$key], $args, $value, 1, false);
			}
			// Fire the event for a function based observer.
			elseif (is_array($this->_observers[$key]))
			{
				self::debugger($this->_observers[$key]['handler'], $args, null, 2, true);
				$value = call_user_func_array($this->_observers[$key]['handler'], $args);
				self::debugger($this->_observers[$key]['handler'], $args, null, 2, false);
			}

			if (isset($value))
			{
				$result[] = $value;
			}
		}

		return $result;
	}
}
