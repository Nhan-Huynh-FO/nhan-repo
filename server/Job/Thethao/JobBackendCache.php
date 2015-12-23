<?php

/**
 * @copyright   Fpt Online
 * @version     20120614
 * @todo        Manipulate backend cache. Re-write cache for Footbal
 * @name        JobBackendCache
 * @author      QuangTM
 */
class Job_Thethao_JobBackendCache
{

    /**
     * Rewrite match's cache football
     * @param array $params
     * @author QuangTM
     */
    public function rewriteMatchCache($params)
    {
        if (!empty($params))
        {
            // Init model Footbal
            $modelFootball = new Thethao_Model_Match();
            $modelFootball->rewriteMatchCache($params);
        }
    }
    /**
     * Rewrite match's cache football Live
     * @author HungNT
     * @param type $params
     */
    public function rewriteMatchLive($params)
    {
        if (!empty($params))
        {
            // Init model Footbal
            $modelFootball = new Thethao_Model_Match();
            $modelFootball->rewriteMatchLive($params);

            //init news instance
            $caching = Thethao_Model_Caching::getInstance();
            $caching->clearCacheFile();
        }
    }

    /**
     * Rewrite BXH cache
     * @param array $params
     * @author QuangTM
     */
    public function rewriteBXHCache($params)
    {
        if (!empty($params))
        {
            // Init model Footbal
            $modelFootball = new Thethao_Model_Football();
            $modelFootball->rewriteBXHCache($params);

            //init news instance
            $caching = Thethao_Model_Caching::getInstance();
            $caching->clearCacheFile();
        }
    }

    /**
     * Rewrite match_betrate cache
     * @param array $params
     * @author QuangTM
     */
    public function rewriteMatchBetrate($params)
    {
        // Init model Footbal
        $modelFootball = new Thethao_Model_Match();
        $modelFootball->rewriteMatchBetrate($params);
    }

    public function rewritePlayerChampion($params)
    {
        if (!empty($params))
        {
            $modelFootball = new Thethao_Model_Player();
            $modelFootball->rewritePlayerChampion($params['season']);

            //init news instance
            $caching = Thethao_Model_Caching::getInstance();
            $caching->clearCacheFile();
        }
    }

    public function rewritePlayerCache($params)
    {
        $playerID = intval($params['PlayerID']);
        $updateType = $params['UpdateType'];

        if ($playerID)
        {
            switch ($updateType)
            {
                case 'player-rating-term':
                    $playerModel = new Thethao_Model_Player();
                    $playerModel->rewritePlayerRatingTerm($playerID);
                    break;
                default:
                    break;
            }
            //init news instance
            $caching = Thethao_Model_Caching::getInstance();
            $caching->clearCacheFile();
        }
    }

    public function rewriteTeamCache($params)
    {
        $teamID = intval($params['TeamID']);
        $updateType = $params['UpdateType'];
        if ($teamID)
        {
            switch ($updateType)
            {
                case 'team-extend':
                    // Init model Footbal
                    $modelTeam = new Thethao_Model_Team();
                    $modelTeam->rewriteTeamCache($params);
                    break;
                case 'player-of-team':
                    // Init model Player of Footbal
                    $modelFootball = new Thethao_Model_Player();
                    $modelFootball->rewriteListPlayersByTeam($params);
                default:
                    break;
            }
        }
        //init news instance
        $caching = Thethao_Model_Caching::getInstance();
        $caching->clearCacheFile();
    }

    public function deleteMatch($params)
    {
        // Init model Footbal
        $modelFootball = new Thethao_Model_Match();
        $modelFootball->deleteMatch($params);
    }

    public function insertMatchPredict($params)
    {
        //Insert predict
        $modelFootball = new Thethao_Model_Match();
        $arrReponse = $modelFootball->insertMatchPredict($params);

        return $arrReponse;
    }

    /**
     * @copyright   Fpt Online
     * @version     20121122
     * @author      HungNT1
     */
    public function rewritePlayerAwardCache($params)
    {
        // Init model Footbal
        $modelPlayer = new Thethao_Model_Player();
        $modelPlayer->rewritePlayerAwardCache($params);
    }

    /**
     * Delete cache tranfer of player football
     * @param array $arrParams - id of player bootball
     * @author HungNT1
     */
    public function rewritePlayerTranferCache($arrParams)
    {
        // Init model Footbal
        $modelFootball = new Thethao_Model_Player();
        $modelFootball->rewritePlayerTranferCache($arrParams);
    }

    /**
     * Rewirte cache player of team
     * @author HungNT1
     * @param type $arrParams - array('teamId'=>arrayPlayerId)
     */
    public function rewritePlayerTeam($arrParams)
    {
        if (!empty($arrParams))
        {
            $params = array();
            foreach ($arrParams as $teamId => $arrPlayerId)
            {
                // Init model Footbal
                $modelFootball = new Thethao_Model_Player();

                $params['TeamID'] = $teamId;
                $params['arrPlayerId'] = $arrPlayerId;
                $modelFootball->rewriteListPlayersByTeam($params);

                //close resource
                Thethao_Global::closeResource();
                Fpt_Data_Model::_destruct();
            }
            //init news instance
            $caching = Thethao_Model_Caching::getInstance();
            $caching->clearCacheFile();
        }
    }


    /**
     * @author      : HungNT
     * call job clear cache keybox fe
     * @todo        : clearCacheKeybox
     * @param type $arrParams
     */
    public function clearCacheKeybox($params)
    {
        // Delete cache
        $keyCache = Thethao_Global::makeCacheKey($params['key_id']);
        if (!$keyCache)
        {
            $keyCache = $params['key_id'];
        }
        Thethao_Global::deleteMemcache(array($keyCache));
        // Get player model
        $model = new Thethao_Model_Block();
        $arrReturn = $model->getKeyBox($params);

        //init news instance
        $caching = Thethao_Model_Caching::getInstance();
        $caching->clearCacheFile();
        //return
        return $arrReturn;
    }

}
