<?php

/**
 * Model name: GiaiTri_Model_Block
 * @author LamTX
 */
class Thethao_Model_Block extends Thethao_Model
{

    protected $_article;
    protected $_block;

    public function __construct()
    {
        $this->_article = Fpt_Data_News_Article::getInstance();
        $this->_block = Fpt_Data_News_Block::getInstance();
    }

    /**
     * Function get list article ids by rule 1
     * @return array(   [total] => 10
      [data] => Array (1920903,1920892)
      [info] => Array
      (
      [id] => 1002566
      [name] => Video
      [link] => /video
      [share_url] => /video
      [child] => Array()
      );
      }
     */
    public function getListArticleIdsByRule1($arrParams)
    {
        //news instance
        $objNews = Thethao_Model_News::getInstance();

        //category instance
        $objCategory = Thethao_Model_Category::getInstance();

        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();

        //get cate info
        $arrCate = $objCategory->getInfoCateByBlock($arrParams);

        //get limit data for block
        $intLimit = $arrParams['limit'];
        if ($arrParams['exclude'])
        {
            //match limit add to exclude
            $arrParams['limit'] += $objBlock->getTotalExclude();
            //get data with rule 1
            $arrReturn = $objNews->getListArticleIdsByRule1($arrParams);
            if (!empty($arrReturn['data']))
            {
                //get data exclude
                $arrExclude = $objBlock->getExclude();
                $arrReturn['data'] = array_diff($arrReturn['data'], $arrExclude);
                $arrReturn['data'] = array_slice($arrReturn['data'], 0, $intLimit);
            }
        }
        else
        {
            $arrReturn = $objNews->getListArticleIdsByRule1($arrParams);
        }

        if (!empty($arrReturn['data']))
        {
            //check bai thuong mai, chi so sanh o trang 1 ma thoi
            if (!empty($arrParams['zone_id']) && $arrParams['offset'] == 0)
            {
                $this->_block->setZoneId($arrParams['zone_id']);
                $arrReturn['data'] = $this->_block->getNewsByZone($arrParams['zone_id'], $arrReturn['data']);
            }
            $this->_article->setIdBasic($arrReturn['data']);
            if (isset($arrParams['setexclude']) && $arrParams['setexclude'] == 1)
            {
                $objBlock->setExclude($arrReturn['data']);
            }
        }
        $arrReturn['info'] = $arrCate['cate'];

        //return data
        return $arrReturn;
    }

    /**
     * Function get list article ids by rule 2
     * @return array(   [total] => 10
      [data] => Array (1920903,1920892)
      [info] => Array
      (
      [id] => 1002566
      [name] => Video
      [link] => /video
      [share_url] => /video
      [child] => Array()
      );
      }
     */
    public function getListArticleIdsByRule2($arrParams)
    {
        //news instance
        $objNews = Thethao_Model_News::getInstance();

        //category instance
        $objCategory = Thethao_Model_Category::getInstance();

        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();

        //get cate info
        $arrCate = $objCategory->getInfoCateByBlock($arrParams);

        //get limit data for block
        $intLimit = $arrParams['limit'];
        if (isset($arrParams['exclude']) && $arrParams['exclude'])
        {
            //match limit add to exclude
            $arrParams['limit'] += $objBlock->getTotalExclude();
            //get data with rule 1
            $arrReturn = $objNews->getListArticleIdsByRule2($arrParams);
            if (!empty($arrReturn['data']))
            {
                //get data exclude
                $arrExclude = $objBlock->getExclude();
                $arrReturn['data'] = array_diff($arrReturn['data'], $arrExclude);
                $arrReturn['data'] = array_slice($arrReturn['data'], 0, $intLimit);
            }
        }
        else
        {
            $arrReturn = $objNews->getListArticleIdsByRule2($arrParams);
        }

        if (!empty($arrReturn['data']))
        {
            //check bai thuong mai, chi so sanh o trang 1 ma thoi
            if (!empty($arrParams['zone_id']) && $arrParams['offset'] == 0)
            {
                $this->_block->setZoneId($arrParams['zone_id']);
                $arrReturn['data'] = $this->_block->getNewsByZone($arrParams['zone_id'], $arrReturn['data']);
            }
            $this->_article->setIdBasic($arrReturn['data']);
            if (isset($arrParams['setexclude']) && $arrParams['setexclude'] == 1)
            {
                $objBlock->setExclude($arrReturn['data']);
            }
        }
        $arrReturn['info'] = $arrCate['cate'];

        //return data
        return $arrReturn;
    }

