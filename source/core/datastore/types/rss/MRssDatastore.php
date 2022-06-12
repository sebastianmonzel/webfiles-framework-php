<?php

namespace webfilesframework\core\datastore\types\rss;

use Exception;
use ReflectionException;
use webfilesframework\core\datastore\MAbstractCachableDatastore;
use webfilesframework\core\datastore\MISingleDatasourceDatastore;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;
use webfilesframework\MWebfilesFrameworkException;

class MRssDatastore extends MAbstractCachableDatastore
    implements MISingleDatasourceDatastore
{

    /** @var string */
    private $m_sUrl;

    /**
     * MDirectoryDatastore constructor.
     *
     * @param string $m_sUrl
     *
     */
    public function __construct(string $m_sUrl)
    {
        $this->m_sUrl = $m_sUrl;
    }

    public function isReadOnly()
    {
        return true;
    }

    public function tryConnect()
    {
        // TODO
    }

    /**
     * @return MWebfileStream
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    public function getAllWebfiles()
    {
        $webfileArray = $this->getAllWebfilesAsArray();
        return new MWebfileStream($webfileArray);
    }

    /**
     * @return array
     * @throws Exception
     */
    private function getAllWebfilesAsArray()
    {
        $feed = new MRssFeed($this->m_sUrl);
        return $feed->loadFeedEntries();
    }

    /**
     * @param int $count
     *
     * @return MWebfileStream
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     * @throws Exception
     */
    public function getLatestWebfiles($count = 5)
    {
        $webfileArray = $this->getAllWebfilesAsArray();
        return new MWebfileStream($webfileArray); // TODO einschränken auf count
    }

    /**
     * @param int $timestamp
     *
     * @return MWebfile|null
     * @throws MWebfilesFrameworkException
     */
    public function getNextWebfileForTimestamp($timestamp)
    {
        return null; // TODO
    }

    /**
     * @param MWebfile $template
     *
     * @return MWebfileStream
     * @throws MWebfilesFrameworkException
     * @throws ReflectionException
     */
    public function searchByTemplate(MWebfile $template)
    {
        if ($this->isDatastoreCached()) {

            if (!$this->isCacheActual()) {
                $this->fillCachingDatastore();
            }
            $webfilesStream = $this->cachingDatastore->searchByTemplate($template);
        } else {
            $webfilesArray = $this->getAllWebfilesAsArray();
            $webfilesArray = $this->filterWebfilesArrayByTemplate($webfilesArray, $template);
            $webfilesStream = new MWebfileStream($webfilesArray);
        }
        return $webfilesStream;
    }

}