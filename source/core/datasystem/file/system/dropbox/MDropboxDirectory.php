<?php

namespace simpleserv\webfilesframework\core\datasystem\file\system\dropbox;

use simpleserv\webfilesframework\core\datasystem\file\system\MDirectory;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDropboxDirectory extends MDirectory
{

    private $dropboxAccount;
    private $dropboxFolderPath;
    private $directoryMetadata;

    /**
     * Enter description here ...
     * @param $path
     * @param $dropboxAccount
     */
    public function __construct($path, MDropboxAccount $dropboxAccount)
    {
        parent::__construct($path);
        $this->dropboxFolderPath = $path;
        $this->dropboxAccount = $dropboxAccount;
        $this->directoryMetadata = $this->dropboxAccount->getDropboxApi()->metaData($path);
        //var_dump($this->directoryMetadata);
    }

    public function getFiles($initMetadata = true)
    {
        //echo "MDropboxDirectory->getFiles(";
        if ($initMetadata) {
            //echo "true";
        } else {
            //echo "false";
        }
        //echo ")<br />";
        $files = array();
        $metdadataBody = $this->directoryMetadata['body'];

        foreach ($metdadataBody->contents as $value) {
            if (!$value->is_dir) {
                $dropboxFile = new MDropboxFile($this->dropboxAccount, $value->path, $initMetadata);
                $date = $value->modified;
                $dropboxFile->setDate($date);
                array_push($files, $dropboxFile);
            }
        }

        return $files;

    }

    public function getLatestFiles($count)
    {
        //echo ("MDropboxDirectory->getLatestFiles(". $count.")<br />");
        $filesArray = $this->getFiles(false);
        //echo count($filesArray);
        //var_export($filesArray);
        $latestFilesArray = array_slice($filesArray, $count * -1);
        //echo count($latestFilesArray);
        foreach ($latestFilesArray as $latestFile) {
            //$latestFile->initMetadata();
        }
        return $latestFilesArray;
    }


    public function getFileNames()
    {
        $fileNames = array();
        $metdadataBody = $this->directoryMetadata['body'];

        foreach ($metdadataBody->contents as $value) {
            if (!$value->is_dir) {
                $lastSlash = strrpos($value->path, '/');
                $path = substr($value->path, $lastSlash + 1);
                array_push($fileNames, $path);
            }
        }

        return $fileNames;
    }

    public function getSubdirectories()
    {
        $subdirectories = array();
        $metdadataBody = $this->directoryMetadata['body'];

        foreach ($metdadataBody['contents'] as $value) {
            if (!$value->is_dir) {
                $dropboxDirectory = new MDropboxDirectory($this->dropboxAccount, $value->path);
                array_push($subdirectories, $dropboxDirectory);
            }
        }

        return $subdirectories;
    }


    /**
     * Enter description here ...
     * @param MDirectory $directory
     * @param $recursivly
     * @param $folder
     */
    public function syncToLocalDirectory(MDirectory $directory, $recursivly, $folder)
    {

        //var_dump($folder);

        $folder = $folder['body'];

        //var_dump($folder);

        $dropboxFolderPath = new MDirectory($directory->getFolderName() . "/" . $folder->path);
        if (!$dropboxFolderPath->exists()) {
            $dropboxFolderPath->create();
        }

        $files = $directory->getFiles();
        $contents = $folder->contents;

        if (sizeof($files) != sizeof($contents) + 2) {

            //DATEIEN MIT DEM GROESSTEN NAMEN ALS ERSTES SYNCHRONISIEREN
            $reverse = array_reverse($contents);

            foreach ($reverse as $value) {
                //echo "value:\n";
                //var_dump($value);
                if (!$value->is_dir) {
                    //SYNC ACTUAL DIRECTORY FILES
                    if ($value->mime_type == 'image/jpeg') {

                        $this->downloadImage($directory, utf8_encode($value->path));
                    } else {
                        $this->downloadFile($directory, utf8_encode($value->path));
                    }
                } else {
                    //SYNC SUBDIRECTORIES
                    if ($recursivly) {
                        if (!file_exists("." . $value->path)) {

                            $dropboxDirectory = new MDropboxDirectory($value->path, $this->dropboxAccount);

                            $this->syncToLocalDirectory(
                            // TODO Subfolder-Pfad zusammenbauen
                                $directory,
                                true,
                                $dropboxDirectory->getFolderMetadata()
                            );
                        }
                    }
                }
            }

        }
    }

    /**
     * Enter description here ...
     * @param MDirectory $rootFolder
     * @param string $filePath
     */
    private function downloadFile(MDirectory $rootFolder, $filePath)
    {

        $file = $this->dropboxAccount->getDropboxApi()->getFile($filePath);
        $filePath = $rootFolder->getFolderName() . $filePath;
        $this->saveToFilesystem($filePath, $file);
    }

    /**
     * Enter description here ...
     * @param MDirectory $rootFolder
     * @param string $filePath
     */
    private function downloadImage(MDirectory $rootFolder, $filePath)
    {

        $file = $this->dropboxAccount->getDropboxApi()->thumbnails($filePath, 'JPEG', 'l');
        $filePath = $rootFolder->getFolderName() . $filePath;
        $this->saveToFilesystem($filePath, $file);
    }

    /**
     * Enter description here ...
     * @param $filePath
     * @param unknown_type $file
     */
    private function saveToFilesystem($filePath, $file)
    {
        if (!file_exists("." . $filePath)) {
            $fp = fopen("." . $filePath, "w");
            fputs($fp, $file['data']);
            fclose($fp);
        }
    }

    /**
     *
     * Enter description here ...
     */
    public function getFolderMetadata()
    {
        return $this->directoryMetadata;
    }

}
