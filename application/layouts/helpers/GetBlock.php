<?php
/**
 * @todo helper get block on page
 * @param <string> $pos block section
 * @return <string> html of block
 * @author LamTX
 */
class Zend_View_Helper_GetBlock extends Zend_View_Helper_Abstract
{

    public function GetBlock($pos = null, $pagecode = false)
    {
        /*if (DEVICE_ENV == 1 && $pagecode == false)
        {
            return '<div id="ajax_block_' . $pos . '"><script type="text/javascript">$(document).ready(function() {if(typeof (partialVne) != \'undefined\'){ partialVne.setBlock(\'' . $pos . '\'); }})</script></div>';
        }
        else
        {*/
            $block = Thethao_Plugin_Block::getInstance();
            return $block->renderBlock($pos, $pagecode);
        //}
    }
}
