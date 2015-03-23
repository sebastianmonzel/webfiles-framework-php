<?php

namespace simpleserv\webfilesframework\core\authentication;

/**
 * 
 * @author semo
 *
 */
class MNotEnoughRightsException extends Exception {
    
	
    public function __construct($code = 0) {
        parent::__construct($code);
    }

    public function __toString() {
        return __CLASS__;
    }

    public function customFunction() {
        echo "Eine eigene Funktion dieses Exceptiontyps\n";
    }
}