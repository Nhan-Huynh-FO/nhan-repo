<?php
/**
 * @copyright       : Fpt Online
 * @version         : 20131118
 * @todo            : Format metadata league
 * @name            : Thethao_Business_Match_Metadata_Match
 * @author          : QuangTM
 */
class Thethao_Business_Match_Metadata_Match
{

    /**
     * Match's id
     * @var int
     */
    private $_matchId;

    /**
     * Season's id
     * @var int
     */
    private $_seasonId;

    /**
     * Season's name
     * @var string
     */
    private $_seasonName;

    /**
     * league's id
     * @var int
     */
    private $_leagueId;

    /**
     * league's name
     * @var string
     */
    private $_leagueName;

    /**
     * Datetime happen match
     * @var int datetime
     */
    private $_datetimeHappen;

    /**
     * Stadium's name
     * @var string
     */
    private $_stadiumName;

    /**
     *
     * @var string
     */
    private $_round;

    /**
     * Team1' id
     * @var int
     */
    private $_team1;

    /**
     * Team1's name
     * @var string
     */
    private $_teamName1;

    /**
     * Team1's logo
     * @var string
     */
    private $_teamLogo1;

    /**
     * Team2's id
     * @var int
     */
    private $_team2;

    /**
     * Team2's name
     * @var string
     */
    private $_teamName2;

    /**
     * Team2's logo
     * @var string
     */
    private $_teamLogo2;

    /**
     * Total goal for team 1
     * @var int
     */
    private $_goal1;

    /**
     * Total goal for team 2
     * @var int
     */
    private $_goal2;

    /**
     * Match's group
     * @var int
     */
    private $_group;

    /**
     * Team 1's half 1 goal
     * @var int
     */
    private $_halfGoal1;

    /**
     * Team 2's half 1 goal
     * @var int
     */
    private $_halfGoal2;

    /**
     * Team 1's half 2 goal
     * @var int
     */
    private $_fullGoal1;

    /**
     * Team 2's half 2 goal
     * @var int
     */
    private $_fullGoal2;

    /**
     * Team 1's penalty
     * @var int
     */
    private $_penalty1;

    /**
     * Team 2's penalty
     * @var int
     */
    private $_penalty2;

    /**
     *
     * @var int
     */
    private $_status;

    /**
     * @return Thethao_Business_Match_Metadata_Match
     */
    public function __construct()
    {
        $this->_datetimeHappen = 0;
        $this->_fullGoal1 = 0;
        $this->_fullGoal2 = 0;
        $this->_goal1 = 0;
        $this->_goal2 = 0;
        $this->_group = 0;
        $this->_halfGoal1 = 0;
        $this->_halfGoal2 = 0;
        $this->_leagueId = 0;
        $this->_leagueName = '';
        $this->_matchId = 0;
        $this->_round = '';
        $this->_seasonId = 0;
        $this->_seasonName = '';
        $this->_stadiumName = '';
        $this->_status = 1;
        $this->_team1 = 0;
        $this->_teamName1 = '';
        $this->_teamLogo1 = '';
        $this->_team2 = 0;
        $this->_teamName2 = '';
        $this->_teamLogo2 = '';
        $this->_penalty1 = 0;
        $this->_penalty2 = 0;
    }

    public function __destruct()
    {
        unset(  $this->_datetimeHappen,
                $this->_fullGoal1,
                $this->_fullGoal2,
                $this->_goal1,
                $this->_goal2,
                $this->_group,
                $this->_halfGoal1,
                $this->_halfGoal2,
                $this->_leagueId,
                $this->_leagueName,
                $this->_matchId,
                $this->_round,
                $this->_seasonId,
                $this->_seasonName,
                $this->_stadiumName,
                $this->_status,
                $this->_team1,
                $this->_team2,
                $this->_teamName1,
                $this->_teamName2,
                $this->_teamLogo1,
                $this->_teamLogo2,
                $this->_penalty1,
                $this->_penalty2
                );
    }

