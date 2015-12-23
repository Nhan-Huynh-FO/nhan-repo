<?php
/**
 *
 */
class Zend_View_Helper_ArticleViewIcon extends Zend_View_Helper_Abstract
{
    protected $_image_url;

    public function __construct()
    {
        $config = Thethao_Global::getApplicationIni();
        $this->_image_url = $config['view']['images'];
    }
    
    public function ArticleViewIcon($article)
    {
        $return = '<img src="'.$this->view->configView['images'].'/graphics/img_blank.gif" alt="" class="icon_content icon_title_'. intval($article['article_icon']) .'">';
        if ( (intval($article['privacy']) & 8192) == 8192 )
        {
            $return .= '&nbsp;<img class="icon_content icon_title_fun" alt="" src="'.$this->_image_url.'/graphics/img_blank.gif" />';
        }//end if
        return $return;
    }
}
?>