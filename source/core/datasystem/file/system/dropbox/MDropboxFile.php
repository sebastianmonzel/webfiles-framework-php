<?php

namespace webfilesframework\core\datasystem\file\system\dropbox;

use webfilesframework\core\datasystem\file\system\MFile;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MDropboxFile extends MFile
{
    protected $dropboxAccount;
    protected $fileMetadata;
    protected $filePath;

    /**
     *
     * Enter description here ...
     * @param MDropboxAccount $account
     * @param $filePath
     * @param bool $initMetadata
     * @internal param unknown_type $fileName
     */
    public function __construct(MDropboxAccount $account, $filePath, $initMetadata = true)
    {
        parent::__construct($filePath);

        $this->filePath = $filePath;
        $this->dropboxAccount = $account;
        if ($initMetadata) {
            $this->initMetadata();
        }
    }

    public function initMetadata()
    {
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
     * @param $overwriteIfExists
     */
    public function download($overwriteIfExists)
    {
        $file = $this->dropboxAccount->getDropboxApi()->getFile($this->filePath);

        if (!file_exists("." . $this->filePath)) {
            $fp = fopen("." . $this->filePath, "w");
            fputs($fp, $file['data']);
            fclose($fp);
        }
    }

    public function downloadImageAsThumbnail()
    {
        $file = $this->dropboxAccount->getDropbox()->thumbnails($this->filePath, 'JPEG', 'l');

        if (!file_exists("." . $this->filePath)) {
            $fp = fopen("." . $this->filePath, "w");
            fputs($fp, $file['data']);
            fclose($fp);
        }
    }
}