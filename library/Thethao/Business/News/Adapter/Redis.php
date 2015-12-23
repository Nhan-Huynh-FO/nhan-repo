<?php

/**
 * @name        :   Thethao_Business_News_Adapter_Redis
 * @author      :   HungNT1
 * @copyright   :   Fpt Online
 * @return      :   Thethao_Business_News_Adapter_Redis
 */
class Thethao_Business_News_Adapter_Redis
{

    /**
     * @var system_model Singleton instance
     */
    protected static $_instance;
    //list redis key
    protected $_list_article_ids_by_rule_1;
    protected $_list_article_ids_by_rule_2;
    protected $_list_article_ids_by_rule_3;
    protected $_list_question;
    protected $_end_list_award;
    protected $_list_award;

        /**
     * Constructor of class
     * @return Thethao_Business_News_Adapter_Redis
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
        $this->_list_question = $key['list_question'];
        $this->_list_award = $key['list_award'];
        $this->_end_list_award = $key['end_list_award'];
        unset($key);
    }

    /**
     * Get singletom instance
     * @return Thethao_Business_News_Adapter_Redis
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
     * get list article ids by rule 1
     * @param array $arrParams
     * @return array
     * @author HungNT1
     */
    public function getListArticleIdsByRule1($arrParams)
    {
        try
        {
            //default return
            $arrReturn = array('total' => 0, 'data' => array());
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');

            if ($redisInstance)
            {
                //get key cache
                $keyCate = vsprintf($this->_list_article_ids_by_rule_1, array($arrParams['category_id'], intval($arrParams['article_type'])));
                //get total
                $arrReturn['total'] = intval($redisInstance->get($keyCate . ':total'));
                //check total data
                if ($arrReturn['total'] > 0 && $arrParams['offset'] < $arrReturn['total'])
                {
                    //get end score
                    $end = Thethao_Global::getRedisPaging($arrParams['offset'], $arrParams['limit']);
                    //get revert score: latest will be show first
                    $arrReturn['data'] = $redisInstance->zRevRange($keyCate, $arrParams['offset'], $end, $arrParams['withcore']);
                }
            }
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }

    /**
     * Set list article ids of get news with rule 1
     * @param type $arrParams
     * @param type $arrData
     * @param type $intTotal
     * @return array()
     * @author HungNT1
     */
    public function setListArticleIdsByRule1($arrParams, $arrData, $intTotal)
    {
        try
        {
            //default return
            $arrReturn = array();
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            if ($redisInstance)
            {
                //get key cache
                $keyCate = vsprintf($this->_list_article_ids_by_rule_1, array($arrParams['category_id'], intval($arrParams['article_type'])));

                //check total
                $intTotal = $intTotal == 0 ? -1 : $intTotal;

                //set total
                $redisInstance->set($keyCate . ':total', $intTotal);

                if (!empty($arrData))
                {
                    //set keycache
                    foreach ($arrData as $key => $score)
                    {
                        //remove if have
                        $redisInstance->zRem($keyCate, $key);
                        //add
                        $arrReturn = $redisInstance->zAdd($keyCate, $score, $key);
                    }
                    //only keep 510 item
                    $redisInstance->zRemRangeByRank($keyCate, 0, -510);
                }
            }
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }

    /**
     * get list article list on order by priority_score
     * @param array $arrParams
     * @return array
     * @author HungNT1
     */
    public function getListArticleIdsByRule2($arrParams)
    {
        try
        {
            //default return
            $arrReturn = array('total' => 0, 'data' => array());
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');

            if ($redisInstance)
            {
                //get key cache
                $keyCate = vsprintf($this->_list_article_ids_by_rule_2, array($arrParams['category_id']));

                //get total
                $arrReturn['total'] = intval($redisInstance->get($keyCate . ':total'));

                //check total data
                if ($arrReturn['total'] > 0 && $arrParams['offset'] < $arrReturn['total'])
                {
                    //get end score
                    $end = Thethao_Global::getRedisPaging($arrParams['offset'], $arrParams['limit']);
                    //get revert score: latest will be show first
                    $arrReturn['data'] = $redisInstance->zRevRange($keyCate, $arrParams['offset'], $end, $arrParams['withcore']);
                }
            }
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }

    /**
     * Set list article list on order by priority_score
     * @param type $arrParams
     * @param type $arrData
     * @param type $intTotal
     * @return array()
     * @author HungNT1
     */
    public function setListArticleIdsByRule2($arrParams, $arrData, $intTotal)
    {
        try
        {
            //default return
            $arrReturn = array();
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            if ($redisInstance)
            {
                //get key cache
                $keyCate = vsprintf($this->_list_article_ids_by_rule_2, array($arrParams['category_id']));

                $intTotal = $intTotal == 0 ? -1 : $intTotal;

                //set total
                $redisInstance->set($keyCate . ':total', $intTotal);

                if (!empty($arrData))
                {
                    //set keycache
                    foreach ($arrData as $key => $score)
                    {
                        //remove if have
                        $redisInstance->zRem($keyCate, $key);
                        //add
                        $arrReturn = $redisInstance->zAdd($keyCate, $score, $key);
                    }
                    //only keep 510 item
                    $redisInstance->zRemRangeByRank($keyCate, 0, -510);
                }

            }
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }

    /**
     * Get news by rule 3 : folder X + subfolder X  => order by publish_time
     * @param array $arrParams
     *      $arrParams['category_id'] int
     *      $arrParams['offset'] int
     *      $arrParams['limit'] int
     *      $arrParams['withcore'] boolean
     * @return array
     * @author HungNT1
     */
    public function getListArticleIdsByRule3($arrParams)
    {
        //default return
        $arrReturn = array('total' => 0, 'data' => array());
        try
        {
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            if ($redisInstance)
            {
                //make key cache
                $keyCache = vsprintf($this->_list_article_ids_by_rule_3, array($arrParams['category_id'], intval($arrParams['article_type'])));

                //get total of this key
                $arrReturn['total']  = intval($redisInstance->get($keyCache . ':total'));

                //check total data
                if ($arrReturn['total'] > 0 && $arrParams['offset'] < $arrReturn['total'])
                {
                    //get end score
                    $end = Thethao_Global::getRedisPaging($arrParams['offset'], $arrParams['limit']);

                    //get revert score: latest will be show first
                    $arrReturn['data'] = $redisInstance->zRevRange($keyCache, $arrParams['offset'], $end, $arrParams['withcore']);
                }

            }
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }

    /**
     * Set list article by rule 3: folder X + subfolder X  => order by publish_time
     * @param type $arrParams
     * @param type $arrData
     * @param type $intTotal
     * @return array()
     * @author HungNT1
     */
    public function setListArticleIdsByRule3($arrParams, $arrData, $intTotal)
    {
        try
        {
            //default return
            $arrReturn = array();
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            if ($redisInstance)
            {
                //get key cache
                $keyCate = vsprintf($this->_list_article_ids_by_rule_3, array($arrParams['category_id'], intval($arrParams['article_type'])));

                $intTotal = $intTotal == 0 ? -1 : $intTotal;

                //set total
                $redisInstance->set($keyCate . ':total', $intTotal);

                if (!empty($arrData))
                {
                    //set keycache
                    foreach ($arrData as $key => $score)
                    {
                        //remove if have
                        $redisInstance->zRem($keyCate, $key);
                        //add
                        $arrReturn = $redisInstance->zAdd($keyCate, $score, $key);
                    }
                    //only keep 510 item
                    $redisInstance->zRemRangeByRank($keyCate, 0, -510);
                }

            }
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }
    
    /**
     * getListQuestion (By object type, type, destination type)
     * @param array $arrParams array(objecttype(int), type(int), destinationtype(int))
     * @return array
     * @author CuongNM17
     */
    public function getListQuestion($arrParams)
    {
        //default return
        $arrReturn = array();
        try
        {
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            
            if ($redisInstance)
            {
                $keyCache = vsprintf($this->_list_question, $arrParams['fromdate']);
                /*//get data
                $redisInstance->zRevRange($keyCache, 0, -1);
                //exec tran
                $rs = $redisInstance->exec();*/
                $rs = $redisInstance->zRevRange($keyCache, 0,-1);  
                if($rs){
                    foreach ($rs as $key => $item) {
                        $arrReturn[] = json_decode($item, true);
                    }
                }              
                
            }

        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }
    
    public function setListQuestion($arrParams,$data)
    {

        $arrReturn = true;
		
        try
        {
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            if ($redisInstance)
            {
				
                $i = 1;
                foreach ($data as $key => $item) {
                    $keyCache = vsprintf($this->_list_question, $arrParams['fromdate']);
					
                    if($i == 1 && !empty($item)){
                        $redisInstance->delete($keyCache);
                    }
					//echo $json_encode($item);
                    $redisInstance->zAdd($keyCache, $item['display_order'], json_encode($item));

                    $i++;
                }
            }
        }

        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }

        return $arrReturn;
    }
    
    
    /**
     * getListQuestion (By object type, type, destination type)
     * @param array $arrParams array(objecttype(int), type(int), destinationtype(int))
     * @return array
     * @author CuongNM17
     */
    public function getListAward($arrParams)
    {
        //default return
        $arrReturn = array();
        try
        {            
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            
            if ($redisInstance)
            {
                $keyCache = vsprintf($this->_list_award, $arrParams['fromdate']);
				
                /*//get data
                $redisInstance->zRevRange($keyCache, 0, -1);
                //exec tran
                $rs = $redisInstance->exec();*/
                $rs = $redisInstance->zRevRange($keyCache, 0,-1);  
                if($rs){
                    foreach ($rs as $key => $item) {
                        $arrReturn[] = json_decode($item, true);
                    }
                }              
                
            }            
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
		//var_dump($arrReturn);
        return $arrReturn;
    }
    
    public function setListAward($arrParams,$data)
    {

        $arrReturn = true;
		
        try
        {
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
				
				if ($redisInstance)
				{
					if(empty($data))
					{
						$keyCache = vsprintf($this->_list_award, $arrParams['fromdate']);
						$redisInstance->delete($keyCache);
						//$redisInstance->zAdd($keyCache, '0', null);
						
					}
                    else
                    {
                        $i = 1;
    					foreach ($data as $key => $item) {
    						$keyCache = vsprintf($this->_list_award, $arrParams['fromdate']);
    						
    						if($i == 1 && !empty($item)){
    							$redisInstance->delete($keyCache);
    						}
    						//echo $json_encode($item);
    						$redisInstance->zAdd($keyCache, $item['display_order'], json_encode($item));
    
    						$i++;
    						
    					}
                    }
					
				}    
						
        }

        catch (Exception $ex)
        {
		
            Thethao_Global::sendLog($ex);
        }

        return $arrReturn;
    }
    
    public function getListEndAward($arrParams)
    {
        //default return
        $arrReturn = array();
        try
        {            
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            
            if ($redisInstance)
            {
                $keyCache = vsprintf($this->_end_list_award, $arrParams['fromdate']);
				
                /*//get data
                $redisInstance->zRevRange($keyCache, 0, -1);
                //exec tran
                $rs = $redisInstance->exec();*/
                $rs = $redisInstance->zRevRange($keyCache, 0,-1);  
                if($rs){
                    foreach ($rs as $key => $item) {
                        $arrReturn[] = json_decode($item, true);
                    }
                }              
                
            }            
        }
        catch (Exception $ex)
        {
            //writelog error
            Thethao_Global::sendLog($ex);
        }
		//var_dump($arrReturn);
        return $arrReturn;
    }
    
    public function setListEndAward($arrParams,$data)
    {

        $arrReturn = true;
		
        try
        {
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
				
				if ($redisInstance)
				{
					if(empty($data))
					{
						$keyCache = vsprintf($this->_end_list_award, $arrParams['fromdate']);
						$redisInstance->delete($keyCache);
						//$redisInstance->zAdd($keyCache, '0', null);
						
					}
                    else
                    {
                        $i = 1;
    					foreach ($data as $key => $item) {
    						$keyCache = vsprintf($this->_end_list_award, $arrParams['fromdate']);
    						
    						if($i == 1 && !empty($item)){
    							$redisInstance->delete($keyCache);
    						}
    						//echo $json_encode($item);
                            $item['display_order'] = 100 - $item['display_order'];
    						$redisInstance->zAdd($keyCache, $item['display_order'], json_encode($item));
    
    						$i++;
    						
    					}
                    }
					
				}    
						
        }

        catch (Exception $ex)
        {
		
            Thethao_Global::sendLog($ex);
        }

        return $arrReturn;
    }
    
}