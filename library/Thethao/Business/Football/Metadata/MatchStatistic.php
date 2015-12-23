<?php

/**
 * @copyright   FOSP
 * @version     201020407
 * @todo        Format match statistic
 * @name        Fpt_Business_MatchStatistic_Metadata_MatchStatistic
 * @author      QuangTM
 */
class Thethao_Business_Football_Metadata_MatchStatistic
{

    /**
     * Team's ID
     * @var int
     */
    private $_teamID;

    /**
     * Report type's ID
     * @var int
     */
    private $_reportTypeID;

    /**
     *
     * @var string
     */
    private $_values;

    public function __construct()
    {
       $this->_reportTypeID = 0;
       $this->_teamID = 0;
       $this->_values = '';
    }

    public function __destruct()
    {
        unset($this->_reportTypeID, $this->_teamID, $this->_values);
    }

    /**
     * Initialize data
     * @param array $entity
     * @return Fpt_Business_MatchStatistic_Metadata_MatchStatistic
     * @author QuangTM
     */
    public function init(array $entity)
    {
        $this->_reportTypeID = intval($entity['report_type_id']);
        $this->_teamID = intval($entity['team_id']);
        $this->_values = !isset($entity['values']) ? 0 : $entity['values'];
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
            'TeamID'       => $this->_teamID,
            'ReportTypeID' => $this->_reportTypeID,
            'Values'       => $this->_values,
        );
    }

}