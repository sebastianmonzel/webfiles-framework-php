<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format\xml\rss;

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
 * @package    de.simpleserv.core.xml.rss
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MRssFeed {
	
	private $title;
	private $link;
	private $description;
	private $language = "en-us";
	private $pubDate;
	
	
	private $itemList;
	private $tagList;

	
	/**
	 * 
	 * Enter description here ...
	 */
	function __construct() {
		$this->itemList = array();
		$this->tagList  = array();
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $item
	 */
	function addItem($item) {
		$this->itemList[] = $item;
	}
	

	public function addTag($tag, $value) {
		$this->tagList[$tag] = $value;
	}

	public function getCode() {
		
		$code  = $this->header();
		$code .= "<channel>\n";
		$code .= "	<title>" . $this->title . "</title>\n";
		$code .= "	<link>" . $this->link . "</link>\n";
		$code .= "	<description>" . $this->description . "</description>\n";
		$code .= "	<language>" . $this->language . "</language>\n";
		$code .= "	<pubDate>" . $this->getPubDate() . "</pubDate>\n";

		foreach($this->tagList as $key => $val) {
			$code .= "<$key>$val</$key>\n";
		}
		foreach($this->itemList as $item) {
			$code .= $item->getCode();
		}

		$code .= "</channel>\n";
		
		$code .= $this->footer();
		$code = str_replace("&", "&amp;", $code);

		return $code;
	}

	/**
	 * 
	 * Enter description here ...
	 */
	function header() {
		
		$code  = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" . "\n";
		$code .= "<rss version=\"2.0\" xmlns:dc=\"2\">" . "\n";
		return $code;
	}

	/**
	 * 
	 * Enter description here ...
	 */
	function footer() {
		return "</rss>";
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $when
	 */
	function setPubDate($when) {
		
		if(strtotime($when) == false) {
			$this->pubDate = date("D, d M Y H:i:s ", $when) . "GMT";
		} else {
			$this->pubDate = date("D, d M Y H:i:s ", strtotime($when)) . "GMT";
		}
	}
	
	/**
	 * 
	 * @return string
	 */
	function getPubDate() {
		
  		if(empty($this->pubDate)) {
			return date("D, d M Y H:i:s ") . "GMT";
  		} else {
			return $this->pubDate;
  		}
	}
	
}
