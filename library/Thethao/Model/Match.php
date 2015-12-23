<?php

/**
 * @name        :   Thethao_Model_Match
 * @author      :   HungNT1
 * @copyright   :   Fpt Online
 */
class Thethao_Model_Match extends Thethao_Model
{

    static $_instance = null;
    private $_match_in_week;
    private $_team_match_commingsoon;
    private $_player_team_match_list;
    private $_match_team_season_league;
    private $_match_time_list;
    private $_match_statistic;
    private $_match_extend;
    private $_report_type_all;
    private $_diem_tin;
    private $_tuong_thuat;
    private $_predict_match_thethao_circle;
    private $_predict_match_thethao_top;
    private $_match_betrate_detail;

    /**
     * return Thethao_Model_Match
     * @author LamTX
     */
    public function __construct()
    {
        $key = Thethao_Global::getConfig('caching');

        $this->_team_match_commingsoon = $key['match_commingsoon'];
        $this->_match_in_week = $key['match_week'];
        $this->_match_team_season_league = $key['match_season_league'];
        $this->_match_statistic = $key['match_statistic'];
        $this->_report_type_all = $key['match_report_type_all'];
        $this->_diem_tin = $key['match_diem_tin'];
        $this->_tuong_thuat = $key['match_tuong_thuat'];
        $this->_predict_match_thethao_circle = $key['match_predict_thethao_circle'];
        $this->_predict_match_thethao_top = $key['match_predict_thethao_top'];
        $this->_match_betrate_detail = $key['match_betrate_detail'];
        $this->_match_time_list = $key['match_time_list'];
        $this->_player_team_match_list = $key['player_team_match_list'];
        $this->_match_extend = $key['match_extend'];
        unset($key);
    }

    /**
     * Get singletom instance
     * @return Thethao_Model_Match
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
     * Build key cache for each item in array
     * @param string $item
     * @param int $key
     * @param string $prefix
     */
    static public function buildKeyCache(&$item, $key, $prefix)
    {
        $item = $prefix . $item;
    }

    /**
     * Clear prefix key cache for each item in array
     * @param string $item
     * @param int $key
     * @param string $prefix
     */
    static public function clearPrefixKeyCache(&$item, $key, $prefix)
    {
        $item = str_replace($prefix, '', $item);
    }

