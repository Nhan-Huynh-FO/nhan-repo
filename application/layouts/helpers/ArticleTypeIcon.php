<?php

/**
 * @author   : TienDQ
 * @name : ArticleTypeIcon
 * @copyright   : FPT Online
 * @todo    : Helper ArticleTypeIcon
 */
class Zend_View_Helper_ArticleTypeIcon extends Zend_View_Helper_Abstract
{

    public function ArticleTypeIcon($arrArticle)
    {
        switch ($arrArticle['article_type'])
        {
            case 3: //Album
                return '<a class="icon_thumb_videophoto icon_photo" href="' . $arrArticle['share_url'] . '" title="'.$arrArticle['title_format'].'">&nbsp;</a>';
                break;
            case 2: //Video
                return '<a class="icon_thumb_videophoto icon_video" href="' . $arrArticle['share_url'] . '" title="'.$arrArticle['title_format'].'">&nbsp;</a>';
                break;
            case 4: //Infographic
                return '<a class="icon_thumb_infographic" href="' . $arrArticle['share_url'] . '" title="'.$arrArticle['title_format'].'">&nbsp;</a>';
                break;
            default:
                return '';
                break;
        }
    }

}

?>