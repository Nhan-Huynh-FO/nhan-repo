<?php

/**
 * @author   : TienDQ
 * @name : IconCommercial
 * @copyright   : FPT Online
 * @todo    : Helper IconCommercial
 */
class Zend_View_Helper_IconCommercial extends Zend_View_Helper_Abstract
{

    function IconCommercial($arrArticle = array(), $category_id = 0)
    {
        if (($arrArticle['privacy'] & 128) == 128 && in_array($arrArticle['original_cate'], $this->view->configView['iconcommercial']))
        {
            if (!in_array($category_id, $this->view->configView['catecommercial']))
            {
                return '<div class="width_common icon_taitro">
                    <span class="no_wrap right">
                        <img class="icon_content icon_title_128" src="' . $this->view->configView['images'] . '/graphics/img_blank.gif" alt="" />
                    </span>
                </div>';
            }
        }
        return '';
    }

}