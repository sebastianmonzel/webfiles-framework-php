<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format\image;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractImageLibraryHandler
{

    public abstract function loadJpg($p_sImage);

    public abstract function loadPng($p_sImage);

}