<?php

namespace simpleserv\webfilesframework\core\datasystem\file\format\image;

use simpleserv\webfilesframework\core\datasystem\file\format\image\MAbstractImageLibraryHandler;
use simpleserv\webfilesframework\core\datasystem\file\system\MFile;


/**
 * #########################################################
 * ######################### devPHP - develop your webapps
 * #########################################################
 * ################## copyrights by simpleserv development
 * #########################################################
 */

/**
 * description
 *
 * @package    de.simpleserv.core.filesystem
 * @author     simpleserv company <info@simpleserv.de>
 * @author     Sebastian Monzel <s_monzel@simpleserv.de>
 * @copyright  2009-2012 simpleserv company
 * @link       http://www.simpleserv.de/
 */
class MImage extends MFile {
	
    protected $m_oImage;
    
    protected $m_oFontHeight;
    protected $m_oFontWidth;

    protected $m_sType;
    
    protected $handler;
    
    public function __construct($filePath,$lazy=true,$type="jpg") {
        
    	parent::__construct($filePath);
    	
    	$this->m_sType = $type;
    	
    	if ( ! $lazy ) {
	        if ( MImage::isImageMagickInstalled() && false ) {
	        	$this->handler = new MImageMagickHandler();
	        } else if ( MImage::isGdInstalled() ) {
	        	$this->handler = new MGdHandler();
	        } else {
	        	
	        }
	        
	        if ( $this->exists() ) {
	            if ( $this->m_sType == "jpg" ) {
	                $this->m_oImage = $this->handler->loadJpg($this->getPath());
	            } else if ( $this->m_sType == "png" ) {
	                $this->m_oImage = $this->handler->loadPng($this->getPath());
	            }
	        }
        
	        $this->m_oFontHeight    = ImageFontHeight(3);
	        $this->m_oFontWidth     = ImageFontWidth(3);
    	}
    }
	
    public static function isImageMagickInstalled() {
    	return (function_exists('NewMagickWand') === TRUE);
    }
    
    public static function isGdInstalled() {
    	if (function_exists("gd_info")) {
    		// libary GD is installed
    		return true;
    		$info = gd_info();
    		if (substr_count($info["GD Version"], "2")) {
    			// version 2.x of libary GD is installed
    			return true;
    		}
    	}
    	return false;
    }

	public function destroy() {
		imagedestroy($this->m_oImage);
	}
    /**
     *
     * @param <type> $p_sFont
     * @param <type> $p_sText
     * @param <type> $p_iY
     * @param <type> $p_iX
     *
     */
    public function writeTextLine($p_sFont,$p_sFontSize,$p_sText,$p_iY = 282,$p_iX = 210) {
        $red = ImageColorAllocate($this->m_oImage, 197, 14, 30);
        imagettftext($this->m_oImage, $p_sFontSize, 0, $p_iX, $p_iY, $red, $p_sFont, utf8_decode($p_sText));
    }

    public function writeTextLines($p_sFont,$p_sFontSize,$p_sTextArray,$p_iY=296,$p_iX = 210) {
        $sWritingText = "";
        foreach($p_sTextArray as $sText){
            $sWritingText .= $sText . "\n";
        }
        $this->writeTextLine($p_sFont,$p_sFontSize,$sWritingText,$p_iY,$p_iX);
    }
    
    /**
     * 
     */
    public function outputInBrowser() {
    	
        if ( $this->m_sType == "jpg" ) {
            Header("Content-Type: image/jpeg");
            imagejpeg($this->m_oImage);
        } else if ( $this->m_sType == "png" ) {
            Header("Content-Type: image/png");
            imagepng($this->m_oImage);
        }
        
        imagedestroy($this->m_oImage);
    }
    
    /**
     * 
     */
    public function outputAsDownload() {
    	
        if ( $this->m_sType == "jpg" ) {
            Header("Content-Type: image/jpeg");
            imagejpeg($this->m_oImage,$this->getPath());
        } else if ( $this->m_sType == "png" ) {
            Header("Content-Type: image/png");
            imagepng($this->m_oImage,$this->getPath());
        }
        
        imagedestroy($this->m_oImage);

    }
    
    /**
     * 
     * @param string $filePath
     * @param number $quality
     */
    public function saveAsFile($filePath = "", $quality = 80) {
    	
        if ( ! empty($filePath) ) {
            $sFilePath = $filePath;
        } else {
            $sFilePath = $this->m_sFilePath;
        }

        if ( $this->m_sType == "jpg" ) {
            //Header("Content-Type: image/jpeg");
            imagejpeg($this->m_oImage,$this->getPath(),$quality);
        } else if ( $this->m_sType == "png" ) {
            //Header("Content-Type: image/png");
            imagepng($this->m_oImage,$this->getPath());
        }
        
    }
	
    /**
     * convert to a normalized format (jpeg or png)
     */
    public function normalize() {
    	
    }
	
