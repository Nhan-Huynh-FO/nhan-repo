<?php

/**
 * @todo        :   model news
 * @author      :   PhuongTN
 * @name        :   Thethao_Model_News
 * @copyright   :   Fpt Online
 * @return      :   Thethao_Model_News
 */
class Thethao_Model_News extends Thethao_Model
{
    protected static $_instance = null;
    protected $_map_article;
    protected $_max_limit = 100;
    protected $_list_article_ids_by_rule_3;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     * @author PhuongTN
     * @return Thethao_Model_News
     */
    public function __construct()
    {
        //get config key caching
        $key = Thethao_Global::getConfig('caching');
        //init News key
        $this->_map_article = $key['map_article'];
        $this->_list_article_ids_by_rule_3 = $key['list_article_ids_by_rule_3'];
        unset($key);
    }

    /**
     * Get singletom instance
     * @return Thethao_Model_News
     * @author PhuongTN
     * @todo Thethao
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
     * Get list article ids with rule 1 of category id
     * @param type $arrParams => array(category_id, article_type, limit, offset, withcore)
     * @return array - list article ids
     * @author HungNT1
     */
    public function getListArticleIdsByRule1($arrParams)
    {
        //default return
        $arrReturn = array('total' => 0, 'data' => array());
        //set paramt default
        $arrDefault = array('category_id' => 0, 'article_type' => 0,'limit' => 0, 'offset' => 0, 'withcore' => FALSE);
        //merge param with default
        $arrParams = array_merge($arrDefault, $arrParams);
        
        //check limit with max_limit to reset limit
        //$arrParams['limit'] = $arrParams['limit']>$this->_max_limit?$this->_max_limit:$arrParams['limit'];
        
        //valid data
        if ($arrParams['category_id'] > 0 && $arrParams['limit'] > 0 && $arrParams['limit'] <= $this->_max_limit)
        {
            //set limit
            $intLimit = $arrParams['limit'];
            //set offset
            $intOffset = $arrParams['offset'];
            //count end record
            $endRecord = $intLimit + $intOffset - 1;
            //get app conf
            $config = Thethao_Global::getApplicationIni();
            //get nosql new instance
            $newsNosql = $this->factory('News', $config['database']['nosql']['adapter']);
            //if end record in redis list range
            if ($endRecord < 500)
            {
                //call redis to get list
                $arrReturn = $newsNosql->getListArticleIdsByRule1($arrParams);
                //reset paramt
                $intLimit = 500;
                $intOffset = 0;
            }

            //check data empty
            if (empty($arrReturn['data']) && $arrReturn['total'] > -1)
            {
                //init mysql instance
                $newsMysql = $this->factory('News', $config['database']['default']['adapter']);
                //get db list
                $arrReturn = $newsMysql->getListArticleIdsByRule1(array(
                    'category_id' => $arrParams['category_id'],
                    'article_type'=> $arrParams['article_type'],
                    'offset' => $intOffset,
                    'limit' => $intLimit));

                if ($endRecord < 500)
                {
                    //set list latest redis
                    $newsNosql->setListArticleIdsByRule1($arrParams, $arrReturn['data'], $arrReturn['total']);
                    //only get array id
                    if (!empty($arrReturn['data']))
                    {
                        $arrReturn['data'] = $arrParams['withcore'] ? $arrReturn['data'] : array_keys($arrReturn['data']);
                        //slice offset, limit
                        $arrReturn['data'] = array_slice($arrReturn['data'], $arrParams['offset'], $arrParams['limit'], $arrParams['withcore']);
                    }
                }
                else
                {
                    if (!empty($arrReturn['data']))
                    {
                        //only get array id
                        $arrReturn['data'] = $arrParams['withcore'] ? $arrReturn['data'] : array_keys($arrReturn['data']);
                    }
                }//end check get db success
            }//end check get redis success
        }//end check valid category_id
        return $arrReturn;
    }

