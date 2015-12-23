<?php

/**
 * @author      :   HungNT1
 * @name        :   Thethao_Business_Football_Adapter_Mysql
 * @copyright   :   Fpt Online
 * @todo        :   Article business
 * @return      : Thethao_Business_Football_Adapter_Mysql
 */
class Thethao_Business_Team_Adapter_Mysql
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
     * Get team extent content
     * @param array $arrTeamIDs
     * @return array|boolean
     * @author QuangTM
     */
    public function getDetailTeamExtendByIDs(array $arrTeamIDs)
    {
        // Set result
        $result = array();
        try
        {
            // Convert to string
            $strTeamIDs = implode(',', $arrTeamIDs);

            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_getDetailTeam(:p_teamid);');
            // Bind param
            $stmt->bindParam('p_teamid', $strTeamIDs, PDO::PARAM_STR);

            // Execute
            $stmt->execute();

            // Get format data
            $formatInstance = new Thethao_Business_Team_Metadata_Team();

            while ($row = $stmt->fetch())
            {
                $result[$row['team_id']] = $formatInstance->init($row)->getFormatedTeamHistory();
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
     * Get list league and season by team attend
     * @param int $teamID
     * @return array|boolean
     * @author QuangTM
     */
    public function getListLeagueAndSeasonTeamAttend($teamID)
    {
        // Default result
        $result = array();
        try
        {
            // Get DB Obj
            $dbObj = Thethao_Global::getDB('sport', 'slave');

            // Prepare SQL
            $stmt = $dbObj->prepare('CALL sp_sport_getLeagueSeasonByTeam(:p_teamid);');

            // Bind param
            $stmt->bindParam('p_teamid', $teamID, PDO::PARAM_INT);

            // Execute
            $stmt->execute();

            while ($row = $stmt->fetch())
            {
                if (!isset($result[$row['league_id']]))
                    $result[$row['league_id']] = array();
                array_push($result[$row['league_id']], $row['season_id']);
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
        // Return result
        return $result;
    }
}