    /**
     * Function get list article ids by rule 3
     */
    public function getListArticleIdsByRule3($arrParams)
    {
        return;
    }

    /**
     * Function get list article ids by rule 4
     * @return array(   [data] => Array (1920903,1920892)
      [info] => Array
      (
      [id] => 1002566
      [name] => Video
      [link] => /video
      [share_url] => /video
      [child] => Array()
      );
      }
     */
    public function getListArticleIdsByRule4($arrParams)
    {
        //news instance
        $objNews = Thethao_Model_News::getInstance();

        //category instance
        $objCategory = Thethao_Model_Category::getInstance();

        //get cate info
        $arrCate = $objCategory->getInfoCateByBlock($arrParams);

        //get data with rule 4
        $arrReturn['data'] = $objNews->getListArticleIdsByRule4($arrParams);
        if (!empty($arrReturn['data']))
        {
            $arrReturn['data'] = array_unique($arrReturn['data']);
            $this->_article->setIdBasic($arrReturn['data']);
        }
        $arrReturn['info'] = $arrCate['cate'];

        //return data
        return $arrReturn;
    }

    /**
     * function get table football for block left
     * @return array()
     */
    public function getTableFootball($arrParams)
    {
        $arrReturn = array('data' => array());

        //if empty then return
        if (empty($arrParams['leagueId']) && empty($arrParams['seasonId']))
        {
            return $arrReturn;
        }

        // Get models' instance
        $modelFootball = Thethao_Model_Football::getInstance();

        // Get league detail with list ids
        $arrLeagueId = explode(',', $arrParams['leagueId']);
        // Get list league with array league id
        $arrLeague = $modelFootball->getListLeagueByIDs($arrLeagueId);

        // Get seasion detail with list ids
        $arrSeasionId = explode(',', $arrParams['seasonId']);
        $arrSeason = $modelFootball->getListSeasonByIDs($arrSeasionId);



        $arrData = array();
        foreach ($arrLeagueId as $index => $leagueID)
        {
            $arrData[$leagueID] = array(
                'tableRanking' => $modelFootball->getListTableRanking($arrSeason[$index]['SeasonID'], $leagueID),
                'seasonID' => $arrSeason[$index]['SeasonID'],
                'leagueID' => $leagueID,
                'leagueName' => $arrLeague[$index]['Name'],
                'leagueNameSeo' => $arrLeague[$index]['NameSeo'],
                'seasonName' => $arrSeason[$index]['NameSeason'],
                'seasonNameSeo' => $arrSeason[$index]['NameSeo']
            );
        }
        $arrReturn['data'] = $arrData;

        return $arrReturn;
    }

