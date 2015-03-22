<?php

namespace \simpleserv\webfiles-framework\core\io\form\validation;

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
 * @package    de.simpleserv.core.validation
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MValidator {
	
	/**
	 * 
	 * @param unknown_type $urlParam
	 * @return string
	 */
	public static function validateUrlParam($urlParam) {
        return htmlspecialchars($urlParam);
    }
	
    /**
     * 
     * @param unknown_type $comment
     * @param unknown_type $len
     * @return mixed
     */
    public static function cutLongWords($comment,$len=25) {
        $returnvalue = $comment;
        do {
            $comment = $returnvalue;
            $returnvalue = preg_replace('~(^|\s)(\S{'.$len.'})(\S)~S', '\1\2 \3', $comment);
        } while ($returnvalue != $comment);
        return $returnvalue;
    }
}

