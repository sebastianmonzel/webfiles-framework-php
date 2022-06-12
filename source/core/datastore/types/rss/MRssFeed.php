<?php

namespace webfilesframework\core\datastore\types\rss;


class MRssFeed {

    private $entries;

    /** @var string */
    private $m_sUrl;

    public function __construct(string $m_sUrl) {

        $this->m_sUrl = $m_sUrl;
    }

    public function loadFeedEntries($maxEntries = 5) {

        $webfileArray = array();

        $xml = simplexml_load_string(file_get_contents($this->m_sUrl));

        $entries = $xml->channel->item;
        $counter = 0;

        foreach($entries as $root) {

            $entry = new MRssFeedEntry();

            $time = strtotime($root->pubDate);
            $entry->setTime($time);
            $entry->setLink(htmlspecialchars($root->link));
            $entry->setHeading(htmlspecialchars($root->title));
            $entry->setDescription(strip_tags($root->description));

            $webfileArray[$time] = $entry;

            $counter++;
            if($counter > $maxEntries) {
                break;
            }
        }

        $this->entries = $webfileArray;

        return $webfileArray;
    }
}