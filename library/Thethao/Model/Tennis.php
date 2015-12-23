<?php
/**
 * @name        :   Thethao_Model_Tennis
 * @author      :   HungNT1
 * @copyright   :   Fpt Online
 * @return      :   Thethao_Model_Tennis
 */
class Thethao_Model_Tennis extends Thethao_Model
{

    static $_instance            = null;

    private $_table_ranking;
    private $_tennis_match_info;
    private $_tennis_player_detail;
    private $_tennis_player_matches;

    /**
     * return Thethao_Model_Tennis
     * @author LamTX
     */
    public function __construct()
    {
        $key = Thethao_Global::getConfig('caching');
        $this->_table_ranking = $key['tennis_table_ranking'];
        $this->_tennis_match_info = $key['tennis_match_info'];
        $this->_tennis_player_detail = $key['tennis_player_detail'];
        $this->_tennis_player_matches = $key['tennis_player_matches'];

        unset($key);

    }

    /**
     * Get singletom instance
     * @return Thethao_Model_Tennis
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
     * Get tennis table ranking from cache
     * If miss cache => get data from database and write cache.
     * @param int $gender Only 0 or 1. 0 => woman (WTA), 1 => man (ATP)
     * @param int|NULL $year  If this parameter is NULL, that means current year
     * @return array|boolean
     * @author QuangTM
     */
    public function getTableTennis($gender = 1, $year = NULL)
    {
        // Create validate
        $validInt = new Zend_Validate_Int();

        // Validate gender
        if (!$validInt->isValid($gender) || $gender < 0 || $gender > 1)
        {
            return FALSE;
        }

        // Standardized year
        $currentYear = intval(date('Y'));
        if ($year == NULL)
        {
            $year = $currentYear;
        }

        // Validate year
        if (!$validInt->isValid($year) || $year > $currentYear)
        {
            return FALSE;
        }

        // Create key cache about tennis player
        $keyTableRanking = vsprintf($this->_table_ranking, array($gender, $year));

        // Get memcache instance
        $memcacheInstance = Thethao_Global::getCache();

        // Read data from cache
        $tableRanking = $memcacheInstance->read($keyTableRanking);

        // If miss cache
        if ($tableRanking === FALSE)
        {
            // Get table ranking from DB
            $tableRanking = $this->_getTennisTableRankingFromDb($gender, $year);

            // Write cache
            if (is_array($tableRanking) && count($tableRanking))
            {
                $memcacheInstance->write($keyTableRanking, $tableRanking);
                Thethao_Global::writeMemcache($keyTableRanking, $tableRanking);
            }
            else
            {
                $memcacheInstance->write($keyTableRanking, -1);
                Thethao_Global::writeMemcache($keyTableRanking, -1);
            }
        }
        // Return table ranking
        if ($tableRanking == -1)
        {
            return array();
        }
        return $tableRanking;
    }

    /**
     * Get tennis table ranking by gender and year. <br />
     * This function only get data from database and NOT write cache (any cache).
     * @param int $gender Only 0 or 1. 0 => woman (WTA), 1 => man (ATP)
     * @param int|NULL $year If this parameter is NULL, that means current year
     * @return array|boolean
     * @author QuangTM
     */
    private function _getTennisTableRankingFromDb($gender, $year = NULL)
    {
        // default result to return
        $tableRanking = FALSE;

        // Create validate
        $validInt = new Zend_Validate_Int();

        // Validate gender
        if (!$validInt->isValid($gender) || $gender < 0 || $gender > 1)
        {
            return FALSE;
        }

        // Standardized year
        $currentYear = intval(date('Y'));
        if ($year == NULL)
        {
            $year = $currentYear;
        }

        // Validate year
        if (!$validInt->isValid($year) || $year > $currentYear)
        {
            return FALSE;
        }

        $config = Thethao_Global::getApplicationIni();

        // Get db teamseason instance
        $tennisMysqlObj = $this->factory('Tennis', $config['database']['default']['adapter']);

        // Get tennis player detail
        $tableRanking = $tennisMysqlObj->getTennisTableRanking($year, $gender);

        // Return result
        return $tableRanking;
    }

