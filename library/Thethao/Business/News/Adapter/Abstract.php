<?php

/**
 * @todo news adapter abstract
 * @return Thethao_Business_News_Adapter_Abstract
 * @author LamTX
 */
abstract class Thethao_Business_News_Adapter_Abstract
{

    /**
     * Get list article by hot
     * @param array $arrParams (category_id,article_type,limit,offset,site_id)
     * @return array
     * @author LamTX
     */
    public function getListHotArticle($arrParams)
    {
        return array();
    }

    /**
     * Get Article Id By Product Id
     * @param array $arrParams
     * @return array $arrResult
     * @author PhongTX 
     */
    public function getArticleIdByProductId($intId)
    {
        return array();
    }

}

?>