    /**
     *
     * @param type $arrPrams
     * @author HungNT1
     */
    public function getFixtureFootball($arrPrams)
    {
        // Init return data
        $arrReturn = array('data' => array());

        $arrSeasonID = explode(',', $arrPrams['seasonId']);
        $arrLeagueID = explode(',', $arrPrams['leagueId']);

        // Determine unix time today (ignore hour, minute, second)
        $unixTimeToday = intval(strtotime(date('Y/m/d')));
        // Determine day of week
        //$dayOfWeek = date('N');
        // Determine unix time of monday
        //$unixTimeModay = $unixTimeToday - ($dayOfWeek - 1) * 86400;
        $unixTimeToday > intval(strtotime('2014/12/17')) && $unixTimeToday = intval(strtotime('2014/12/17'));
        $unixTimeModay = $unixTimeToday - 1 * 86400;

        // Determine unix time of sunday
        //$unixTimeSunday = $unixTimeToday + ( 7 - $dayOfWeek + 1) * 86400 - 1;
        $unixTimeSunday = intval(strtotime('2014/12/20')) + 86400 - 1;

        // Get model Football
        $footballModel = Thethao_Model_Football::getInstance();
        $matchModel = Thethao_Model_Match::getInstance();
        // Get detail league
        $arrLeague = $footballModel->getListLeagueByIDs($arrLeagueID);

        $arrData = array();
        foreach ($arrLeagueID as $k => $leagueId)
        {
            $arrData[$leagueId] = array(
                'fixtureData' => $matchModel->getMatchInWeek($arrSeasonID[$k], $leagueId, $unixTimeModay, $unixTimeSunday),
                'leagueId' => $leagueId,
                'leagueName' => $arrLeague[$k]['Name'],
                'leagueNameSeo' => $arrLeague[$k]['NameSeo'],
            );
        }

        $arrReturn['data'] = $arrData;

        return $arrReturn;
    }

    /**
     * Get block table tennis player
     * @param array $arrParam
     * @return array()
     * @author HungNT1
     */
    public function getTableTennis($arrParams)
    {
        //init data return
        $arrReturn = array('data' => array());

        $year = date('Y');
        $limit = $arrParams['limit'];
        $tennisModel = Thethao_Model_Tennis::getInstance();

        $modelObject = Fpt_Data_Materials_Object::getInstance();
        $modelTennis = $modelObject->getTennis();

        //get ranking ATP
        $arrTennisRankingATP = $tennisModel->getTableTennis(1, $year);
        $arrTennisRankingATP = array_slice($arrTennisRankingATP, 0, $limit, true);

        //get ranking wtp
        $arrTennisRankingWTP = $tennisModel->getTableTennis(0, $year);
        $arrTennisRankingWTP = array_slice($arrTennisRankingWTP, 0, $limit, true);

        //array_merge
        $arrIds = array_merge(array_keys($arrTennisRankingATP), array_keys($arrTennisRankingWTP));

        if (!empty($arrIds))
        {
            //get and set id
            $modelTennis->setId($arrIds);
        }

        $arrReturn['data'] = array(
            '1' => $arrTennisRankingATP,
            '2' => $arrTennisRankingWTP
        );

        return $arrReturn;
    }

    /**
     * Get block interview in the verticle The Thao
     * @param type $arrParam
     * @return type
     */
    public function getBlockInterView($arrParam)
    {
        $arrReturn = array();
        $objCategory = Thethao_Model_Category::getInstance();
        //get Info Cate
        $arrCate = $objCategory->getInfoCateByBlock($arrParam);
        //Init model
        $modelMews = Thethao_Model_News::getInstance();
        //Get
        $arrReturn = $modelMews->getListArticleIdsByRule2($arrParam);

        if (!empty($arrReturn['data']))
        {
            $this->_article->setIdBasic($arrReturn['data']);
            //Init Obj Interview
            $objInterview = Fpt_Data_News_Interview::getInstance();
            //Get Image, Time interview
            $arrParam = array('article_id' => $arrReturn['data'][0]);

            $arrReturn['info']['interview'] = $objInterview->getInterviewInfo($arrParam);
            $arrReturn['info']['cate'] = $arrCate['cate'];
        }

        return $arrReturn;
    }

