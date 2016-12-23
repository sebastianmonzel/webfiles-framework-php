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
class MDirectoryDatastore extends MAbstractCachableDatastore
    implements MISingleDatasourceDatastore
{

    /**
     * @var MDirectory
     */
    private $m_oDirectory;
    public static $m__sClassName = __CLASS__;


    public function __construct(MDirectory $directory, $isRemoteDatastore = false)
    {
        if ($directory == null || !$directory instanceof MDirectory) {
            throw new MDatastoreException("Cannot instantiate a DirectoryDatastore without valid directory.");
        }

        $this->m_oDirectory = $directory;
    }

    public function isReadOnly()
    {
        return !$this->m_oDirectory->isWritable();
    }

    public function tryConnect()
    {
        return $this->m_oDirectory->exists();
    }

    /**
     * @param int $time
     * @return MWebfile|null
     */
    public function getNextWebfileForTimestamp($time)
    {
        $webfiles = $this->getLatestWebfiles(4);
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
        $webfileArray = $this->getWebfilesAsArray();
        return new MWebfileStream($webfileArray);
    }

    public function getWebfilesAsArray()
    {
        $files = $this->m_oDirectory->getFiles();
        return $this->transformFilesIntoWebfilesArray($files);
    }

    public function getLatestWebfiles($count = 5)
    {
        $filesArray = $this->m_oDirectory->getLatestFiles($count);
        return $this->transformFilesIntoWebfilesArray($filesArray);
    }

    private function transformFilesIntoWebfilesArray($files)
    {

        $webfileArray = array();

        /** @var MFile $file */
        /** @var MWebfile $item */
        foreach ($files as $file) {

            $item = $this->transformFileIntoWebfile($file);
            if ( $item != null ) {
                $webfileArray = $this->addWebfileSafetyToArray($item,$webfileArray);
            }
        }

        return $webfileArray;
    }

    /**
     * @param MFile $file
     * @param bool $forceTransformation
     * @return MWebfile|null
     */
    private function transformFileIntoWebfile(MFile $file, $forceTransformation = false)
    {

        $webfileArray = array();

        /** @var MWebfile $item */
        $lowerCaseFileExtension = strtolower($file->getExtension());

        if ($lowerCaseFileExtension == "jpg" || $lowerCaseFileExtension == "jpeg") {

            $normalizedFile = new MFile($file->getFolder() . "/normal/" . $file->getName());

            if ($normalizedFile->exists()) {
                // TODO exif-aufnahmedatum auslesen und im webfile objekt setzen
                $item = new MImage($normalizedFile->getPath());
            } else {
                $item = new MImage($file->getPath());
            }
        } else if ($lowerCaseFileExtension == "webfile" || $forceTransformation) {
            $fileContent = $file->getContent();
            $item = MWebfile::staticUnmarshall($fileContent);

        } else {
            // TODO write warn to log that file is ignored
            return null;
        }

        if ( $item->getTime() == null) {
            $item->setTime($file->getDate());
        }

        return $item;
    }




    public function storeWebfile(MWebfile $webfile)
    {
        $directoryPath = $this->m_oDirectory->getPath();
        // TODO implizite annahme, dass dateiname immer gleich id ist lösen
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
            $webfiles = $this->filterWebfilesArrayByTemplate($webfiles, $template);
        }
        return $webfiles;
    }

    public function deleteByTemplate(MWebfile $template)
    {
        $webfiles = $this->searchByTemplate($template);

        if ($this->isDatastoreCached()) {
            $this->cachingDatastore->deleteByTemplate($template);
        }

        foreach ($webfiles as $webfile) {
            // TODO implizite annahme, dass dateiname immer gleich id ist lösen
            $file = new MFile($this->m_oDirectory->getPath() . "/" . $webfile->getId() . ".webfile");
            $file->delete();
        }
    }


    /**
     * Normalizes directory datastore:
     *  - normalizes filenames
     * @param bool $useHumanReadableTimestamp attention - timezones are actually not , merging webfile datastores from
     * different timezones can lead to mismatches.
     */
    public function normalize($useHumanReadableTimestamp = false) {
        $filesArray = $this->m_oDirectory->getFiles();

        /** @var MFile $file */
        /** @var MWebfile $webfile */
        foreach ($filesArray as $file) {
            $webfile = $this->transformFileIntoWebfile($file);

            $timestamp = null;
            if (!$useHumanReadableTimestamp || true) {
                // make sure timestamp has always same count of letters, as filenames are handled
                // alphanumerically by filesystem -> sorting will not work if not normalized
                $timestampAsString = strval($webfile->getTime());
                $timestampAsStringLength = strlen($timestampAsString);

                $targetLength = 10;
                $difference = $targetLength - $timestampAsStringLength;

                $filler = "";
                while (strlen($filler) < $difference) {
                    $filler .= "0";
                }

                $timestamp = $filler . $timestampAsString;

            }

            $file->renameTo($timestamp . '_wf_' . $file->getName());
        }
    }

    private function readMetaInformation() {
        $file = new MFile(".metainformation");
        $metaInformationWebfile = $this->transformFileIntoWebfile($file,true);

        // TODO define metaInformation
        // - normalized true/false
        // - human readable timestamps true/false
        // - timezone


    }

    private function normalizeFile(MFile $file) {

    }

}