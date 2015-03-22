<?php

namespace \simpleserv\webfiles-framework\core\datasystem\file\format\image;

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