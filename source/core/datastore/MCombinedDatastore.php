<?php

namespace simpleserv\webfilesframework\core\datastore;

use \simpleserv\webfilesframework\core\datastore\MAbstractDatastore;
use \simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 */

/**
 * Combines different datastores in one datastore together. 
 *
 * @package    de.simpleserv.core.datastore
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MCombinedDatastore extends MAbstractDatastore {
	
	private $registeredDatastores = array();
	
	public function tryConnect() {
		return true;
	}
	
	public function isReadOnly() {
		return true;
	}
	
	public function getNextWebfileForTime($time) {
		// nothing todo
	}
	
	public function registerDatastore(MAbstractDatastore $datastore) {
		array_push($this->registeredDatastores, $datastore);
	}
	
	/**
	 * 
	 * @param number $days
	 */
	private function aggregateDatastores($days = 500) {
		
		$nextWebfiles = array();
		$aggregatedWebfiles = array();
		
		// SUBTRACT GIVEN DAYS FROM NOW
		$timeBeforeGivenDays = time() - ($days * 24 * 3600);
		
		// FILL ARRAY "nextWebfiles" WITH INITIAL VALUES
		foreach ($this->registeredDatastores as $datastore) {
			
			$nextWebfile = $datastore->getNextWebfileForTime($timeBeforeGivenDays);
			if ( isset($nextWebfile) ) {
				
				
				$nextWebfileTime = $nextWebfile->getTime();
				
				$nextWebfiles[$nextWebfileTime]['datastore'] = $datastore;
				$nextWebfiles[$nextWebfileTime]['webfile']   = $nextWebfile;
			}
		}
		
		// SELECT FIRST WEBFILE TO BE ADDED TO THE AGGREGATED VALUES
		$nextWebfile = $this->selectNextWebfile($nextWebfiles);
		
		// SELECT THE OTHER WEBFILES
		while ( isset($nextWebfile) ) {
			
			array_push($aggregatedWebfiles, $nextWebfile);
			$nextWebfile = $this->selectNextWebfile($nextWebfiles);
		}
		
		return $aggregatedWebfiles;
	}
	
	/**
	 * 
	 * @param unknown $nextWebfiles
	 */
	private function selectNextWebfile(&$nextWebfiles) {
		
		$oldestTimestamp = time();
		$nextWebfile = null;
		$datastore = null;
		
		foreach ($nextWebfiles as $key => $value) {
			if ( $key < $oldestTimestamp) {
				$oldestTimestamp = $key;
				$nextWebfile = $nextWebfiles[$key]['webfile'];
				$datastore = $nextWebfiles[$key]['datastore'];
			}
		}
		
		if ( isset($datastore) ) {
			$nextWebfileTemp = $datastore->getNextWebfileForTime($oldestTimestamp);
			if ( isset($nextWebfileTemp) ) {
				
				$nextWebfileTempTime = $nextWebfileTemp->getTime();
				
				$nextWebfiles[$nextWebfileTempTime]['webfile'] = $nextWebfileTemp;
				$nextWebfiles[$nextWebfileTempTime]['datastore'] = $datastore;
			}
			
			unset($nextWebfiles[$oldestTimestamp]);
		}
		
		return $nextWebfile;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MAbstractDatastore::getWebfilestream()
	 */
	public function getWebfilestream() {
		
		$webfiles = $this->getWebfilesFromDatastore();
		return new MWebfileStream($webfiles);
	}
	
	public function getLatestWebfiles($count = 5) {
		
	}
	
	public function getByTemplate(MWebfile $webfile) {
		
	}
	
	
	public function getDatasetsFromDatastore() {
		
	}
	
	public function getLatestDatasets($count = 5, $reverse = true) {
		
	}
	
	public function getWebfilesFromDatastore() {
		if ( count($this->registeredDatastores) == 0 ) {
			//throw new Exception
		}
		
		$aggregatedWebfiles = $this->aggregateDatastores();
		return $aggregatedWebfiles;
	}
	
}