<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format\image;

use \simpleserv\webfilesframework\core\datasystem\file\format\image\MAbstractImageLibraryHandler;


/**
 * 
 * @author semo
 *
 */
class MGdHandler extends MAbstractImageLibraryHandler {
	
	/**
	 * (non-PHPdoc)
	 * @see MAbstractImageLibraryHandler::loadJpg()
	 */
	public function loadJpg($p_sImage) {
		return imagecreatefromjpeg($p_sImage);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see MAbstractImageLibraryHandler::loadPng()
	 */
	public function loadPng($p_sImage) {
		return imagecreatefrompng($p_sImage);
	}
	
}