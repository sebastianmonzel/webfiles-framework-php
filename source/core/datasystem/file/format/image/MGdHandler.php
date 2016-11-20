<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format\image;




/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MGdHandler extends MAbstractImageLibraryHandler
{

    /**
     * (non-PHPdoc)
     * @see MAbstractImageLibraryHandler::loadJpg()
     * @param $p_sImage
     * @return resource
     */
    public function loadJpg($p_sImage)
    {
        return imagecreatefromjpeg($p_sImage);
    }

    /**
     * (non-PHPdoc)
     * @see MAbstractImageLibraryHandler::loadPng()
     * @param $p_sImage
     * @return resource
     */
    public function loadPng($p_sImage)
    {
        return imagecreatefrompng($p_sImage);
    }

}