<?php

namespace webfilesframework\core\datastore;

use webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\database\MDatabaseConnection;
use webfilesframework\core\datasystem\file\system\MDirectory;

/**
 * Creates depending on the given type a datastore, which can
 * be used to access and store webfiles.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDatastoreFactory
{

	/**
	 * Creates a new datastore. The following input object types are
	 * actually supported:
	 * <ul>
	 *        <li>MDirectory</li>
	 *        <li>MDatabaseConnection</li>
	 * </ul>
	 *
	 * @param MDirectory|MDatabaseConnection $item
	 *
	 * @return MAbstractDatastore Returns a datastore depending on the
	 * given item.
	 * @throws MDatastoreException
	 * @throws \ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
    public static function createDatastore($item)
    {
        if ($item instanceof MDirectory) {
            return new MDirectoryDatastore($item);
        } else if ($item instanceof MDatabaseConnection) {
            return new MDatabaseDatastore($item);
        } else {
            throw new Exeption("Unsupported type to create datastore.");
        }
    }
}