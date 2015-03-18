<?php
/**
 * @copyright  Copyright (C) 2012 - 2014 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

/**
 * Class RedRedubDispatcher
 *
 * @since  1
 */
class RedDebugJoomlaDispatcher extends JEventDispatcher
{
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
	 * trigger
	 *
	 * @param   string  $event  Event name
	 * @param   array   $args   Gets an array of the function's argument list.
	 *
	 * @return array
	 */
	public function trigger($event, $args = array())
	{
		list($type, $names) = $this->getInfo($event);
		PlgSystemRedDebug::$logPlugin[] = array(
			'type' => $type,
			'method' => $event,
			'names' => $names,
			'args' => $args
		);

		return parent::trigger($event, $args);
	}
}
