<?php

namespace \simpleserv\webfiles-framework\core\codegeneration;

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
 * @package    de.simpleserv.core.abstraction.code
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MAbstractClassMethod extends MAbstractCodeItem {
	
	protected $visibility = "public";
	protected $name;
	protected $content;
	
	protected $parameters = array();
	
	
	public function getVisibility() {
		return $this->visibility;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getContent() {
		return $this->content;
	}
	
	
}