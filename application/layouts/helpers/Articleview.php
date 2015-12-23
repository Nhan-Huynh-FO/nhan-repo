<?php

/**
 * Buil 
 */
class Zend_View_Helper_Articleview extends Zend_View_Helper_Abstract {

    public function Articleview($arrArticle = array()) {
        //return '<span class="widget-interactions" data-type="view" data-objecttype="' . $arrArticle['article_type'] . '" data-objectid="' . $arrArticle['article_id'] . '"></span>';
        return '<span class="view_num" data-type="view" data-objecttype="' . $arrArticle['article_type'] . '" data-objectid="' . $arrArticle['article_id'] . '"></span>';
    }

}