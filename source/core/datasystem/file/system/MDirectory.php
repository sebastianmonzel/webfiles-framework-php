<?php

namespace simpleserv\webfilesframework\core\datasystem\file\system;

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
                    $file->setDate(filemtime($file->getPath()));
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
     * @return MArray - array with filenames
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
	
	public function getSubdirectories() {
        $oDirectories = array();
        if ($oDiractoryHandle = opendir($this->m_sPath)) {
            while (false !== ($sFileName = readdir($oDiractoryHandle))) {
                if ( $sFileName != "." && $sFileName != ".." && ( is_dir($this->m_sFolderName . "/" . $sFileName) ) ) {
                    array_push($oDirectories, $sFileName);
                }
            }
        }
		sort($oDirectories);
        return $oDirectories;
    }
    
    public function create() {
    	mkdir($this->m_sPath, 0700, TRUE);
    }

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
     *
     */
    public function exists() {
        return file_exists($this->m_sPath);
    }

}
