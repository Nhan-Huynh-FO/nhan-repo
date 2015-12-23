<?php

/**
 * JobAsync
 *
 * @author PhuongTN
 */
class Job_Model_Async
{

    /**
     * Job sync cache when add 1 article
     * @param array $params array('articleID')
     * @author PhuongTN
     */
    public function addArticle($params)
    {
        //default return
        $response = false;
        try
        {
            //init params
            $articleID = intval($params['article_id']);

            if ($articleID > 0)
            {
                //init news instance
                $modelArtile = Thethao_Model_Article::getInstance();

                //add article to cache
                $response = $modelArtile->addArticle($articleID);

                //clear cache file
                self::clearApcFile();
            }
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $response;
    }

    /**
     * job edit article
     * @param type $data
     * @return type
     */
    public function editArticle($params)
    {
        //default return
        $response = false;
        try
        {
            $articleID      = intval($params['article_id']);
            $modelArticle   = Thethao_Model_Article::getInstance();
            $response       = $modelArticle->deleteArticle($params['old_detail']);
            $response       = $modelArticle->addArticle($articleID);

            //clear cache file
            self::clearApcFile();
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $response;
    }

    /**
     * job delete article
     * @param type $data
     * @return type
     */
    public function deleteArticle($params)
    {
        //default return
        $response = false;
        try
        {
            $modelArticle = Thethao_Model_Article::getInstance();
            if (isset($params['isMulti']) && $params['isMulti'] === true)
            {
                foreach ($params['arrArticles'] as $articleDetail)
                {
                    $response[] = $modelArticle->deleteArticle($articleDetail);
                }
            }
            else
            {
                $articleDetail = $params['old_detail'];
                $response      = $modelArticle->deleteArticle($articleDetail);
            }

            //clear cache file
            self::clearApcFile();
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $response;
    }

    /**
     * update hot position all by cate
     * @return type
     */
    public function updateHotByCate()
    {
        $response = true;
        try
        {
            //clear cache file
            self::clearApcFile();
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $response;
    }

    /**
     * job edit/delete article
     * @param type $data
     * @return type
     */
    public function updateCategory()
    {
        //default return
        $response = false;
        try
        {
            $modelCate = Thethao_Model_Category::getInstance();
            $response  = $modelCate->editCategory();
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $response;
    }

    /**
     * job insert Feedback Video (Phản hồi của user về Video)
     * @param type $data
     * @return type
     */
    public function userFeedbackVideo($params)
    {
        //default return
        $response = false;
        try
        {
            $modelFeedback = Thethao_Model_Feedback::getInstance();
            $response  = $modelFeedback->insertFeedbackVideo($params);
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $response;
    }

    /**
     * function clear cache file
     */
    static function clearApcFile()
    {
        //init news instance
        $caching	= Thethao_Model_Caching::getInstance();
        $caching->clearCacheFile();

    }

    /**
     * @author   : PhongTX - 20/06/2013
     * call job update caching FE
     * @param : string $strPageCode
     * @name : updateCache
     * @copyright   : FPT Online
     * @todo    : updateCache
     */
    public function updateBlock($arrParams)
    {
        try
        {
            //default response
            $return = array('update_block' => 0);
            //new instance object
            $modelBlock = new Thethao_Model_Block();
            $arrPageCode = $arrParams['strPageCode'];

            $response = array();

            if (!empty($arrPageCode))
            {
                foreach ($arrPageCode as $pageCode)
                {
                    $response[] = $modelBlock->updateBlock($pageCode);
                }

                self::clearApcFile();
            }
            $return['update_block'] = $response;
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $return;
    }
    
    /**
     * @author   : TrangNT30
     * call job update caching FE
     * @param : string $strDate
     * @name : updateCache
     * @copyright   : FPT Online
     * @todo    : updateCache
     */
    public function updateQuestion($arrParams)
    {
        try
        {
            //default response
            $return = array('update_question' => 0);
            //new instance object
            $modelNews = new Thethao_Model_News();
            
            $response = array();

            if (!empty($arrParams))
            {               
                $response[] = $modelNews->getListQuestion(array(
                'isGearman'     => true,
                'fromdate'    => $arrParams['fromdate'],
                'todate'    => $arrParams['todate'],                   
                ));              

                self::clearApcFile();
            }
            $return['update_question'] = $response;
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $return;
    }
    
    /**
     * @author   : TrangNT30
     * call job update caching FE
     * @param : string $strDate
     * @name : updateCache
     * @copyright   : FPT Online
     * @todo    : updateCache
     */
    public function updateAward($arrParams)
    {
        try
        {
            //default response
            $return = array('update_award' => 0);
            //new instance object
            $modelNews = new Thethao_Model_News();
            
            $response = array();

            if (!empty($arrParams))
            {             
                if($arrParams['award']==AWARD_DOVUI)
                {
                    $response[] = $modelNews->getListEndAward(array(
                    'isGearman'     => true,
                    'object_type'  => OBJECT_TYPE_DOVUI,
                    'award'  => AWARD_DOVUI,
                    'fromdate'    => $arrParams['fromdate'],
                    'todate'    => $arrParams['todate'],           
                    ));   
                }
                else
                {
                    $response[] = $modelNews->getListAward(array(
                    'isGearman'     => true,
                    'object_type'  => OBJECT_TYPE_DOVUI,
                    'award'  => $arrParams['award'],
                    'fromdate'    => $arrParams['fromdate'],
                    'todate'    => $arrParams['todate'],           
                    ));    
                }           

                self::clearApcFile();
            }
            $return['update_award'] = $response;
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $return;
    }
}