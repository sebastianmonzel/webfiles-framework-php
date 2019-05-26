<?php

namespace webfilesframework\core\datasystem\file\format\media\image\handler;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
abstract class MAbstractImageLibraryHandler
{

    public abstract function loadJpg($p_sImage);

    public abstract function loadPng($p_sImage);

}