    /**
     * @author   : PhongTX - 29/01/2013
     * call job update caching FE
     * @param : string $strPageCode
     * @name : updateCache
     * @copyright   : FPT Online
     * @todo    : updateCache
     */
    public function updateBlock($strPageCode)
    {

        //init return
        $arrResponse = array();
        $objBlockModelNosql = new Thethao_Business_Block_Adapter_Redis();
        //rewrite redis
        $objBlockModelSql = new Thethao_Business_Block_Adapter_Mysql();

        $arrDevice = array(1, 2, 4);
        foreach ($arrDevice as $device)
        {
            //layout mobile
            $arrLayout = $objBlockModelSql->getBlockByPage($strPageCode, $device, 1);
            $arrResponse[] = $objBlockModelNosql->setBlockByPage($strPageCode, $device, $arrLayout);
        }

        //Return data
        return $arrResponse;
    }

    /**
     * Get news by topic_id: order by  publish_time desc
     * @param array $arrParam array(topic_id, offset, limit)
     * @return array
     * @author HungNT1
     * @todo Topic - Thethao
     */
    public function getArticleByTopic($arrParam)
    {

        //init param return
        $arrReturn = array('data' => array(), 'total' => 0);
        //New object
        $objTopic = new Fpt_Data_News_Topic();

        if ($arrParam['exclude'])
        {
            //get Instance Block
            $objBlock = Thethao_Plugin_Block::getInstance();
            //get limit data for block
            $intLimit = $arrParam['limit'];
            //match limit add to exclude
            $arrParam['limit'] += $objBlock->getTotalExclude();
            //get Data main
            $arrReturn = $objTopic->getArticleByTopicId($arrParam);
            //calc limit
            $arrReturn['data'] = array_slice(array_diff($arrReturn['data'], $objBlock->getExclude()), 0, $intLimit);
        }
        else
        {
            //get Data main
            $arrReturn = $objTopic->getArticleByTopicId($arrParam);
        }
        $arrTopicInfo = $objTopic->getDetailTopic($arrParam['topic_id']);

        $arrTopicInfo['name'] = $arrTopicInfo['title'];
        $arrReturn['info'] = $arrTopicInfo;
        $arrReturn['info']['link'] = $arrTopicInfo['share_url'];
        if (!empty($arrReturn['data']))
        {
            //set id
            $this->_article->setIdBasic($arrReturn['data']);
        }

        //return
        return $arrReturn;
    }

