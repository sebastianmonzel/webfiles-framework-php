<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format;

use \simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * 
 * @author semo
 *
 */
class MYoutubeVideo extends MWebfile {
	
	private $m_sKey;
	
	public static $m__sClassName = __CLASS__;
	
	public function getKey() {
		return $this->m_sKey;
	}
	
	public function __toString() {
		return "<iframe width=\"300\" height=\"169\" src=\"//www.youtube.com/embed/" . $this->m_sKey . "\" frameborder=\"0\" allowfullscreen></iframe>";
	}
}