    /**
     * 
     * @param unknown $p_iWidth
     * @param unknown $p_iHeight
     * @param string $p_sFilePath
     * @throws Exception
     */
    public function saveScaledImgAsFile($width,$height,$filePath = "") {
    	
        if ( ! $this->exists() ) {
            $iErrorCode     = 30;
            throw new Exception(
                    "File does not exists. Given File in the Image Object has to be created or has to be an existant file.",
                    $iErrorCode
            );
        }

        if ( ! empty($filePath) ) {
            $sFilePath = $filePath;
        } else {
            $sFilePath = $this->getPath();
        }
        
        $oScaledImage = imagecreatetruecolor($width,$height);
        imagecopyresampled($oScaledImage,$this->m_oImage,0,0,0,0,$width,$height, $this->getImageWidth(), $this->getImageHeight()  );
        
        if ( $this->m_sType == "jpg" ) {
            //Header("Content-Type: image/jpeg");
            imagejpeg($oScaledImage,$sFilePath);
        } else if ( $this->m_sType == "png" ) {
            //Header("Content-Type: image/png");
            imagepng($oScaledImage,$sFilePath);
        }
		imagedestroy($oScaledImage);
    }

    public function getImageWidth() {
    	
        $oImageSize = getimagesize($this->getPath());

        //e.g. for $sSizeString "width=\"600\" height=\"643\""
        $sSizeString = $oImageSize[3];
        $oSizeStringArray = explode("\"",$sSizeString);
        return $oSizeStringArray[1];
    }

    public function getImageHeight() {
    	
    	echo "getImageHeight";
    	
        $oImageSize = getimagesize($this->getPath());

        //e.g. for $sSizeString 'width="600" height="643"'
        $sSizeString = $oImageSize[3];
        $oSizeStringArray = explode("\"",$sSizeString);
        return $oSizeStringArray[3];
    }
    

    public function saveScaledImgAsFileWithHeight($p_iHeight,$p_sFilePath = "") {
    	
        $iWidth  = $this->getImageWidth();
        $iHeight = $this->getImageHeight();
        $iProportion = $p_iHeight/$iHeight;

        $iNewWidth = ceil($iProportion * $iWidth);

        $this->saveScaledImgAsFile($iNewWidth, $p_iHeight, $p_sFilePath);
    }

    public function saveScaledImgAsFileWithWidth($p_iWidth,$p_sFilePath = "") {
    	
        $iWidth  = $this->getImageWidth();
        $iProportion = $p_iWidth/$iWidth;
        $iHeight = $this->getImageHeight();

        $iNewHeight = ceil($iProportion * $iHeight);

        $this->saveScaledImgAsFile($p_iWidth, $iNewHeight, $p_sFilePath);
    }


    public function saveScaledImgAsFileWithBiggerSize($p_iBiggerSize,$p_sFilePath = "") {
    	
        if ( $this->getImageWidth() > $this->getImageHeight() ) {
            $this->saveScaledImgAsFileWithWidth($p_iBiggerSize,$p_sFilePath);
        } else {
            $this->saveScaledImgAsFileWithHeight($p_iBiggerSize,$p_sFilePath);
        }
    }
    
    /**
     * 
     * @param unknown $p_iPercent
     * @param string $p_sFilePath
     */
    public function saveScaledImgAsFileWithPercent($p_iPercent,$p_sFilePath = "") {
    	
        $iNewHeigt = ceil($this->getImageHeight() * ($p_iPercent / 100));
        $iNewWidth = ceil($this->getImageWidth() * ($p_iPercent / 100));

        $this->saveScaledImgAsFile($iNewWidth, $iNewHeigt, $p_sFilePath);
    }
    
    /**
     * 
     */
    public function detroy() {
        imagedestroy($this->m_oImage);
    }

    public function fontSizeCheck($size) {
        imagettftext(
        				$this->m_oImage, 
        				40, 
        				0, 
        				282, 
        				210, 
        				$black, 
        				$p_sFont, 
        				".............................................................................................................................");
    }
	
    /**
     * 
     */
    public function mirror() {
    	
		$img_x=imagesx($this->m_oImage);
	    $img_y=imagesy($this->m_oImage);
	    
	    $oTempImage = imagecreatetruecolor($img_x, $img_y);
	    imagecopyresampled($oTempImage, $this->m_oImage, 0, 0, $img_x, 0, $img_x, $img_y, -$img_x, $img_y);
	    $this->m_oImage = $oTempImage;
    }
    
    public function __toString() {
    	return "<img src=\"" . $this->getPath() . "\" width=\"300\"><br />";
    }
    
    public static function hasImageTypeExtension(MFile $image) {
    	return ($image->getExtension() == "jpg" 
    				|| $image->getExtension() == "jpeg" 
    				|| $image->getExtension() == "png");
    }
    
}