    /**
     * Get list matches happen in week
     * @param int $seasonId
     * @param int $leagueId
     * @param int $beginDate
     * @param int $endDate
     * @return array|boolean
     * @author QuangTM
     */
    public function getMatchInWeek($seasonId, $leagueId, $beginDate, $endDate)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_match_in_week, array($leagueId, $seasonId, $beginDate, $endDate));

        // Read cache
        $listMatches = $memcacheInstance->read($keyCache);

        // If miss cache
        if ($listMatches === FALSE || $listMatches['time'] < $beginDate)
        {
            try
            {
                $listMatches = array();

                $config = Thethao_Global::getApplicationIni();
                // Get db teamseason instance
                $matchObj = $this->factory('Match', $config['database']['default']['adapter']);

                // Get data from db
                $listMatches['data'] = $matchObj->getMatchInWeek($seasonId, $leagueId, $beginDate, $endDate);
                $listMatches['time'] = $beginDate;

                // Set memcache
                $memcacheInstance->write($keyCache, $listMatches);
                Thethao_Global::writeMemcache($keyCache, $listMatches);
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }
        // Return data
        return $listMatches['data'];
    }

    /**
     * Get list match id by team, season, league
     * @param int $teamID
     * @param int $leagueID
     * @param int $seasonID
     * @return array|boolean
     * @author QuangTM
     */
    public function getMatchIDsByTeamLeagueSeason($teamID, $leagueID, $seasonID)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_match_team_season_league, array($teamID, $leagueID, $seasonID));
        // Read cache
        $listMatcheIDs = $memcacheInstance->read($keyCache);

        // If miss cache
        if ($listMatcheIDs === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();
                // Get db teamseason instance
                $matchObj = $this->factory('Match', $config['database']['default']['adapter']);
                // Get data from db
                $listMatcheIDs = $matchObj->getMatchIDsByTeamLeagueSeason($teamID, $leagueID, $seasonID);

                // Write to cache
                if (count($listMatcheIDs))
                {
                    $memcacheInstance->write($keyCache, $listMatcheIDs);
                    Thethao_Global::writeMemcache($keyCache, $listMatcheIDs);
                }
                else
                {
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }
        else if ($listMatcheIDs == -1)
            return array();
        return $listMatcheIDs;
    }

    /**
     * Get list matches by happen time
     * @param int $unixTime
     * @return array
     * @author QuangTM
     */
    public function getMatchIDsHappenTime($unixTime, $leagueID = NULL, $gmt = 0)
    {
        $memcacheInstance = Thethao_Global::getCache();
        // Set key cache with in day
        $leagueID = intval($leagueID);
        $keyCache = vsprintf($this->_match_time_list, array($unixTime, $leagueID, $gmt));

        // Read cache
        $listMatches = $memcacheInstance->read($keyCache);
        // If miss cache
        if ($listMatches === FALSE)
        {
            try
            {
                $begin = $unixTime;
                $end = $unixTime + 86399; // 23:59:59

                $config = Thethao_Global::getApplicationIni();
                // Get db teamseason instance
                $matchObj = $this->factory('Match', $config['database']['default']['adapter']);
                $leagueID = $leagueID == 0 ? NULL : $leagueID;
                // Get data from db
                $listMatches = $matchObj->getListMatchesByHappenTime($begin, $end, $leagueID, $gmt);
                // Write to cache
                if (count($listMatches))
                {
                    $memcacheInstance->write($keyCache, $listMatches);
                    Thethao_Global::writeMemcache($keyCache, $listMatches);
                }
                else
                {
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }
        else if ($listMatches == -1)
            return array();
        //$listMatches = $memcacheInstance->read($keyCache);
        //var_dump($keyCache, $listMatches);die;
        return $listMatches;
    }

    /**
     * Get match statistic by team and match
     * @param int $teamID
     * @param int $matchID
     * @return array|boolean
     * @author QuangTM
     */
    public function getMatchStatistic($teamID, $matchID)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_match_statistic, array($matchID, $teamID));

        // Read cache
        $arrMatchStatistic = $memcacheInstance->read($keyCache);

        // Miss cache
        if ($arrMatchStatistic === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();
                // Get db teamseason instance
                $matchStatisticObj = $this->factory('Match', $config['database']['default']['adapter']);

                // Get data from db
                $arrMatchStatistic = $matchStatisticObj->getMatchStatistic($teamID, $matchID);

                // Write to cache
                if (count($arrMatchStatistic))
                {
                    $memcacheInstance->write($keyCache, $arrMatchStatistic);
                    Thethao_Global::writeMemcache($keyCache, $arrMatchStatistic);
                }
                else
                {
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }
        else if ($arrMatchStatistic == -1)
        {
            return array();
        }

        return $arrMatchStatistic;
    }

    public function getAllReportType()
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = $this->_report_type_all;

        // Read cache
        $arrReportType = $memcacheInstance->read($keyCache);
        // Miss cache
        if ($arrReportType === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();
                // Get db teamseason instance
                $reportTypeObj = $this->factory('Match', $config['database']['default']['adapter']);

                // Get data from db
                $arrReportType = $reportTypeObj->getAllReportType();

                // Write to cache
                if (count($arrReportType))
                {
                    $memcacheInstance->write($keyCache, $arrReportType);
                    Thethao_Global::writeMemcache($keyCache, $arrReportType);
                }
                else
                {
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }
        else if ($arrReportType == -1)
        {
            return array();
        }

        return $arrReportType;
    }

    /**
     * MatchRelate: Get diem tin
     * @param int $matchID
     * @return array
     * @author QuangTM
     */
    public function getDiemTin($matchID)
    {
        //set result
        $diemTin = NULL;
        try
        {
            // Prepare for reading cache
            $memcacheInstance = Thethao_Global::getCache();
            $keyCache = vsprintf($this->_diem_tin, array($matchID));

            // Read from cache
            $diemTin = $memcacheInstance->read($keyCache);

            // Miss cache
            if ($diemTin === FALSE)
            {
                // Get config
                $config = Thethao_Global::getApplicationIni();

                // Get Mysql object access table match_relate
                $matchRelateMysqlObj = $this->factory('Match', $config['database']['default']['adapter']);
                // Get data (missed)
                $diemTin = $matchRelateMysqlObj->getDiemTin($matchID);

                // Write to cache
                if ($diemTin !== FALSE)
                {
                    $memcacheInstance->write($keyCache, $diemTin);
                    Thethao_Global::writeMemcache($keyCache, $diemTin);
                }
                else
                {
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
        // Return result
        return $diemTin == -1 ? NULL : $diemTin;
    }

    /**
     * MatchRelate - Get tuong thuat
     * @param int $matchID
     * @return array
     * @author QuangTM
     */
    public function getTuongThuat($matchID)
    {
        //set result
        $tuongThuat = NULL;
        try
        {
            // Prepare for reading cache
            $memcacheInstance = Thethao_Global::getCache();
            $keyCache = vsprintf($this->_tuong_thuat, array($matchID));

            // Read from cache
            $tuongThuat = $memcacheInstance->read($keyCache);
            // Miss cache
            if ($tuongThuat === FALSE)
            {
                // Get config
                $config = Thethao_Global::getApplicationIni();

                // Get Mysql object access table match_relate
                $matchRelateMysqlObj = $this->factory('Match', $config['database']['default']['adapter']);

                // Get data (missed)
                $tuongThuat = $matchRelateMysqlObj->getTuongThuat($matchID);

                // Write to cache
                if ($tuongThuat !== FALSE && !empty($tuongThuat))
                {
                    $memcacheInstance->write($keyCache, $tuongThuat);
                    Thethao_Global::writeMemcache($keyCache, $tuongThuat);
                }
                else
                {
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
        // Return result
        return $tuongThuat == -1 ? NULL : $tuongThuat;
    }

    /**
     * Match: Insert getReport MatchPredict
     * @param int $intMatchID
     * @return array
     * @author TrongNV
     */
    public function getReportMatchPredict($intMatchID)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_predict_match_thethao_circle, array($intMatchID));

        //Read cache
        $arrReportMatchPredict = $memcacheInstance->read($keyCache);
        //Miss cache
        if ($arrReportMatchPredict === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();
                // Get db match instance
                $matchObj = $this->factory('Match', $config['database']['default']['adapter']);

                // Get match from db
                $arrReportMatchPredict = $matchObj->getReportMatchPredict($intMatchID);
                // Write to cache
                if (!empty($arrReportMatchPredict))
                {
                    $memcacheInstance->write($keyCache, $arrReportMatchPredict);
                    Thethao_Global::writeMemcache($keyCache, $arrReportMatchPredict);
                }
                else
                {
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }
        if ($arrReportMatchPredict == -1)
            return array();
        return $arrReportMatchPredict;
    }

    /**
     * Match: Insert getTop MatchPredict
     * @param int $intMatchID, int $limit
     * @return array
     * @author TrongNV
     */
    public function getTopMatchPredict($intMatchID)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_predict_match_thethao_top, array($intMatchID));

        //Read cache
        $arrTopPredictCache = $memcacheInstance->read($keyCache);

        //Miss cache
        if ($arrTopPredictCache === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();
                // Get db match instance
                $matchObj = $this->factory('Match', $config['database']['default']['adapter']);

                // Get match from db
                $arrTopMatchPredict = $matchObj->getTopMatchPredict($intMatchID);

                // Handle format cache
                $arrTopPredictCache = array();

                if (!empty($arrTopMatchPredict))
                {
                    foreach ($arrTopMatchPredict as $row)
                    {
                        $arrTopPredictCache[$row['goal_team1'] . ' - ' . $row['goal_team2']] = $row['num_user'];
                    }
                }

                // Write to cache
                if (!empty($arrTopPredictCache))
                {
                    $memcacheInstance->write($keyCache, $arrTopPredictCache);
                    Thethao_Global::writeMemcache($keyCache, $arrTopPredictCache);
                }
                else
                {
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }
        if ($arrTopPredictCache == -1)
            return array();
        return $arrTopPredictCache;
    }

    /**
     * MatchBetrate: Get ty le cuoc
     * @param int $leagueID, int $seasonID
     * @return array
     * @author Dungdv2
     */
    public function getMatchBetrate(array $arrMatchIDs)
    {
        //set result
        $result = array();
        try
        {
            $arrMissMatchIDs = array();
            $key = $this->_match_betrate_detail;
            $memcacheInstance = Thethao_Global::getCache();
            // Loop for read from cache
            foreach ($arrMatchIDs as $matchID)
            {
                $keyCache = $key . $matchID;
                // Read from cache
                $matchBetrate = $memcacheInstance->read($keyCache);
                // Miss cache
                if ($matchBetrate === FALSE)
                    array_push($arrMissMatchIDs, $matchID);
                else
                    $result[$matchID] = $matchBetrate;
            }
            // If miss some items
            if (count($arrMissMatchIDs))
            {
                // Get config
                $config = Thethao_Global::getApplicationIni();

                // Get Mysql object access table match_betrate
                $matchBetrateMysqlObj = $this->factory('Match', $config['database']['default']['adapter']);

                // Get multi match betrate (missed)
                $arrMatchBetrate = $matchBetrateMysqlObj->getMatchBetrateByIDs($arrMissMatchIDs);

                if (!empty($arrMatchBetrate))
                {
                    // Loop to write into cache
                    foreach ($arrMatchBetrate as $matchID => $matchBetrate)
                    {
                        // make key cache
                        $keyCache = $key . $matchID;
                        // Write to memcache
                        $memcacheInstance->write($keyCache, $matchBetrate);
                        Thethao_Global::writeMemcache($keyCache, $matchBetrate);
                        // Merge into result
                        $result[$matchID] = $matchBetrate;
                    }
                }
                else
                {
                    // Loop to write into cache
                    foreach ($arrMissMatchIDs as $matchID)
                    {
                        // make key cache
                        $keyCache = $key . $matchID;
                        // Write to memcache
                        $memcacheInstance->write($keyCache, -1);
                        Thethao_Global::writeMemcache($keyCache, -1);
                        // Merge into result
                        $result[$matchID] = array();
                    }
                }
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }

        return $result;
    }

    /**
     * Get Extend Match detail
     *
     * @param array $arrMatchId
     */
    public function getDetailMatchExtend($arrMatchId)
    {
        // If input player id is not array or not item in it
        if (!is_array($arrMatchId) || !count($arrMatchId))
        {
            return FALSE;
        }

        // Create key cache about tennis player
        $keyMatchDetail = vsprintf($this->_match_extend, '');

        // Create array key cache
        //array_walk($arrMatchId, 'self::buildKeyCache', $keyMatchDetail);

        // Get memcache instance
        $memcacheInstance = Thethao_Global::getCache();

        // Default result => that's combined data
        $result = array();
        $arrMissId = array();
        // Read data from cache
        if (!empty($arrMatchId))
        {
            foreach ($arrMatchId as $id)
            {
                $dataFromCache = $memcacheInstance->read($keyMatchDetail . $id);
                if ($dataFromCache === FALSE)
                {
                    if (!empty($id))
                    {
                        array_push($arrMissId, $id);
                    }
                }
                else {
                    $result[$id] = $dataFromCache;
                }
            }
        }
        if (!empty($arrMissId))
        {
            // Get data from database
            $result = $this->_getDetailMatchFromDb($arrMissId);
            if (!empty($result))
            {
                // Loop throught out data from DB and write cache
                foreach ($result as $id => $info)
                {
                    $memcacheInstance->write($keyMatchDetail . $id, $info);
                    Thethao_Global::writeMemcache($keyMatchDetail . $id, $info);
                }
            }
        }

        return $result;
    }

    /**
     * Get information statistic about match detail.<br />
     * Input parameter should be int, array or string separate by comma.<br />
     * This function only get data from database and NOT write cache (any cache).
     * @param int|array|string $playerID
     * @return array
     * @author HungNT1
     */
    protected function _getDetailMatchFromDb($matchId)
    {
        // If input player id is number integer
        if (is_int($matchId))
        {
            $matchId = array($matchId);
        }

        // If input player id is a string separate by comma
        if (is_string($matchId))
        {
            $matchId = explode(',', $matchId);
        }

        // If input player id is not array or not item in it
        if (!is_array($matchId) || !count($matchId))
        {
            return FALSE;
        }

        $config = Thethao_Global::getApplicationIni();

        // Get Mysql object access table match_betrate
        $matchMysqlObj = $this->factory('Match', $config['database']['default']['adapter']);

        // Get multi match betrate (missed)
        $arrMatchExtend = $matchMysqlObj->getMatchExtendByIDs($matchId);

        // Return result
        return $arrMatchExtend;
    }

    /**
     * Insert predict match
     * @param int $params
     * @return bool
     * @author TrongNV
     */
    public function insertMatchPredict($params)
    {
        $result = array();
        $config = Thethao_Global::getApplicationIni();

        // Get db match instance
        $matchObj = $this->factory('Match', $config['database']['default']['adapter']);

        // Get data from db
        $result['result'] = $matchObj->insertMatchPredict($params);

        if ($result['result'])
        {
            $memcacheInstance = Thethao_Global::getCache();
            $keyCache = vsprintf($this->_predict_match_thethao_circle, array($params['match_id']));
            $keyCacheTop = vsprintf($this->_predict_match_thethao_top, array($params['match_id']));
            //delete cache
            Thethao_Global::deleteMemcache(array($keyCache, $keyCacheTop));

            $arrReportMatchResult = $this->getReportMatchPredict($params['match_id']);

            $arrTopPredict = $this->getTopMatchPredict($params['match_id']);

            // Handle cache report predict
            if (!empty($arrReportMatchResult))
            {
                if ($params['goal_team1'] > $params['goal_team2'])
                {
                    $arrReportMatchResult[0] ++;
                }
                elseif ($params['goal_team1'] < $params['goal_team2'])
                {
                    $arrReportMatchResult[1] ++;
                }
                else
                {
                    $arrReportMatchResult[2] ++;
                }
            }

            // Handle cache top predict
            $keyTop = $params['goal_team1'] . ' - ' . $params['goal_team2'];

            if (isset($arrTopPredict[$keyTop]))
                $arrTopPredict[$keyTop] ++;
            else
                $arrTopPredict[$keyTop] = 1;

            arsort($arrTopPredict);
            // Write to cache
            if (!empty($arrReportMatchResult))
            {
                $memcacheInstance->write($keyCache, $arrReportMatchResult);
                Thethao_Global::writeMemcache($keyCache, $arrReportMatchResult);
            }
            if (!empty($arrTopPredict))
            {
                $memcacheInstance->write($keyCacheTop, $arrTopPredict);
                Thethao_Global::writeMemcache($keyCacheTop, $arrTopPredict);
            }
        }
        return $result;
    }

    /**
     * push job insert match predict
     * @param array $params
     * @return boolean
     * @author PhuongTN
     */
    public function pushJobInsertMatchPredict($params)
    {
        try
        {
            $result = array('result' => false);

            $config = Thethao_Global::getApplicationIni();

            //Create job client
            $jobClient = Thethao_Global::getJobClientInstance('sport');

            //get job func name
            $jobFunc = $config['job']['task']['sport']['match_predict'];

            //default
            $jobParams = array();

            //add job class name
            $jobParams['class'] = 'Job_Thethao_JobBackendCache';

            //reg func delete article
            $jobParams['function'] = 'insertMatchPredict';
            //init array args
            $jobParams['args'] = $params;

            //Register job with normal priority
            $pushJob = $jobClient->doBackgroundTask($jobFunc, $jobParams);
            $pushJob = $jobClient->doBackgroundTask($jobFunc . '_new', $jobParams);
            if ($pushJob != false)
            {
                $result['result'] = true;
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
        return $result;
    }

    /**
     * Function rewrite match cache for football
     */
    public function rewriteMatchCache($params)
    {
        // GMT +7 (viet nam)
        $gmt = 7;
        //int time happen to delete list fixture
        if (!empty($params['time']))
        {
            foreach ($params['time'] as $intTime)
            {
                $keyCache1 = vsprintf($this->_match_time_list, array($intTime, $params['league_id'], $gmt));
                $keyCache2 = vsprintf($this->_match_time_list, array($intTime, 0, $gmt));

                Thethao_Global::deleteMemcache(array($keyCache1, $keyCache2));
                // Rewrite cache for list matches happen in time
                $this->getMatchIDsHappenTime($intTime, $params['league_id'], $gmt);
                $this->getMatchIDsHappenTime($intTime, 0, $gmt);
            }
        }
        // Get parameters
        $leagueID = intval($params['league_id']);
        $seasonID = intval($params['season_id']);

        $modelObject = new Fpt_Data_Materials_Object();

        $arrMatch = array();
        // Execute data match
        if (isset($params['match']) && !empty($params['match']))
        {
            //get app conf
            $config = Thethao_Global::getApplicationIni();
            $matchNosql = $this->factory('Match', $config['database']['nosql']['adapter']);

            foreach ($params['match'] as $id => $match)
            {
                $keyCache1 = vsprintf($this->_match_team_season_league, array($match['team1_id'], $leagueID, $seasonID));
                $keyCache2 = vsprintf($this->_match_team_season_league, array($match['team2_id'], $leagueID, $seasonID));
                Thethao_Global::deleteMemcache(array($keyCache1, $keyCache2));
                $this->getMatchIDsByTeamLeagueSeason($match['team1_id'], $leagueID, $seasonID);
                $this->getMatchIDsByTeamLeagueSeason($match['team2_id'], $leagueID, $seasonID);

                //add match id=>time-happen
                $arrMatch[$id] = $match['time_happen'];
                if (isset($match['result']))
                {
					/*
                    $keyCache1 = vsprintf($this->_player_team_match_list, array($id, $match['team1_id']));
                    $keyCache2 = vsprintf($this->_player_team_match_list, array($id, $match['team2_id']));
                    $keyCache3 = vsprintf($this->_match_statistic, array($id, $match['team1_id']));
                    $keyCache4 = vsprintf($this->_match_statistic, array($id, $match['team2_id']));
                    Thethao_Global::deleteMemcache(array($keyCache1, $keyCache2, $keyCache3, $keyCache4));

                    $modelPlayer = new Thethao_Model_Player();
                    $modelPlayer->getPlayersTeamMatch($id, $match['team1_id']);
                    $modelPlayer->getPlayersTeamMatch($id, $match['team2_id']);
                    $this->getMatchStatistic($match['team1_id'], $id);
                    $this->getMatchStatistic($match['team2_id'], $id);
					*/
                }
                //update detail match
                $modelObject->getMatch()->updateObject($id, true);
            }
        }
        if (!empty($arrMatch))
        {
            $matchNosql->setMatchIDsByLeague($leagueID, $arrMatch);
			/*
            //update extend
            $arrKeyMatchExtend = $arrMatch;
            // Create key cache about tennis player
            $keyMatchDetail = vsprintf($this->_match_extend, '');

            // Create array key cache
            array_walk($arrKeyMatchExtend, 'self::buildKeyCache', $keyMatchDetail);
            // Delete all cache
            $arrKeyCache = array();
            foreach ($arrKeyMatchExtend as $keyCache)
            {
                $arrKeyCache[] = $keyCache;
            }
            Thethao_Global::deleteMemcache($arrKeyCache);

            $chunkMatchIDs = array_chunk($arrMatch, 100);

            foreach ($chunkMatchIDs as $arrMatchId)
            {
                $this->getDetailMatchExtend($arrMatchId);
            }
			*/
        }

    }

    /**
     * Function rewrite match cache for football Live
     */
    public function rewriteMatchLive($params)
    {

        $modelObject = new Fpt_Data_Materials_Object();

        $arrMatch = array();
        // Execute data match
        if (isset($params['match']) && !empty($params['match']))
        {
            $memcacheInstance = Thethao_Global::getCache();
            foreach ($params['match'] as $id => $match)
            {

                //add match id=>time-happen
                $arrMatch[$id] = $match['time_happen'];
                if (!empty($match['player']))
                {
                    $keyCache1 = vsprintf($this->_player_team_match_list, array($id, $match['team1_id']));
                    $keyCache2 = vsprintf($this->_player_team_match_list, array($id, $match['team2_id']));
                    Thethao_Global::deleteMemcache(array($keyCache1, $keyCache2));

                    $arrPlayersTeamHome = $match['player'][$match['team1_id']];
                    $arrPlayersTeamAway = $match['player'][$match['team2_id']];

                    $memcacheInstance->write($keyCache1, $arrPlayersTeamHome);
                    Thethao_Global::writeMemcache($keyCache1, $arrPlayersTeamHome);
                    $memcacheInstance->write($keyCache2, $arrPlayersTeamAway);
                    Thethao_Global::writeMemcache($keyCache2, $arrPlayersTeamAway);
                }
                if (isset($match['Event']) && !empty($match['Event']))
                {
                    $keyMatchDetail = vsprintf($this->_match_extend, '');
                    //set extend match detail
                    $keyMatchDetail = $keyMatchDetail . $id;

                    Thethao_Global::deleteMemcache(array($keyMatchDetail));

                    //write cache memcache
                    $memcacheInstance->write($keyMatchDetail, $match['Event']);
                    Thethao_Global::writeMemcache($keyMatchDetail, $match['Event']);
                }
                //update detail match
                if (isset($match['Result']) && ($match['Result'])){
                    $modelObject->getMatch()->updateObject($id, true);
                }
            }
        }
    }

    /**
     *
     * @param type $params
     */
    public function rewriteMatchBetrate($params)
    {
        // Get param
        $arrMatchIDs = $params['arrMatchIDs'];

        // Verify param
        if (is_array($arrMatchIDs) && count($arrMatchIDs))
        {
            // Get key team
            $key = $this->_match_betrate_detail;
            $arrKeyCache = array();
            foreach ($arrMatchIDs as $matchID)
            {
                $arrKeyCache[] = $key . $matchID;
            }
            Thethao_Global::deleteMemcache($arrKeyCache);

            $this->getMatchBetrate($arrMatchIDs);
        }
    }

    /**
     * function delete match
     * @param type $params
     */
    public function deleteMatch($params)
    {
        // Get params
        $matchDetail = $params['MatchDetail'];

        // Verify parameter
        if (is_array($matchDetail))
        {
            $modelObject = Fpt_Data_Materials_Object::getInstance();
            $modelObject->getMatch()->updateObject($matchDetail['match_id']);

            $arrGmtTime = array(0, 7);

            $dateTimeHappenTemp = strtotime(date('d-m-Y', ($matchDetail['datetime_happen'] + 25200)));
            $arrKeyCache = array();
            foreach ($arrGmtTime as $gmt)
            {
                $arrKeyCache[] = vsprintf($this->_match_time_list, array($dateTimeHappenTemp, $matchDetail['league_id'], $gmt));
            }
            Thethao_Global::deleteMemcache($arrKeyCache);


            $keyCache1 = vsprintf($this->_match_team_season_league, array($matchDetail['team1'], $matchDetail['league_id'], $matchDetail['season_id']));
            $keyCache2 = vsprintf($this->_match_team_season_league, array($matchDetail['team2'], $matchDetail['league_id'], $matchDetail['season_id']));
            $keyCache3 = vsprintf($this->_match_statistic, array($matchDetail['match_id'], $matchDetail['team1']));
            $keyCache4 = vsprintf($this->_match_statistic, array($matchDetail['match_id'], $matchDetail['team2']));
            Thethao_Global::deleteMemcache(array($keyCache1, $keyCache2, $keyCache3, $keyCache4));

            //get app conf
            $config = Thethao_Global::getApplicationIni();
            $matchNosql = $this->factory('Match', $config['database']['nosql']['adapter']);

            $matchNosql->deleteMatchByLeague($matchDetail['league_id'], $matchDetail['match_id']);

        }
    }

    /**
     * Get list match ids by league
     * @param int $teamID
     * @param int $beginHappenDateTime
     * @param int $endHappenDateTime
     * @param boolean - asort array
     * @return array|boolean
     * @author ThuyNT
     */
    public function getMatchIDsByLeague($leagueID, $beginHappenDateTime = NULL, $endHappenDateTime = NULL, $rev = TRUE)
    {
        // Default result to return
        $result = FALSE;
        $flag = TRUE;
        try
        {
            // Get application config
            $config = Thethao_Global::getApplicationIni();
            //get object redis
            $matchNosqlInstance = $this->factory('Match', $config['database']['nosql']['adapter']);

            // Read from redis first
            $result = $matchNosqlInstance->getListMatchesByHappenTime($leagueID, $beginHappenDateTime, $endHappenDateTime);

            // If miss cache redis => read from DB
            if (empty($result['data']) && FROM_JOB == 1)
            {
                //get mysql instance
                $matchMysqlInstance = $this->factory('Match', $config['database']['default']['adapter']);
                // Read data from MySQL
                $result = $matchMysqlInstance->getListMatchesByHappenTime($beginHappenDateTime, $endHappenDateTime, $leagueID, $gmt = 0, $flag);
                //check to set redis
                if (!empty($result))
                {
                    $matchNosqlInstance->setMatchIDsByLeague($leagueID, $result);
                }
                $intTotal = count($result);
                $arrTest = array_values($result);
                if ($beginHappenDateTime)
                {
                    for ($i = $intTotal - 1; $i > 0; $i--)
                    {
                        if ($arrTest[$i] >= $beginHappenDateTime)
                        {
                            $result = array_slice($result, 0, $i + 1, true);
                            $i = 0;
                        }
                    }
                }
                if ($endHappenDateTime)
                {
                    for ($i = 0; $i < $intTotal; $i++)
                    {
                        if ($arrTest[$i] <= $endHappenDateTime)
                        {
                            $result = array_slice($result, $i, NULL, true);
                            $i = $intTotal;
                        }
                    }
                }
            }
            else
            {
                $result = $result['data'];
            }
            if (!$rev && !empty($result))
            {
                asort($result);
            }
        }
        catch (Exception $ex)
        {
            Fpt_Data_Model::sendLog($ex);
        }
        return $result;
    }

}
?>