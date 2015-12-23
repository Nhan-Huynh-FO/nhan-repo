<?php

/**
 * @copyright       : FOSP
 * @version         : 20120322
 * @todo            : Format metadata league
 * @name            : Thethao_Business_Football_Metadata_League
 * @author          : QuangTM
 */
class Thethao_Business_Football_Metadata_League
{
    /**
     *
     * @var int
     */
    private $_leagueId;

    /**
     *
     * @var string
     */
    private $_name;

    /**
     *
     * @var int
     */
    private $_order;

    /**
     *
     * @var int
     */
    private $_parameterA;

    /**
     *
     * @var int
     */
    private $_status;

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
     * @var string
     */
    private $_nameSeo;

    public function __construct()
    {
        $this->_creationTime = 0;
        $this->_leagueId = 0;
        $this->_name = '';
        $this->_order = 0;
        $this->_parameterA = 0;
        $this->_status = 0;
        $this->_updateTime =  0;
        $this->_nameSeo = '';
    }

    public function __destruct()
    {
        unset ( $this->_creationTime,
                $this->_leagueId,
                $this->_name,
                $this->_order,
                $this->_parameterA,
                $this->_status,
                $this->_updateTime,
                $this->_nameSeo
            );
    }

    /**
     * Initialize data
     * @param array $entity
     * @return Thethao_Business_Football_Metadata_Football
     */
    public function init($entity)
    {
        $this->_creationTime = intval($entity['creation_time']);
        $this->_leagueId = intval($entity['league_id']);
        $this->_name = !isset ($entity['name']) ? '' : $entity['name'];
        $this->_order = intval($entity['order']);
        $this->_parameterA = intval($entity['parameter_a']);
        $this->_status = intval($entity['status']);
        $this->_updateTime =  intval($entity['update_time']);
        $this->_nameSeo = Fpt_Filter::setSeoLink($entity['name']);
        return $this;
    }

    /**
     * Get formated data
     * @return array
     */
    public function getFormatedData()
    {
        return array(
            'LeagueID' => $this->_leagueId,
            'Name' => $this->_name,
            'Order' => $this->_order,
            'ParameterA' => $this->_parameterA,
            'Status' => $this->_status,
            'CreationTime' => $this->_creationTime,
            'UpdateTime' => $this->_updateTime,
            'NameSeo' => $this->_nameSeo,
        );
    }

}