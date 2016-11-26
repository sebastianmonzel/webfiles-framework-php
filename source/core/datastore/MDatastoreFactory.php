<?php

namespace simpleserv\webfilesframework\core\datastore;

use simpleserv\webfilesframework\core\datasystem\database\MDatabaseConnection;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;
use simpleserv\webfilesframework\core\datastore\types\directory\MDirectoryDatasourceDatastore;

use simpleserv\webfilesframework\core\datastore\types\database\MDatabaseDatasourceDatastore;
use simpleserv\webfilesframework\MItem;

/**
 * Creates depending on the given type a datastore, which can
 * be used to access and store webfiles.
 *
 * @author     simpleserv company < info@simpleserv.de >
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
     * @param MItem $item
     * @return MAbstractDatastore Returns a datastore depending on the
     * given item.
     */

    /**
     *
     * @param MItem $item
     * @return null|MDatabaseDatasourceDatastore|MDirectoryDatasourceDatastore
     */
    public static function createDatastore(MItem $item)
    {
        if ($item instanceof MDirectory) {
            return new MDirectoryDatasourceDatastore($item);
        } else if ($item instanceof MDatabaseConnection) {
            return new MDatabaseDatasourceDatastore($item);
        } else {
            return null;
        }
    }
}