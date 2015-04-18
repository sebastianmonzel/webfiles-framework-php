<?php

namespace simpleserv\webfilesframework\core\datastore;

use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;
use simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatastore;

use simpleserv\webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use simpleserv\webfilesframework\MItem;

/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 */

/**
 * description
 *
 * @package    de.simpleserv.core.datastore
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MDatastoreFactory {
	
	public static function createDatastore(MItem $item) {
		if ( $item instanceof MDirectory ) {
			return new MDirectoryDatastore($item);
		} else if ($item instanceof MDatabaseConnection) {
			return new MDatabaseDatastore($item);
		} else {
			return null;
		}
	}
}