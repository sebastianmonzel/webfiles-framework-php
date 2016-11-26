<?php

namespace simpleserv\webfilesframework\core\datastore\types\directory;

use simpleserv\webfilesframework\core\datastore\functions\MIDatastoreFunction;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;
use simpleserv\webfilesframework\core\datastore\MAbstractCachableDatastore;
use simpleserv\webfilesframework\core\datastore\MISingleDatasourceDatastore;
use simpleserv\webfilesframework\core\datastore\MDatastoreException;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectoryWebfileGrabber;
use simpleserv\webfilesframework\core\datasystem\file\system\MFile;
use simpleserv\webfilesframework\core\datasystem\file\format\image\MImage;
use simpleserv\webfilesframework\core\datastore\webfilestream\MWebfileStream;
use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;

/**
 * Class to connect to a datastore based on a directory.
 * <b>Conventions on the datastore:</b>
 * <ul>
 *        <li>filename is equal to the id of the webfile</li>
 *        <li></li>
 * </ul>
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDirectoryDatasourceDatastore extends MAbstractCachableDatastore
    implements MISingleDatasourceDatastore
{

    /**
     * @var MDirectory
     */
    private $m_oDirectory;
    public static $m__sClassName = __CLASS__;


    public function __construct(MDirectory $directory, $isRemoteDatastore = false)
    {

        if ($directory == null) {
            throw new MDatastoreException("Cannot instantiate a DirectoryDatastore without valid directory.");
        }
        $this->m_oDirectory = $directory;
    }

    public function isReadOnly()
    {
        return false;
    }

    public function tryConnect()
    {
        return true;
    }

    /**
     * @param int $time
     * @return MWebfile|null
     */
    public function getNextWebfileForTimestamp($time)
    {
        $itemGrabber = new MDirectoryWebfileGrabber($this->m_oDirectory);
        $webfiles = $itemGrabber->grabLatestWebfiles(4);

        ksort($webfiles);

        foreach ($webfiles as $key => $webfile) {
            if ($key > $time) {
                return $webfile;
            }
        }
        return null;
    }

    public function getWebfilesAsStream()
    {

        $files = $this->m_oDirectory->getFiles();

        $webfileArray = array();

        /** @var MFile $file */
        foreach ($files as $file) {
            $fileExtension = $file->getExtension();

            if (strtolower($fileExtension) == "jpg" || strtolower($fileExtension) == "jpeg") {

                $normalizedFile = new MFile($file->getFolder() . "/normal/" . $file->getName());

                if ($normalizedFile->exists()) {
                    array_push($webfileArray, new MImage($normalizedFile->getPath()));
                } else {
                    array_push($webfileArray, new MImage($file->getPath()));
                }
            } else if ($fileExtension == "webfile") {
                $fileContent = $file->getContent();
                array_push($webfileArray, MWebfile::staticUnmarshall($fileContent));
            } else {
                array_push($webfileArray, $file);
            }
        }

        return new MWebfileStream($webfileArray);
    }

    public function getWebfilesAsArray()
    {
        $itemGrabber = new MDirectoryWebfileGrabber($this->m_oDirectory);
        return $itemGrabber->grabWebfiles();
    }

    public function getLatestWebfiles($count = 5)
    {
        $itemGrabber = new MDirectoryWebfileGrabber($this->m_oDirectory);
        return $itemGrabber->grabLatestWebfiles($count);
    }

    public function storeWebfile(MWebfile $webfile)
    {
        $directoryPath = $this->m_oDirectory->getPath();
        $file = new MFile($directoryPath . "/" . $webfile->getId() . ".webfile");
        $file->writeContent($webfile->marshall(), true);
    }

    /**
     * (non-PHPdoc)
     * @see MAbstractDatastore::storeWebfilesFromWebfilestream()
     * @param MWebfileStream $webfileStream
     */
    public function storeWebfilesFromStream(MWebfileStream $webfileStream)
    {
        $webfiles = $webfileStream->getWebfiles();

        foreach ($webfiles as $webfile) {
            $this->storeWebfile($webfile);
        }

    }

    public function hasItem(MWebfile $item)
    {
        $directoryPath = $this->m_oDirectory->getPath();
        $file = new MFile($directoryPath . "/" . $item->getId() . ".webfile");
        return $file->exists();
    }

    /**
     * @param MWebfile $template
     * @return array
     */
    public function searchByTemplate(MWebfile $template)
    {
        if ($this->isDatastoreCached()) {

            if (!$this->isCacheActual()) {
                $this->fillCachingDatastore();
            }
            $webfiles = $this->cachingDatastore->searchByTemplate($template);
        } else {
            $webfiles = $this->getWebfilesAsArray();
            $webfiles = $this->filterWebfilesByTemplate($webfiles, $template);
        }
        return $webfiles;
    }

    /**
     *
     * @param array $webfiles
     * @param MWebfile $template
     * @return array
     */
    private function filterWebfilesByTemplate($webfiles, MWebfile $template)
    {

        $filteredWebfiles = array();
        $attributes = $template->getAttributes(true);

        foreach ($webfiles as $webfile) {

            $validWebfile = true;

            /** @var \ReflectionProperty $attribute */
            foreach ($attributes as $attribute) {

                $attribute->setAccessible(true);

                $templateValue = $attribute->getValue($webfile);

                if (
                    $templateValue != "?"
                    && !($templateValue instanceof MIDatastoreFunction)) {

                    $webfileValue = $attribute->getValue($webfile);
                    if ($templateValue != $webfileValue) {
                        $validWebfile = false;
                    }
                }
            }

            if ($validWebfile) {
                $filteredWebfiles[] = $webfile;
            }
        }

        return $filteredWebfiles;
    }

    public function deleteByTemplate(MWebfile $template)
    {
        $webfiles = $this->searchByTemplate($template);

        if ($this->isDatastoreCached()) {
            $this->cachingDatastore->deleteByTemplate($template);
        }

        foreach ($webfiles as $webfile) {
            $file = new MFile($this->m_oDirectory->getPath() . "/" . $webfile->getId() . ".webfile");
            $file->delete();
        }
    }

    public function getLatestCachingTime()
    {

    }

    public function isCacheActual()
    {
        return false;
    }

}