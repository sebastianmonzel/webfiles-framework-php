<?php

namespace webfilesframework\core\datasystem\file\format\media;


use webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MYoutubeVideo extends MWebfile
{

    private $m_sKey;


    public function getKey()
    {
        return $this->m_sKey;
    }

    public function __toString()
    {
        return "<iframe width=\"300\" height=\"169\" src=\"//www.youtube.com/embed/" . $this->m_sKey . "\" frameborder=\"0\" allowfullscreen></iframe>";
    }
}
