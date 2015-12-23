<?php

/**
 * @copyright   FOSP
 * @version     20121127
 * @todo        Redis access tennis match schedule
 * @name        Fpt_Business_Tennis_Adapter_Redis
 * @author      QuangTM
 */
class Thethao_Business_Tennis_Adapter_Redis
{
    /**
     *
     * @var Fpt_Business_Tennis_Adapter_Redis
     */
    protected static $instance;
    protected  $_tennis_matches;
    protected $_tennis_player_matches;


    /**
     * Constructor of class
     * @return Thethao_Business_Sport_Adapter_Redis
     * @author HungNT1
    */
    public function __construct()
    {
        //get key caching
        $key = Thethao_Global::getConfig('caching');
        //init keys redis related to this class
        $this->_tennis_matches = $key['tennis_matches'];
        $this->_tennis_player_matches = $key['tennis_player_matches'];

        unset($key);
    }
    /**
     * Get Fpt_Business_Tennis_Adapter_Redis's instance
     * @return Fpt_Business_Tennis_Adapter_Redis
     * @author QuangTM
     */
    public static function getInstance()
    {
        if (!isset(self::$instance) || !self::$instance instanceof self)
        {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Insert tennis matches into some key redis
     * @param array $matchInfo
     * @return boolean
     * @author QuangTM
     */
    public function insertTennisMatches($matchInfo)
    {
        try
        {
            // Get redis instance
            $redisInstance = Thethao_Global::getRedis('object');

            // Get key redis for adding match by team
            $keyTennisMatches       = vsprintf($this->_tennis_matches, '');
            $keyTennisPlayerMatches = vsprintf($this->_tennis_player_matches, '');
            $keyTennisPlayer1a      = $keyTennisPlayerMatches . $matchInfo['player1a'];
            $keyTennisPlayer2a      = $keyTennisPlayerMatches . $matchInfo['player2a'];
            $keyTennisPlayer1b      = $matchInfo['player1b'] != 0 ? $keyTennisPlayerMatches . $matchInfo['player1b'] : NULL;
            $keyTennisPlayer2b      = $matchInfo['player2b'] != 0 ? $keyTennisPlayerMatches . $matchInfo['player2b'] : NULL;

            // Add multi tasks for redis
            $redisInstance->zRem($keyTennisMatches, $matchInfo['match_id']);
            $redisInstance->zAdd($keyTennisMatches, $matchInfo['datetime_happen'], $matchInfo['match_id']);

            $redisInstance->zRem($keyTennisPlayer1a, $matchInfo['match_id']);
            $redisInstance->zAdd($keyTennisPlayer1a, $matchInfo['datetime_happen'], $matchInfo['match_id']);

            $redisInstance->zRem($keyTennisPlayer2a, $matchInfo['match_id']);
            $redisInstance->zAdd($keyTennisPlayer2a, $matchInfo['datetime_happen'], $matchInfo['match_id']);

            if ($keyTennisPlayer1b != NULL && $keyTennisPlayer2b != NULL)
            {
                $redisInstance->zRem($keyTennisPlayer1b, $matchInfo['match_id']);
                $redisInstance->zAdd($keyTennisPlayer1b, $matchInfo['datetime_happen'], $matchInfo['match_id']);

                $redisInstance->zRem($keyTennisPlayer2b, $matchInfo['match_id']);
                $redisInstance->zAdd($keyTennisPlayer2b, $matchInfo['datetime_happen'], $matchInfo['match_id']);
            }
            // Execute
            $redisInstance->exec();

            return TRUE;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
    }

    /**
     * Get tennis match ids from date to date
     * @param int $fromDate unix timestamp
     * @param int $toDate unix timestamp
     * @return array|boolean
     * @author QuangTM
     */
    public function getTennisSchedule($fromDate, $toDate)
    {
        try
        {
            //get instance redis
            $redisInstance = Thethao_Global::getRedis('article');
            // Get key redis for adding match by team
            $keyTennisMatches = vsprintf($this->_tennis_matches, '');

            // Get array match id
            return $redisInstance->zRevRangeByScore($keyTennisMatches, $toDate, $fromDate, array(
                    'withscores' => TRUE,
                ));
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
    }

    /**
     * Get matches id which player attends
     * @param int $playerID
     * @param int $fromDate unix timestamp
     * @param int $toDate unix timestamp
     * @param boolean $reverse
     * @param int $limit
     * @param int $offset
     * @return array|boolean
     * @author QuangTM
     */
    public function getMatchesPlayerAttend($playerID, $fromDate, $toDate, $reverse = TRUE, $limit = 20, $offset = 0)
    {
        try
        {
            // Get redis instance
            $redisInstance = Thethao_Global::getRedis('article');

            // Get key redis for adding match by team
            // Get key cache
            $keyTennisMatches = vsprintf($this->_tennis_player_matches, $playerID);

            // Get array match id
            $arrMatchesID = array();
            if ($reverse === TRUE)
            {
                $arrMatchesID = $redisInstance->zRevRangeByScore($keyTennisMatches, $toDate, $fromDate, array(
                    'withscores' => TRUE,
                    'limit'      => array($offset, $limit),
                    )
                );
            }
            else
            {
                $arrMatchesID = $redisInstance->zRangeByScore($keyTennisMatches, $fromDate, $toDate, array(
                    'withscores' => TRUE,
                    'limit'      => array($offset, $limit),
                    )
                );
            }

            // Return result
            return $arrMatchesID;
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
    }
}