    /**
     * Get tennis matches information by one id or array id
     * @param int|array $matchID
     * @return array
     * @author QuangTM
     * @example getTennisMatches(1);
     * @example getTennisMatches(array(11,12));
     */
    public function getTennisMatches($matchID)
    {
        // Default result
        $result = array();

        // If input data is an integer data type
        if (is_int($matchID))
        {
            $matchID = array($matchID);
        }
        // Create key cache about tennis player
        $keyTennisMatch = vsprintf($this->_tennis_match_info, '');
        // Create array key cache
        array_walk($matchID, 'self::buildKeyCache', $keyTennisMatch);

        // Get memcache instance
        $memcacheInstance = Thethao_Global::getCache();
        $arrMissCache = array();
        $dataFromCache = array();
        // Read data from cache
        foreach ($matchID as $key => $keyCache)
        {
            $dataFromCache[$keyCache] = $memcacheInstance->read($keyCache);
            if ($dataFromCache[$keyCache] === false)
            {
                //get id miss cache
                array_push($arrMissCache, $keyCache);
            }
        }

        // Miss all cache
        // Read data from database, after that write cache for each match
        if (!empty ($arrMissCache))
        {
            /**
             * Clear prefix key cache in array player id
             * Use array player id to get data from DB
             */
            array_walk($arrMissCache, 'self::clearPrefixKeyCache', $keyTennisMatch);

            // Get data from database
            $result = $this->_getTennisMatchesFromDb($arrMissCache);

            // Write cache for each match & assign to array
            foreach ($result as $id => $info)
            {
                $memcacheInstance->write($keyTennisMatch . $id, $info);
                Thethao_Global::writeMemcache($keyTennisMatch . $id, $info);
                $dataFromCache[$keyTennisMatch . $id] = $info;
            }

        }
        unset($arrMissCache);
        /**
         * Some key miss from cache => get it from DB and write cache
         * Combine data from cache and from database.
         * return combined data
         */

        // Loop throught out data from cache to get some miss data
        foreach ($dataFromCache as $key => $value)
        {
            // Convert key in data from cache into player id
            $id = str_replace($keyTennisMatch, '', $key);

            // Push this cached data into array result
            $result[$id] = $value;
        }
        unset ($dataFromCache);

        // Return result
        return $result;
    }

    /**
     * Get tennis schedule from date to date.<br />
     * Data consist of matches' detail information
     * @param int $fromDate unix timestamp
     * @param int $toDate unix timestamp
     * @return array
     * @author QuangTM
     * @example getTennisSchedule(strtotime('2012/11/29'), time())
     */
    public function getTennisSchedule($fromDate, $toDate)
    {
        // Get application config
        $config = Thethao_Global::getApplicationIni();

        // Get obj access redis key team_match
        $tennisNosql = $this->factory('Tennis', $config['database']['nosql']['adapter']);

        // Get tennis matches id from redis
        $arrMatchesID = $tennisNosql->getTennisSchedule($fromDate, $toDate);

        // Get tennis matches information
        if (count($arrMatchesID))
        {
            return $this->getTennisMatches(array_keys($arrMatchesID));
        }
        return array();
    }

    /**
     * Get information statistic about tennis player.<br />
     * Input parameter should be int, array or string separate by comma.<br />
     * This function only get data from database and NOT write cache (any cache).
     * @param int|array|string $playerID
     * @return array
     * @author QuangTM
     */
    protected function _getTennisPlayerInformationFromDb($playerID)
    {
        // If input player id is number integer
        if (is_int($playerID))
        {
            $playerID = array($playerID);
        }

        // If input player id is a string separate by comma
        if (is_string($playerID))
        {
            $playerID = explode(',', $playerID);
        }

        // If input player id is not array or not item in it
        if (!is_array($playerID) || !count($playerID))
        {
            return FALSE;
        }

        $config = Thethao_Global::getApplicationIni();

        // Get db teamseason instance
        $tennisMysqlObj = $this->factory('Tennis', $config['database']['default']['adapter']);

        // Get tennis player stats
        $arrPlayerStats = $tennisMysqlObj->getTennisPlayerStatsByPlayerIDs($playerID);

        // Return result
        return $arrPlayerStats;
    }

