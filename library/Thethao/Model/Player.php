<?php

/**
 * @name        :   Thethao_Model_Player
 * @author      :   HungNT1
 * @copyright   :   Fpt Online
 */
class Thethao_Model_Player extends Thethao_Model
{

    static $_instance = null;
    private $_player_team_match_list;
    private $_player_award;
    private $_detail_info_trans_term;
    private $_detail_player_rating_term;
    private $_player_champion_by_season;
    private $_player_by_team;

    /**
     * return Thethao_Model_Player
     * @author LamTX
     */
    public function __construct()
    {
        $key = Thethao_Global::getConfig('caching');

        $this->_player_team_match_list = $key['player_team_match_list'];
        $this->_player_award = $key['player_award'];
        $this->_detail_info_trans_term = $key['player_detail_info_trans_term'];
        $this->_detail_player_rating_term = $key['player_detail_rating_term'];
        $this->_player_champion_by_season = $key['player_champion_by_season'];
        $this->_player_by_team = $key['player_by_team'];

        unset($key);
    }

    /**
     * Get singletom instance
     * @return Thethao_Model_Player
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
     * Get list players by team and match
     * @param int $matchID
     * @param int $teamID
     * @return array|boolean
     * @author QuangTM
     */
    public function getPlayersTeamMatch($matchID, $teamID)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_player_team_match_list, array($matchID, $teamID));

        // Read cache
        $arrPlayersTeamMatch = $memcacheInstance->read($keyCache);

        // Miss cache
        if ($arrPlayersTeamMatch === FALSE)
        {
            try
            {
                $config = Thethao_Global::getApplicationIni();

                // Get db teamseason instance
                $playerTeamMatchObj = $this->factory('Player', $config['database']['default']['adapter']);

                // Get data from db
                $arrPlayersTeamMatch = $playerTeamMatchObj->getPlayersTeamMatch($matchID, $teamID);

                // Write to cache
                if (count($arrPlayersTeamMatch))
                {

                    $memcacheInstance->write($keyCache, $arrPlayersTeamMatch);
                    Thethao_Global::writeMemcache($keyCache, $arrPlayersTeamMatch);
                }
                else
                {
                    $memcacheInstance->write($keyCache, -1, 3600);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex);
            }
        }
        else if ($arrPlayersTeamMatch == -1)
        {
            return array();
        }
        return $arrPlayersTeamMatch;
    }

    /**
     * Get detail player by id
     * @param int $player_id, $type
     * @return array
     */
    public function getDetailPlayerByID($player_id, $type)
    {
        $playerRating = array();
        try
        {
            $memcacheInstance = Thethao_Global::getCache();
            $keyCache = vsprintf($this->_player_award, array($player_id, $type));
            //read cache
            $playerRating = $memcacheInstance->read($keyCache);
            //miss cache
            if ($playerRating === false)
            {

                // Get application's configuation
                $config = Thethao_Global::getApplicationIni();

                // Get mysql object access player
                $playerMysqlObj = $this->factory('Player', $config['database']['default']['adapter']);

                // Get detail players
                $playerRating = $playerMysqlObj->getDetailPlayerByID($player_id, $type);

                // write cache
                $memcacheInstance->write($keyCache, $playerRating);
                Thethao_Global::writeMemcache($keyCache, $playerRating);
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
        return $playerRating;
    }

    /*
     * Group player award
     */

    public function groupPlayerAward($awards)
    {
        $arrAwards = array();
        $arr = array();
        foreach ($awards as $award)
        {
            $awardId = $award['league_id'] . $award['award'];
            if (!in_array($awardId, $arr))
            {
                $arrAwards[$awardId] = array();
                $arrAwards[$awardId]['name'] = $award['name'];
                $arrAwards[$awardId]['award'] = $award['award'];
                $arrAwards[$awardId]['total'] = 1;
                $arrAwards[$awardId]['season'] = $award['name_season'];
                $arr[] = $awardId;
            }
            else
            {
                $arrAwards[$awardId]['season'] .= ',' . $award['name_season'];
                $arrAwards[$awardId]['total'] ++;
            }
        }
        return $arrAwards;
    }

    /**
     * Function get detail trans term of player
     * @param type $player_id
     * @return type
     */
    public function getInformationTransfer($player_id)
    {
        $playerRating = array();
        try
        {

            $memcacheInstance = Thethao_Global::getCache();
            $keyCache = vsprintf($this->_detail_info_trans_term, array($player_id));
            //read cache
            $playerRating = $memcacheInstance->read($keyCache);

            //miss cache
            if ($playerRating === false)
            {
                // Get application's configuation
                $config = Thethao_Global::getApplicationIni();

                // Get mysql object access player
                $playerMysqlObj = $this->factory('Player', $config['database']['default']['adapter']);

                // Get detail players
                $playerRating = $playerMysqlObj->getInformationTransfer($player_id);

                if (!empty($playerRating))
                {
                    // write cache
                    $memcacheInstance->write($keyCache, $playerRating);
                    Thethao_Global::writeMemcache($keyCache, $playerRating);
                }
                else
                {
                    // write cache
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
        if ($playerRating == -1)
            return array();
        return $playerRating;
    }

    /**
     *
     * @param type $player_id
     * @return type
     */
    public function getDetailPlayerRatingTerm($player_id)
    {
        $playerRating = array();
        try
        {

            $memcacheInstance = Thethao_Global::getCache();
            $keyCache = vsprintf($this->_detail_player_rating_term, array($player_id));

            //read cache
            $playerRating = $memcacheInstance->read($keyCache);

            //miss cache
            if ($playerRating === false)
            {

                // Get application's configuation
                $config = Thethao_Global::getApplicationIni();

                // Get mysql object access player
                $playerMysqlObj = $this->factory('Player', $config['database']['default']['adapter']);

                // Get detail players
                $playerRating = $playerMysqlObj->getDetailPlayerRatingTerm($player_id);
                if (!empty($playerRating))
                {
                    // write cache
                    $memcacheInstance->write($keyCache, $playerRating);
                    Thethao_Global::writeMemcache($keyCache, $playerRating);
                }
                else
                {
                    // write cache
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
        if ($playerRating == -1)
            return array();
        return $playerRating;
    }

    public function rewritePlayerAwardCache($params)
    {
        // Get params
        $playerID = intval($params['PlayerID']);
        $typeID = intval($params['TypeID']);

        // Delete cache player award
        $keyCache1 = vsprintf($this->_player_award, array($playerID, $typeID));
        Thethao_Global::deleteMemcache(array($keyCache1));
        $this->getDetailPlayerByID($playerID, $typeID);
    }

    public function rewritePlayerTranferCache($arrParams)
    {
        // Get id player tranfer
        $playerId = $arrParams['PlayerID'];

        // Delete cache player award
        $keyCache = vsprintf($this->_detail_info_trans_term, array($playerId));
        Thethao_Global::deleteMemcache(array($keyCache));

        $this->getInformationTransfer($playerId);
    }

    public function rewritePlayerRatingTerm($playerId)
    {
        // Delete cache player award
        $keyCache = vsprintf($this->_detail_player_rating_term, array($playerId));
        Thethao_Global::deleteMemcache(array($keyCache));

        $this->getDetailPlayerRatingTerm($playerId);
    }

    /**
     * Get list top players by season
     * @param type $seasonId
     * @return array|boolean
     * @author PhongTX
     */
    public function getListTopPlayers($seasonId)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_player_champion_by_season, array($seasonId));

        // Read from cache
        $arrTopPlayers = $memcacheInstance->read($keyCache);
        // Miss cache
        if ($arrTopPlayers === FALSE)
        {
            try
            {
                // Get application's configuation
                $config = Thethao_Global::getApplicationIni();

                // Get mysql object access player
                $playerMysqlObj = $this->factory('Player', $config['database']['default']['adapter']);

                // Get data from db
                $arrTopPlayers = $playerMysqlObj->getListTopPlayers($seasonId);

                // Write to cache
                $memcacheInstance->write($keyCache, $arrTopPlayers);
                Thethao_Global::writeMemcache($keyCache, $arrTopPlayers);
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex, 1);
            }
        }
        return $arrTopPlayers;
    }

    /**
     * Get list players by team
     * @param int $teamId
     * @return array
     * @author ThuyNT
     */
    public function getListPlayersByTeam($teamId)
    {
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf($this->_player_by_team, array($teamId));

        // Read from cache
        $arrListPlayers = $memcacheInstance->read($keyCache);
        // Miss cache
        if ($arrListPlayers === FALSE)
        {
            try
            {
                // Get application's configuation
                $config = Thethao_Global::getApplicationIni();

                // Get mysql object access player
                $playerMysqlObj = $this->factory('Player', $config['database']['default']['adapter']);

                // Get data from db
                $arrListPlayers = $playerMysqlObj->getListPlayersByTeam($teamId);
                if(!empty($arrListPlayers))
                {
                    // Write to cache
                    $memcacheInstance->write($keyCache, $arrListPlayers);
                    Thethao_Global::writeMemcache($keyCache, $arrListPlayers);
                }
                else
                {
                    // Store into cache
                    $memcacheInstance->write($keyCache, -1);
                    Thethao_Global::writeMemcache($keyCache, -1);
                }
            }
            catch (Exception $ex)
            {
                Thethao_Global::sendlog($ex, 1);
            }
        }
        else if ($arrListPlayers == -1)
        {
            $arrListPlayers = array();
        }
        return $arrListPlayers;
    }

    public function rewriteListPlayersByTeam($arrParams)
    {
        $teamId = intval($arrParams['TeamID']);

        // Delete cache player award
        $keyCache = vsprintf($this->_player_by_team, array($teamId));
        Thethao_Global::deleteMemcache(array($keyCache));

        $this->getListPlayersByTeam($teamId);

        if (isset($arrParams['arrPlayerId']) && !empty($arrParams['arrPlayerId']))
        {

			$atrunk = array_chunk($arrParams['arrPlayerId'], 5);

			foreach ($atrunk as $trunk)
			{
				// call object framework to detete cache
				$modelObject = new Fpt_Data_Materials_Object();
				foreach ($trunk as $playerId)
				{
					if ($playerId > 0) {
						//update cache for player (id player, status to getdetail)
						$modelObject->getPlayer()->updateObject($playerId, true);
					}
				}
				//close resource
                Fpt_Data_Model::_destruct();
			}
        }
    }

    /**
     * rewrite player champion
     * @param type $seasonId
     * @return array|boolean
     * @author PhongTX
     */
    public function rewritePlayerChampion($seasonId)
    {
        try
        {
            $keyCache = vsprintf($this->_player_champion_by_season, array($seasonId));
            //Delete memcache
            Thethao_Global::deleteMemcache(array($keyCache));
            $this->getListTopPlayers($seasonId);
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
    }

}

?>