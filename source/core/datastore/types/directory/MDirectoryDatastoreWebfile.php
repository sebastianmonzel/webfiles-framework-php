<?php

namespace simpleserv\webfilesframework\core\datastore\types\directory;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
use simpleserv\webfilesframework\core\datastore\MAbstractCachableDatastore;
use simpleserv\webfilesframework\core\datastore\MISingleDatastore;
use simpleserv\webfilesframework\core\datastore\MDatastoreException;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectoryWebfileGrabber;
use simpleserv\webfilesframework\core\datasystem\file\system\MFile;
use simpleserv\webfilesframework\core\datasystem\file\format\image\MImage;
use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;
use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;

/**
 * Class to connect to a datastore based on a directory.
 * <b>Conventions on the datastore:</b>
 * <ul>
 * 		<li>filename is equal to the id of the webfile</li>
 * 		<li></li>
 * </ul>
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDirectoryDatastoreWebfile extends MWebfile
								implements MAbstractDatastore {
	
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
	public function tryConnect() {
		initDatastore();
		$this->directoryDatastore->tryConnect();
	}

	/* (non-PHPdoc)
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::isReadOnly()
	 */
	public function isReadOnly() {
		initDatastore();
		$this->directoryDatastore->isReadOnly();
	}

	/* (non-PHPdoc)
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getNextWebfileForTimestamp()
	 */
	public function getNextWebfileForTimestamp($time) {
		initDatastore();
		$this->directoryDatastore->getNextWebfileForTimestamp($time);
	}

	/* (non-PHPdoc)
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getWebfilesAsStream()
	 */
	public function getWebfilesAsStream() {
		initDatastore();
		$this->directoryDatastore->getWebfilesAsStream();
	}

	/* (non-PHPdoc)
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getWebfilesAsArray()
	 */
	public function getWebfilesAsArray() {
		initDatastore();
		$this->directoryDatastore->getWebfilesAsArray();
	}

	/* (non-PHPdoc)
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getLatestWebfiles()
	 */
	public function getLatestWebfiles($count = 5) {
		initDatastore();
		$this->directoryDatastore->getLatestWebfiles($count);
	}

	/* (non-PHPdoc)
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::getByTemplate()
	 */
	public function getByTemplate(MWebfile $template) {
		initDatastore();
		$this->directoryDatastore->getByTemplate($template);
	}

	/* (non-PHPdoc)
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::storeWebfile()
	 */
	public function storeWebfile(MWebfile $webfile) {
		initDatastore();
		$this->directoryDatastore->storeWebfile($webfile);
	}

	/* (non-PHPdoc)
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::storeWebfilesFromStream()
	 */
	public function storeWebfilesFromStream(MWebfileStream $webfileStream) {
		initDatastore();
		$this->directoryDatastore->storeWebfilesFromStream($webfileStream);
	}

	/* (non-PHPdoc)
	 * @see \simpleserv\webfilesframework\core\datastore\MAbstractDatastore::deleteByTemplate()
	 */
	public function deleteByTemplate(MWebfile $template) {
		initDatastore();
		$this->directoryDatastore->deleteByTemplate($template);
	}

	private function initDatastore() {
		if ( ! isset($this->directoryDatastore) ) {
			$directory = new MDirectory($this->m_sDirectoryPath);
			$this->directoryDatastore = new MDirectoryDatastore($directory);
		}
	}

}