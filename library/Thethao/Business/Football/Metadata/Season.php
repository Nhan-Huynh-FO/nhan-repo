<?php

/**
 * @copyright       : Fpt Online
 * @version         : 20131118
 * @todo            : Format data record season
 * @name            : Fpt_Business_Season_Metadata_Season
 * @author          : QuangTM
 */
class Thethao_Business_Football_Metadata_Season
{
    /**
     *
     * @var int
     */
    private $_seasonId;

    /**
     *
     * @var int
     */
    private $_leagueId;

    /**
     *
     * @var string
     */
    private $_nameSeason;

    /**
     *
     * @var string
     */
    private $_beginDate;

    /**
     *
     * @var string
     */
    private $_endDate;

    /**
     *
     * @var int
     */
    private $_numTeam;

    /**
     *
     * @var string
     */
    private $_nameSeo;

    /**
     *
     * @var int
     */
    private $_status;

    public function __construct()
    {
        $this->_beginDate = '';
        $this->_endDate = '';
        $this->_leagueId = 0;
        $this->_numTeam = 0;
        $this->_seasonId = 0;
        $this->_nameSeason = '';
        $this->_status = 0;
        $this->_nameSeo = '';
    }

    public function __destruct()
    {
        unset ( $this->_beginDate,
                $this->_endDate,
                $this->_leagueId,
                $this->_numTeam,
                $this->_seasonId,
                $this->_nameSeason,
                $this->_status,
                $this->_nameSeo
                );
    }

    /**
     * Initialize data
     * @param array $entity
     * @return Fpt_Business_Season_Metadata_Season
     */
    public function init($entity)
    {
        $this->_beginDate = !isset ($entity['begin_date']) || empty ($entity['begin_date']) ? NULL : $entity['begin_date'];
        $this->_endDate = !isset ($entity['end_date']) || empty ($entity['end_date']) ? NULL : $entity['end_date'];
        $this->_leagueId = intval($entity['league_id']);
        $this->_numTeam = intval($entity['num_team']);
        $this->_seasonId = intval($entity['season_id']);
        $this->_nameSeason = !isset ($entity['name_season']) || empty ($entity['name_season']) ? NULL : $entity['name_season'];
        $this->_status = intval($entity['status']);
        $this->_nameSeo = Fpt_Filter::setSeoLink($entity['name_season']);
        return $this;
    }

    /**
     * Get formated data
     * @return array
     */
    public function getFormatedData()
    {
        return array(
            'SeasonID' => $this->_seasonId,
            'LeagueID' => $this->_leagueId,
            'NameSeason' => $this->_nameSeason,
            'BeginDate' => $this->_beginDate,
            'EndDate' => $this->_endDate,
            'NumTeam' => $this->_numTeam,
            'Status'    => $this->_status,
            'NameSeo'   => $this->_nameSeo
        );
    }

}