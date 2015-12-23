<?php
/**
 * @author   : PhongTX
 * @name : Articlecomment
 * @copyright   : FPT Online
 * @todo    : Helper Articlecomment: view number comment
 */
class Zend_View_Helper_ArticleComment extends Zend_View_Helper_Abstract
{
    protected $_image_url;

    public function __construct()
    {
        $config = Thethao_Global::getApplicationIni();
        $this->_image_url = $config['view']['images'];
    }

    public function ArticleComment($arrArticle=array())
    {    
        $return =   '<a class="icon_commend" style="display: none;" href="'. $arrArticle['share_url'] .'#box_comment" title="'.$arrArticle['title_format'].'">';
        $return .=      '<span class="txt_num_comment" data-type="comment" data-objecttype="' . $arrArticle['article_type'] . '" data-objectid="' . $arrArticle['article_id'] . '">';
        $return .=          '<img class="icon_content icon_title_coment" alt="comment" src="'.$this->_image_url.'/graphics/img_blank.gif">';
        $return .=      '&nbsp</span>';
        $return .='</a>';
        return $return;
    }

}