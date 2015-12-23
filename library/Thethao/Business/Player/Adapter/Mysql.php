<?php

/**
 * @author      :   HungNT1
 * @name        :   Thethao_Business_Football_Adapter_Mysql
 * @copyright   :   Fpt Online
 * @todo        :   Article business
 * @return      : Thethao_Business_Football_Adapter_Mysql
 */
class Thethao_Business_Player_Adapter_Mysql
{

    protected static $_instance = null;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     * @author LamTX
     */
    public function __construct()
    {
        
    }

    /**
     * Get singletom instance
     * @return Thethao_Business_Player_Adapter_Mysql
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
     * Get list player by team and match
     * @param int $matchID
     * @param int $teamID
     * @return array|boolean
     * @author QuangTM
     */
    public function getPlayersTeamMatch($matchID, $teamID)
    {
        // Set result
        $result = array();
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_sport_getDetailPlayerTeamMatch(:p_teamid, :p_matchid);');

            // Bind param
            $stmt->bindParam('p_teamid', $teamID, PDO::PARAM_INT);
            $stmt->bindParam('p_matchid', $matchID, PDO::PARAM_INT);

            // Execute
            $stmt->execute();

            // Get format data
            $formatInstance = new Thethao_Business_Player_Metadata_PlayerTeamMatch();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[$row['player_id']] = $formatInstance->init($row)->getFormatedData();
            }

            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt);
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
        return $result;
    }

    /**
     * Get detail player by id
     * @param int $player_id, $type
     * @return array|boolean
     * @author QuangTM
     */
    public function getDetailPlayerByID($player_id, $type)
    {
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            $stmt = $dbObj->prepare('Call sp_sport_gePlayerAwardByType(:p_playerid,:p_type);');
            // bind params
            $stmt->bindParam('p_playerid', $player_id, PDO::PARAM_INT);
            $stmt->bindParam('p_type', $type, PDO::PARAM_INT);

            // execute
            $stmt->execute();

            //Fetch Result
            $result = $stmt->fetchAll();

            // Close cursor
            $stmt->closeCursor();

            // Release
            unset($stmt);

            //Return array
            return $result;
        }
        catch (Exception $ex)
        {
            Fpt_Data_Model::sendLog($ex);
            return FALSE;
        }
    }

    public function getInformationTransfer($player_id)
    {
        $result = array();
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            $stmt = $dbObj->prepare('Call sp_getTranferInfo(:p_playerid);');
            // bind params
            $stmt->bindParam('p_playerid', $player_id, PDO::PARAM_INT);

            // execute
            $stmt->execute();


            //Fetch Result
            $result = $stmt->fetchAll();

            // Close cursor
            $stmt->closeCursor();

            // Release
            unset($stmt);
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
        //Return array
        return $result;
    }

    public function getDetailPlayerRatingTerm($player_id)
    {
        //set Result
        $result = array(
            'data' => array(),
            'total' => 0
        );
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            $stmt = $dbObj->prepare('Call sp_getDetailPlayerRatingTerm(:p_playerid);');
            // bind params
            $stmt->bindParam('p_playerid', $player_id, PDO::PARAM_INT);

            // execute
            $stmt->execute();


            while ($row = $stmt->fetch())
            {
                $result['total'] += $row['rating_point'];
                array_push($result['data'], $row);
            }

            // Close cursor
            $stmt->closeCursor();

            // Release
            unset($stmt);
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
        //Return array
        return $result;
    }

    /**
     * Get list top players by season
     * @param type $seasonId
     * @return array|boolean 
     * @author PhongTX
     */
    public function getListTopPlayers($seasonId)
    {
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_fesport_getPlayerChampionBySeason(:p_season);');

            // Bind param
            $stmt->bindParam('p_season', $seasonId, PDO::PARAM_INT);

            // Execute
            $stmt->execute();

            // Get result
            $result = array();

            // Get format data
            $formatInstance = new Thethao_Business_Player_Metadata_Player();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                array_push($result, $formatInstance->init($row)->getFormatedData());
            }

            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt, $formatInstance);

            return $result;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
    }

    /**
     * Get list players by team
     * @param int $teamId
     * @return array
     * @author ThuyNT
     */
    public function getListPlayersByTeam($teamId)
    {
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_getListPlayersByTeam(:p_teamid);');

            // Bind param
            $stmt->bindParam('p_teamid', $teamId, PDO::PARAM_INT);

            // Execute
            $stmt->execute();

            // Get result
            $result = array();

            while ($row = $stmt->fetch())
            {
                $result[] = $row['player_id'];
            }
            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt);

            return $result;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
    }

}
