<?php

/**
 * @copyright   FOSP
 * @version     20120827
 * @todo        Abstract access tennis_ranking table
 * @name        Fpt_Business_Tennis_Adapter_Abstract
 * @author      Dungdv2
 */
abstract class Thethao_Business_Tennis_Adapter_Abstract
{

    /**
     *
     * @var Fpt_Business_TennisRanking_Adapter_Abstract
     */
    protected static $instance;

    protected function __construct()
    {

    }

    /**
     * Get Fpt_Business_TennisRanking_Adapter_Abstract's instance
     * @return Fpt_Business_TennisRanking_Adapter_Abstract
     * @author Dungdv2
     */
    abstract public static function getInstance();

    /**
     * Get list player by team and match
     * @param int $gender
     * @param int $year
     * @return array|boolean
     * @author Dungdv2
     */
    public function getTennisRanking($gender, $year)
    {
        return FALSE;
    }

    /**
     * Get matches id by date (unixtime)
     * @param int $date
     * @return array
     */
    public function getTennisMatchByTime($fromDate, $toDate)
    {
        return array();
    }

    /**
     * Get matches detail by list id
     * @param array $arrMatchesID
     * @return array
     */
    public function getTennisMatchDetailByID($arrMatchesID)
    {
        return FALSE;
    }

    /**
     * Get tennis set by match id
     * @param array $arrMatchIds
     * @return array|boolean
     */
    public function getTennisSet($arrMatchIds)
    {
        return FALSE;
    }

    /**
     * Get tennis league by list id
     * @param array $arrLeagueIDs
     * @return array|boolean
     */
    public function getTennisLeagueByIDs($arrLeagueIDs)
    {
        return FALSE;
    }

    /**
     * Get list tennis player detail by array player id
     * @param array $arrPlayerIDs
     * @return array|boolean
     * @author QuangTM
     */
    public function getTennisPlayerByIDs($arrPlayerIDs)
    {
        return FALSE;
    }

    /**
     * Get list statistics of tennis player by array player id
     * @param array $arrPlayerIDs
     * @return array|boolean
     * @author QuangTM
     */
    public function getTennisPlayerStatsByPlayerIDs($arrPlayerIDs)
    {
        return FALSE;
    }

    /**
     * Get tennis table ranking by year and gender
     * @param int $year
     * @param int $gender
     * @return array|boolean
     * @author QuangTM
     */
    public function getTennisTableRanking($year, $gender)
    {
        return FALSE;
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
        return FALSE;
    }


}