    /**
     * Get tennis matches information from DB without caching.<br />
     * This function only get data from database and dont write any cache.<br />
     * Input parameter can be integer data, array data or string with data is seperated by comma
     * @param int|string|array $matchId
     * @return array|boolean
     *
     */
    protected function _getTennisMatchesFromDb($matchId)
    {
        // default result to return
        $arrTennisMatches = array();

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

        // Get db teamseason instance
        $tennisMysqlObj = $this->factory('Tennis', $config['database']['default']['adapter']);
        // Get tennis matches from DB
        $arrTennisMatches = $tennisMysqlObj->getTennisMatches($matchId);

        // Default player id attend tennis matches
        $arrPlayerID = array();

        if (!empty ($arrTennisMatches))
        {
            // Loop throught out array tennis matches to get player id
            foreach ($arrTennisMatches as $tennisMatch)
            {
                array_push($arrPlayerID, $tennisMatch['player1a'], $tennisMatch['player1b'], $tennisMatch['player2a'], $tennisMatch['player2b']);
            }
        }
        // Get unique and valid player id
        $arrPlayerID = array_unique(array_filter($arrPlayerID));

        // Break down array player id into many smaller spices
        $arrChunks = array_chunk($arrPlayerID, 100);

        // Loop throught these chunks to get player tennis basic information.
        // After that mapping again into match info
        foreach ($arrChunks as $chunk)
        {
            // Loop array match to map player info
            foreach ($arrTennisMatches as $matchID => $matchInfo)
            {
                if (!isset($arrTennisMatches[$matchID]['player1a_info']))
                {
                    $arrTennisMatches[$matchID]['player1a_info'] = $arrPlayerInfo[$matchInfo['player1a']];
                }
                if (!isset($arrTennisMatches[$matchID]['player2a_info']))
                {
                    $arrTennisMatches[$matchID]['player2a_info'] = $arrPlayerInfo[$matchInfo['player2a']];
                }

                if (!isset($arrTennisMatches[$matchID]['player1b_info']))
                {
                    $arrTennisMatches[$matchID]['player1b_info'] = $matchInfo['player1b'] != 0 ? $arrPlayerInfo[$matchInfo['player1b']] : NULL;
                }
                if (!isset($arrTennisMatches[$matchID]['player2b_info']))
                {
                    $arrTennisMatches[$matchID]['player2b_info'] = $matchInfo['player2b'] != 0 ? $arrPlayerInfo[$matchInfo['player2b']] : NULL;
                }
                if (!isset($arrTennisMatches[$matchID]['datetime_happen_readable']))
                {
                    $arrTennisMatches[$matchID]['datetime_happen_readable'] = date('d/m/Y', $arrTennisMatches[$matchID]['datetime_happen']);
                }
            }
        }
        return $arrTennisMatches;
    }

