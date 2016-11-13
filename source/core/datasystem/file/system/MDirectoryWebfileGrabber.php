<?php

namespace simpleserv\webfilesframework\core\datasystem\file\system;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
use simpleserv\webfilesframework\core\datastore\MDatastoreException;

/**
 * Converts files in a directory into webfiles.
 *
 * @package    de.simpleserv.core.filesystem
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MDirectoryWebfileGrabber
{

    private $directory;

    /**
     * @param $directory
     * @throws MDatastoreException
     */
    public function __construct($directory)
    {

        if ($directory == null) {
            throw new MDatastoreException("Cannot instantiate a DirectoryItemGrabber without valid directory.");
        }

        $this->directory = $directory;

    }

    /**
     *
     * Enter description here ...
     */
    public function grabWebfiles()
    {

        $filesArray = $this->directory->getFiles();
        $objectsArray = array();

        foreach ($filesArray as $file) {

            $fileContent = $file->getContent();

            $item = MWebfile::staticUnmarshall($fileContent);
            $time = $file->getDate();

            $objectsArray[$time] = $item;
        }

        return $objectsArray;
    }

    public function grabLatestWebfiles($count)
    {

        $filesArray = $this->directory->getLatestFiles($count);
        $objectsArray = array();

        foreach ($filesArray as $file) {

            $fileContent = $file->getContent();

            $item = MWebfile::staticUnmarshall($fileContent);
            $time = $file->getDate();
            $item->setTime($time);

            $objectsArray[$time] = $item;
        }

        return $objectsArray;
    }
}

