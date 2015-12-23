<?php

/**
 * @name        :   Thethao_Business_Sport_Adapter_Redis
 * @author      :   HungNT1
 * @copyright   :   Fpt Online
 * @return      :   Thethao_Business_Sport_Adapter_Redis
 */
class Thethao_Business_Match_Adapter_Redis
{

    /**
     * @var system_model Singleton instance
     */
    protected static $_instance;
    //list redis key
    protected $_team_match_league;
    protected $_league_match;

    /**
     * Constructor of class
     * @return Thethao_Business_Match_Adapter_Redis
     * @author HungNT1
     */
    public function __construct()
    {
        //get key caching
        $key = Thethao_Global::getConfig('caching');
        //init keys redis related to this class
        $this->_team_match_league = $key['football_team_match_league'];
        $this->_league_match = $key['football_league_match'];

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
     * Get list league and season by team attend
     * @param int $teamID
     * @return array|boolean
     * @author QuangTM
     */
    public function getListLeagueAndSeasonTeamAttend($teamID)
    {
        $result = array();
        try
        {
            // Get redis instance
            $redisInstance = Thethao_Global::getRedis('article');

            // Get key cache
            $pattern = vsprintf($this->_team_match_league, array($teamID, '*'));

            // Get array key
            $arrKeys = $redisInstance->keys($pattern);

            // Pattern for get league
            $pattern = '/' . str_replace('*', '(\d+)', $pattern) . '/';
            $match = null;
            foreach ($arrKeys as $key)
            {
                if (preg_match($pattern, $key, $match))
                {
                    $result[$match[1]] = $redisInstance->sMembers($key);
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
     * Lay cau thu ghi ban, the do, the vang trong tran dau
     * @author PhongTX
     * @param type $intMatchId
     * @return type
     */
    public function getDetailMatchExtendScorecard($intMatchId)
    {
        $arrReturn = array();
        try
        {
            // Get redis instance
            $redisInstance = Thethao_Global::getRedis('object');
            //get key cache
            $keyCache = vsprintf($this->_match_extend_scorecard, array($intMatchId));
            //Get redis
            $arrReturn = $redisInstance->get($keyCache);
            if ($arrReturn == -1)
                return array();
            if (!empty($arrReturn))
            {
                $arrReturn = Zend_Json::decode($arrReturn);
            }
            //return data
            return $arrReturn;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
    }

    /**
     * Get list match ids by league
     * @param int $teamID
     * @param int $beginHappenDateTime
     * @param int $endHappenDateTime
     * @return array|boolean
     * @author ThuyNT
     */
    public function getListMatchesByHappenTime($leagueID, $beginHappenDateTime = NULL, $endHappenDateTime = NULL)
    {
        try
        {
            $arrReturn = array();
            // Get redis instance
            $redisInstance = Thethao_Global::getRedis('object');

            // Get key redis for getting match by team
            $keyCache = sprintf($this->_league_match, $leagueID);

            // Return result
            $arrReturn['data'] = $redisInstance->zRevRangeByScore($keyCache, $endHappenDateTime == NULL ? '+inf' : $endHappenDateTime, $beginHappenDateTime == NULL ? '-inf' : $beginHappenDateTime, array('withscores' => TRUE));

            return $arrReturn;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
            return FALSE;
        }
    }
    /**
     * Set redist list match by league id
     * @param int $leagueID
     * @param array $arrData
     * @return type
     */
    public function setMatchIDsByLeague($leagueID, $arrData)
    {
        try
        {
            $arrReturn = array();
            // Get redis instance
            $redisInstance = Thethao_Global::getRedis('object');

            // Get key redis for adding match by league
            $keyCache = sprintf($this->_league_match, $leagueID);
            //loop data to add
            foreach ($arrData as $matchId => $dateTimeHappend)
            {
                $redisInstance->zAdd($keyCache, -1, $matchId);
                $redisInstance->zDelete($keyCache, $matchId);
                $arrReturn[] = $redisInstance->zAdd($keyCache, $dateTimeHappend, $matchId);
            }
            //keep last 500 item
            $redisInstance->zRemRangeByRank($keyCache, 0, -510);
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);

        }
        return $arrReturn;
    }

    /**
     * Delete match in league by league id and match id
     * @param type $leagueId
     * @param type $matchId
     */
    public function deleteMatchByLeague($leagueId, $matchId)
    {
        try
        {
            // Get redis instance
            $redisInstance = Thethao_Global::getRedis('object');

            // Get key redis for adding match by league
            $keyCache = sprintf($this->_league_match, $leagueId);

            $redisInstance->zAdd($keyCache, -1, $matchId);

            return $redisInstance->zDelete($keyCache, $matchId);
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
            return false;
        }
    }
}
