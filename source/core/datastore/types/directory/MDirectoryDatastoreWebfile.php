<?php

namespace webfilesframework\core\datastore\types\directory;

use webfilesframework\core\datastore\MAbstractDatastore;
use webfilesframework\core\datastore\MDatastoreException;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;
use webfilesframework\core\datasystem\file\system\MDirectory;
use webfilesframework\MWebfilesFrameworkException;


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


	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::tryConnect()
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    public function tryConnect()
    {
        $this->initDatastore();
        return $this->directoryDatastore->tryConnect();
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::isReadOnly()
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    public function isReadOnly()
    {
        $this->initDatastore();
        return $this->directoryDatastore->isReadOnly();
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getNextWebfileForTimestamp()
	 *
	 * @param $timestamp
	 *
	 * @return MWebfile|null
	 * @throws MWebfilesFrameworkException
	 * @throws MDatastoreException
	 * @throws \ReflectionException
	 */
    public function getNextWebfileForTimestamp($timestamp)
    {
        $this->initDatastore();
        return $this->directoryDatastore->getNextWebfileForTimestamp($timestamp);
    }

	/**
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 *@see \webfilesframework\core\datastore\MAbstractDatastore::getAllWebfiles()
	 */
    public function getAllWebfiles()
    {
        $this->initDatastore();
        return $this->directoryDatastore->getAllWebfiles();
    }

	/**
	 * @see \webfilesframework\core\datastore\MAbstractDatastore::getLatestWebfiles()
	 *
	 * @param int $count
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws MDatastoreException
	 * @throws \ReflectionException
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
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
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
	 * @throws MWebfilesFrameworkException
	 * @throws MDatastoreException
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
	 * @throws MWebfilesFrameworkException
	 * @throws MDatastoreException
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
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    public function deleteByTemplate(MWebfile $template)
    {
        $this->initDatastore();
        $this->directoryDatastore->deleteByTemplate($template);
    }

	/**
	 * @throws \ReflectionException
	 * @throws MWebfilesFrameworkException
	 * @throws MDatastoreException
	 */
	private function initDatastore()
    {
        if (!isset($this->directoryDatastore)) {
            $directory = new MDirectory($this->m_sDirectoryPath);
            $this->directoryDatastore = new MDirectoryDatastore($directory);
        }
    }

}