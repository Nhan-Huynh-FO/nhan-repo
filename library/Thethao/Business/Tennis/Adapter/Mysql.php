<?php

/**
 * @copyright   Fpt Online
 * @version     20131119
 * @name        Thethao_Business_Tennis_Adapter_Mysql
 * @author      HungNT1
 * @return      Thethao_Business_Tennis_Adapter_Mysql
 */
class Thethao_Business_Tennis_Adapter_Mysql
{

    /**
     * @todo Mysql access to tennis_ranking table
     * Get Fpt_Business_Tennis_Adapter_Mysql's instance
     * @return Thethao_Business_Tennis_Adapter_Mysql
     * @author HungNT1
     */
    public static function getInstance()
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
     * Get tennis table ranking by year and gender
     * @param int $year
     * @param int $gender Only 0 or 1. 0 => woman (WTA), 1 => man (ATP)
     * @return array|boolean
     * @author QuangTM
     */
    public function getTennisTableRanking($year, $gender)
    {
        // Get result
        $result = array();
        try
        {
            // Validate int
            $validInt = new Zend_Validate_Int();

            // Validate input
            if (!$validInt->isValid($year) || !$validInt->isValid($gender))
            {
                return FALSE;
            }

            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_tennis_getTennisRanking(:p_gender, :p_year);');

            // Bind param
            $stmt->bindParam('p_gender', $gender, PDO::PARAM_INT);
            $stmt->bindParam('p_year', $year, PDO::PARAM_INT);

            // Execute
            $stmt->execute();

            // Get format data
            $formatInstance = new Thethao_Business_Tennis_Metadata_Ranking();

            while ($row = $stmt->fetch())
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
     * Get list statistics of tennis player by array player id
     * @param array $arrPlayerIDs
     * @return array|boolean
     * @author QuangTM
     */
    public function getTennisPlayerStatsByPlayerIDs($arrPlayerIDs)
    {
        // Set result
        $result = array();
        try
        {
            if (!is_array($arrPlayerIDs) || !count($arrPlayerIDs))
            {
                return FALSE;
            }

            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_tennis_getPlayerStat(:p_playerid);');

            // Bind param
            $stmt->bindParam('p_playerid', implode(',', $arrPlayerIDs), PDO::PARAM_STR);

            // Execute
            $stmt->execute();

            while ($row = $stmt->fetch())
            {
                if (!isset($result[$row['playerid']]))
                {
                    $result[$row['playerid']] = array();
                }
                array_push($result[$row['playerid']], $row);
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
     * Get full information about tennis match.<br />
     * Consist of tennis sets in matches, league, and season
     * @param array $arrMatchIDs list id of matches
     * @return array|boolean
     * @author QuangTM
     */
    public function getTennisMatches($arrMatchIDs)
    {
        // Get result
        $result = array();
        try
        {
            // Validate input paramter
            if (!is_array($arrMatchIDs) || !count($arrMatchIDs))
            {
                return FALSE;
            }

            // Convert array match id into string
            $strMatchID = implode(',', $arrMatchIDs);
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL to get tennis matches detail
            $stmt = $dbObj->prepare('CALL sp_tennis_getDetailTennisMatch(:p_matchid);');

            // Bind param
            $stmt->bindParam('p_matchid', $strMatchID, PDO::PARAM_STR);

            // Execute
            $stmt->execute();

            $arrLeagues = array();
            $arrSeasons = array();

            while ($row = $stmt->fetch())
            {
                // Get match detail
                $result[$row['match_id']] = $row;

                // Default set in match
                $result[$row['match_id']]['set'] = array();

                // Default league and season name for this match
                $result[$row['match_id']]['league_name'] = NULL;
                $result[$row['match_id']]['season_name'] = NULL;

                // Push array league and season
                array_push($arrSeasons, $row['season_id']);
                array_push($arrLeagues, $row['league_id']);
            }

            // Unique league and season and convert them into string
            $strSeasons = implode(',', array_unique($arrSeasons));
            $strLeagues = implode(',', array_unique($arrLeagues));

            // Close
            $stmt->closeCursor();

            // Prepare SQL to get tennis sets by match
            $stmt = $dbObj->prepare('CALL sp_tennis_getTennisSet(:p_matchid);');

            // Bind param
            $stmt->bindParam('p_matchid', $strMatchID, PDO::PARAM_STR);

            // Execute
            $stmt->execute();

            while ($row = $stmt->fetch())
            {
                $result[$row['match_id']]['set'][$row['set_id']] = $row;
            }

            // Close
            $stmt->closeCursor();

            $strLeagues = !empty($strLeagues) ? $strLeagues : null;

            // Prepare SQL to get tennis leagues
            $stmt = $dbObj->prepare('CALL sp_tennis_getTennisLeague(:p_leagueid);');

            // Bind param
            $stmt->bindParam('p_leagueid', $strLeagues, PDO::PARAM_STR);

            // Execute
            $stmt->execute();

            // Default result league has got
            $arrLeagues = array();

            while ($row = $stmt->fetch())
            {
                $arrLeagues[$row['league_id']] = $row['league_name'];
            }

            // Close
            $stmt->closeCursor();

            // Prepare SQL to get tennis leagues
            $stmt = $dbObj->prepare('CALL sp_tennis_getTennisLeague(:p_leagueid);');

            // Bind param
            $stmt->bindParam('p_leagueid', $strLeagues, PDO::PARAM_STR);

            // Execute
            $stmt->execute();

            // Default result league has got
            $arrLeagues = array();

            while ($row = $stmt->fetch())
            {
                $arrLeagues[$row['league_id']] = $row['league_name'];
            }

            // Close
            $stmt->closeCursor();

            // Default result league has got
            $arrSeasons = array();

            if (!empty ($strSeasons))
            {
                // Prepare SQL to get tennis leagues
                $stmt = $dbObj->prepare('CALL sp_tennis_getTennisSeasonByID(:p_seasonid);');

                // Bind param
                $stmt->bindParam('p_seasonid', $strSeasons, PDO::PARAM_STR);

                // Execute
                $stmt->execute();



                while ($row = $stmt->fetch())
                {
                    $arrSeasons[$row['season_id']] = $row['name_season'];
                }
            }
            // Close
            $stmt->closeCursor();

            // Release variables
            unset($stmt);

            // By league name into match info
            foreach ($result as $matchId => $matchInfo)
            {
                $result[$matchId]['league_name'] = $arrLeagues[$result[$matchId]['league_id']];
                $result[$matchId]['season_name'] = $arrSeasons[$result[$matchId]['season_id']];
            }

        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex, 1);
        }
        return $result;
    }

}