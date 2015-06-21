<?php

namespace simpleserv\webfilesframework\core\datasystem\file\system;

use simpleserv\webfilesframework\MItem;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 */

/**
 * Encapsulates the access on directories. 
 *
 * @package    de.simpleserv.core.filesystem
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MDirectory extends MItem {

    protected $m_sPath;

    public function __construct($p_sPath) {
        $this->m_sPath = $p_sPath;
    }

    
	/**
     * Returns the names of all files in the directory as an array.
     * @return MArray - array with filenames
     */
    public function getFiles() {
    	
        $filesArray = array();
		
        if ($oDiractoryHandle = opendir($this->m_sPath)) {
            while (false !== ($sFileName = readdir($oDiractoryHandle))) {
                if ( $sFileName != "." && $sFileName != ".." && ( ! is_dir($this->m_sPath . "/" . $sFileName) ) ) {
                    $file = new MFile($this->getPath() . "/" . $sFileName);
                	array_push($filesArray, $file);
                }
            }
        }
        return $filesArray;
    }
    
    public function getLatestFiles($count ) {
    	
    	$filesArray = $this->getFiles();
    	$latestFilesArray = array_slice($filesArray, $count * -1);
    	return $latestFilesArray;
    }
    
    /**
     * Returns the names of all files in the directory as an array.
     * @return array with filenames
     */
    public function getFileNames() {
    	
    	$filesArray = $this->getFiles();
        $filenamesArray = array();
        
        foreach ($filesArray as $file) {
        	$sFileName = $file->getName();
        	array_push($filenamesArray, $sFileName);
        }
		sort($filenamesArray);
        return $filenamesArray;
    }
	
    /**
     * Returns the subdirectories of the current directory.
     * 
     * @return array list of directories
     */
	public function getSubdirectories() {
        $directories = array();
        if ($directoryHandle = opendir($this->m_sPath)) {
            while (false !== ($sFileName = readdir($directoryHandle))) {
                if ( $sFileName != "." && $sFileName != ".." && ( is_dir($this->m_sFolderName . "/" . $sFileName) ) ) {
                    array_push($directories, $sFileName);
                }
            }
        }
		sort($directories);
        return $directories;
    }
    
    /**
     * Creates the present directory.
     */
    public function create() {
    	mkdir($this->m_sPath, 0700, TRUE);
    }
	
    /**
     * Creates a subdirectory in the present directory.
     */
    public function createSubDirectory($p_sName) {
    	
    	$subdirectoryPath = $this->m_sPath . "/" . $p_sName;
    	
    	$subdirectory = new MDirectory($subdirectoryPath);
    	
    	if ( ! $subdirectory->exists() ) {		
	        mkdir($subdirectoryPath);
    	}
    	
        return $subdirectory;
    }

    /**
     * Returns the manually defined name of the folder.
     * @return String folderName
     */
    public function getPath() {
        return $this->m_sPath;
    }

    /**
     * Checks if the present directory exists or not.
     * 
     * @return boolean Returns if the present directory exists or not.
     */
    public function exists() {
        return file_exists($this->m_sPath);
    }
    
    /**
     * Grabs all webfiles that exist in file representation in the
     * present directory.
     * 
     * @return array list of webfiles.
     */
    public function grabWebfiles() {
    
    	$filesArray = $this->getFiles();
    	$webfilesArray = convertFilesToWebfileObjects($filesArray);
    
    	return $webfilesArray;
    }
    
    public function grabLatestWebfiles($count) {
    
    	$filesArray = $this->getLatestFiles($count);
    	$webfilesArray = convertFilesToWebfileObjects($filesArray);
    
    	return $webfilesArray;
    }
    
    private function convertFilesToWebfileObjects($filesArray) {
    	
    	$webfilesArray = array();
    	
    	foreach ($filesArray as $file) {
    	
    		$fileContent = $file->getContent();
    		$webfile = MWebfile::staticUnmarshall($fileContent);
    		 
    		$time = $file->getDate();
    		$webfile->setTime($time);
    		$webfilesArray[$time] = $webfile;
    	}
    	
    	return $webfilesArray;
    	
    }
    
    /**
     *
     * Enter description here ...
     */
    public function grabDatasets() {
    	$datasetsArray = array();
    
    	$webfilesArray = $this->grabWebfiles();
    
    	foreach ($webfilesArray as $webfile) {
    		$webfileDataset = $webfile->getDataset();
    		$datasetsArray[] = $webfileDataset;
    	}
    	return $datasetsArray;
    }

}
