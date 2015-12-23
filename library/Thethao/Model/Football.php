<?php

/**
 * @name        :   Thethao_Model_Football
 * @author      :   HungNT1
 * @copyright   :   Fpt Online
 */
class Thethao_Model_Football extends Thethao_Model
{

    static $_instance            = null;
    private $_league_detail;
    private $_season_detail;
    private $_season_by_league_list;
    private $_table_detail;
    private $_league_by_group_list;
    private $_league_all;

    /**
     * return Thethao_Model_Football
     * @author LamTX
     */
    public function __construct()
    {
        $key = Thethao_Global::getConfig('caching');

        $this->_table_detail            = $key['football_table_detail'];
        $this->_league_all              = $key['football_league_all'];
        $this->_league_detail           = $key['football_league_detail'];
        $this->_season_detail           = $key['football_season_detail'];
        $this->_season_by_league_list   = $key['football_season_by_league_list'];
        $this->_league_by_group_list    = $key['football_league_by_group_list'];

        unset($key);

    }

    /**
     * Get singletom instance
     * @return Thethao_Model_Football
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
     * Get full table ranking by season and league
     * @param int $seasonId
     * @param int $leagueId
     * @return array
     */
    public function getListTableRanking($seasonId, $leagueId)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_table_detail, array($leagueId, $seasonId));

        // Read cache
        $tableRanking = $memcacheInstance->read($keyCache);
        // If miss cache
        if ($tableRanking === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();
                // Get db teamseason instance
                $teamseasonObj = $this->factory('Football', $config['database']['default']['adapter']);

                // Get data from db
                $tableRanking = $teamseasonObj->getListTableRanking($seasonId, $leagueId);

                // Write to cache
                $memcacheInstance->write($keyCache, $tableRanking);
                Thethao_Global::writeMemcache($keyCache, $tableRanking);
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }
        return $tableRanking;
    }

    /**
     * Get all list leagues
     * @return array|bool
     * @author QuangTM
     */
    public function getLeagueAll()
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = $this->_league_all;

        // Read cache
        $listAllLeagues = $memcacheInstance->read($keyCache);

        // Miss cache
        if ($listAllLeagues === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();
                // Get db teamseason instance
                $leagueObj = $this->factory('Football', $config['database']['default']['adapter']);

                // Get data from db
                $listAllLeagues = $leagueObj->getLeagueAll();

                // Write to cache
                $memcacheInstance->write($keyCache, $listAllLeagues);
                Thethao_Global::writeMemcache($keyCache, $listAllLeagues);
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }

        return $listAllLeagues;
    }

    /**
     * leagues
     * @desc get detail league of football with league id
     * @param type $arrLeagueIDs
     * @return type
     * @author HungNT1
     */
    public function getListLeagueByIDs($arrLeagueIDs)
    {
        $arrReturn = array();
        $arrMissCache = array();
        $memcacheInstance = Thethao_Global::getCache();
        foreach ($arrLeagueIDs as $leagueID)
        {
            $keyCache = vsprintf($this->_league_detail, $leagueID);
            $leagueDetail = $memcacheInstance->read($keyCache);
            if ($leagueDetail === FALSE)
            {
                array_push($arrMissCache, $leagueID);
            }
            else
            {
                array_push($arrReturn, $leagueDetail);
            }
        }
        // If miss some items
        if (count($arrMissCache))
        {
            // Get config
            $config = Thethao_Global::getApplicationIni();

            // Get DB Obj
            $footballMySQL = $this->factory('Football', $config['database']['default']['adapter']);

            // Get multi article
            $arrMissLeagues = $footballMySQL->getListLeagueByIDs($arrMissCache);

            // Write memcache for each
            foreach ($arrMissLeagues as $leagueDetail)
            {
                $keyCache = vsprintf($this->_league_detail, $leagueDetail['LeagueID']);
                $memcacheInstance->write($keyCache, $leagueDetail);
                Thethao_Global::writeMemcache($keyCache, $leagueDetail);
            }

            $arrReturn = array_merge($arrReturn, $arrMissLeagues);
        }
        return $arrReturn;
    }

    /**
     * Season
     * @desc    get detail season of football with array season id
     * @param   array $arrSeasonIDs - array season id
     * @return  array - array detail season
     * @author  HungNT1
     */
    public function getListSeasonByIDs($arrSeasonIDs)
    {
        $result         = array();
        $arrMissCache   = array();
        $memcacheInstance = Thethao_Global::getCache();
        foreach ($arrSeasonIDs as $seasonID)
        {
            $keyCache = vsprintf($this->_season_detail, $seasonID);
            $seasonDetail = $memcacheInstance->read($keyCache);
            if ($seasonDetail === FALSE)
            {
                array_push($arrMissCache, $seasonID);
            }
            else
            {
                array_push($result, $seasonDetail);
            }
        }

        // If miss some items
        if (count($arrMissCache))
        {
            // Get config
            $config = Thethao_Global::getApplicationIni();

            // Get DB Obj
            $footballMySQL = $this->factory('Football', $config['database']['default']['adapter']);

            // Get multi article
            $arrMissSeasons = $footballMySQL->getListSeasonByIDs($arrMissCache);

            // Write memcache for each
            foreach ($arrMissSeasons as $seasonDetail)
            {
                $keyCache = vsprintf($this->_season_detail, $seasonDetail['SeasonID']);
                $memcacheInstance->write($keyCache, $seasonDetail);
                Thethao_Global::writeMemcache($keyCache, $seasonDetail);
            }

            $result = array_merge($result, $arrMissSeasons);
        }
        return $result;
    }

    /**
     * Season: Get list season by league
     * @param int $leagueID
     * @return array
     */
    public function getListSeasonByLeagueID($leagueID)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_season_by_league_list, array($leagueID));

        // Read cache
        $listSeasons = $memcacheInstance->read($keyCache);

        // Miss cache
        if ($listSeasons === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();
                // Get db teamseason instance
                $seasonObj = $this->factory('Football', $config['database']['default']['adapter']);

                // Get data from db
                $listSeasons = $seasonObj->getListSeasonByLeagueID($leagueID);

                //Sort descending list season
                if (is_array($listSeasons) && count($listSeasons))
                {
                    $arrSeasonByLeagueSort = array();
                    foreach ($listSeasons as $value)
                    {
                        $arrSeasonByLeagueSort[$value['NameSeason']] = $value;

                    }
                    krsort($arrSeasonByLeagueSort);
                    $listSeasons = $arrSeasonByLeagueSort;
                }

                // Write to cache
                $memcacheInstance->write($keyCache, $listSeasons);
                Thethao_Global::writeMemcache($keyCache, $listSeasons);
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }

        return $listSeasons;
    }

    /**
     * Get list league by group
     * @param int $group
     * @return array
     */
    public function getListLeagueByGroup($group)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_league_by_group_list, array($group));
        // Read cache
        $listLeagues = $memcacheInstance->read($keyCache);

        // Miss cache
        if ($listLeagues === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();
                // Get db teamseason instance
                $leagueObj = $this->factory('Football', $config['database']['default']['adapter']);

                // Get data from db
                $listLeagues = $leagueObj->getListLeagueByGroup($group);

                // Write to cache
                $memcacheInstance->write($keyCache, $listLeagues);
                Thethao_Global::writeMemcache($keyCache, $listLeagues);
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }

        return $listLeagues;
    }

    /**
     * Rewrite table of football
     * @param array $params(LeagueID, SeasonID)
     */
    public function rewriteBXHCache($params)
    {
        // Get params
        $leagueID = intval($params['LeagueID']);
        $seasonID = intval($params['SeasonID']);

        // Make key cache from param
        $keyCache = vsprintf($this->_table_detail, array($leagueID, $seasonID));

        // Delete cache
        Thethao_Global::deleteMemcache(array($keyCache));

        // Re-write cache team_season
        return $this->getListTableRanking($seasonID, $leagueID);
    }
}
?>