<?php

namespace simpleserv\webfilesframework\template;

use simpleserv\webfilesframework\core\datasystem\file\system\MFile;
use simpleserv\webfilesframework\MItem;
use simpleserv\webfilesframework\core\time\MTimestampHelper;
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
 * @package    de.simpleserv.template
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MTemplate extends MItem {
	
	var $name;
	var $content;
	
	var $dataset;
	
	var $result;
	
	var $isCompiled = false;
	
	/**
	 * Constructs a new template with a given content.
	 * 
	 * @param MString $content content of the template. can be an empty value to be set later.
	 */
	function __construct($content = null) {
		$this->content = $content;
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param $dataset
	 */
	function setDataset($dataset) {
				
		if ( is_object($this->dataset) ) {
			if ( $this->dataset instanceof MItem ) {
				$this->dataset = $this->dataset->getDataset();
			} else {
				//@todo error handling: object invalid
				return;
			}
		} elseif ( is_array($dataset) ) {
			$this->dataset = $dataset;
		}
	}
	
	/**
	 * Replaces the placeholders and returns the result.
	 */
	public function compileTemplate($isMultiplyTemplate = true) {
		$this->isCompiled = true;
		$this->result = "";
		$this->replacePlaceHolders($isMultiplyTemplate);
		return $this->result;
	}
	
	/**
	 * Oriented on array "$dataset". Replaces all occurances of key with value, if there is no subarray.
	 * If there is an subarray as much occurances as elements in the subarray are, will be replaced.
	 * OR if only one occurance is there the template code will be multiplied.
	 */
	private function replacePlaceHolders($multiplyTemplate = true) {
		
		// apply field functions to datasets and remove them from template
		$preparedContent = $this->applyFieldFunctions($this->content);
		
		$this->result = $preparedContent;
		
		if ( is_array($this->dataset) ) {
			$arrayElementCount = 1;
			
			foreach ($this->dataset as $key => $value) {
				if ( ! is_array($value) && ! is_object($value) ) {
					$this->result = str_replace ( "{" . $key . "}" , $value , $this->result );
				} else {
					if ( is_object($value) ) {
						$value = $value->getDataset();
						//var_dump($value);
					}
					if ( $this->isSimpleTemplate() ) {
						
						//replace and then add another template content to the result
						foreach ($value as $key => $innerValue) {
							$this->result = str_replace ( "{" . $key . "}" , $innerValue , $this->result );
						}
						if ( $arrayElementCount < count($this->dataset) ) {
							$this->result .= $preparedContent;
						}
					} else {
						//replace every occurance in the template one after the other
						foreach ($value as $key => $innerValue) {
							$this->result = preg_replace ("({".$key."})" , $innerValue , $this->result, 1);
						}
					}
				}
				$arrayElementCount++;
			}
		}
	}
	
	/**
	 * Checks if there is only one occourance of every dataset key to 
	 * be replacedin the template
	 */
	public function isSimpleTemplate() {
		
		foreach ($this->dataset as $key => $value) {
			if ( ! is_array($value) && ! is_object($value) ) {
				$matchCount = preg_match_all("({".$key."})" , $this->content , $matches);
				if ( $matchCount == 1 ) {
					return true;
				} else if ( $matchCount > 1 ) {
					return false;
				}
			} else {
				if ( is_object($value) ) {
					$value = $value->getDataset();
				}
				foreach ($value as $key => $innerValue) {
					if ( is_object($value) ) {
						$value = $value->getDataset();
					}
					$matchCount = preg_match_all("({".$key."})" , $this->content , $matches);
					if ( $matchCount == 1 ) {
						return true;
					} else if ( $matchCount > 1 ) {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	private function applyTemplateFunctions() {
		
	}
	
	private function applyFieldFunctions($content) {
		$preparedContent = $this->applyFieldFunctionDate($content);
		$preparedContent = $this->applyFieldFunctionDateTime($preparedContent);
		return $this->applyFieldFunctionTime($preparedContent);
	}
	
	private function applyFieldFunctionDate($content) {
		if ( $this->dataset != null ) {
			foreach ($this->dataset as $key => &$value) {
				if ( ! is_array($value) && ! is_object($value) ) {
					$matchCount = preg_match_all("({date:".$key."})" , $this->content , $matches);
					if ( $matchCount > 0 ) {
						$this->dataset[$key] = MTimestampHelper::getFormatedDate($value);
					}
				} else {
					
					if ( is_object($value) ) {
						$value = $value->getDataset();
					}
					
					foreach ($value as $key => $innerValue) {
						$matchCount = preg_match_all("({date:".$key."})" , $this->content , $matches);
						if ( $matchCount > 0 ) {
							$value[$key] = MTimestampHelper::getFormatedDate($innerValue);
						}
					}
				}
			}
		}
		
		return str_replace ( "{date:", "{", $content );
		
	}
	
	private function applyFieldFunctionTime($content) {
		if ( $this->dataset != null ) {
			foreach ($this->dataset as $key => &$value) {
				if ( ! is_array($value) && ! is_object($value) ) {
					$matchCount = preg_match_all("({time:".$key."})" , $this->content , $matches);
					if ( $matchCount > 0 ) {
						$this->dataset[$key] = MTimestampHelper::getFormatedTime($value);
					}
				} else {
					
					if ( is_object($value) ) {
						$value = &$value->getDataset();
					}
					
					foreach ($value as $key => $innerValue) {
						$matchCount = preg_match_all("({time:".$key."})" , $this->content , $matches);
						if ( $matchCount > 0 ) {
							$value[$key] = MTimestampHelper::getFormatedTime($innerValue);
						}
					}
				}
			}
		}
		return str_replace ( "{time:", "{", $content );
		
	}
	
	private function applyFieldFunctionDateTime($content) {
		if ( $this->dataset != null ) {
			foreach ($this->dataset as $key => &$value) {
				if ( ! is_array($value) && ! is_object($value) ) {
					$matchCount = preg_match_all("({datetime:".$key."})" , $this->content , $matches);
					if ( $matchCount > 0 ) {
						$this->dataset[$key] = MTimestampHelper::getFormatedDate($value) . " - " . MTimestampHelper::getFormatedTime($value);
					}
				} else {
					
					if ( is_object($value) ) {
						$value = &$value->getDataset();
					}
					
					foreach ($value as $key => $innerValue) {
						$matchCount = preg_match_all("({datetime:".$key."})" , $this->content , $matches);
						if ( $matchCount > 0 ) {
							$value[$key] = MTimestampHelper::getFormatedDate($innerValue) . " - " . MTimestampHelper::getFormatedTime($innerValue);
						}
					}
				}
			}
		}
		
		return str_replace ( "{datetime:", "{", $content );
		
	}
	
	public function setContentByFile(MFile $file) {
		$this->content = $file->getContent();
	}
	
	/**
	 * Returns the defined content of the template.
	 */
	public function getContent() {
		return $this->content;
	}
	
	/**
	 * Returns the replacment result. 
	 */
	public function getResult() {
		if ( $this->isCompiled == false ) {
			$this->compileTemplate();
		}
		return $this->result;
	}
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function show() {
		eval ( "?>" . $this->result );
	}
	
	
	
}
