<?php

/**
 * Buil helper image object
 * @author HungNT1
 */
class Zend_View_Helper_Imageurl extends Zend_View_Helper_Abstract
{
    public function Imageurl($strPath, $strSize = 'size1', $fullLink = true)
    {
        //image config
        $imagesConf = $this->view->imageSize;

        //Check empty
        if ( empty($strPath) )
        {
            $strPath    = "f9.{$imagesConf['cdn']}/notfound.jpg";
        }
        else if (!$fullLink)
        {
            $strPath    = $imagesConf['cdn'] . '/' . $strPath;
        }

        //Check config ini
        if ( array_key_exists($strSize, $imagesConf) )
        {
            $prefix = key($imagesConf[$strSize]);
            $size   = $imagesConf[$strSize][$prefix];
        }
        else
        {
            $prefix = 's';
            $size   = '50x50';
        }//end if

		//Check url to define prefix
		$httpEnv	= 'http://';
		if ( strpos($strPath, $httpEnv.'m.') !== false )
		{
			$prefix		= "{$httpEnv}{$prefix}.";
			$strPath	= str_replace($httpEnv.'m.', '', $strPath);
		}
		elseif ( strpos($strPath, $httpEnv) !== false )
		{
			$prefix		= $httpEnv;
			$strPath	= str_replace($httpEnv, '', $strPath);
		}
		else
		{
			$prefix		= "{$httpEnv}{$prefix}.";
		}//end if

        //Return
        return $prefix . preg_replace('/\.[^.]+$/', (empty($size) ? '' : '_' . $size) . '$0', $strPath);
    }

}