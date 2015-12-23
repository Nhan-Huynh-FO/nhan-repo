<?php

/**
 * @name        :   Thethao_Model_Team
 * @author      :   HungNT1
 * @copyright   :   Fpt Online
 */
class Thethao_Model_Team extends Thethao_Model
{

    static $_instance            = null;
    private $_team_extend;


    /**
     * return Thethao_Model_Team
     * @author LamTX
     */
    public function __construct()
    {
        $key = Thethao_Global::getConfig('caching');
        $this->_team_extend     = $key['team_extend'];
        unset($key);

    }

    /**
     * Get singletom instance
     * @return Thethao_Model_Team
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
     * Get Detail Extend of team
     * @param type $arrIds
     */
    public function getDetailTeamExtendByIDs($arrTeamIDs)
    {
        $result = array();
        try
        {
            $arrMissTeamIDs = array();

            //get memcahce instance
            $memcacheInstance  = Thethao_Global::getCache();
            // Loop for read from cache
            foreach ($arrTeamIDs as $teamID)
            {
                $keyCache          = vsprintf($this->_team_extend, array($teamID));

                $teamExtendContent = $memcacheInstance->read($keyCache);

                if ($teamExtendContent === FALSE)
                {
                    array_push($arrMissTeamIDs, $teamID);
                }
                else
                {
                    $result[$teamID] = $teamExtendContent;
                }
            }
            // If miss some items
            if (count($arrMissTeamIDs))
            {
                // Get config
                $config = Thethao_Global::getApplicationIni();

                // Get Mysql object access table team
                $teamMysqlObj = $this->factory('Team', $config['database']['default']['adapter']);

                // Get multi team (missed)
                $arrMissTeams = $teamMysqlObj->getDetailTeamExtendByIDs($arrMissTeamIDs);

                // Loop to write into cache
                foreach ($arrMissTeams as $teamID => $teamExtendContent)
                {
                    // Make key memcache
                    $keyCache = vsprintf($this->_team_extend, array($teamID));

                    // Write to memcache
                    $memcacheInstance->write($keyCache, $teamExtendContent);
                    Thethao_Global::writeMemcache($keyCache, $teamExtendContent);

                    // Merge to result
                    $result[$teamID] = $teamExtendContent;
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
     * Get list league and season by team attend
     * @param int $teamID
     * @return array|boolean
     * @author QuangTM
     */
    public function getListLeagueAndSeasonTeamAttend($teamID)
    {
        // Default result to return
        $result = FALSE;
        try
        {
            // Get application config
            $config = Thethao_Global::getApplicationIni();

            // Get object to access table rating_player
            $teamMatchRedisObj = $this->factory('Team', $config['database']['nosql']['adapter']);

            // Read from redis first
            $result = $teamMatchRedisObj->getListLeagueAndSeasonTeamAttend($teamID);

            // If miss cache redis => read from DB
            if (!$result)
            {
                // Get object to access mysql DB
                $teamMatchMysqlObj = $this->factory('Team', $config['database']['default']['adapter']);

                // Read data from MySQL
                $result = $teamMatchMysqlObj->getListLeagueAndSeasonTeamAttend($teamID);
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
        return $result;
    }

    /**
     *
     * @param type $params
     */
    public function rewriteTeamCache($params)
    {
        $teamID     = intval($params['TeamID']);
        $updateType = $params['UpdateType'];
        if ($params['arrPlayerId'])
        {
            $arrPlayerId = $params['arrPlayerId'];
        }
        if ($teamID)
        {
            switch ($updateType)
            {
                case 'team-extend':
                    $keyCache = vsprintf($this->_team_extend, array($teamID));
                    Thethao_Global::deleteMemcache(array($keyCache));
                    $this->getDetailTeamExtendByIDs(array($teamID));
                    break;
                default:
                    break;
            }
        }
        if (!empty($arrPlayerId))
        {
            // call object framework to detete cache
            $modelObject = new Fpt_Data_Materials_Object();
            foreach ($arrPlayerId as $playerId)
            {
                //update cache for player (id player, status to getdetail)
                $modelObject->getPlayer()->updateObject($playerId, true);
            }
        }
    }
}
?>