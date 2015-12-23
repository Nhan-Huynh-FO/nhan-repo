<?php

/**
 * @name        :   Thethao_Model_Article
 * @author      :   PhuongTN
 * @copyright   :   Fpt Online
 */
class Thethao_Model_Article extends Thethao_Model
{

    static $_instance            = null;
    private $_key_rss_latest_news;
    private $_tuong_thuat;

    /**
     * return Thethao_Model_Article
     * @author LamTX
     */
    public function __construct()
    {
        //get config key caching
        $key = Thethao_Global::getConfig('caching');
        $this->_key_rss_latest_news = $key['key_rss_latest_news'];
        $this->_tuong_thuat = $key['match_tuong_thuat'];
        unset($key);
    }

    /**
     * Get singletom instance
     * @return Thethao_Model_Article
     * @author LamTX
     */
    public final static function getInstance()
    {
        //Check instance
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        //Return instance
        return self::$_instance;
    }


    /**
     * get list rule 1 for job
     * @param array $arrArticleDetail
     * @author PhuongTN
     * @todo thethao
     */
    public function getArticleForJob(&$arrArticleDetail)
    {
        try
        {
            //get all category listed on
            $arrListOn = array();
            if(isset($arrArticleDetail['cate'][SITE_ID]))
            {
                $arrListOn = $arrArticleDetail['cate'][SITE_ID];
            }
            //get cate instance
            $categoryInstance = new Thethao_Model_Category();
            //init arr cate
            $arrCate = array($arrArticleDetail['original_cate']);

            //check if show_on_folder or not
            if ($arrArticleDetail['show_status'] == 1)
            {
                $arrCate = array_merge($arrCate, $arrListOn);
            }

            //get detail all category related
            $arrCateDetail = $categoryInstance->getDetailByArrCate($arrCate);

            //check show status
            if ($arrArticleDetail['show_status'] == 1)
            {
                //init rule 1 with this original_cate
                $arrCateRule1 = array($arrArticleDetail['original_cate']);
                //loop array list on to check subcate
                foreach ($arrListOn as $intCateId)
                {
                    if(isset($arrCateDetail[$intCateId]['child_recursive']))
                    {
                        //this cate recursive children
                        $listOnChild = (array) $arrCateDetail[$intCateId]['child_recursive'];
                        //check valid rule 1: article belong to subcate of liston
                        if (in_array($arrArticleDetail['original_cate'], $listOnChild))
                        {
                            $arrCateRule1[] = $intCateId;
                        }
                    }
                    //Update rule 1
					//- Lấy các tin: được set folder vào folder cấp trên của X và được liston vào folder X
					if(isset($arrCateDetail[$intCateId]['full_parent_original']))
                    {
                        //this cate recursive children
                        $listOnChild = (array) $arrCateDetail[$intCateId]['full_parent_original'];
                        //check valid rule 1: article belong to subcate of liston
                        if (in_array($arrArticleDetail['original_cate'], $listOnChild))
                        {
                            $arrCateRule1[] = $intCateId;
                        }
                    }
                }//end loop liston
                $arrArticleDetail['cate_rule1']   = array_unique($arrCateRule1);
                $arrArticleDetail['cate_list_on'] = array_unique($arrListOn);
            }
            $arrArticleDetail['matchId'] = false;
            //kiem tra xem bai nay co phai tuong thuat tran dau khong?=> return false/true
            if ($arrArticleDetail['article_type'] == 5)
            {
                //check live match is redirect to match
                if (isset($arrArticleDetail['list_object_type']) && !empty($arrArticleDetail['list_object_type']))
                {
                    //check exist object match article and have 1 obj
                    if (isset($arrArticleDetail['list_object_type'][OBJECT_TYPE_MATCH_ARTICLE])
                            && count($arrArticleDetail['list_object_type'][OBJECT_TYPE_MATCH_ARTICLE] == 1))
                    {
                        $arrArticleDetail['matchId'] = $arrArticleDetail['list_object_type'][OBJECT_TYPE_MATCH_ARTICLE][0];
                    }
                }
            }

        }
        catch(Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
    }

    /**
     * Function add article from backend editor
     * @param array $arrArticleDetail
     * @return boolean | array
     * @author PhuongTN
     * @todo thethao
     * @modify HungNT1
     */
    public function addArticle($intArticleId)
    {
        try
        {
            //default response
            $response = array();
            //new article fw
            $objArticle = Fpt_Data_News_Article::getInstance();
            //get full article
            $arrArticleDetail = $objArticle->getArticleFull($intArticleId);

            //check cate on site
            if ( !$arrArticleDetail['cate'] || !array_key_exists(SITE_ID, $arrArticleDetail['cate']) )
            {
                return false;
            }//end if
            //check is schedule article
            if ($arrArticleDetail['publish_time'] > time())
            {
                return false;
            }

            //init model news
            $modelNews = Thethao_Model_News::getInstance();

            //format data
            $this->getArticleForJob($arrArticleDetail);

            //get app conf
            $config = Thethao_Global::getApplicationIni();
            /* @var $newsNosql Thethao_Business_News_Adapter_Nosql */
            $newsNosql = $this->factory('Article', $config['database']['nosql']['adapter']);

            //rule 1
            foreach ($arrArticleDetail['cate_rule1'] as $intCateId)
            {
                $arrDBParams = array(
                    'category_id'   => $intCateId,
                    'article_type'  => 0,
                    'offset'        => 0,
                    'limit'         => 1);
                //get db list
                $modelNews->getListArticleIdsByRule1($arrDBParams);

                $arrDBParams = array(
                    'category_id'   => $intCateId,
                    'article_type'  => $arrArticleDetail['article_type'],
                    'offset'    => 0,
                    'limit'     => 1);
                //get db list
                $modelNews->getListArticleIdsByRule1($arrDBParams);

            }

            //rule 2
            foreach ($arrArticleDetail['cate_list_on'] as $intCateId)
            {
                $modelNews->getListArticleIdsByRule2(array(
                    'category_id'   => $intCateId,
                    'offset'    => 0,
                    'limit'     => 1)
                );
            }
            //add list news
            $response['rule2'] = $newsNosql->addArticle($arrArticleDetail);

            //precache rule 3 cho site seagame
            $param_rule3 = array(
                'category_id' => SEA_GAMES,
                'offset' => 0,
                'limit' => 1
            );
            $response['rule3'] = $modelNews->getListArticleIdsByRule3($param_rule3);
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $response;
    }

    /**
     * delete related list news when delete article from BE editor
     * @param array $arrArticleDetail old detail article
     * @return array
     * @author PhuongTN
     * @todo thethao
     */
    public function deleteArticle($arrArticleDetail)
    {
        try
        {
            $arrResponse = array();
            //get app conf
            $config = Thethao_Global::getApplicationIni();
            //get list rule category
            $this->getArticleForJob($arrArticleDetail);


            //get news nosql instance
            $newsNosql = $this->factory('Article', $config['database']['nosql']['adapter']);
            //call deletea list
            $arrResponse['nosql_article'] = $newsNosql->deleteArticle($arrArticleDetail);

            //init model news
            $modelNews = Thethao_Model_News::getInstance();
            //precache rule 3 cho site seagames
            $param_rule3 = array(
                'category_id' => SEA_GAMES,
                'offset' => 0,
                'limit' => 1
            );
            $arrResponse['rule3'] = $modelNews->getListArticleIdsByRule3($param_rule3);

        }
        catch(Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
        //return data
        return $arrResponse;
    }

}

?>