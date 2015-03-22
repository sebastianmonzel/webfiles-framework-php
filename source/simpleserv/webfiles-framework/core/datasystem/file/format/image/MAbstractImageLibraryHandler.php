<?php

namespace \simpleserv\webfiles-framework\core\datasystem\file\format\image;

/**
 * 
 * @author semo
 *
 */
abstract class MAbstractImageLibraryHandler extends MItem {
	
	public abstract function loadJpg($p_sImage);
		
	public abstract function loadPng($p_sImage);	
	
}