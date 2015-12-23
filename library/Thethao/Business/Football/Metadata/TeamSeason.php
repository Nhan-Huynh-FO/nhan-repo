<?php

/**
 * @copyright       : FOSP
 * @version         : 20120319
 * @todo            : Team season format data
 * @author          : QuangTM
 * @name            : Thethao_Business_Football_Metadata_TeamSeason
 */
class Thethao_Business_Football_Metadata_TeamSeason
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
     * @var int
     */
    private $_teamId;

    /**
     *
     * @var string
     */
    private $_name;

    /**
     *
     * @var string
     */
    private $_table;

    /**
     *
     * @var int
     */
    private $_won;

    /**
     *
     * @var int
     */
    private $_drawn;

    /**
     *
     * @var int
     */
    private $_lost;

    /**
     *
     * @var int
     */
    private $_goalAgainst;

    /**
     *
     * @var int
     */
    private $_goalScore;

    /**
     *
     * @var int
     */
    private $_homeWon;

    /**
     *
     * @var int
     */
    private $_homeDrawn;

    /**
     *
     * @var int
     */
    private $_homeLost;

    /**
     *
     * @var int
     */
    private $_homeGoalScore;

    /**
     *
     * @var int
     */
    private $_homeGoalAgainst;

    /**
     *
     * @var int
     */
    private $_awayWon;

    /**
     *
     * @var int
     */
    private $_awayDrawn;

    /**
     *
     * @var int
     */
    private $_awayLost;

    /**
     *
     * @var int
     */
    private $_awayGoalScore;

    /**
     *
     * @var int
     */
    private $_awayGoalAgainst;

    /**
     *
     * @var int
     */
    private $_goalDiffenrence;

    /**
     *
     * @var int
     */
    private $_point;

    /**
     *
     * @var int
     */
    private $_numMatch;

    /**
     *
     * @var int
     */
    private $_position;

    /**
     *
     * @var int
     */
    private $_posChange;

    /**
     *
     * @var string
     */
    private $_lastGamesResult;

    /**
     *
     * @var int
     */
    private $_updateTime;

    /**
     *
     * @var string
     */
    private $_nameSeo;

    public function __construct()
    {
        $this->_teamId = 0;
        $this->_awayDrawn = 0;
        $this->_awayGoalAgainst = 0;
        $this->_awayGoalScore = 0;
        $this->_awayLost = 0;
        $this->_awayWon = 0;
        $this->_drawn = 0;
        $this->_goalAgainst = 0;
        $this->_goalDiffenrence = 0;
        $this->_goalScore = 0;
        $this->_homeDrawn = 0;
        $this->_homeGoalAgainst = 0;
        $this->_homeGoalScore = 0;
        $this->_homeLost = 0;
        $this->_homeWon = 0;
        $this->_lost = 0;
        $this->_numMatch = 0;
        $this->_point = 0;
        $this->_posChange = 0;
        $this->_position = 0;
        $this->_seasonId = 0;
        $this->_table = '';
        $this->_won = 0;
        $this->_leagueId = 0;
        $this->_lastGamesResult = '';
        $this->_name = '';
        $this->_updateTime = 0;
        $this->_nameSeo = '';
    }

    public function __destruct()
    {
        unset(  $this->_teamId,
                $this->_awayDrawn,
                $this->_awayGoalAgainst,
                $this->_awayGoalScore,
                $this->_awayLost,
                $this->_awayWon,
                $this->_drawn,
                $this->_goalAgainst,
                $this->_goalDiffenrence,
                $this->_goalScore,
                $this->_homeDrawn,
                $this->_homeGoalAgainst,
                $this->_homeGoalScore,
                $this->_homeLost,
                $this->_homeWon,
                $this->_lost,
                $this->_numMatch,
                $this->_point,
                $this->_posChange,
                $this->_position,
                $this->_seasonId,
                $this->_table,
                $this->_won,
                $this->_lastGamesResult,
                $this->_updateTime,
                $this->_nameSeo
            );
    }

    /**
     * Init data
     * @param array $entity
     * @return Thethao_Business_Football_Metadata_TeamSeason
     */
    public function init($entity)
    {
        $this->_teamId = intval($entity['team_id']);
        $this->_awayDrawn = intval($entity['away_drawn']);
        $this->_awayGoalAgainst = intval($entity['away_goal_against']);
        $this->_awayGoalScore = intval($entity['away_goal_score']);
        $this->_awayLost = intval($entity['away_lost']);
        $this->_awayWon = intval($entity['away_won']);
        $this->_goalDiffenrence = intval($entity['goal_diffenrence']);
        $this->_homeDrawn = intval($entity['home_drawn']);
        $this->_homeGoalAgainst = intval($entity['home_goal_against']);
        $this->_homeGoalScore = intval($entity['home_goal_score']);
        $this->_homeLost = intval($entity['home_lost']);
        $this->_homeWon = intval($entity['home_won']);
        $this->_numMatch = intval($entity['num_match']);
        $this->_point = intval($entity['point']);
        $this->_posChange = intval($entity['pos_change']);
        $this->_position = intval($entity['position']);
        $this->_seasonId = intval($entity['season_id']);
        $this->_table = empty($entity['table']) || !isset($entity['table']) ? NULL : $entity['table'];
        $this->_won = !isset($entity['won']) ? $this->_homeWon + $this->_awayWon : $entity['won'];
        $this->_lost = !isset($entity['lost']) ? $this->_homeLost + $this->_awayLost : $entity['lost'];
        $this->_drawn = !isset($entity['drawn']) ? $this->_homeDrawn + $this->_awayDrawn : $entity['drawn'];
        $this->_goalAgainst = !isset($entity['goal_against']) ? $this->_homeGoalAgainst + $this->_awayGoalAgainst : $entity['goal_against'];
        $this->_goalScore = !isset($entity['goal_score']) ? $this->_homeGoalScore + $this->_awayGoalScore : $entity['goal_score'];
        $this->_leagueId = intval($entity['league_id']);
        $this->_lastGamesResult = $entity['last_games_result'];
        $this->_name = $entity['name'];
        $this->_updateTime = $entity['update_time'];
        $this->_nameSeo = Fpt_Filter::setSeoLink($entity['name']);
        return $this;
    }

    /**
     * Format data
     * @return array
     */
    public function getFormatedData()
    {
        return array(
            'TeamID' => $this->_teamId,
            'AwayDrawn' => $this->_awayDrawn,
            'AwayGoalAgainst' => $this->_awayGoalAgainst,
            'AwayGoalScore' => $this->_awayGoalScore,
            'AwayLost' => $this->_awayLost,
            'AwayWon' => $this->_awayWon,
            'Drawn' => $this->_drawn,
            'GoalAgainst' => $this->_goalAgainst,
            'GoalDiffenrence' => $this->_goalDiffenrence,
            'GoalScore' => $this->_goalScore,
            'HomeDrawn' => $this->_homeDrawn,
            'HomeGoalAgainst' => $this->_homeGoalAgainst,
            'HomeGoalScore' => $this->_homeGoalScore,
            'HomeLost' => $this->_homeLost,
            'HomeWon' => $this->_homeWon,
            'Lost' => $this->_lost,
            'NumMatch' => $this->_numMatch,
            'Point' => $this->_point,
            'PosChange' => $this->_posChange,
            'Position' => $this->_position,
            'SeasonID' => $this->_seasonId,
            'Table' => $this->_table,
            'Won' => $this->_won,
            'LeagueID' => $this->_leagueId,
            'LastGameResult' => $this->_lastGamesResult,
            'Name' => $this->_name,
            'UpdateTime' => $this->_updateTime,
            'NameSeo' => $this->_nameSeo
        );
    }

}