    /**
     * Get player detail by array player id or one player. <br />
     * The input parameter could be integer, array or string data type.<br />
     * If input is string data type, must separated by comma.
     * @param int|array|string $playerID
     * @return array
     * @author QuangTM
     * @example getTennisPlayerDetail(10)
     * @example getTennisPlayerDetail('10,11,12')
     * @example getTennisPlayerDetail(array(10,11,12))
     */
    public function getTennisPlayerDetail($playerID)
    {
        // If input player id is number integer
        if (is_int($playerID))
        {
            $playerID = array($playerID);
        }

        // If input player id is a string separate by comma
        if (is_string($playerID))
        {
            $playerID = explode(',', $playerID);
        }

        // If input player id is not array or not item in it
        if (!is_array($playerID) || !count($playerID))
        {
            return FALSE;
        }


        // Create key cache about tennis player
        $keyPlayerDetail = vsprintf($this->_tennis_player_detail, '');
        // Create array key cache
        array_walk($playerID, 'self::buildKeyCache', $keyPlayerDetail);

        // Get memcache instance
        $memcacheInstance = Thethao_Global::getCache();

        // Read data from cache
        $dataFromCache = $memcacheInstance->read($playerID);

        /**
         * Miss all data from cache.
         * Read from DB, write cache for all and return data
         */
        if ($dataFromCache === FALSE)
        {
            /**
             * Clear prefix key cache in array player id
             * Use array player id to get data from DB
             */
            array_walk($playerID, 'self::clearPrefixKeyCache', $keyPlayerDetail);

            // Get data from database
            $result = $this->_getTennisPlayerInformationFromDb($playerID);
            // Loop throught out data from DB and write cache
            foreach ($result as $id => $info)
            {
                $memcacheInstance->write($keyPlayerDetail . $id, $info);
                Thethao_Global::writeMemcache($keyPlayerDetail . $id, $info);
            }
        }
        /**
         * Some key miss from cache => get it from DB and write cache
         * Combine data from cache and from database.
         * return combined data
         */
        else
        {
            // Default result => that's combined data
            $result = array();

            // Default array miss data
            $arrMissData = array();

            // Loop throught out data from cache to get some miss data
            foreach ($dataFromCache as $key => $value)
            {
                // Convert key in data from cache into player id
                $id = str_replace($keyPlayerDetail, '', $key);

                // Push this cached data into array result
                $result[$id] = $value;
            }
            // Computes id miss data
            $arrMissData = array_diff($playerID, array_keys($dataFromCache));

            if (count($arrMissData))
            {
                /**
                 * Clear prefix key cache in array player id
                 * Use array player id to get data from DB
                 */
                array_walk($arrMissData, 'self::clearPrefixKeyCache', $keyPlayerDetail);

                // Get data from database
                $arrMissData = $this->_getTennisPlayerInformationFromDb($arrMissData);

                // Loop throught out data from DB and write cache
                foreach ($arrMissData as $id => $info)
                {
                    // Write cache
                    $memcacheInstance->write($keyPlayerDetail . $id, $info);
                    Thethao_Global::writeMemcache($keyPlayerDetail . $id, $info);

                    // Push into result
                    $result[$id] = $info;
                }
            }
        }

        return $result;
    }

    /**
     * Get matches id which player attends
     * @param int $playerID
     * @param int $fromDate unix timestamp
     * @param int $toDate unix timestamp
     * @param boolean $reverse
     * @param int $limit
     * @param int $offset
     * @return array|boolean
     * @author QuangTM
     * @example getMatchesPlayerAttend(11, strtotime('-4 months'), time())
     */
    public function getMatchesPlayerAttend($playerID, $fromDate, $toDate, $reverse = TRUE, $limit = 20, $offset = 0)
    {
        // Get application config
        $config = Thethao_Global::getApplicationIni();

        // Get obj access redis key team_match
        $tennisRedisObj = $this->factory('Tennis', $config['database']['nosql']['adapter']);

        // Get tennis matches id from redis
        $arrMatchesID = $tennisRedisObj->getMatchesPlayerAttend($playerID, $fromDate, $toDate, $reverse, $limit, $offset);

        // Get tennis matches information
        if (count($arrMatchesID))
        {
            return $this->getTennisMatches(array_keys($arrMatchesID));
        }
        return array();
    }

     /**
     * Get tennis table ranking from cache.<br />
     * If miss cache => get data from database and write cache.
     * @param int $gender Only 0 or 1. 0 => woman (WTA), 1 => man (ATP)
     * @param int|NULL $year  If this parameter is NULL, that means current year
     * @return array|boolean
     * @author QuangTM
     */
    public function getTennisTableRanking($gender, $year = NULL)
    {
        // Create validate
        $validInt = new Zend_Validate_Int();

        // Validate gender
        if (!$validInt->isValid($gender) || $gender < 0 || $gender > 1)
        {
            return FALSE;
        }

        // Standardized year
        $currentYear = intval(date('Y'));
        if ($year == NULL)
        {
            $year = $currentYear;
        }

        // Validate year
        if (!$validInt->isValid($year) || $year > $currentYear)
        {
            return FALSE;
        }

        // Create key cache about tennis player
        $keyTableRanking = vsprintf($this->_table_ranking, array($gender, $year));

        // Get memcache instance
        $memcacheInstance = Thethao_Global::getCache();

        // Read data from cache
        $tableRanking = $memcacheInstance->read($keyTableRanking);

        // If miss cache
        if ($tableRanking === FALSE)
        {
            // Get table ranking from DB
            $tableRanking = $this->_getTennisTableRankingFromDb($gender, $year);

            // Write cache
            if (is_array($tableRanking) && count($tableRanking))
            {
                $memcacheInstance->write($keyTableRanking, $tableRanking);
                Thethao_Global::writeMemcache($keyTableRanking, $tableRanking);
            }
            else
            {
                $memcacheInstance->write($keyTableRanking, -1);
                Thethao_Global::writeMemcache($keyTableRanking, -1);
            }
        }

        // Return table ranking
        if ($tableRanking == -1)
        {
            return array(
                'table'      => NULL,
                'updateTime' => NULL,
            );
        }
        return $tableRanking;
    }

