<?php

/**
 * LogAnalysis Job
 *
 * @author tamhv@fpt.net
 */
class Job_Thethao_JobCache
{

    /**
     * Job send error message
     *
     */
    public function writeArticleCache($params)
    {
        // Return response
        return ;
    }

    /**
     * Clear memcached about matches
     * @param array $params
     * @return array
     * @author QuangTM
     */
    public function clearCacheMatch($params)
    {
        // Get params
        $arrMatchesID  = $params['arrMatchesID'];
        $arrHappenDate = $params['arrHappenDate'];
        try
        {
            // Delete cache each match
            $arrKeyCache = array();
            foreach ($arrMatchesID as $matchID)
            {
                $arrKeyCache[] = Thethao_Global::makeCacheKey(Thethao_Model_Match::MATCH_DETAIL, $matchID);
            }
            Thethao_Global::deleteMemcache($arrKeyCache);
            foreach ($arrHappenDate as $leagueID => $arrDateHappen)
            {
                $arrKeyCache = array();
                foreach ($arrDateHappen as $dateHappen)
                {
                    $arrKeyCache[] = Thethao_Global::makeCacheKey(Thethao_Model_Match::MATCHES_TIME, array ($dateHappen, intval($leagueID)));
                }
                Thethao_Global::deleteMemcache($arrKeyCache);
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
        return;
    }

    /**
     * Clear cache team-season (table ranking)
     * @param array $params
     * @return array
     * @author QuangTM
     */
    public function clearCacheRanking($params)
    {
        // Get params
        $seasonID = $params['seasonID'];
        $leagueID = $params['leagueID'];

        try
        {
            $keyCache = Thethao_Global::makeCacheKey(Thethao_Model_TeamSeason::TEAM_SEASON_RANKING, array($leagueID, $seasonID));
            Thethao_Global::deleteMemcache(array($keyCache));
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
        return ;
    }

    /**
     * Clear cache top player
     * @param array $params
     * @return array
     * @author QuangTM
     */
    public function clearCacheTopPlayer($params)
    {
        // Get params
        $seasonID = $params['seasonID'];
        try
        {
            $keyCache         = Thethao_Global::makeCacheKey(Thethao_Model_PlayerChampion::PLAYER_CHAMPION_BY_SEASON, $seasonID);
            Thethao_Global::deleteMemcache(array($keyCache));
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
        return ;
    }

}
