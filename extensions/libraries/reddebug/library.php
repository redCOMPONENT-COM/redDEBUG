<?php
/**
 * RedDebug Library file.
 * Including this file into your application will make RedDESK available to use.
 *
 * @package    RedDebug.Library
 * @copyright  Copyright (C) 2013 redCOMPONENT.com. All rights reserved.
 * @license    GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

// Define RedDESK Library Folder Path
define('JPATH_REDDEBUG_LIBRARY', __DIR__);

// Register libraries prefix
JLoader::registerPrefix('RedDebug', JPATH_REDDEBUG_LIBRARY, true);
JLoader::discover('RedDebug', JPATH_REDDEBUG_LIBRARY, true);
JLoader::setup();
