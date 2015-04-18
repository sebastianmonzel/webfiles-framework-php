<?php

namespace simpleserv\webfilesframework\core\datastore\types\remote;


use simpleserv\webfilesframework\core\datastore\MAbstractDatastore;
use simpleserv\webfilesframework\core\io\request\MPostHttpRequest;
use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Makes it 
 * 
 * @author semo
 */
class MRemoteDatastore extends MAbstractDatastore {
	
	private $m_sWebfilesUrl;
	private $datastoreId;
	
	public static $m__sClassName = __CLASS__;
	
	public function __construct($contentInfoServiceUrl, $datastoreId) {
		$this->m_sWebfilesUrl = $contentInfoServiceUrl;
		$this->m_sDatastoreName = $datastoreId;
	}
	
	public function tryConnect() {
		return true;
	}
	
	public function isReadOnly() {
		return true;
	}
	
	public function getWebfilestream($data = null) {
		
		$requestResult = $this->makeRequest($data);
		return new MWebfileStream($requestResult);
	}
	
	
	private function makeRequest($data = null) {
		
		$requestUrl = $this->m_sWebfilesUrl . "/services/contentInformationService/?datastoreId=" . $this->m_sDatastoreName;
		$request = new MPostHttpRequest($requestUrl, $data);
		$webfilestreamContent = $request->makeRequest();
		
		return $webfilestreamContent;
	}
	
	public function getDatasetsFromDatastore() {
		
	}
	
	public function getLatestDatasets($count = 5, $reverse = true) {
		// TODO
	}
	
	public function getWebfilesFromDatastore() {
		return $this->getWebfilestream()->getWebfiles();
	}
	
	public function getLatestWebfiles($count = 5){
		// TODO
	}
	
	public function getByTemplate(MWebfile $webfile) {
		
		$data = array();
		$data['method'] = "getByTemplate";
		$data['template'] = $webfile->marshall();
		
		return $this->getWebfilestream($data)->getWebfiles();
	}
	
}