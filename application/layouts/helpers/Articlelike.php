<?php

/**
 * @author   : PhongTX
 * @name : Articlelike
 * @copyright   : FPT Online
 * @todo    : Helper Articlelike: VnE like
 */
class Zend_View_Helper_Articlelike extends Zend_View_Helper_Abstract
{

    public function Articlelike($article_id, $object_type = 1)
    {
        //object_type: 1:article; 2:Album; 3:Video; 4: playlist ; 5:Movie; 6:BST (Fashion); 7:profile Sao; 8:TVShow; 9:Book; 10: Song; 11: match; 12: team; 13 player; 14 tennis
        if (DEVICE_ENV != 1)
        {
            return '<div class="item_social" data-component="true" data-component-type="like" data-component-objectid="' . $article_id . '" data-component-siteid="' . SITE_ID . '" data-objecttype="' . $object_type . '"></div>';
        }
        else
        {
            return '<div class="block_btn_like right" data-component="true" data-component-type="like" data-component-objectid="' . $article_id . '" data-component-siteid="' . SITE_ID . '" data-objecttype="' . $object_type . '"></div>';
        }
    }

}