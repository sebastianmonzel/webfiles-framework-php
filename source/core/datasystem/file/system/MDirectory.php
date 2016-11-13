<?php

namespace simpleserv\webfilesframework\core\datasystem\file\system;

use simpleserv\webfilesframework\MItem;
use simpleserv\webfilesframework\core\datasystem\file\format\MWebfile;

/**
 * Encapsulates the access on directories.
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDirectory extends MFile
{

    protected $m_sPath;

    public function __construct($p_sPath)
    {
        parent::__construct($p_sPath);
        $this->m_sPath = $p_sPath;
    }


    /**
     * Returns the names of all files in the directory as an array.
     * @return array array with file objects
     */
    public function getFiles()
    {

        $filenames = array();

        if ($oDiractoryHandle = opendir($this->m_sPath)) {
            while (false !== ($filename = readdir($oDiractoryHandle))) {
                if ($filename != "." && $filename != ".." && (!is_dir($this->m_sPath . "/" . $filename))) {
                    $file = new MFile($this->getPath() . "/" . $filename);
                    array_push($filenames, $file);
                }
            }
        }
        return $filenames;
    }

    /**
     * @param $count
     * @return array
     */
    public function getLatestFiles($count)
    {

        $filesArray = $this->getFiles();
        $latestFilesArray = array_slice($filesArray, $count * -1);
        return $latestFilesArray;
    }

    /**
     * Returns the names of all files in the directory as an array.
     * @return array with filenames
     */
    public function getFileNames()
    {

        $filesArray = $this->getFiles();
        $filenamesArray = array();

        foreach ($filesArray as $file) {
            $sFileName = $file->getName();
            array_push($filenamesArray, $sFileName);
        }
        sort($filenamesArray);
        return $filenamesArray;
    }

    /**
     * Returns the subdirectories of the current directory.
     *
     * @return array list of directories
     */
    public function getSubdirectories()
    {
        $directories = array();
        if ($directoryHandle = opendir($this->m_sPath)) {
            while (false !== ($sFileName = readdir($directoryHandle))) {
                if (
                    $sFileName != "."
                        && $sFileName != ".."
                        && (is_dir($this->m_sPath . "/" . $sFileName))) {

                    array_push($directories, $sFileName);
                }
            }
        }
        sort($directories);
        return $directories;
    }

    /**
     * Creates the present directory.
     */
    public function create()
    {
        mkdir($this->m_sPath, 0700, TRUE);
    }

    /**
     * Creates a subdirectory in the present directory.
     */
    public function createSubDirectory($p_sName)
    {

        $subdirectoryPath = $this->m_sPath . "/" . $p_sName;

        $subdirectory = new MDirectory($subdirectoryPath);

        if (!$subdirectory->exists()) {
            mkdir($subdirectoryPath);
        }

        return $subdirectory;
    }

    /**
     * Returns the manually defined name of the folder.
     * @return String folderName
     */
    public function getPath()
    {
        return $this->m_sPath;
    }

    /**
     * Checks if the present directory exists or not.
     *
     * @return boolean Returns if the present directory exists or not.
     */
    public function exists()
    {
        return file_exists($this->m_sPath);
    }

    /**
     * Grabs all webfiles that exist in file representation in the
     * present directory.
     *
     * @return array list of webfiles.
     */
    public function grabWebfiles()
    {

        $filesArray = $this->getFiles();
        $webfilesArray = $this->convertFilesToWebfileObjects($filesArray);

        return $webfilesArray;
    }

    public function grabLatestWebfiles($count)
    {
        $filesArray = $this->getLatestFiles($count);
        $webfilesArray = $this->convertFilesToWebfileObjects($filesArray);

        return $webfilesArray;
    }

    /**
     * @param array $filesArray
     * @return array
     */
    private function convertFilesToWebfileObjects($filesArray)
    {
        $webfilesArray = array();

        foreach ($filesArray as $file) {

            $fileContent = $file->getContent();
            $webfile = MWebfile::staticUnmarshall($fileContent);

            $time = $file->getDate();
            $webfile->setTime($time);
            $webfilesArray[$time] = $webfile;
        }

        return $webfilesArray;

    }

    /**
     *
     * Enter description here ...
     */
    public function grabDatasets()
    {
        $datasetsArray = array();

        $webfilesArray = $this->grabWebfiles();

        foreach ($webfilesArray as $webfile) {
            $webfileDataset = $webfile->getDataset();
            $datasetsArray[] = $webfileDataset;
        }
        return $datasetsArray;
    }

}
