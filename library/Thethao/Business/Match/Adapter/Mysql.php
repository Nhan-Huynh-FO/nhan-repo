<?php

/**
 * @author      :   HungNT1
 * @name        :   Thethao_Business_Football_Adapter_Mysql
 * @copyright   :   Fpt Online
 * @todo        :   Article business
 * @return      : Thethao_Business_Football_Adapter_Mysql
 */
class Thethao_Business_Match_Adapter_Mysql
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
     * @return Thethao_Business_Match_Adapter_Mysql
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
     * Get matches from begin date to end date
     * @param int $seasonId
     * @param int $leagueId
     * @param int $beginDate
     * @param int $endDate
     * @return array|bool
     */
    public function getMatchInWeek($seasonId, $leagueId, $beginDate, $endDate)
    {
        try
        {
            // Get DB instance
            $objDB = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $objDB->prepare('Call sp_code_sport_getMathScoreByTime(:p_season_id, :p_league_id, :p_start, :p_end);');

            // Bind params
            $stmt->bindParam('p_season_id', $seasonId, PDO::PARAM_INT);
            $stmt->bindParam('p_league_id', $leagueId, PDO::PARAM_INT);
            $stmt->bindParam('p_start', $beginDate, PDO::PARAM_INT);
            $stmt->bindParam('p_end', $endDate, PDO::PARAM_INT);

            // Excute
            $stmt->execute();

            // Fetch to get result
            $result = array();
            $matchFormat = new Thethao_Business_Match_Metadata_Match();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                array_push($result, $matchFormat->init($row)->getFormatedData());
            }

            // Release variables
            unset($stmt, $matchFormat);

            // Return result
            return $result;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
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
        try
        {
            $sport_m = Thethao_Global::getDB('sport', 'slave');

            // Prepare query
            $stmt = $sport_m->prepare('CALL sp_sport_getMatchByLeagueSeasonTeam(:p_leagueid, :p_seasonid, :p_teamid)');

            // Bind params
            $stmt->bindParam('p_leagueid', $leagueID, PDO::PARAM_INT);
            $stmt->bindParam('p_seasonid', $seasonID, PDO::PARAM_INT);
            $stmt->bindParam('p_teamid', $teamID, PDO::PARAM_INT);

            //Fetch result
            $stmt->execute();
            $result = array();
            while ($row = $stmt->fetch())
            {
                array_push($result, $row['match_id']);
            }

            //Close cursor
            $stmt->closeCursor();

            //Release
            unset($stmt);

            return $result;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
    }

    /**
     * Get list match ids by happen time
     * @param int $begin
     * @param int $end
     * @return array|boolean
     * @author QuangTM
     */
    public function getListMatchesByHappenTime($begin, $end, $leagueID = NULL, $gmt = 0, $flag= FALSE)
    {
        try
        {
            if(!$flag)
            {
                $begin -= $gmt * 3600;
                $end -= $gmt * 3600;
            }



            // Get DB instance
            $objDB = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $objDB->prepare('Call sp_fesport_getMatchByHappenTime(:p_begindate, :p_enddate, :p_league_id);');

            // Bind params
            $stmt->bindParam('p_begindate', $begin, PDO::PARAM_INT);
            $stmt->bindParam('p_enddate', $end, PDO::PARAM_INT);
            if (empty($leagueID))
                $stmt->bindParam('p_league_id', $leagueID, PDO::PARAM_NULL);
            else
                $stmt->bindParam('p_league_id', $leagueID, PDO::PARAM_INT);

            // Excute
            $stmt->execute();

            // Fetch to get result
            $result = array();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                if($flag)
                {
                    $result[$row['match_id']] = $row['datetime_happen'];
                }
                else
                {
                    array_push($result, $row['match_id']);
                }
            }

            // Release variables
            unset($stmt);

            // Return result
            return $result;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
    }

    /**
     * Get match statistic by team and match id
     * @param int $teamID
     * @param int $matchID
     * @return array|boolean
     */
    public function getMatchStatistic($teamID, $matchID)
    {
        // Set result
        $result = array();

        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_sport_getMatchStatistic(:p_teamid, :p_matchid);');


            // Bind param
            $stmt->bindParam('p_teamid', $teamID, PDO::PARAM_INT);
            $stmt->bindParam('p_matchid', $matchID, PDO::PARAM_INT);

            // Execute
            $stmt->execute();

            // Get format data
            $formatInstance = new Thethao_Business_Match_Metadata_MatchStatistic();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[$row['report_type_id']] = $formatInstance->init($row)->getFormatedData();
            }

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
     * Get all report types
     * @return boolean|array
     * @author QuangTM
     */
    public function getAllReportType()
    {
        // Set result
        $result = array();
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_sport_getReportType();');

            // Execute
            $stmt->execute();

            // Get format data
            $formatInstance = new Thethao_Business_Match_Metadata_ReportType();

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
     * MatchRelate - Get data for diem tin
     * @param int $matchID
     * @return array
     * @author QuangTM
     */
    public function getDiemTin($matchID)
    {
        // Get full
        $result = $this->__getMatchRelateByMatchID($matchID);

        // Get format instance
        $formatInstance = new Thethao_Business_Match_Metadata_MatchRelate();

        // Get formated data for diem tin
        $result = $formatInstance->init($result)->getFormatedDataForDiemTin();

        // Release resource
        unset($formatInstance);

        return $result;
    }

    /**
     * MatchRelate - Get data for tuong thuat
     * @param int $matchID
     * @return array
     * @author QuangTM
     */
    public function getTuongThuat($matchID)
    {
        // Get full
        $result = $this->__getMatchRelateByMatchID($matchID);

        // Get format instance
        $formatInstance = new Thethao_Business_Match_Metadata_MatchRelate();

        // Get formated data for diem tin
        $result = $formatInstance->init($result)->getFormatedDataForTuongThuat();

        // Release resource
        unset($formatInstance);

        return $result;
    }

    /**
     * Match: getReport match predict
     * @param int $intMatchID
     * @return array
     * @author TrongNV
     */
    public function getReportMatchPredict($intMatchID)
    {
        //set result
        $result = array();
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_sport_getReportPredictMatch(:p_matchid);');

            // Bind param
            $stmt->bindParam('p_matchid', $intMatchID, PDO::PARAM_INT);
            // Execute
            $stmt->execute();
            while ($row = $stmt->fetch())
            {
                if ($row['result_code'] == 3)
                    $result[0] = $row['num_user'];
                else if ($row['result_code'] == 0)
                    $result[1] = $row['num_user'];
                else
                    $result[2] = $row['num_user'];
            }
            ksort($result);
            $stmt->closeCursor();
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
        //Return array
        return $result;
    }

    /**
     * Match: Get top match predict
     * @param int $match_id and $limit
     * @return array|bool
     * @author TrongNV
     */
    public function getTopMatchPredict($intMatchID)
    {
        try
        {
            $limit = NULL;
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_sport_getTopPreditionMatch(:p_matchid, :p_limit);');

            // Bind param
            $stmt->bindParam('p_matchid', $intMatchID, PDO::PARAM_INT);
            $stmt->bindParam('p_limit', $limit);
            // Execute
            $stmt->execute();
            $result = $stmt->fetchAll();
            $stmt->closeCursor();

            //Return array
            return $result;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex, 1);
        }
    }

    /**
     * MatchBetrate: Get full data from DB
     * @param int $seasonID, $leagueID
     * @return array
     * @author Dungdv2
     */
    public function getMatchBetrateByIDs(array $arrMatchIDs)
    {
        // Set result
        $result = array();
        try
        {
            // Convert to string
            $strMatchIDs = implode(',', $arrMatchIDs);

            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');
            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_sport_getMatchBetrate(:p_matchid);');
            // Bind param
            $stmt->bindParam('p_matchid', $strMatchIDs, PDO::PARAM_STR);
            // Execute
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                $result[$row['match_id']] = $row;
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
     * Insert match predict
     * @param array $arrMatchPredict
     * @return bool
     * @author HungNT1
     */
    public function insertMatchPredict($params)
    {
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'master');
            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_sport_insertPredictMatch(:p_matchid, :p_mobile, :p_userid, :p_goalteam1, :p_goalteam2, :p_numpeople,:p_codepre,:p_type);');
            // Bind param
            $stmt->bindParam('p_matchid', $params['match_id'], PDO::PARAM_INT);
            $stmt->bindParam('p_mobile', $params['mobile'], PDO::PARAM_STR);
            $stmt->bindParam('p_userid', $params['userid'], PDO::PARAM_INT);
            $stmt->bindParam('p_goalteam1', $params['goal_team1'], PDO::PARAM_INT);
            $stmt->bindParam('p_goalteam2', $params['goal_team2'], PDO::PARAM_INT);
            $stmt->bindParam('p_numpeople', $params['numpeople'], PDO::PARAM_INT);
            $stmt->bindParam('p_codepre', $params['codepre'], PDO::PARAM_STR);
            $stmt->bindParam('p_type', $params['type'], PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();
            $stmt->closeCursor();
            //Return
            return $result['result'];
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
    }

    /**
     * MatchRelate - Get full data from DB
     * @param int $matchID
     * @return array
     * @author QuangTM
     */
    protected function __getMatchRelateByMatchID($matchID)
    {
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_getMatchRelateByMatchId(:p_matchid);');

            // Bind param
            $stmt->bindParam('p_matchid', $matchID, PDO::PARAM_INT);

            // Execute
            $stmt->execute();

            // Get result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt);
            return $result;
        }
        catch (Exception $ex)
        {
            Fpt_Data_Model::sendLog($ex);
            return FALSE;
        }
    }

    /**
     * Get extend of match detail
     * @param type $arrMatchId
     * @return boolean|array
     */
    public function getMatchExtendByIDs($arrMatchId)
    {

        $result = array();
        try
        {
            if (!is_array($arrMatchId) || !count($arrMatchId))
            {
                return FALSE;
            }
            $strMatchId = implode(',', $arrMatchId);
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_fesport_getDetailMatchByIDs(:p_matchids);');

            // Bind param
            $stmt->bindParam('p_matchids', $strMatchId, PDO::PARAM_STR);

            // Execute
            $stmt->execute();

            while ($row = $stmt->fetch())
            {
                if (!isset($result[$row['match_id']]))
                {
                    $result[$row['match_id']] = array();
                }
                $result[$row['match_id']] = Zend_Json::decode($row['event']);
            }

            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt);
        }
        catch (Exception $ex)
        {
            Fpt_Data_Model::sendLog($ex);
            return FALSE;
        }
        return $result;
    }

}
