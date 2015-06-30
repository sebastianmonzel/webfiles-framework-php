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
class MDirectoryDatastore extends MAbstractCachableDatastore 
							implements MISingleDatastore {
	
	private $m_oDirectory;
	public static $m__sClassName = __CLASS__;
	
	
	public function __construct($directory, $isRemoteDatastore = false) {
		echo "test";
		if ( $directory == null ) {
			throw new MDatastoreException("Cannot instantiate a DirectoryDatastore without valid directory.");
		}
		$this->m_oDirectory = $directory;
	}
	
	
	public function tryConnect() {
		return true;
	}
	
	public function getNextWebfileForTime($time) {
		
		$itemGrabber = new MDirectoryWebfileGrabber($this->m_oDirectory);
		$webfiles = $itemGrabber->grabLatestWebfiles(4);
		
		ksort($webfiles);
		
		foreach ($webfiles as $key => $value) {
			if ( $key > $time ) {
				return $value;
			}
		}
		
	}
	
	public function getWebfilestream() {
		
		$files = $this->m_oDirectory->getFiles();
		
		$webfileArray = array();
		
		foreach ($files as $file) {
			$fileExtension = $file->getExtension();
			
			if ( strtolower($fileExtension) == "jpg" || strtolower($fileExtension) == "jpeg" ) {
				
				$normalizedFile = new MFile($file->getFolder() . "/normal/" . $file->getName());
				
				if ( $normalizedFile->exists() ) {
					array_push($webfileArray, new MImage($normalizedFile->getPath()));
				} else {
					array_push($webfileArray, new MImage($file->getPath()));
				}
			} else if ( $fileExtension == "webfile" ) {
				$fileContent = $file->getContent();
				array_push($webfileArray, MWebfile::staticUnmarshall($fileContent));
			} else {
				array_push($webfileArray, $file);
			}
		}
		
		return new MWebfileStream($webfileArray);
	}
	
	public function getDatasetsFromDatastore() {
		$itemGrabber = new MDirectoryWebfileGrabber($this->m_oDirectory);
		return $itemGrabber->grabDatasets();
	}
	
	public function getLatestDatasets($count = 5, $reverse = true) {
		
		$itemGrabber = new MDirectoryWebfileGrabber($this->m_oDirectory);
		$items = $itemGrabber->grabLatestWebfiles($count);
		
		$items = array_slice($items, $count * -1);
		if ( $reverse ) {
			$items = array_reverse($items);
		}
		return $items;
	}
	
	public function getWebfilesFromDatastore() {
		$itemGrabber = new MDirectoryWebfileGrabber($this->m_oDirectory);
		return $itemGrabber->grabWebfiles();
	}
	
	public function getLatestWebfiles($count = 5) {
		$itemGrabber = new MDirectoryWebfileGrabber($this->m_oDirectory);
		return $itemGrabber->grabLatestWebfiles($count);
	}
	
	public function storeWebfile(MWebfile $webfile) {
		$directoryPath = $this->m_oDirectory->getPath();
		$file = new MFile($directoryPath . "/" . $webfile->getId() . ".webfile");
		$file->writeContent($webfile->marshall(),true);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MAbstractDatastore::storeWebfilesFromWebfilestream()
	 */
	public function storeWebfilesFromWebfilestream(MWebfileStream $webfileStream) {
		$webfiles = $webfileStream->getWebfiles();
	
		foreach ($webfiles as $webfile) {
			$this->storeWebfile($webfile);
		}
	
	}
	
	public function hasItem(MWebfile $item) {
		$directoryPath = $this->m_oDirectory->getPath();
		$file = new MFile($directoryPath . "/" . $item->getId() . ".webfile");
		return $file->exists();
	}
	
	public function getByTemplate(MWebfile $template){
		if ( $this->isDatastoreCached() ) {
			
			if ( ! $this->isCacheActual() ) {
				$this->fillCachingDatastore();
			}
			$webfiles = $this->cachingDatastore->getByTemplate($template);
		} else {
			$webfiles = $this->getWebfilesFromDatastore();
			$webfiles = $this->filterWebfilesByTemplate($webfiles,$template);
			return $webfiles;
		}
	}
	
	/**
	 * 
	 * @param unknown $webfiles
	 * @param MWebfile $template
	 * @return multitype:unknown
	 */
	private function filterWebfilesByTemplate($webfiles, MWebfile $template) {
		
		$filteredWebfiles = array();
		$attributes = $template->getAttributes(true);
	    
		foreach ($webfiles as $webfile) {
			
			$validWebfile = true;
			
	    	foreach ($attributes as $attribute) {
	    		
	    		$attribute->setAccessible(true);
	    		
	    		$name  = $attribute->getName();
	    		$templateValue = $attribute->getValue($webfile);
	    		
	    		if ( $value != "?" && ! ($value instanceof MIDatastoreFunction) ) {
	    			
	    			$webfileValue = $attribute->getValue($webfile);
	    			if ( $templateValue != $webfileValue ) {
	    				$validWebfile = false;
	    			}
	    			
	    		}
	    		
	    	}
	    	
	    	if ( $validWebfile ) {
	    		$filteredWebfiles[] = $webfile;
	    	}    	
		}
		
		return $filteredWebfiles;
	}
	
	public function deleteByTemplate(MWebfile $template) {
		$webfiles = $this->getByTemplate($template);
		
		if ( $this->isDatastoreCached() ) {
			$this->cachingDatastore->deleteByTemplate($template);
		}
		
		foreach ($webfiles as $webfile) {
			$file = new MFile($directoryPath . "/" . $webfile->getId() . ".webfile");
			$file->delete();
		}
	}
	
	public function getLatestCachingTime() {
		
	}
	
	public function isCacheActual() {
		return false;
	}
	
}