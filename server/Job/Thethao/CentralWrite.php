<?php
class Job_Thethao_CentralWrite
{
    public function updateMatchByFan($params)
    {
        // Default response
        $response = NULL;

        try
        {
            // Get config application
            $config = Thethao_Global::getApplicationIni();

            // Get team_match object
            $matchMysqlObj = $this->factory('Match', $config['database']['default']['adapter']);
            // Get match infos fan
            $arrMatchFan    = $matchMysqlObj->getFanByMatch($params['matchId']);

            //Update
            if ( isset($arrMatchFan['match_id']) && is_numeric($arrMatchFan['match_id']) && $arrMatchFan['match_id']>0 )
            {
                if($params['likeType'] == 0)
                {
                    $score = -1;
                }
                else
                {
                    $score = 1;
                }
                // Params update
                $paramUpdate = array('matchId' => $params['matchId'],
                    'fanteam_1' => ($params['fanLikeTeam'] == 1) ? $arrMatchFan['fan_team1'] + $score : $arrMatchFan['fan_team1'],
                    'fanteam_2' => ($params['fanLikeTeam'] == 2) ? $arrMatchFan['fan_team2'] + $score : $arrMatchFan['fan_team2']
                );

                if ( $matchMysqlObj->updateMatchByFan($paramUpdate) )
                {
                    $keyCache = Thethao_Global::makeCacheKey(Thethao_Model_Match::EURO2012_MATCH_LIKE, array($params['matchId']));
                    Thethao_Global::deleteMemcache(array($keyCache));
                }//end if
            }//end if

        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }

        return $response;
    }
}