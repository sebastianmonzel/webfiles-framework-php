<?php

namespace simpleserv\webfilesframework\core\datastore\types\directory;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;


use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;
use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;

/**
 * Wrapper class to connect to a datastore based on a directory.
 * <b>Conventions on the datastore:</b>
 * <ul>
 *        <li>filename is equal to the id of the webfile</li>
 *        <li></li>
 * </ul>
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDirectoryDatastoreWebfile extends MAbstractDatastore
{

    private $m_sDirectoryPath;
    /**
     *
     * @var MDirectoryDatastore
     */
    private $directoryDatastore;

    public static $m__sClassName = __CLASS__;


    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::tryConnect()
     */
    public function tryConnect()
    {
        $this->initDatastore();
        return $this->directoryDatastore->tryConnect();
    }

    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::isReadOnly()
     */
    public function isReadOnly()
    {
        $this->initDatastore();
        return $this->directoryDatastore->isReadOnly();
    }

    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getNextWebfileForTimestamp()
     */
    public function getNextWebfileForTimestamp($time)
    {
        $this->initDatastore();
        return $this->directoryDatastore->getNextWebfileForTimestamp($time);
    }

    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getWebfilesAsStream()
     */
    public function getWebfilesAsStream()
    {
        $this->initDatastore();
        return $this->directoryDatastore->getWebfilesAsStream();
    }

    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getWebfilesAsArray()
     */
    public function getWebfilesAsArray()
    {
        $this->initDatastore();
        return $this->directoryDatastore->getWebfilesAsArray();
    }

    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getLatestWebfiles()
     */
    public function getLatestWebfiles($count = 5)
    {
        $this->initDatastore();
        return $this->directoryDatastore->getLatestWebfiles($count);
    }

    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getByTemplate()
     */
    public function searchByTemplate(MWebfile $template)
    {
        $this->initDatastore();
        return $this->directoryDatastore->searchByTemplate($template);
    }

    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::storeWebfile()
     */
    public function storeWebfile(MWebfile $webfile)
    {
        $this->initDatastore();
        return $this->directoryDatastore->storeWebfile($webfile);
    }

    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::storeWebfilesFromStream()
     */
    public function storeWebfilesFromStream(MWebfileStream $webfileStream)
    {
        $this->initDatastore();
        return $this->directoryDatastore->storeWebfilesFromStream($webfileStream);
    }

    /* (non-PHPdoc)
     * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::deleteByTemplate()
     */
    public function deleteByTemplate(MWebfile $template)
    {
        $this->initDatastore();
        return $this->directoryDatastore->deleteByTemplate($template);
    }

    private function initDatastore()
    {
        if (!isset($this->directoryDatastore)) {
            $directory = new MDirectory($this->m_sDirectoryPath);
            $this->directoryDatastore = new MDirectoryDatastore($directory);
        }
    }

}