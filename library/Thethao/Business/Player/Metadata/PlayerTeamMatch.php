<?php

/**
 * @copyright   FOSP
 * @version     20120409
 * @todo        Format player_team_match data
 * @name        Thethao_Business_Player_Metadata_PlayerTeamMatch
 * @author      QuangTM
 */
class Thethao_Business_Player_Metadata_PlayerTeamMatch
{

    /**
     *
     * @var int
     */
    private $_playerID;

    /**
     *
     * @var int
     */
    private $_numCoat;

    /**
     *
     * @var int
     */
    private $_type;

    /**
     *
     * @var string
     */
    private $_position;

    /**
     *
     * @var string
     */
    private $_playerName;

    /**
     *
     * @var string
     */
    private $_event;

    public function __construct()
    {
        $this->_event = '';
        $this->_numCoat = 0;
        $this->_playerID = 0;
        $this->_playerName = '';
        $this->_position = '';
        $this->_type = 0;
    }

    public function __destruct()
    {
        unset($this->_event, $this->_numCoat, $this->_playerID, $this->_playerName, $this->_position, $this->_type);
    }

    /**
     * Initialize data
     * @param array $entity
     * @return Thethao_Business_Player_Metadata_PlayerTeamMatch
     * @author QuangTM
     */
    public function init(array $entity)
    {
        $this->_event = isset($entity['event']) ? $entity['event'] : '';
        $this->_numCoat = isset($entity['num_coat']) ? intval($entity['num_coat']) : 0;
        $this->_playerID = isset($entity['player_id']) ? intval($entity['player_id']) : 0;
        $this->_playerName = isset($entity['player_name']) ? $entity['player_name'] : '';
        $this->_position = isset($entity['position']) ? $entity['position'] : '';
        $this->_type = isset($entity['type']) ? intval($entity['type']) : 0;
        return $this;
    }

    /**
     * Get formated data
     * @return array
     * @author QuangTM
     */
    public function getFormatedData()
    {
        return array(
            'PlayerID'   => $this->_playerID,
            'PlayerName' => $this->_playerName,
            'NumCoat'    => $this->_numCoat,
            'Type'       => $this->_type,
            'Position'   => $this->_position,
            'Event'      => $this->_event
        );
    }

}