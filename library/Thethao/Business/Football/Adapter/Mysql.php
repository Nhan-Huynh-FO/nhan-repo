<?php

/**
 * @author      :   HungNT1
 * @name        :   Thethao_Business_Football_Adapter_Mysql
 * @copyright   :   Fpt Online
 * @todo        :   Article business
 * @return      : Thethao_Business_Football_Adapter_Mysql
 */
class Thethao_Business_Football_Adapter_Mysql
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
     * @return Thethao_Business_Football_Adapter_Mysql
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
     * Get list league by list ids
     * @param array $arrLeagueIDs
     * @return array|bool
     * @author QuangTM
     */
    public function getListLeagueByIDs ($arrLeagueIDs)
    {
        try
        {
            // Convert from array to string
            $strLeagueIds = implode(',', $arrLeagueIDs);

            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_fesport_getListLeaguesByIDs(:p_leagueids);');

            // Bind param
            $stmt->bindParam('p_leagueids', $strLeagueIds, PDO::PARAM_STR);

            // Execute
            $stmt->execute();

            // Get result
            $result = array ();

            // Get format data
            $formatInstance = new Thethao_Business_Football_Metadata_League();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                array_push($result, $formatInstance->init($row)->getFormatedData());

            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt, $strLeagueIds, $formatInstance);

            return $result;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
    }

    /**
     * Get all list leagues
     * @return aray|bool
     * @author QuangTM
     */
    public function getLeagueAll()
    {
        // Get result
        $result = array ();
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_fesport_getListLeagues();');

            // Execute
            $stmt->execute();

            // Get format data
            $formatInstance = new Thethao_Business_Football_Metadata_League();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                array_push($result, $formatInstance->init($row)->getFormatedData());

            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt, $formatInstance);

        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
        return $result;
    }

    /**
     * Get list season by league
     * @param int $leagueID
     * @return array|bool
     * @author QuangTM
     */
    public function getListSeasonByLeagueID($leagueID)
    {
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_fesport_getListSeasonByLeague(:p_leagueid);');

            // Bind param
            $stmt->bindParam('p_leagueid', $leagueID, PDO::PARAM_INT);

            // Execute
            $stmt->execute();

            // Get result
            $result = array();

            // Get format data
            $formatInstance = new Thethao_Business_Football_Metadata_Season();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                array_push($result, $formatInstance->init($row)->getFormatedData());

            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt, $formatInstance);

            return $result;
        }
        catch (Exception $ex)
        {
            Fpt_Log::sendlog(4, $ex->getCode(), $ex->getMessage(), $ex->getTraceAsString(), null, null);
            return FALSE;
        }
    }
    /**
     * Get list season by ids
     * @param array $arrSeasonIDs
     * @return array|bool
     * @author QuangTM
     */
    public function getListSeasonByIDs($arrSeasonIDs)
    {
        try
        {
            // Convert from array to string
            $strSeasonIds = implode(',', $arrSeasonIDs);

            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');
            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_fesport_getListSeasonByIDs(:p_seasonids);');

            // Bind param
            $stmt->bindParam('p_seasonids', $strSeasonIds, PDO::PARAM_STR);

            // Execute
            $stmt->execute();

            // Get result
            $result = array();

            // Get format data
            $formatInstance = new Thethao_Business_Football_Metadata_Season();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
                array_push($result, $formatInstance->init($row)->getFormatedData());

            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt, $strSeasonIds, $formatInstance);

            return $result;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
    }

    /**
     *
     * @param type $seasonId
     * @param type $leagueId
     * @return type
     */
    public function getListTableRanking($seasonId, $leagueId)
    {
        // Set to get result
        $result = array();
        try
        {
            // Get DB instance
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('Call sp_getBangXepHang(:p_league_id, :p_season_id);');

            // Bind params
            $stmt->bindParam('p_league_id', $leagueId, PDO::PARAM_INT);
            $stmt->bindParam('p_season_id', $seasonId, PDO::PARAM_INT);

            // Excute
            $stmt->execute();


            $teamSeasonFormat = new Thethao_Business_Football_Metadata_TeamSeason();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[$row['team_id']] = $teamSeasonFormat->init($row)->getFormatedData();
            }

            // Release variables
            unset($stmt, $teamSeasonFormat);
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
        // Return result
        return $result;
    }

    /**
     * Get list league by group
     * @param int $group
     * @return array|boolean
     * @author QuangTM
     */
    public function getListLeagueByGroup ($group)
    {
        // Get result
        $result = array();
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_fe_sport_getLeagueByGroup(:p_group);');

            // Bind param
            $stmt->bindParam('p_group', $group, PDO::PARAM_INT);

            // Execute
            $stmt->execute();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[$row['league_id']] = $row['name'];
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
}
