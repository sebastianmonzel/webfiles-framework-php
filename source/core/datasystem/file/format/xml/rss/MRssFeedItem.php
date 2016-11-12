<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format\xml\rss;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MRssFeedItem
{

    private $title;
    private $link;
    private $description;
    private $pubDate;
    private $guid;
    private $tags;
    private $attachment;
    private $length;
    private $mimetype;

    /**
     *
     * Enter description here ...
     */
    function __construct()
    {
        $this->tags = array();
    }

    /**
     *
     * Enter description here ...
     * @param $when
     */
    function setPubDate($when)
    {
        if (strtotime($when) == false) {
            $this->pubDate = date("D, d M Y H:i:s ", $when) . "GMT";
        } else {
            $this->pubDate = date("D, d M Y H:i:s ", strtotime($when)) . "GMT";
        }
    }

    /**
     *
     * Enter description here ...
     */
    function getPubDate()
    {
        if (empty($this->pubDate)) {
            return date("D, d M Y H:i:s ") . "GMT";
        } else {
            return $this->pubDate;
        }
    }

    /**
     *
     * Enter description here ...
     */
    function getCode()
    {
        $code .= "<item>\n";
        $code .= "	<title>" . $this->title . "</title>\n";
        $code .= "	<link>" . $this->link . "</link>\n";
        $code .= "	<description>" . $this->description . "</description>\n";
        $code .= "	<pubDate>" . $this->getPubDate() . "</pubDate>\n";

        if ($this->attachment != "")
            $code .= "<enclosure url='{$this->attachment}' length='{$this->length}' type='{$this->mimetype}' />";

        if (empty($this->guid)) {
            $this->guid = $this->link;
        }
        $code .= "	<guid>" . $this->guid . "</guid>\n";
        $code .= "</item>\n";
        return $code;
    }

    /**
     *
     * Enter description here ...
     * @param $url
     * @param $mimetype
     * @param $length
     */
    function enclosure($url, $mimetype, $length)
    {
        $this->attachment = $url;
        $this->mimetype = $mimetype;
        $this->length = $length;
    }

}