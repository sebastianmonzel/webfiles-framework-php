<?php

namespace \simpleserv\webfiles-framework\core\datasystem\file\system;

/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 *
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
class MFile extends MWebfile {
	
	protected $m_sFolderName = "./";
	protected $m_sFileName;
	
	protected $m_dDate;
	
	
	public function __construct($fileName) {
		
		if ( $this->containsFileseperator($fileName) ) {
			$this->m_sFolderName = static::extractFolderName($fileName);
			$this->m_sFileName = static::extractFileName($fileName);
		} else {
			$this->m_sFileName = $fileName;
		}
		
	}
	
	
	public function containsFileseperator($fileName) {
		if ( strpos($fileName, "\\") !== FALSE ) {
			return true;
		} else if ( strpos($fileName, "/") !== FALSE ) {
			return true;
		}
		return false;
	}
	
	/**
	 * Returns the content of the given file.
	 * @return MString: file content
	 */
	public function getContent() {
		return file_get_contents($this->getPath());
	}
	
	/**
	 * Writes content to harddrive.
	 * 
	 * @param unknown_type $content
	 * @param unknown_type $overwrite
	 */
	public function writeContent($content, $overwrite = false) {
		
		if ($overwrite) {
			$fh = fopen($this->getPath(), 'w');
		} else {
			$fh = fopen($this->getPath(), 'w+');
		}
		fwrite($fh, $content);
		fclose($fh);
	}
	
	/**
	 * Checks if the file exists.
	 */
	public function exists() {
		return (file_exists($this->getPath()) || @fopen($path,"r")==true);
	}
	
	public static function extractFileName($filePath) {
		if ( strrpos($filePath, "/") !== FALSE ) {
			$filePath = preg_replace('~(/+)~', '/', $filePath);
			return substr($filePath, strrpos($filePath, "/")+1);
		} else if ( strrpos($filePath, "\\") !== FALSE ) {
			return substr($filePath, strrpos($filePath, "\\")+1);
		}
	}
	
	public static function extractFolderName($filePath) {
		if ( strrpos($filePath, "/") !== FALSE ) {
			$filePath = preg_replace('~(/+)~', '/', $filePath);
			return substr($filePath, 0, strrpos($filePath, "/")+1);
		} else if ( strrpos($filePath, "\\") !== FALSE ) {
			return substr($filePath, 0, strrpos($filePath, "\\")+1);
		}
	}
	
	/**
	 * 
	 */
	public function getPath() {
		return $this->m_sFolderName . "/" . $this->m_sFileName;
	}
	
	/**
	 * Returns the given file name.
	 */
	public function getName() {
		return $this->m_sFileName;
	}
	
	/**
	 * Returns the given folder name.
	 */
	public function getFolder() {
		return $this->m_sFolderName;
	}
	
	/**
	 * 
	 */
	public function getExtension() {
		
		$pointPosition = strrpos($this->m_sFileName, ".");
		
		if ( $pointPosition === FALSE ) {
			return "";
		} else {
			return substr($this->m_sFileName, $pointPosition+1);
		}
		
	}
	
	/**
	 * Deletes the specified file.
	 */
	public function delete() {
		unlink($this->getPath());
	}
	
	public function getDate()
	{
		return $this->m_dDate;
	}
	
	public function setDate($date)
	{
		$this->m_dDate = $date;
	}
	
	public function __toString() {
		return "File \"" . $this->getName() . "\"<br />";
	}
	
}