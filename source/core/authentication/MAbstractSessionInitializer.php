<?php

namespace simpleserv\webfilesframework\core\authentication;

/**
 * 
 * @author semo
 *
 */
abstract class MAbstractSessionInitializer {
	
	public abstract function initializeByUserObject(MUser $user);
	
}