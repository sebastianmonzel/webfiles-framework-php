<?php

namespace simpleserv\webfilesframework\core\authentication;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractSessionInitializer
{

    public abstract function initializeByUserObject(MUser $user);

}