<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format\image;

use simpleserv\webfilesframework\MItem;

/**
 * 
 * @author semo
 *
 */
abstract class MAbstractImageLibraryHandler extends MItem {
	
	public abstract function loadJpg($p_sImage);
		
	public abstract function loadPng($p_sImage);	
	
}