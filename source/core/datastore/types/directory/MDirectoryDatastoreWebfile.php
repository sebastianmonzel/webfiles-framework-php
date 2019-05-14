<?php

namespace webfilesframework\core\datastore\types\directory;

use webfilesframework\core\datasystem\file\format\MWebfile;


use webfilesframework\core\datastore\webfilestream\MWebfileStream;
use webfilesframework\core\datasystem\file\system\MDirectory;
use webfilesframework\core\datastore\MAbstractDatastore;

/**
 * Wrapper class to connect to a datastore based on a directory.
 * <b>Conventions on the datastore:</b>
 * <ul>
 *        <li>filename is equal to the id of the webfile</li>
 *        <li></li>
 * </ul>
 *
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


	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::tryConnect()
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
    public function tryConnect()
    {
        $this->initDatastore();
        return $this->directoryDatastore->tryConnect();
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::isReadOnly()
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
    public function isReadOnly()
    {
        $this->initDatastore();
        return $this->directoryDatastore->isReadOnly();
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getNextWebfileForTimestamp()
	 *
	 * @param $time
	 *
	 * @return MWebfile|null
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 */
    public function getNextWebfileForTimestamp($time)
    {
        $this->initDatastore();
        return $this->directoryDatastore->getNextWebfileForTimestamp($time);
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getWebfilesAsStream()
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
    public function getWebfilesAsStream()
    {
        $this->initDatastore();
        return $this->directoryDatastore->getWebfilesAsStream();
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getWebfilesAsArray()
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
    public function getWebfilesAsArray()
    {
        $this->initDatastore();
        return $this->directoryDatastore->getWebfilesAsArray();
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getLatestWebfiles()
	 *
	 * @param int $count
	 *
	 * @return array
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 */
    public function getLatestWebfiles($count = 5)
    {
        $this->initDatastore();
        return $this->directoryDatastore->getLatestWebfiles($count);
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getByTemplate()
	 *
	 * @param MWebfile $template
	 *
	 * @return array
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
    public function searchByTemplate(MWebfile $template)
    {
        $this->initDatastore();
        return $this->directoryDatastore->searchByTemplate($template);
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getByTemplate()
	 *
	 * @param MWebfile $webfile
	 *
	 * @return void
	 * @throws \ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 */
    public function storeWebfile(MWebfile $webfile)
    {
        $this->initDatastore();
        $this->directoryDatastore->storeWebfile($webfile);
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::storeWebfilesFromStream()
	 *
	 * @param MWebfileStream $webfileStream
	 *
	 * @return void
	 * @throws \ReflectionException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 */
    public function storeWebfilesFromStream(MWebfileStream $webfileStream)
    {
        $this->initDatastore();
        $this->directoryDatastore->storeWebfilesFromStream($webfileStream);
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::deleteByTemplate()
	 *
	 * @param MWebfile $template
	 *
	 * @return void
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
    public function deleteByTemplate(MWebfile $template)
    {
        $this->initDatastore();
        $this->directoryDatastore->deleteByTemplate($template);
    }

	/**
	 * @throws \webfilesframework\core\datastore\MDatastoreException
	 * @throws \webfilesframework\MWebfilesFrameworkException
	 */
	private function initDatastore()
    {
        if (!isset($this->directoryDatastore)) {
            $directory = new MDirectory($this->m_sDirectoryPath);
            $this->directoryDatastore = new MDirectoryDatastore($directory);
        }
    }

}