<?php

namespace simpleserv\webfilesframework\core\datasystem\file\system\dropbox;

use simpleserv\webfilesframework\core\datasystem\file\system\MFile;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDropboxFile extends MFile
{

    var $dropboxAccount;
    var $filename;
    var $fileMetadata;
    var $filePath;

    /**
     *
     * Enter description here ...
     * @param MDropboxAccount $account
     * @param unknown_type $fileName
     */
    public function __construct(MDropboxAccount $account, $filePath, $initMetadata = true)
    {
        parent::__construct($filePath);
        //echo "MDropboxFile->__contruct([account], ". $filePath . ", ";
        if ($initMetadata) {
            //echo "true";
        } else {
            //echo "false";
        }

        //echo")<br />";
        $this->filePath = $filePath;
        $this->dropboxAccount = $account;
        if ($initMetadata) {
            $this->initMetadata();
        }
    }

    public function initMetadata()
    {
        //echo "MDropboxFile->initMetadata()<br />";
        $this->fileMetadata = $this->dropboxAccount->getDropboxApi()->metaData($this->filePath);
        $lastSlash = strrpos($this->fileMetadata['body']->path, '/');
        $fileName = substr($this->fileMetadata['body']->path, $lastSlash + 1);
        $this->fileName = $fileName;
    }

    public function getContent()
    {
        $file = $this->dropboxAccount->getDropboxApi()->getFile($this->filePath);
        return $file['data'];
    }

    public function writeContent($content, $overwrite = false)
    {
        // TODO
    }

    /**
     *
     * Enter description here ...
     */
    public function upload()
    {

    }

    /**
     *
     * Enter description here ...
     */
    public function download($overwriteIfExists)
    {
        $file = $this->dropboxAccount->getDropboxApi()->getFile($filePath);

        if (!file_exists("." . $filePath)) {
            $fp = fopen("." . $filePath, "w");
            fputs($fp, $file['data']);
            fclose($fp);
        }
    }

    public function downloadImageAsThumbnail()
    {
        $file = $account->getDropbox()->thumbnails($filePath, 'JPEG', 'l');

        /*if ( file_exists(".".$filePath) ) {
            unlink (".".$filePath);
        }*/

        if (!file_exists("." . $filePath)) {
            $fp = fopen("." . $filePath, "w");
            fputs($fp, $file['data']);
            fclose($fp);
        }

    }

}