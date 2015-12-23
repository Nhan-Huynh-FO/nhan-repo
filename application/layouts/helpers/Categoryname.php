<?php

/**
 * Buil
 */
class Zend_View_Helper_Categoryname extends Zend_View_Helper_Abstract {

    public function Categoryname($intCateID) {
        $arrCatInfo = Thethao_Model_Category::getInstance()->getDetailbyCateId($intCateID);

        return empty($arrCatInfo) ? '' : $arrCatInfo['catename'];
        
    }

}