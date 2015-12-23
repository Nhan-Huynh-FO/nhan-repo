<?php

/**
 * @name        :   Thethao_Business_Article_Adapter_Redis
 * @author      :   HungNT1
 * @copyright   :   Fpt Online
 * @return      :   Thethao_Business_Article_Adapter_Redis
 */
class Thethao_Business_Article_Adapter_Redis
{
    /**
     * @var system_model Singleton instance
     */
    protected static $_instance;
    //list redis key
    protected $_list_article_ids_by_rule_1;
    protected $_list_article_ids_by_rule_2;
    protected $_list_article_ids_by_rule_3;

        /**
     * Constructor of class
     * @return Thethao_Business_Article_Adapter_Redis
     * @author HungNT1
     */
    public function __construct()
    {
        //get key caching
        $key = Thethao_Global::getConfig('caching');
        //init keys redis related to this class
        $this->_list_article_ids_by_rule_1 = $key['list_article_ids_by_rule_1'];
        $this->_list_article_ids_by_rule_2 = $key['list_article_ids_by_rule_2'];
        $this->_list_article_ids_by_rule_3 = $key['list_article_ids_by_rule_3'];

        unset($key);
    }

    /**
     * Get singletom instance
     * @return Thethao_Business_Article_Adapter_Redis
     * @author HungNT1
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
     * add article to list news
     * @param array $arrArticle
     *          array( articledetail full, cate_rule1, cate_rule2, cate_rule3)
     * @return boolean|array
     * @author PhuongTN
     * @modify HungNT1
     */
    public function addArticle($arrArticle)
    {
        try
        {
            //default return
            $arrReturn = false;
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            //check can get redis instance
            if ($redisInstance)
            {
                $arrArticle['publish_time'] = intval($arrArticle['publish_time']);
                $arrArticle['score']        = intval($arrArticle['score']);
                $arrArticle['article_type'] = intval($arrArticle['article_type']);
                //Rule1:Add list folder news
                if (!empty($arrArticle['cate_rule1']))
                {
                    //loop category rule1
                    foreach ($arrArticle['cate_rule1'] as $cateId)
                    {
                        //generate key cache with all article (text, video, photo)
                        $keyRule1 = vsprintf($this->_list_article_ids_by_rule_1, array($cateId, 0));
                        $keyRule1Total  = $keyRule1 . ':total';
                        $totalRule1     = $redisInstance->get($keyRule1Total);
                        if ($totalRule1 != 0)
                        {
                            $zrem = $redisInstance->zRem($keyRule1, $arrArticle['article_id']);
                            $totalRule1 = ($zrem ? ($totalRule1 - 1) : $totalRule1);
                            $zadd = $redisInstance->zAdd($keyRule1, $arrArticle['score'], $arrArticle['article_id']);
                            if ($zadd)
                            {
                                $totalRule1 = ($totalRule1 == -1 ? 1 : ($totalRule1 + 1));
                                $redisInstance->set($keyRule1Total, $totalRule1);
                                //only keep last 510 item
                                $redisInstance->zRemRangeByRank($keyRule1, 0, -510);
                            }
                        }
                        //generate key cache with article type (1-text, 2-video, 3-photo)
                        $keyRule1 = vsprintf($this->_list_article_ids_by_rule_1, array($cateId, $arrArticle['article_type']));
                        $keyRule1Total  = $keyRule1 . ':total';
                        $totalRule1     = ($redisInstance->get($keyRule1Total));
                        if ($totalRule1 != 0)
                        {
                            $zrem = $redisInstance->zRem($keyRule1, $arrArticle['article_id']);
                            $totalRule1 = ($zrem ? ($totalRule1 - 1) : $totalRule1);
                            $zadd = $redisInstance->zAdd($keyRule1, $arrArticle['score'], $arrArticle['article_id']);
                            if ($zadd)
                            {
                                $totalRule1 = ($totalRule1 == -1 ? 1 : ($totalRule1 + 1));
                                $redisInstance->set($keyRule1Total, $totalRule1);
                                //only keep last 510 item
                                $redisInstance->zRemRangeByRank($keyRule1, 0, -510);
                            }
                        }
                    }
                }

                //Rule2:Add list folder + list on news
                if (!empty($arrArticle['cate_list_on']))
                {
                    //loop cate
                    foreach ($arrArticle['cate_list_on'] as $cateId)
                    {
                        //set key cache with article type
                        $keyArticleType = sprintf($this->_list_article_ids_by_rule_2, $cateId);
                        $totalRule2     = ($redisInstance->get($keyArticleType . ':total'));
                        if ($totalRule2 != 0)
                        {
                            $zrem = $redisInstance->zRem($keyArticleType, $arrArticle['article_id']);
                            $totalRule2 = ($zrem ? ($totalRule2 - 1) : $totalRule2);
                            $zadd = $redisInstance->zAdd($keyArticleType, $arrArticle['score'], $arrArticle['article_id']);
                            if ($zadd == 1)
                            {
                                $totalRule2 = ($totalRule2 == -1 ? 1 : ($totalRule2 + 1));
                                $redisInstance->set($keyArticleType . ':total', $totalRule2);
                                //only keep last 510 item
                                $redisInstance->zRemRangeByRank($keyArticleType, 0, -510);
                            }
                        }
                    }
                }

                //Rule3:Latest news
                if (!empty($arrArticle['cate_rule3']))
                {
                    //loop cate rule 3
                    foreach ($arrArticle['cate_rule3'] as $cateId)
                    {
                        //generate key rule 3
                        $keyRule3 = vsprintf($this->_list_article_ids_by_rule_3, array($cateId, 0));
                        //generate key total rule 3
                        $keyRule3Total = $keyRule3 . ':total';
                        $totalRule3 = $redisInstance->get($keyRule3Total);
                        if($totalRule3 != 0)
                        {
                            //add to sortset
                            $zrem = $redisInstance->zRem($keyRule3, $arrArticle['article_id']);
                            $totalRule3 = $zrem ? $totalRule3 - 1 : $totalRule3;
                            $zadd = $redisInstance->zAdd($keyRule3, $arrArticle['publish_time'], $arrArticle['article_id']);
                            if ($zadd)
                            {
                                $totalRule3 = ($totalRule3 == -1 ? 1 : ($totalRule3 + 1));
                                //set new total
                                $redisInstance->set($keyRule3 . ':total', $totalRule3);
                                //only keep last 510 item
                                $redisInstance->zRemRangeByRank($keyRule3, 0, -510);
                            }
                        }
                        //set key cache with article type
                        $keyRule3 = vsprintf($this->_list_article_ids_by_rule_3, array($cateId, $arrArticle['article_type']));
                        //generate key total rule 3
                        $keyRule3Total = $keyRule3 . ':total';
                        $totalRule3 = $redisInstance->get($keyRule3Total);
                        if($totalRule3 != 0)
                        {
                            //add to sortset
                            $zrem = $redisInstance->zRem($keyRule3, $arrArticle['article_id']);
                            $totalRule3 = ($zrem ? ($totalRule3 - 1) : $totalRule3);
                            $zadd = $redisInstance->zAdd($keyRule3, $arrArticle['publish_time'], $arrArticle['article_id']);
                            if ($zadd)
                            {
                                $totalRule3 = ($totalRule3 == -1 ? 1 : ($totalRule3 + 1));
                                //set new total
                                $redisInstance->set($keyRule3 . ':total', $totalRule3);
                                //only keep last 510 item
                                $redisInstance->zRemRangeByRank($keyRule3, 0, -510);
                            }
                        }

                    }//end foreach
                    //set key cache with article type
                    $keyRule3 = vsprintf($this->_list_article_ids_by_rule_3, array(SITE_ID, 0));
                    //generate key total rule 3
                    $keyRule3Total = $keyRule3 . ':total';
                    $totalRule3 = $redisInstance->get($keyRule3Total);
                    if($totalRule3 != 0)
                    {
                        //add to sortset
                        $zrem = $redisInstance->zRem($keyRule3, $arrArticle['article_id']);
                        $totalRule3 = ($zrem ? ($totalRule3 - 1) : $totalRule3);
                        $zadd = $redisInstance->zAdd($keyRule3, $arrArticle['publish_time'], $arrArticle['article_id']);
                        if ($zadd)
                        {
                            $totalRule3 = ($totalRule3 == -1 ? 1 : ($totalRule3 + 1));
                            //set new total
                            $redisInstance->set($keyRule3 . ':total', $totalRule3);
                            //only keep last 510 item
                            $redisInstance->zRemRangeByRank($keyRule3, 0, -510);
                        }
                    }
                    //set key cache with article type
                    $keyRule3 = vsprintf($this->_list_article_ids_by_rule_3, array(SITE_ID, $arrArticle['article_type']));
                    //generate key total rule 3
                    $keyRule3Total = $keyRule3 . ':total';
                    $totalRule3 = $redisInstance->get($keyRule3Total);
                    if($totalRule3 != 0)
                    {
                        //add to sortset
                        $zrem = $redisInstance->zRem($keyRule3, $arrArticle['article_id']);
                        $totalRule3 = ($zrem ? ($totalRule3 - 1) : $totalRule3);
                        $zadd = $redisInstance->zAdd($keyRule3, $arrArticle['publish_time'], $arrArticle['article_id']);
                        if ($zadd)
                        {
                            $totalRule3 = ($totalRule3 == -1 ? 1 : ($totalRule3 + 1));
                            //set new total
                            $redisInstance->set($keyRule3 . ':total', $totalRule3);
                            //only keep last 510 item
                            $redisInstance->zRemRangeByRank($keyRule3, 0, -510);
                        }
                    }

                }//end rule3
                $arrReturn = true;
            }//end check redis instance
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }

    /**
     * delete article from BE Editor by arr info
     * @param array $arrArticle
     * @return boolean|array
     * @author PhuongTN
     */
    public function deleteArticle($arrArticle)
    {
        try
        {
            //default return
            $arrReturn = false;
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            //check can get redis instance
            if ($redisInstance)
            {
                //Rule1:Add list folder news
                if (!empty($arrArticle['cate_rule1']))
                {
                    //loop category rule1
                    foreach ($arrArticle['cate_rule1'] as $cateId)
                    {
                        //generate key cache with all article (text, video, photo) cate original
                        $keyCache = vsprintf($this->_list_article_ids_by_rule_1, array($cateId, 0));
                        $redisInstance->zAdd($keyCache, -1, $arrArticle['article_id']);
                        $rs = $redisInstance->zDelete($keyCache, $arrArticle['article_id']);
                        if ($rs)
                        {
                            $totalRule1 = ($redisInstance->get($keyCache . ':total'));
                            if ($totalRule1 > 0)
                            {
                                $redisInstance->set($keyCache . ':total', ($totalRule1 - 1));
                            }
                        }

                        //generate key cache with all article (text, video, photo) cate original
                        $keyCache = vsprintf($this->_list_article_ids_by_rule_1, array($cateId, $arrArticle['article_type']));
                        $redisInstance->zAdd($keyCache, -1, $arrArticle['article_id']);
                        $rs = $redisInstance->zDelete($keyCache, $arrArticle['article_id']);
                        if ($rs)
                        {
                            $totalRule1 = ($redisInstance->get($keyCache . ':total'));
                            if ($totalRule1 > 0)
                            {
                                $redisInstance->set($keyCache . ':total', ($totalRule1 - 1));
                            }
                        }
                    }
                }

                //Rule2:Add list folder + list on news
                if (!empty($arrArticle['cate_list_on']))
                {
                    //loop cate
                    foreach ($arrArticle['cate_list_on'] as $cateId)
                    {
                        //generate key cache
                        $keyCache = sprintf($this->_list_article_ids_by_rule_2, $cateId);
                        $redisInstance->zAdd($keyCache, -1, $arrArticle['article_id']);
                        $rs = $redisInstance->zDelete($keyCache, $arrArticle['article_id']);
                        if ($rs) {
                            $total = ($redisInstance->get($keyCache . ':total'));
                            if ($total > 0)
                            {
                                $redisInstance->set($keyCache . ':total', ($total - 1));
                            }
                        }
                    }
                }

                //Rule3:Latest news
                if (!empty($arrArticle['cate_rule3']))
                {
                    //loop cate rule 3
                    foreach ($arrArticle['cate_rule3'] as $cateId)
                    {
                        $keyCache = vsprintf($this->_list_article_ids_by_rule_3, array($cateId, 0));
                        $redisInstance->zAdd($keyCache, -1, $arrArticle['article_id']);
                        $rs = $redisInstance->zDelete($keyCache, $arrArticle['article_id']);
                        if ($rs) {
                            $total = intval($redisInstance->get($keyCache . ':total'));
                            if ($total > 0)
                            {
                                $redisInstance->set($keyCache . ':total', ($total - 1));
                            }
                        }

                        $keyCache = vsprintf($this->_list_article_ids_by_rule_3, array($cateId, $arrArticle['article_type']));
                        $redisInstance->zAdd($keyCache, -1, $arrArticle['article_id']);
                        $rs = $redisInstance->zDelete($keyCache, $arrArticle['article_id']);
                        if ($rs) {
                            $total = intval($redisInstance->get($keyCache . ':total'));
                            if ($total > 0)
                            {
                                $redisInstance->set($keyCache . ':total', ($total - 1));
                            }
                        }
                    }
                    $keyCache = vsprintf($this->_list_article_ids_by_rule_3, array(SITE_ID, 0));
                    $redisInstance->zAdd($keyCache, -1, $arrArticle['article_id']);
                    $rs = $redisInstance->zDelete($keyCache, $arrArticle['article_id']);
                    if ($rs) {
                        $total = intval($redisInstance->get($keyCache . ':total'));
                        if ($total > 0)
                        {
                            $redisInstance->set($keyCache . ':total', ($total - 1));
                        }
                    }

                    $keyCache = vsprintf($this->_list_article_ids_by_rule_3, array(SITE_ID, $arrArticle['article_type']));
                    $redisInstance->zAdd($keyCache, -1, $arrArticle['article_id']);
                    $rs = $redisInstance->zDelete($keyCache, $arrArticle['article_id']);
                    if ($rs) {
                        $total = intval($redisInstance->get($keyCache . ':total'));
                        if ($total > 0)
                        {
                            $redisInstance->set($keyCache . ':total', ($total - 1));
                        }
                    }
                }
                // end rule 3
                $arrReturn = true;
            }//end check redis instance
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }
}