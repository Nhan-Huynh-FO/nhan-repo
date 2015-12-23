<?php
/**
 * @todo category adapter abstract
 * @return Thethao_Business_Category_Adapter_Abstract
 * @author PhuongTN
 */
abstract class Thethao_Business_Category_Adapter_Abstract
{
    /**
     * @todo get full category by parent id
     * @param <int> $intCategoryID
     * @return <array>
     * @author PhuongTN
     */
    public function getFullCategoryByParentId($intCategoryID)
    {
        return array();
    }
}
?>