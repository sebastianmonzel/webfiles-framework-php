<?php

namespace webfilesframework\core\datastore\types\directory;

use webfilesframework\core\datastore\MAbstractCachableDatastore;
use webfilesframework\core\datastore\MDatastoreException;
use webfilesframework\core\datastore\MISingleDatasourceDatastore;
use webfilesframework\core\datasystem\file\format\media\MImage;
use webfilesframework\core\datasystem\file\format\MWebfile;
use webfilesframework\core\datasystem\file\format\MWebfileStream;
use webfilesframework\core\datasystem\file\system\MDirectory;
use webfilesframework\core\datasystem\file\system\MFile;
use webfilesframework\MWebfilesFrameworkException;

/**
 * Class to connect to a datastore based on a directory.
 * <b>Conventions on the datastore:</b>
 * <ul>
 *        <li>filename is equal to the id of the webfile</li>
 *        <li></li>
 * </ul>
 * Uses file ".metadatainformation" to store metadatainformation by
 * type MDirectoryDatastoreMetainformation.<br />
 * <br />
 * Simple handling for jpg images is integrated.
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDirectoryDatastore extends MAbstractCachableDatastore
    implements MISingleDatasourceDatastore
{

    /** @var MDirectory */
    private $m_oDirectory;

    /** @var MDirectoryDatastoreMetainformation */
    private $metaInformationWebfile;

    private $THUMB_IMAGES_FOLDER_NAME = "images-thumbssize";
    private $NORMAL_IMAGES_FOLDER_NAME = "images-normalsize";


	/**
	 * MDirectoryDatastore constructor.
	 *
	 * @param MDirectory $directory
	 *
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	public function __construct(MDirectory $directory)
    {
        if ($directory == null || !$directory instanceof MDirectory) {
            throw new MDatastoreException("Cannot instantiate a DirectoryDatastore without valid directory.");
        }
        $this->m_oDirectory = $directory;

        if ($this->metaInformationExist()) {
            $this->metaInformationWebfile = $this->readMetaInformation();
        } else {
            $this->metaInformationWebfile = new MDirectoryDatastoreMetainformation();
        }
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
	 * @return MWebfileStream
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	public function getWebfilesAsStream()
    {
        $webfileArray = $this->getWebfilesAsArray();
        return new MWebfileStream($webfileArray);
    }

	/**
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws \Exception
	 */
	public function getWebfilesAsArray()
    {
        $files = $this->m_oDirectory->getFiles();
        return $this->translateFilesIntoWebfilesArray($files);
    }

	/**
	 * @param int $count
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 * @throws \Exception
	 */
	public function getLatestWebfiles($count = 5)
    {
        if (! $this->metaInformationWebfile->isNormalized() ) {
            throw new MWebfilesFrameworkException(
                "searching for latest webfiles only possible when datastore is normalized.");
        }
        $filesArray = $this->m_oDirectory->getLatestFiles($count);
        return $this->translateFilesIntoWebfilesArray($filesArray);
    }

	/**
	 * @param int $timestamp
	 *
	 * @return MWebfile|null
	 * @throws MWebfilesFrameworkException
	 */
    public function getNextWebfileForTimestamp($timestamp)
    {

        if (! $this->metaInformationWebfile->isNormalized() ) {
            throw new MWebfilesFrameworkException(
                "searching for next webfile only possible when datastore is normalized.");
        }

        $webfiles = $this->getWebfilesAsArray();
        ksort($webfiles);

        foreach ($webfiles as $key => $webfile) {
            if ( $key > $timestamp) {
                return $webfile;
            }
        }
        return null;
    }

	/**
	 * @param $files
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	private function translateFilesIntoWebfilesArray($files)
    {

        $webfileArray = array();

        /** @var MFile $file */
        /** @var MWebfile $item */
        foreach ($files as $file) {

            $webfile = $this->readFileAsWebfile($file);
            if ( $webfile != null ) {
                $webfileArray = $this->addWebfileSafetyToArray($webfile,$webfileArray);
            }
        }

        return $webfileArray;
    }

	/**
	 * @param MFile $file
	 * @param bool  $forceTransformation
	 *
	 * @return MWebfile|null
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
    private function readFileAsWebfile(MFile $file, $forceTransformation = false)
    {

        /** @var MWebfile $item */
        $lowerCaseFileExtension = strtolower($file->getExtension());

        // AUTOBOXING for MImage-Definition
        if ($lowerCaseFileExtension == "jpg" || $lowerCaseFileExtension == "jpeg") {

            $normalizedFile = new MFile($file->getFolder() . "/" . $this->NORMAL_IMAGES_FOLDER_NAME . "/" . $file->getName());

            if ($normalizedFile->exists() && false) { // TODO as long as exif information not copied use the original image
                $item = new MImage($normalizedFile->getPath());
            } else {
                $item = new MImage($file->getPath());
            }
            $item->setTime($item->readExifDate());

        } else if ($lowerCaseFileExtension == "webfile" || $forceTransformation) {

            $fileContent = $file->getContent();
            $item = MWebfile::staticUnmarshall($fileContent);

        } else {
            return null;
        }

        if ( $item->getTime() == null) {
            if ( $file->getDate() != null ) {
                $item->setTime($file->getDate());
            } else {
                $item->setTime(time());
            }
        }

        return $item;
    }

	/**
	 * @param MFile    $file
	 * @param MWebfile $webfile
	 *
	 * @throws \ReflectionException
	 */
	private function writeWebfileAsFile(MFile $file, MWebfile $webfile)
	{
		$file->writeContent($webfile->marshall(),true);
	}


	/**
	 * @param MWebfile $webfile
	 *
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	public function storeWebfile(MWebfile $webfile)
    {
        $directoryPath = $this->m_oDirectory->getPath();
        // TODO implizite annahme, dass dateiname immer gleich id ist lösen
        // TODO normalize hier anwenden
        $file = new MFile($directoryPath . "/" . $webfile->getId() . ".webfile");

        $file->writeContent($webfile->marshall(), true);

        if ( $this->metaInformationWebfile->isNormalized() ) {
            $this->normalizeFile(
                $file,
                $this->metaInformationWebfile->isUseHumanReadableTimestamps(),
                $this->metaInformationWebfile->containsThumbnails()
                );
        }
    }

	/**
	 * @param MWebfileStream $webfileStream
	 *
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
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
	 *
	 * @return array
	 * @throws MWebfilesFrameworkException
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

	/**
	 * @param MWebfile $template
	 *
	 * @throws MDatastoreException
	 * @throws MWebfilesFrameworkException
	 */
	public function deleteByTemplate(MWebfile $template)
    {
        $webfiles = $this->searchByTemplate($template);
        $mapping = $this->createWebfileIdToFilenameMapping();

        if ($this->isDatastoreCached()) {
            $this->cachingDatastore->deleteByTemplate($template);
        }

        foreach ($webfiles as $webfile) {
            $webfileId = $webfile->getId();
            $file = new MFile($this->m_oDirectory->getPath() . "/" . $mapping[$webfileId]);
            $file->delete();
        }
    }

	/**
	 * @throws MWebfilesFrameworkException
	 */
	public function deleteAll()
    {
        $webfiles = $this->getWebfilesAsArray();
        $mapping = $this->createWebfileIdToFilenameMapping();

        foreach ($webfiles as $webfile) {
            $webfileId = $webfile->getId();
            $file = new MFile($this->m_oDirectory->getPath() . "/" . $mapping[$webfileId]);
            $file->delete();
        }
    }

	/**
	 * @return array
	 * @throws MWebfilesFrameworkException
	 * @throws \Exception
	 */
	private function createWebfileIdToFilenameMapping()
    {
        $filesArray = $this->m_oDirectory->getFiles();

        $mapping = array();
        /** @var MFile $file */
        /** @var MWebfile $webfile */
        foreach ($filesArray as $file) {
            $webfile = $this->readFileAsWebfile($file);
            if ( $webfile != null ) {
                $webfileId = $webfile->getId();
                if ( isset($mapping[$webfileId]) ) {
                    throw new MWebfilesFrameworkException("id '" . $webfileId . "' exists twice or more in the actual datastore.");
                }
                $mapping[$webfileId] = $file->getName();
            }
        }

        return $mapping;
    }


	/**
	 * Normalizes directory datastore:
	 *  - normalizes filenames
	 *
	 * @param bool $useHumanReadableTimestamps attention - timezones are actually not , merging webfile datastores from
	 *                                         different timezones can lead to mismatches.
	 *
	 * @param bool $saveThumbnailsForImages
	 *
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 * @throws \Exception
	 */
    public function normalize($useHumanReadableTimestamps = false, $saveThumbnailsForImages = false) {
        $filesArray = $this->m_oDirectory->getFiles();

        /** @var MFile $file */
        /** @var MWebfile $webfile */
        foreach ($filesArray as $file) {
            $this->normalizeFile($file,$useHumanReadableTimestamps, $saveThumbnailsForImages);
        }

        $this->metaInformationWebfile->setNormalized(true);
        $this->metaInformationWebfile->setUseHumanReadableTimestamps($useHumanReadableTimestamps);
        $this->metaInformationWebfile->setContainsThumbnails($saveThumbnailsForImages);
        $this->writeMetaInformation($this->metaInformationWebfile);
    }

	/**
	 * @param MFile $file
	 * @param bool  $useHumanReadableTimestamps
	 * @param bool  $saveThumbnailsForImages
	 *
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	private function normalizeFile(MFile $file, $useHumanReadableTimestamps = false, $saveThumbnailsForImages = false) {
        $webfile = $this->readFileAsWebfile($file);

        $timestamp = null;
        if ( $webfile != null ) {

            if ( !$this->isFilenameNormalized($file) ) { // normalize only once

                if ( !$useHumanReadableTimestamps || true ) {
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

            if ( $webfile instanceof MImage && $saveThumbnailsForImages) {
                $this->createThumbnailsForFile($webfile);
            }
        }
    }

	/**
	 * Checks if filename was normalized with a regex:
	 * http://www.phpliveregex.com/p/imN
	 *
	 * @param MFile $file
	 *
	 * @return false|int
	 */
    private function isFilenameNormalized(MFile $file) {
        //
        return preg_match("/(\d*)_wf_(.*[.].*)/", $file->getName(), $output_array);
    }

    private function denormalizeFile(MFile $file) {
        //
        if ( $this->isFilenameNormalized($file) ) {
            preg_match("/(\d*)_wf_(.*[.].*)/", $file->getName(), $output_array);

            $file->renameTo($output_array[2]);
        }
    }

    private function createThumbnailsForFile(MImage $image) {

        $this->m_oDirectory->createSubDirectoryIfNotExists($this->THUMB_IMAGES_FOLDER_NAME);
        $this->m_oDirectory->createSubDirectoryIfNotExists($this->NORMAL_IMAGES_FOLDER_NAME);

        $image->loadImage();

        $image->saveScaledImgAsFileWithBiggerSize(
            160, $this->m_oDirectory->getPath() . "/" . $this->THUMB_IMAGES_FOLDER_NAME . "/" . $image->getName());
        $image->saveScaledImgAsFileWithBiggerSize(
            600, $this->m_oDirectory->getPath() . "/" . $this->NORMAL_IMAGES_FOLDER_NAME . "/" . $image->getName());

        flush();
        $image->destroy();
    }

    private function metaInformationExist() {
        $file = new MFile($this->m_oDirectory->getPath() . "\\" . ".metainformation");
        return $file->exists();
    }

	/**
	 * @return MWebfile|null
	 * @throws MWebfilesFrameworkException
	 * @throws \ReflectionException
	 */
	private function readMetaInformation() {
        $file = new MFile($this->m_oDirectory->getPath() . "\\" . ".metainformation");
        return $this->readFileAsWebfile($file,true);
    }

	/**
	 * @param MDirectoryDatastoreMetainformation $metainformation
	 *
	 * @throws \ReflectionException
	 */
	private function writeMetaInformation(MDirectoryDatastoreMetainformation $metainformation) {
        $file = new MFile($this->m_oDirectory->getPath() . "\\" . ".metainformation");
        $this->writeWebfileAsFile($file, $metainformation);
    }

    /**
     * @return MDirectory
     */
    public function getDirectory() {
        return $this->m_oDirectory;
    }

}