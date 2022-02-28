<?php

namespace webfilesframework\core\datastore\types\rss;


class MRssFeed {

    private $entries;

    /** @var string */
    private $m_sUrl;

    public function __construct(string $m_sUrl) {

        $this->m_sUrl = $m_sUrl;
    }

    public function loadFeedEntries() {

        $webfileArray = array();

        $max_entries = 5;

        $xml = simplexml_load_string(file_get_contents($this->m_sUrl));

        $entries = $xml->channel->item;
        $counter = 0;

        foreach($entries as $root) {

            $entry = new MRssFeedEntry();

            var_dump($root);

            $time = strtotime($root->pubDate);
            $entry->setTime($time);
            $entry->setLink(htmlspecialchars($root->link));
            $entry->setHeading(htmlspecialchars($root->title));
            $entry->setDescription(strip_tags($root->description));

            $webfileArray[$time] = $entry;

            $counter++;
            // Ausgabe nach x Einträgen beenden:
            if($counter > $max_entries) {
                break;
            }
        }

        return $webfileArray;
    }
}
//
//// Feed-URL des RSS-Feeds
//$feed_url = 'https://blog.selfhtml.org/feed/';
//
//// In welcher Datei soll der Cache abgelegt werden?
//$feedcache_path = __DIR__.'/feed_cache.html';
//
//// Wie alt in Sekunden darf der Cache sein? (1800 s entsprechen einer halben Stunde)
//$feedcache_max_age = 1800;
//
//// Wie viele Einträge sollen angezeigt werden?
//
//if(!file_exists($feedcache_path) or filemtime($feedcache_path) < (time() - $feedcache_max_age)) {
//    $xml = simplexml_load_string(file_get_contents($feed_url));
//    $output = '<p>Die '.(int)$max_entries.' neusten Einträge aus dem <a href="'.htmlspecialchars($xml->channel->link).'">'.htmlspecialchars($xml->channel->title).'</a></p>'.PHP_EOL;
//    $entries = $xml->channel->item;
//    $counter = 0;
//    $output .= '<ul>';
//
//    foreach($entries as $root) {
//        $counter++;
//        // Ausgabe nach x Einträgen beenden:
//        if($counter > $max_entries) {
//            break;
//        }
//        $date = date('d.m.Y', strtotime($root->pubDate));
//        // Anreißertext:
//        //$description = strip_tags($root->description);
//        $output .= '<li><a href="'.htmlspecialchars($root->link).'" title="'.htmlspecialchars($date).'">'.htmlspecialchars($root->title).'</a></li>'.PHP_EOL;
//    }
//    $output .= '</ul>';
//    echo $output;
//    file_put_contents($feedcache_path, $output);
//} else {
//    echo file_get_contents($feedcache_path);
//}