    /**
     * Function get data from table keybox by key_box_id
     * @param array $params
     * @return array data
     */
    public function getKeyBox($params)
    {
        try
        {
            // Build key cache
            $keyCache = Thethao_Global::makeCacheKey($params['key_id']);
            if (!$keyCache)
            {
                $keyCache = $params['key_id'];
            }

            // Get instance memcache
            $memcacheInstance = Thethao_Global::getCache();
            // Read hot match in memcache
            $arrReturn = $memcacheInstance->read($keyCache);

            //check cache
            if ($arrReturn === FALSE)
            {
                //get app config
                $config = Thethao_Global::getApplicationIni();
                //get mysql instance
                $mysqlInstance = $this->factory('Block', $config['database']['default']['adapter']);
                // Get data in DB
                $arrReturn = $mysqlInstance->getKeyBox('"' . $params['key_id'] . '"');
                //Decode Json for write memcache
                $arrReturn = Zend_Json::decode($arrReturn[$params['key_id']]);
                if (!empty($arrReturn))
                {
                    // Store into cache
                    $memcacheInstance->write($keyCache, $arrReturn);
                    Thethao_Global::writeMemcache($keyCache, $arrReturn);
                }
                else
                {
                    // Store into cache
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
            else if ($arrReturn == -1)
            {
                $arrReturn = array();
            }
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }

    /**
     * Get list team of the football
     * @param array $arrParam - array of league id and season id
     */
    public function getTeamFootball($arrParam)
    {
        //Get params
        $strLeagueID = $arrParam['leagueId'] ? $arrParam['leagueId'] : '1,2,3,4';
        $strSeasonID = $arrParam['seasonId'] ? $arrParam['seasonId'] : '1,2,3,4';

        // Get models' instance
        $modelFootball = Thethao_Model_Football::getInstance();
        $modelObject = Fpt_Data_Materials_Object::getInstance();
        $modelTeam = $modelObject->getTeam();
        //convert string to array
        $arrLeagueID = explode(',', $strLeagueID);
        $arrSeasonID = explode(',', $strSeasonID);
        // Get list league
        $arrLeague = $modelFootball->getListLeagueByIDs($arrLeagueID);

        // Get list season
        $arrSeason = $modelFootball->getListSeasonByIDs($arrSeasonID);

        //get table ranking
        $arrData = array();
        foreach ($arrLeagueID as $index => $leagueID)
        {
            //get list team
            $rank = $modelFootball->getListTableRanking($arrSeason[$index]['SeasonID'], $leagueID);

            //check and set id object
            if (!empty($rank))
            {
                $arrTeamIds = array_keys($rank);
                $modelTeam->setId($arrTeamIds);
            }
            $arrData[$leagueID] = $arrTeamIds;
        }
        //assign to view
        return array(
            'arrData' => $arrData,
            'arrLeague' => $arrLeague,
        );
    }

    /**
     * Function get block without data
     * getBlockNoData
     */
    public function getBlockNoData($param = NULL)
    {
        return array('data' => $param);
    }

    /**
     * Get detail table ranking by league and season
     * @param array $arrParam - array of league id and season id
     */
    public function getListTableRanking()
    {
        $leagueId = LEAGUE_ID;
        $seasonId = SEASON_ID;
        $modelObject = Fpt_Data_Materials_Object::getInstance();
        // Get models instance
        $modelFootball = Thethao_Model_Football::getInstance();
        $tableRanking = $modelFootball->getListTableRanking($seasonId, $leagueId);
        if (!empty($tableRanking))
        {
            //set id object
            $modelObject->getTeam()->setId(array_keys($tableRanking));
        }
        //assign to view
        return array(
            'tableRanking' => $tableRanking
        );
    }

    /**
     * Function get list match id by league, get list team by season id & league id, get list player
     * @param array
     */
    public function boxPredicted($params)
    {
        try
        {
            $matchModel = Thethao_Model_Match::getInstance();
            $modelObject = Fpt_Data_Materials_Object::getInstance();
            $beginHappenDateTime = (time() - (100*60)); //lay truoc do 100p
            $endHappenDateTime = 1405382400; // ngay 15-7-2014
            $sort = false;
            // Get list match id by league id with time happened from current -> 15/7
            $arrMatchIDs = $matchModel->getMatchIDsByLeague(LEAGUE_ID, $beginHappenDateTime, $endHappenDateTime, $sort);
            // Count future
            $count = count($arrMatchIDs);
            if (is_array($arrMatchIDs) && $count > $params['limit'])
            {
                $arrMatchIDs = array_slice($arrMatchIDs, 0, $params['limit'], true);
            }
            if (!empty($arrMatchIDs))
            {
                $modelObject->getMatch()->setId(array_keys($arrMatchIDs));
            }
            //get list team id
            $arrTeamId = $this->getListTableRanking();
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }

        //return array result
        return array(
            'arrMatchIDs' => $arrMatchIDs,
            'arrTeamId' => $arrTeamId
        );
    }

    /**
     * function get List Top Players for block left
     * @return array()
     */
    public function getListTopPlayers($arrParams)
    {
        $arrReturn = array('data' => array());

        //if empty then return
        if (empty($arrParams['season_id']))
        {
            return $arrReturn;
        }
        // Get models' instance
        $modelPlayer = Thethao_Model_Player::getInstance();
        //Init arrData
        $arrData = array();
        //Get data
        $arrData = $modelPlayer->getListTopPlayers($arrParams['season_id']);
        if (count($arrData) > $arrParams['limit'])
        {
            $arrReturn['data'] = array_slice($arrData, 0, $arrParams['limit']);
        }
        else
        {
            $arrReturn['data'] = $arrData;
        }
        return $arrReturn;
    }

}

?>
