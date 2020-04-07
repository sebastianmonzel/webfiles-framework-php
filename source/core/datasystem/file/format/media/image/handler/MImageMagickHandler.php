<?php

namespace webfilesframework\core\datasystem\file\format\media\image\handler;


use Exception;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MImageMagickHandler extends MAbstractImageLibraryHandler
{
	/**
	 * @param $p_sImage
	 *
	 * @throws Exception
	 */
    public function loadJpg($p_sImage)
    {
        throw new Exception("method not implemented yet");
    }

	/**
	 * @param $p_sImage
	 *
	 * @throws Exception
	 */
    public function loadPng($p_sImage)
    {
        throw new Exception("method not implemented yet");
    }

}