    /**
     * Get list article ids with rule 2 of category id
     * @param type $arrParams => array(category_id, 'from_date' => NULL, 'to_date' => NULL,limit, offset, withcore)
     * @return array ('total', 'data'=>array()) - list article ids
     * @author HungNT1
     */
    public function getListArticleIdsByRule2($arrParams)
    {
        //default return
        $arrReturn = array('total' => 0, 'data' => array());
        //set paramt default
        $arrDefault = array('category_id' => 0, 'from_date' => NULL, 'to_date' => NULL, 'limit' => 0, 'offset' => 0, 'withcore' => FALSE);
        //merge param with default
        $arrParams = array_merge($arrDefault, (array)$arrParams);
        
        //check limit with max_limit to reset limit
        //$arrParams['limit'] = ($arrParams['limit'] <= 0 || $arrParams['limit'] > $this->_max_limit) ? $this->_max_limit : $arrParams['limit'];
        
        //valid data
        if ($arrParams['category_id'] > 0 && $arrParams['limit'] > 0 && $arrParams['limit'] <= $this->_max_limit)
        {
            //set limit
            $intLimit = $arrParams['limit'];
            //set offset
            $intOffset = $arrParams['offset'];
            //count end record
            $endRecord = $intLimit + $intOffset - 1;
            //get app conf
            $config = Thethao_Global::getApplicationIni();
            //get nosql new instance
            $newsNosql = $this->factory('News', $config['database']['nosql']['adapter']);
            //if end record in redis list range
            if ($endRecord < 500)
            {
                //call redis to get list
                $arrReturn = $newsNosql->getListArticleIdsByRule2($arrParams);
                //reset paramt
                $intLimit = 500;
                $intOffset = 0;
            }

            //check data empty
            if (empty($arrReturn['data']) && $arrReturn['total'] > -1)
            {
                //init mysql instance
                $newsMysql = $this->factory('News', $config['database']['default']['adapter']);
                //get db list
                $arrReturn = $newsMysql->getListArticleIdsByRule2(array(
                    'category_id' => $arrParams['category_id'],
                    'offset' => $intOffset,
                    'limit' => $intLimit));

                //slice offset, limit if end record < 500
                if($endRecord < 500)
                {
                    //set list latest redis
                    $newsNosql->setListArticleIdsByRule2($arrParams, $arrReturn['data'], $arrReturn['total']);
                    if (!empty($arrReturn['data']))
                    {
                        $arrReturn['data'] = $arrParams['withcore'] ? $arrReturn['data'] : array_keys($arrReturn['data']);
                        $arrReturn['data'] = array_slice($arrReturn['data'], $arrParams['offset'], $arrParams['limit'], $arrParams['withcore']);
                    }
                }
                else {
                    if (!empty($arrReturn['data']))
                    {
                        //only get array id
                        $arrReturn['data'] = $arrParams['withcore'] ? $arrReturn['data'] : array_keys($arrReturn['data']);
                    }
                }
            }//end check get redis success
        }//end check valid category_id

        return $arrReturn;
    }

    /**
     * Get news by rule 3 : folder X + subfolder X  => order by publish_time
     * @param array $arrParams array(category_id, offset, limit)
     * @return array
     * @author HungNT1
     */
    public function getListArticleIdsByRule3($arrParams)
    {
        //default return
        $arrReturn = array('total' => 0, 'data' => array());
        //set paramt default
        $arrDefault = array('category_id' => 0, 'limit' => 10, 'offset' => 0);
        //merge param with default
        $arrParams = array_merge($arrDefault, $arrParams);

        //format params
        $arrParams['limit'] = intval($arrParams['limit']);
        $arrParams['offset'] = intval($arrParams['offset']);

        //check limit with max_limit to reset limit
        //$arrParams['limit'] = $arrParams['limit']>$this->_max_limit?$this->_max_limit:$arrParams['limit'];

        //valid data
        if ($arrParams['category_id'] > 0 && $arrParams['limit'] > 0 && $arrParams['limit'] <= $this->_max_limit)
        {
            //Check is from job
            $isFromJob = defined('FROM_JOB') && FROM_JOB == 1;
            // Get instance memcache
            $memCacheInstance = Thethao_Global::getCache('all');
            $keyCache = vsprintf($this->_list_article_ids_by_rule_3, array($arrParams['category_id']));
            //get data from mem cached
            $arrReturn = $memCacheInstance->read($keyCache);
            //check data empty
            if ($isFromJob || $arrReturn === false)
            {
                //get app conf
                $config = Thethao_Global::getApplicationIni();
                //init mysql instance
                $newsMysql = $this->factory('News', $config['database']['default']['adapter']);
                //get db list
                $arrReturn = $newsMysql->getListArticleIdsByRule3(array(
                    'category_id' => $arrParams['category_id'],
                    'offset' => 0, //get tu record dau tien
                    'limit' => 10)); //max record

                //set memcached
                $memCacheInstance->write($keyCache, $arrReturn);
                if ($isFromJob)
                {
                    Thethao_Global::writeMemcache($keyCache, $arrReturn, 'all');
                }
            }
        }//end check valid category_id
        //process data return
        if (!empty($arrReturn['data']))
        {
            //only get array id
            $arrReturn['data'] = array_slice(array_keys($arrReturn['data']), $arrParams['offset'], $arrParams['limit']);
        }

        return $arrReturn;
    }

