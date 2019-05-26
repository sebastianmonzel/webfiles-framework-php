<?php

namespace webfilesframework\core\datasystem\file\format\media\image\handler;


/**
 * description
 *
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
        // workaround for: "Invalid SOS parameters for sequential JPEG" - actually ignoring it
        ini_set ('gd.jpeg_ignore_warning', 1);
        return @imagecreatefromjpeg($p_sImage);
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