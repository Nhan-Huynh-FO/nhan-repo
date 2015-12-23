<?php

/**
 * @copyright   FOSP
 * @version     20120827
 * @todo        Format tennis_ranking data
 * @name        Fpt_Business_Tennis_Metadata_Tennis
 * @author      Dungdv2 
 */
class Fpt_Business_Tennis_Metadata_Tennis
{

    /**
     *
     * @var int
     */
    private $_gender;

    /**
     *
     * @var int
     */
    private $_year;

    /**
     *
     * @var int
     */
    private $_playerId;

    /**
     *
     * @var @var int
     */
    private $_rankingPosition;

    /**
     *
     * @var string
     */
    private $_playerName;

    /**
     *
     * @var int
     */
    private $_movement;
    
    /**
     *
     * @var int
     */
    private $_nationality;
    /**
     *
     * @var int
     */
    private $_updateTime;
    

    public function __construct()
    {
        $this->_gender = 0;
        $this->_year = 0;
        $this->_playerID = 0;
        $this->_rankingPosition = 0;
        $this->_playerName = '';   
        $this->_movement = 0;
        $this->_nationality = 0;
        $this->_rankingPoint = 0;
        $this->_updateTime = 0;
    }

    public function __destruct()
    {
        unset($this->_gender, $this->_year, $this->_playerID, $this->_rankingPosition, $this->_playerName,$this->_movement, $this->_nationality,$this->_rankingPoint,$this->_updateTime);
    }

    /**
     * Initialize data
     * @param array $entity
     * @return Fpt_Business_Tennis_Metadata_Tennis
     * @author Dungdv2
     */
    public function init(array $entity)
    {
        $this->_gender = isset($entity['gender']) ? intval($entity['gender']) : 0;
        $this->_year = isset($entity['year']) ? intval($entity['year']) : 0;
        $this->_playerID = isset($entity['player_id']) ? intval($entity['player_id']) : 0;
        $this->_rankingPosition = isset($entity['ranking_position']) ? intval($entity['ranking_position']) : 0;
        $this->_playerName = isset($entity['player_name']) ? $entity['player_name'] : '';
        $this->_movement = isset($entity['movement']) ? intval($entity['movement']) : 0;
        $this->_nationality = isset($entity['nationality']) ? intval($entity['nationality']) : 0;
        $this->_rankingPoint = isset($entity['ranking_point']) ? intval($entity['ranking_point']) : 0;
        $this->_updateTime = isset($entity['update_time']) ? intval($entity['update_time']) : 0;
        return $this;
    }

    /**
     * Get formated data
     * @return array
     * @author Dungdv2
     */
    public function getFormatedData()
    {
        return array(
            'Gender'    => $this->_gender,
            'Year'       => $this->_year,
            'PlayerID'   => $this->_playerID,
            'RankingPosition'   => $this->_rankingPosition,
            'PlayerName' => $this->_playerName,
            'Movement'      => $this->_movement,
            'Nationality'      => $this->_nationality,
            'RankingPoint'   => $this->_rankingPoint,
            'updateTime'   => $this->_updateTime
        );
    }

}