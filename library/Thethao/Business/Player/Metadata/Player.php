<?php

/**
 * @copyright       : FOSP
 * @version         : 20120323
 * @todo            : Player Champion format
 * @name            : Player
 * @author          : QuangTM
 */
class Thethao_Business_Player_Metadata_Player
{

    /**
     *
     * @var int
     */
    private $_championId;

    /**
     *
     * @var int
     */
    private $_playerId;

    /**
     *
     * @var string
     */
    private $_playerName;

    /**
     *
     * @var string
     */
    private $_playerImage;

    /**
     *
     * @var string
     */
    private $_playerPosition;

    /**
     *
     * @var int
     */
    private $_seasionId;

    /**
     *
     * @var int
     */
    private $_teamId;

    /**
     *
     * @var string
     */
    private $_teamName;

    /**
     *
     * @var int
     */
    private $_goals;

    /**
     *
     * @var int
     */
    private $_penalties;

    /**
     *
     * @var int
     */
    private $_creationTime;

    /**
     *
     * @var int
     */
    private $_updateTime;

    /**
     *
     * @var int
     */
    private $_status;

    public function __construct ()
    {
        $this->_championId = 0;
        $this->_creationTime = 0;
        $this->_goals = 0;
        $this->_penalties = 0;
        $this->_playerId = 0;
        $this->_playerName = '';
        $this->_playerImage = '';
        $this->_playerPosition = '';
        $this->_seasionId = 0;
        $this->_status = 1;
        $this->_teamId = 0;
        $this->_teamName = '';
        $this->_updateTime = 0;
    }

    public function __destruct ()
    {
        unset($this->_championId, $this->_creationTime, $this->_goals, $this->_penalties, $this->_playerId, $this->_playerName, $this->_playerImage, $this->_playerPosition, $this->_seasionId, $this->_status, $this->_teamId, $this->_teamName, $this->_updateTime);
    }

    /**
     * Initialize data
     * @param array $entity
     * @return PlayerChampion 
     */
    public function init ($entity)
    {
        $this->_championId = intval($entity['champion_id']);
        $this->_creationTime = intval($entity['creation_time']);
        $this->_goals = intval($entity['goals']);
        $this->_penalties = intval($entity['penalties']);
        $this->_playerId = intval($entity['player_id']);
        $this->_playerName = !isset($entity['player_name']) ? NULL : $entity['player_name'];
        $this->_playerImage = !isset($entity['image']) ? NULL : $entity['image'];
        $this->_playerPosition = !isset($entity['position']) ? NULL : $entity['position'];
        $this->_seasionId = intval($entity['season_id']);
        $this->_status = intval($entity['status']);
        $this->_teamId = intval($entity['team_id']);
        $this->_teamName = !isset($entity['team_name']) ? NULL : $entity['team_name'];
        $this->_updateTime = intval($entity['update_time']);
        return $this;
    }

    /**
     * Get formated data
     * @return array
     * @author QuangTM
     */
    public function getFormatedData ()
    {
        return array (
            'ChampionID'     => $this->_championId,
            'PlayerID'       => $this->_playerId,
            'PlayerName'     => $this->_playerName,
            'PlayerImage'    => $this->_playerImage,
            'PlayerPosition' => $this->_playerPosition,
            'SeasonID'       => $this->_seasionId,
            'TeamID'         => $this->_teamId,
            'TeamName'       => $this->_teamName,
            'Goals'          => $this->_goals,
            'Penalties'      => $this->_penalties,
            'CreationTime'   => $this->_creationTime,
            'UpdateTime'     => $this->_updateTime,
            'Status'         => $this->_status
        );
    }

}