    /**
     * Get news by rule 4 : folder X + subfolder X in week => order by total_view desc, publish_time desc
     * from in the framework
     * @author HungNT1
     * @param type array('category_id', 'offset', 'limit')
     * @return array('data' => array());
     */
    public function getListArticleIdsByRule4($arrParams)
    {
        //set paramt default
        $arrDefault = array('category_id' => NULL, 'site_id' => SITE_ID, 'type' => 1, 'limit' => 10, 'offset' => 0);
        //merge param with default
        $arrParams = array_merge($arrDefault, $arrParams);
        
        //check limit with max_limit to reset limit
        $arrParams['limit'] = $arrParams['limit']>$this->_max_limit?$this->_max_limit:$arrParams['limit'];
        
        $arrReturn = array();
        try
        {
            $intLimit = intval($arrParams['limit']);
            if ($arrParams['category_id'] == SITE_ID)
            {
                $arrParams['category_id'] = NULL;
            }
            $objArticle = Fpt_Data_News_Article::getInstance();

            $arrReturn = $objArticle->getNewsByTopView($arrParams);
            //Count data
            $count = count($arrData);
            if ($count < $intLimit)
            {
                $arrParams['category_id'] = NULL;
                $arrParams['site_id'] = SITE_ID;
                $arrParams['limit'] = 10;
                $arrData = $objArticle->getNewsByTopView($arrParams);
                $arrReturn = array_merge($arrReturn, $arrData);
            }
            if (!empty($arrData))
            {
                $arrReturn = array_slice($arrReturn, 0, $intLimit);
            }
        }
        catch (Zend_Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }
    /**
     * Get Article Id By Product Id
     * @param int $intId
     * @return $articleId
     * @author HungNT1
     * @todo Thethao
     */
    public function getArticleIdByProductId($intId)
    {
        try
        {
            //get memcache instance
            $memcached = Thethao_Global::getCache('all');
            //make key cache
            $keyCache = vsprintf($this->_map_article, array($intId));

            //get cache
            $articleId = $memcached->read($keyCache);

            if ($articleId == false)
            {
                //get config
                $config = Thethao_Global::getApplicationIni();
                //get db instance
                $newsMysql = $this->factory('News', $config['database']['default']['adapter']);
                //call sp to get article id by product id, return array
                $articleId = $newsMysql->getArticleIdByProductId($intId);

                if (count($articleId) > 0)
                {
                    //Wire question answer cache here!
                    $memcached->write($keyCache, $articleId,43200);
                }
                else
                {
                    $memcached->write($keyCache, -1, 43200);

                }
            }//end check if miss cache
            return $articleId;
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
    }

    /**
     * @desc check valid calback jsonp
     * @param type $subject
     * @return type
     */
    function isValidCallback($subject)
    {
        $identifier_syntax
                = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

        $reserved_words = array('break', 'do', 'instanceof', 'typeof', 'case',
            'else', 'new', 'var', 'catch', 'finally', 'return', 'void', 'continue',
            'for', 'switch', 'while', 'debugger', 'function', 'this', 'with',
            'default', 'if', 'throw', 'delete', 'in', 'try', 'class', 'enum',
            'extends', 'super', 'const', 'export', 'import', 'implements', 'let',
            'private', 'public', 'yield', 'interface', 'package', 'protected',
            'static', 'null', 'true', 'false');

        return preg_match($identifier_syntax, $subject)
                && !in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
    }
    
    /**
     * getListQuestionWorldCup - Get mysql (dung de nap cache trong truong hop flush cache)
     * @param array $arrParams array(datetime(int), limit(int))
     * @return array
     * @author CUONGNM17
     */
    public function getListQuestion($arrParams)
    {        
        $arrReturn = false; 
        $config = Thethao_Global::getApplicationIni();
        $nosql = $this->factory('News', $config['database']['nosql']['adapter']);
        if($arrParams['isGearman'] == false)
        {
            $arrReturn = $nosql->getListQuestion($arrParams);
        }

        if(empty($arrReturn) || $arrReturn == false){
            $config = Thethao_Global::getApplicationIni();
            //init mysql instance
            $newsMysql = $this->factory('News', $config['database']['default']['adapter']);
            //get db list
            
            $arrReturn = $newsMysql->getListQuestion($arrParams);
            if($arrReturn){
                $nosql->setListQuestion($arrParams,$arrReturn);
            }            
        }
        return $arrReturn;
    }
    
    /**
     * getListAward - Get mysql (dung de nap cache trong truong hop flush cache)
     * @param array $arrParams array(datetime(int), limit(int))
     * @return array
     * @author CUONGNM17
     */
    public function getListAward($arrParams)
    {
		
        $arrReturn = false; 
        $config = Thethao_Global::getApplicationIni();
        $nosql = $this->factory('News', $config['database']['nosql']['adapter']);   
		
        if($arrParams['isGearman'] == false)
        {            
            $arrReturn = $nosql->getListAward($arrParams);
			//var_dump($arrReturn);
        }
        if(empty($arrReturn) || $arrReturn == false){
			
            $config = Thethao_Global::getApplicationIni();
            //init mysql instance
            $newsMysql = $this->factory('News', $config['database']['default']['adapter']);
            //get db list
           
            $arrReturn = $newsMysql->getListAward($arrParams);
			
			$nosql->setListAward($arrParams,$arrReturn);
            
					
        }
        return $arrReturn;
    }
    
    public function getListEndAward($arrParams)
    {
		
        $arrReturn = false; 
        $config = Thethao_Global::getApplicationIni();
        $nosql = $this->factory('News', $config['database']['nosql']['adapter']);   
		
        if($arrParams['isGearman'] == false)
        {            
            $arrReturn = $nosql->getListEndAward($arrParams);
        }
        if(empty($arrReturn) || $arrReturn == false){
			
            $config = Thethao_Global::getApplicationIni();
            //init mysql instance
            $newsMysql = $this->factory('News', $config['database']['default']['adapter']);
            //get db list
           
            $arrReturn = $newsMysql->getListAward($arrParams);
			
			$nosql->setListEndAward($arrParams,$arrReturn);
            
					
        }
        return $arrReturn;
    }
    
    public function getListAward11($arrParams)
    {
        $arrReturn = false; 
        $config = Thethao_Global::getApplicationIni();
        $nosql = $this->factory('News', $config['database']['nosql']['adapter']);   
		
        if($arrParams['isGearman'] == false)
        {         	
            $arrReturn = $nosql->getListAward($arrParams);
			//var_dump($arrReturn);
        }
        if(empty($arrReturn) || $arrReturn == false){
            $config = Thethao_Global::getApplicationIni();
            //init mysql instance
            $newsMysql = $this->factory('News', $config['database']['default']['adapter']);
            //get db list
			
			
           
            $arrReturn = $newsMysql->getListAward($arrParams);
			
			$nosql->setListAward($arrParams,$arrReturn);
            
					
        }
		
        return $arrReturn;
    }
    
	
    public function getListAwardByWeek($arrParams,$week)
    {   
        
        $sdate = DOVUI_SDATE;
        $edate = DOVUI_EDATE;
        $currentDate = new DateTime();
        $currentDate = $currentDate->format('Y-m-d');
        $sdateNolmal = date('Y-m-d H:i:s', DOVUI_SDATE);
        $sdateNolmal = new DateTime($sdateNolmal);
        $edateNolmal = date('Y-m-d H:i:s', DOVUI_SDATE);
        $edateNolmal = new DateTime($edateNolmal);


		$addDay = "P".(($week) * 7)."D";
		$sdateNolmal->add(new DateInterval($addDay));
        $sdateNolmal->sub(new DateInterval("P1D"));
		$addDay = "P".(($week - 0) * 7)."D";
		$edateNolmal->add(new DateInterval($addDay));
	

        $db = array();
        for($i = 0; $i < 7; $i++)
        {
            $fromdate = $sdateNolmal->format('Y-m-d 00:00:00');
            $todate = $sdateNolmal->format('Y-m-d 23:59:59');
            $fromdate = strtotime($fromdate);  
            $todate = strtotime($todate);  
            //set Param
            $arrParams['fromdate'] = $fromdate;
            $arrParams['todate'] = $todate;
			$dt = self::getListAward11($arrParams);
			if(!empty($dt))
				$db['data'][] = $dt[0];
			$sdateNolmal->sub(new DateInterval('P1D'));
        }
        
		return $db;
        
        
    }
    public function genHtmlListAward($arrData,$week,$strDate)
	{
		$i = 1;
		$html;
		$html = '<div class="label">'.$strDate.'</div>';
		$html .= '<table><thead><tr><th class="day"><div>Ngày</div></th><th class="name"><div>Họ tên</div></th>';
        $html .='<th class="phone"><div>Số điện thoại</div></th><th class="tld"><div>Trả lời đúng</div></th>';
        $html .='<th class="time"><div>Thời gian</div></th></tr></thead><tbody>';
		foreach($arrData['data'] as $item)
	    {
			if($i == count($arrData['data']) && $week > 1)
				$html .= '<tr class="last">';
			else
				$html .= '<tr>';
            $html .= '<td class="day">' .date('d/m',$item['award_time']) .'</td>';
			$html .='<td class="name">'.$item['full_name'].'</td>';
			$html .='<td class="phone">'.substr($item['mobile'],0,strlen($item['mobile'])-3) .'xxx</td>';
			$html .='<td class="tld">'.$item['amount'] .'/10</td>';
			$html .='<td class="time">'.self::formatMilliseconds($item['vote_date']).'</td>';
			$html .= '</tr>';
			$i++;
		}
		$html .= '</tbody></table>';
		echo $html;
	}
	public function genHtmlBlockAward($arrData,$week,$strDate,$currentWeek)
	{
	//var_dump($arrData);
	   $html;
       
       $html = '<table id="table'.$week.'">';
       if($week != $currentWeek)
       {
        $html = '<table id="table'.$week.'" style="display:none">';
       }
       $html .= '<thead><tr><th class="d">Ngày</th><th class="n_p">Họ tên - SĐT</th><th class="tl">TLĐ</th><th class="t">TG</th></tr></thead><tbody>';
	   if(!empty($arrData))
	   {
		   foreach($arrData['data'] as $item)
		   {
			$html .= '<tr><td class="d">' .date('d/m',$item['award_time']) .'</td>';
			$html .= '<td class="n_p"><strong>'.$item['full_name'].'<span>'.substr($item['mobile'],0,strlen($item['mobile'])-3).'xxx</span></strong></td>';
			$html .= '<td class="tl"><a href="/dovui/?day='.$item['award_time'].'" style="color:#328005">'.$item['amount'].'/10</a></td>';
			$html .= '<td class="t">'.self::formatMilliseconds($item['vote_date']).'</td></tr>';
		   }
	   }
	   else
	   {
		
	   }
       $html .= '</tbody></table>';
       echo $html;
	}
    function formatMilliseconds1($milliseconds) {
    $seconds = floor($milliseconds / 1000);
    $minutes = floor($seconds / 60);
    $hours = floor($minutes / 60);
    $milliseconds = ($milliseconds % 1000) / 10;
    $seconds = $seconds % 60;
    $minutes = $minutes % 60;

    $format = '%uh%02u\'%02u"%02u';
    $time = sprintf($format, $hours, $minutes, $seconds, $milliseconds);
    return rtrim($time, '0');
}
    public function formatMilliseconds($milliseconds) {
        $seconds = floor($milliseconds / 1000);
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $milliseconds = ($milliseconds % 1000) / 10;
        $seconds = $seconds % 60;
        $minutes = $minutes % 60;
    
        $format = '%2us%2u';
        $time = sprintf($format, $seconds, $milliseconds);
        return rtrim($time, '0');
	}
}
