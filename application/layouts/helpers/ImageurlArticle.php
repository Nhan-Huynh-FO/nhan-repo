<?php

/**
 * @todo helper generate url image with size
 * @param <string> $strPath img original path
 * @param <string> $strSize img thumbnail size
 * @return <string> url image with size
 * @author NhuanTP
 * @modify HungNT1
 */
class Zend_View_Helper_ImageurlArticle extends Zend_View_Helper_Abstract
{
    /*
     * Nguyên tắc chung xác định thumb
     * · Với ảnh tỷ lệ 4:3 Lấy kích thước 165x124 làm mốc:
     *  > 165x124 thì lấy Thumb
     *  <= 165x124 thì lấy Small thumb (nếu ko Pick small thumb thì mặc định lấy thumb như hiện tại)
     * · Với tất cả ảnh dọc: lấy Vertical thumb (nếu ko Pick vertical thumb thì mặc định lấy thumb như hiện tại)
     */

    public function ImageurlArticle($article, $strSize = 'size1', $type = 0)
    {
        //image config
        $imagesConf = $this->view->imageSize;

        //Config size
        //Small thumb => thumbnail_url2
        //Vertical thumb => thumbnail_url3
        $arrThumbConfig = array();

        //Set $strPath
        $strPath    = $article['thumbnail_url'];

        //Check empty
        if ( empty($strPath) )
        {
            $strPath    = "f9.{$imagesConf['cdn']}/notfound.jpg";
        }
        else
        {
            $strThumbConfig = isset($arrThumbConfig[$strSize]) ? $arrThumbConfig[$strSize] : 'thumbnail_url';
            //Check data co ton tai thumbnail_url2 hoac thumbnail_url3
            $strPath =  isset($article[$strThumbConfig]) ? $article[$strThumbConfig] : $article['thumbnail_url'];
        }//end if

        //Check config ini
        if ( array_key_exists($strSize, $imagesConf) )
        {
            $prefix = key($imagesConf[$strSize]);
            $size   = $imagesConf[$strSize][$prefix];
        }
        else
        {
            $prefix = 's';
            $size   = '60x60';
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
        $result = $prefix . preg_replace('/\.[^.]+$/', (empty($size) ? '' : '_' . $size) . '$0', $strPath);

        //Return
        return $result;
    }
}