    /**
     * Initialize data
     * @param array $entity
     * @return Thethao_Business_Match_Metadata_Match
     */
    public function init($entity)
    {
        $this->_datetimeHappen = empty($entity['datetime_happen']) || !isset($entity['datetime_happen']) ? 0 : $entity['datetime_happen'];
        $this->_fullGoal1 = empty($entity['full_goal_1']) || !isset($entity['full_goal_1']) ? 0 : intval($entity['full_goal_1']);
        $this->_fullGoal2 = empty($entity['full_goal_2']) || !isset($entity['full_goal_2']) ? 0 : intval($entity['full_goal_2']);
        $this->_goal1 = empty($entity['goal1']) || !isset($entity['goal1']) ? 0 : intval($entity['goal1']);
        $this->_goal2 = empty($entity['goal2']) || !isset($entity['goal2']) ? 0 : intval($entity['goal2']);
        $this->_group = intval($entity['group']);
        $this->_halfGoal1 = empty($entity['half_goal_1']) || !isset($entity['half_goal_1']) ? 0 : intval($entity['half_goal_1']);
        $this->_halfGoal2 = empty($entity['half_goal_2']) || !isset($entity['half_goal_2']) ? 0 : intval($entity['half_goal_2']);
        $this->_leagueId = intval($entity['league_id']);
        $this->_leagueName = !isset($entity['league_name']) || empty($entity['league_name']) ? '' : $entity['league_name'];
        $this->_matchId = intval($entity['match_id']);
        $this->_round = empty($entity['round']) || !isset($entity['round']) ? '' : $entity['round'];
        $this->_seasonId = intval($entity['season_id']);
        $this->_seasonName = empty($entity['name_season']) || !isset($entity['name_season']) ? '' : $entity['name_season'];
        $this->_stadiumName = $entity['stadium_name'];
        $this->_status = empty($entity['status']) || !isset($entity['status']) ? 1 : intval($entity['status']);
        $this->_team1 = intval($entity['team1']);
        $this->_teamName1 = !isset($entity['name1']) ? '' : $entity['name1'];
        $this->_teamLogo1 = !isset($entity['logo1']) ? '' : $entity['logo1'];
        $this->_team2 = intval($entity['team2']);
        $this->_teamName2 = !isset($entity['name2']) ? '' : $entity['name2'];
        $this->_teamLogo2 = !isset($entity['logo2']) ? '' : $entity['logo2'];
        $this->_penalty1 = !isset($entity['penalty1']) || empty($entity['penalty1']) ? 0 : intval($entity['penalty1']);
        $this->_penalty2 = !isset($entity['penalty2']) || empty($entity['penalty2']) ? 0 : intval($entity['penalty2']);
        return $this;
    }

    public function getFormatedData()
    {
        return array(
            'MatchID'        => $this->_matchId,
            'SeasonID'       => $this->_seasonId,
            'SeasonName'     => $this->_seasonName,
            'LeagueID'       => $this->_leagueId,
            'LeagueName'     => $this->_leagueName,
            'DatetimeHappen' => $this->_datetimeHappen,
            'StadiumName'    => $this->_stadiumName,
            'Round'          => $this->_round,
            'Team1'          => $this->_team1,
            'TeamName1'      => $this->_teamName1,
            'TeamLogo1'      => $this->_teamLogo1,
            'Team2'          => $this->_team2,
            'TeamName2'      => $this->_teamName2,
            'TeamLogo2'      => $this->_teamLogo2,
            'Goal1'          => $this->_goal1,
            'Goal2'          => $this->_goal2,
            'Group'          => $this->_group,
            'HalfGoal1'      => $this->_halfGoal1,
            'HalfGoal2'      => $this->_halfGoal2,
            'FullGoal1'      => $this->_fullGoal1,
            'FullGoal2'      => $this->_fullGoal2,
            'Penalty1'       => $this->_penalty1,
            'Penalty2'       => $this->_penalty2,
            'Status'         => $this->_status,
        );
    }

}