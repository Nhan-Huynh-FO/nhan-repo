<?php

/**
 * @name        :   Thethao_Business_Sport_Adapter_Redis
 * @author      :   HungNT1
 * @copyright   :   Fpt Online
 * @return      :   Thethao_Business_Sport_Adapter_Redis
 */
class Thethao_Business_Player_Adapter_Redis
{
    /**
     * @var system_model Singleton instance
     */
    protected static $_instance;
    //list redis key
    protected $_team_match_league;

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
        $this->_team_match_league = $key['football_team_match_league'];

        unset($key);
    }

    /**
     * Get singletom instance
     * @return Thethao_Business_Article_Adapter_Redis
     * @author HungNT1
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
     * Get list league and season by team attend
     * @param int $teamID
     * @return array|boolean
     * @author QuangTM
     */
    public function getListLeagueAndSeasonTeamAttend($teamID)
    {
        $result = array();
        try
        {
            // Get redis instance
            $redisInstance = Thethao_Global::getRedis('article');

            // Get key cache
            $pattern = vsprintf($this->_team_match_league, array($teamID, '*'));

            // Get array key
            $arrKeys = $redisInstance->keys($pattern);

            // Pattern for get league
            $pattern = '/' . str_replace('*', '(\d+)', $pattern) . '/';
            $match   = null;
            foreach ($arrKeys as $key)
            {
                if (preg_match($pattern, $key, $match))
                {
                    $result[$match[1]] = $redisInstance->sMembers($key);
                }
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
        return $result;
    }
}