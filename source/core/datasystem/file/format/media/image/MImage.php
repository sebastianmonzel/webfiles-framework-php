<?php

namespace webfilesframework\core\datasystem\file\format\media\image;


use webfilesframework\core\datasystem\file\system\MFile;
use webfilesframework\core\datatypes\time\MTimestampHelper;


/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MImage extends MFile
{

    protected $m_oImage;

    protected $m_sType;

    protected $handler;


    public function __construct($filePath, $loadImageResourceOnCreation = false, $type = "jpg") {

        parent::__construct($filePath);

        $this->m_sType = $type;

        if ($loadImageResourceOnCreation) {
            $this->loadImage();
        }
    }

    public function loadImage() {

        if (MImage::isImageMagickInstalled() && false) {
            $this->handler = new MImageMagickHandler();
        } else if (MImage::isGdInstalled()) {
            $this->handler = new MGdHandler();
        } else {

        }

        if ($this->exists()) {
            if ($this->m_sType == "jpg") {
                $this->m_oImage = $this->handler->loadJpg($this->getPath());
            } else if ($this->m_sType == "png") {
                $this->m_oImage = $this->handler->loadPng($this->getPath());
            }
        }
    }

    public static function isImageMagickInstalled()
    {
        return (function_exists('NewMagickWand') === TRUE);
    }

    public static function isGdInstalled()
    {
        if (function_exists("gd_info")) {
            return true;
        }
        return false;
    }

    public static function isGd2Installed()
    {
        if (function_exists("gd_info")) {
            $info = gd_info();
            if (substr_count($info["GD Version"], "2")) {
                // version 2.x of libary GD is installed
                return true;
            }
        }
        return false;
    }

    public function destroy()
    {
        imagedestroy($this->m_oImage);
    }

    /**
     *
     */
    public function outputInBrowser()
    {

        if ($this->m_sType == "jpg") {
            header("Content-Type: image/jpeg");
            imagejpeg($this->m_oImage);
        } else if ($this->m_sType == "png") {
            header("Content-Type: image/png");
            imagepng($this->m_oImage);
        }

        imagedestroy($this->m_oImage);
    }

    public function readExifDate() {
        $exifData = @exif_read_data($this->getPath());

        if ( isset($exifData['DateTimeDigitized']) ) {
            return MTimestampHelper::getTimestampFromExifFormatedDateTime(
                $exifData['DateTimeDigitized']); //e.g. 2016:12:22 14:49:07
        } else if ( isset($exifData['DateTimeOriginal']) ) {
            return MTimestampHelper::getTimestampFromExifFormatedDateTime(
                $exifData['DateTimeOriginal']); //e.g. 2016:12:22 14:49:07
        } else {
            echo "warn: " . $this->getPath() . " does not have defined exifdate.\n";
            //var_dump($exifData);
        }
        return null;
    }

    /**
     *
     */
    public function outputAsDownload()
    {

        if ($this->m_sType == "jpg") {
            header("Content-Type: image/jpeg");
            imagejpeg($this->m_oImage, $this->getPath());
        } else if ($this->m_sType == "png") {
            header("Content-Type: image/png");
            imagepng($this->m_oImage, $this->getPath());
        }

        imagedestroy($this->m_oImage);

    }

    /**
     *
     * @param string $filePath
     * @param int|number $quality
     */
    public function saveAsFile($filePath = "", $quality = 80)
    {

        if (!empty($filePath)) {
            $sFilePath = $filePath;
        } else {
            $sFilePath = $this->getPath();
        }

        if ($this->m_sType == "jpg") {
            //Header("Content-Type: image/jpeg");
            imagejpeg($this->m_oImage, $sFilePath, $quality);
        } else if ($this->m_sType == "png") {
            //Header("Content-Type: image/png");
            imagepng($this->m_oImage, $sFilePath);
        }

    }

    /**
     *
     * @param $width
     * @param $height
     * @param string $filePath
     * @throws \Exception
     * @internal param unknown $p_iWidth
     * @internal param unknown $p_iHeight
     * @internal param string $p_sFilePath
     */
    public function saveScaledImgAsFile($width, $height, $filePath = "")
    {

        if (!$this->exists()) {
            $iErrorCode = 30;
            throw new \Exception(
                "File does not exists. Given File in the Image Object has to be created or has to be an existent file.",
                $iErrorCode
            );
        }

        if (!empty($filePath)) {
            $sFilePath = $filePath;
        } else {
            $sFilePath = $this->getPath();
        }

        $oScaledImage = imagecreatetruecolor($width, $height);
        imagecopyresampled($oScaledImage, $this->m_oImage, 0, 0, 0, 0, $width, $height, $this->getImageWidth(), $this->getImageHeight());

        if ($this->m_sType == "jpg") {
            //Header("Content-Type: image/jpeg");
            imagejpeg($oScaledImage, $sFilePath);
        } else if ($this->m_sType == "png") {
            //Header("Content-Type: image/png");
            imagepng($oScaledImage, $sFilePath);
        }
        imagedestroy($oScaledImage);
    }

    public function getImageWidth()
    {

        $oImageSize = getimagesize($this->getPath());

        //e.g. for $sSizeString "width=\"600\" height=\"643\""
        $sSizeString = $oImageSize[3];
        $oSizeStringArray = explode("\"", $sSizeString);
        return $oSizeStringArray[1];
    }

    public function getImageHeight()
    {

        $oImageSize = getimagesize($this->getPath());

        //e.g. for $sSizeString 'width="600" height="643"'
        $sSizeString = $oImageSize[3];
        $oSizeStringArray = explode("\"", $sSizeString);
        return $oSizeStringArray[3];
    }


    public function saveScaledImgAsFileWithHeight($p_iHeight, $p_sFilePath = "")
    {

        $iWidth = $this->getImageWidth();
        $iHeight = $this->getImageHeight();
        $iProportion = $p_iHeight / $iHeight;

        $iNewWidth = ceil($iProportion * $iWidth);

        $this->saveScaledImgAsFile($iNewWidth, $p_iHeight, $p_sFilePath);
    }

    public function saveScaledImgAsFileWithWidth($p_iWidth, $p_sFilePath = "")
    {

        $iWidth = $this->getImageWidth();
        $iProportion = $p_iWidth / $iWidth;
        $iHeight = $this->getImageHeight();

        $iNewHeight = ceil($iProportion * $iHeight);

        $this->saveScaledImgAsFile($p_iWidth, $iNewHeight, $p_sFilePath);
    }


    public function saveScaledImgAsFileWithBiggerSize($p_iBiggerSize, $p_sFilePath = "")
    {

        // TODO exif informationen mitkopieren
        ini_set ('gd.jpeg_ignore_warning', 1);
        if ($this->getImageWidth() > $this->getImageHeight()) {
            $this->saveScaledImgAsFileWithWidth($p_iBiggerSize, $p_sFilePath);
        } else {
            $this->saveScaledImgAsFileWithHeight($p_iBiggerSize, $p_sFilePath);
        }
    }

    /**
     *
     * @param int $p_iPercent
     * @param string $p_sFilePath
     */
    public function saveScaledImgAsFileWithPercent($p_iPercent, $p_sFilePath = "")
    {

        $iNewHeigt = ceil($this->getImageHeight() * ($p_iPercent / 100));
        $iNewWidth = ceil($this->getImageWidth() * ($p_iPercent / 100));

        $this->saveScaledImgAsFile($iNewWidth, $iNewHeigt, $p_sFilePath);
    }

    /**
     *
     */
    public function detroy()
    {
        imagedestroy($this->m_oImage);
    }

    /**
     *
     */
    public function mirror()
    {

        $img_x = imagesx($this->m_oImage);
        $img_y = imagesy($this->m_oImage);

        $oTempImage = imagecreatetruecolor($img_x, $img_y);
        imagecopyresampled($oTempImage, $this->m_oImage, 0, 0, $img_x, 0, $img_x, $img_y, -$img_x, $img_y);
        $this->m_oImage = $oTempImage;
    }

    public function __toString()
    {
        return "<img src=\"" . $this->getPath() . "\" width=\"300\"><br />";
    }

    public static function hasImageTypeExtension(MFile $image)
    {
        return ($image->getExtension() == "jpg"
            || $image->getExtension() == "jpeg"
            || $image->getExtension() == "png");
    }

}