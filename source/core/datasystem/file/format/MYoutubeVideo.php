<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format;

use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * description
 * 
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
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