    /**
     * Insert tennis match into redis key
     * @param array $matchInfo
     * @return boolean
     * @author QuangTM
     */
    public function insertTennisMatchIntoRedis($matchInfo)
    {
        // Get application config
        $config = Thethao_Global::getApplicationIni();

        // Get obj access redis key team_match
        $tennisRedisObj = $this->factory('Tennis', $config['database']['nosql']['adapter']);

        // Insert tennis info into redis
        return $tennisRedisObj->insertTennisMatches($matchInfo);
    }

    /**
     * Pre cache tennis player
     * @param type $params
     */
    public function preCachingTennisPlayer($params)
    {
        // Create array key cache
        $arrKeyPlayerDetail = $params['arrPlayerIds'];

        // Create key cache about tennis player
        $keyPlayerDetail = vsprintf($this->_tennis_player_detail, '');
        // Create array key cache
        array_walk($arrKeyPlayerDetail, 'self::buildKeyCache', $keyPlayerDetail);

        // Delete all cache
        $arrKeyCache = array();
        foreach ($arrKeyPlayerDetail as $keyCache)
        {
            $arrKeyCache[] = $keyCache;
        }
        Thethao_Global::deleteMemcache($arrKeyCache);

        $chunkPlayerIDs = array_chunk($params['arrPlayerIds'], 100);

        foreach ($chunkPlayerIDs as $arrPlayerIDs)
        {
            $this->getTennisPlayerDetail($arrPlayerIDs);
        }
    }

    public function preCachingTennisRanking($params)
    {
        // Validate input
        if (!isset($params['year']) || !isset($params['gender']))
        {
            return FALSE;
        }

        // Create validate integer
        $validInt = new Zend_Validate_Int();

        // Validate integer data input
        if (!$validInt->isValid($params['year']) || !$validInt->isValid($params['gender']) ||
            $params['year'] <= 0 || $params['year'] > intval(date('Y')) ||
            ($params['gender'] != 0 && $params['gender'] != 1))
        {
            return FALSE;
        }
        // Create key cache
        $key = vsprintf($this->_table_ranking, array($params['gender'], $params['year']));
        // Delete cache
        Thethao_Global::deleteMemcache(array($key));

        // Get tennis table ranking
        $this->getTennisTableRanking($params['gender'], $params['year']);
    }

    public function preCachingTennisMatch($params)
    {
        // Create key cache about tennis player
        $keyTennisMatch = vsprintf($this->_tennis_match_info, '');

        $arrKeyTennisMatch = $params['arrMatchIDs'];
        // Create array key cache
        array_walk($arrKeyTennisMatch, 'self::buildKeyCache', $keyTennisMatch);

        // Delete all cache
        $arrKeyCache = array();
        foreach ($arrKeyTennisMatch as $keyCache)
        {
            $arrKeyCache[] = $keyCache;
        }
        Thethao_Global::deleteMemcache($arrKeyCache);

        $chunkMatchIDs = array_chunk($params['arrMatchIDs'], 100);

        foreach ($chunkMatchIDs as $arrMatchIDs)
        {
            // Pre-Cache for tennis match
            $arrMatchInfo = $this->getTennisMatches($arrMatchIDs);

            // Push data into redis
            foreach ($arrMatchInfo as $matchInfo)
            {
                $this->insertTennisMatchIntoRedis($matchInfo);
            }
        }
        return TRUE;
    }
}

?>