<?php

namespace \simpleserv\webfiles-framework\core\authentication;

/**
 * 
 * @author semo
 *
 */
abstract class MAbstractSessionInitializer {
	
	public abstract function initializeByUserObject(MUser $user);
	
}