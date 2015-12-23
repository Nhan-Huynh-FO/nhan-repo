<?php
/**
 * @todo helper get block on page
 * @param <string> $pos block section
 * @return <string> html of block
 * @author LamTX
 */
class Zend_View_Helper_GetBlockOther extends Zend_View_Helper_Abstract
{

    public function GetBlockOther($pos = null, $pagecode = false)
    {
        $block = Thethao_Plugin_Block::getInstance();
        return $block->renderBlock($pos, $pagecode);
    }
}
