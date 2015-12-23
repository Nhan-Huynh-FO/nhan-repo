<?php

/**
 * @copyright   FOSP
 * @version     20120427
 * @todo        Format data team
 * @name        Fpt_Business_Team_Metadata_Team
 * @author      QuangTM
 */
class Thethao_Business_Football_Metadata_Team
{

    /**
     *
     * @var int
     */
    private $_teamID;

    /**
     *
     * @var int
     */
    private $_tagId;


    /**
     *
     * @var string
     */
    private $_award;

    /**
     *
     * @var string
     */
    private $_avatar;

    /**
     *
     * @var string
     */
    private $_historylead;

    /**
     *
     * @var string
     */
    private $_historycontent;

    public function __construct()
    {
        $this->_teamID = 0;
        $this->_tagId = 0;
        $this->_award = '';
        $this->_avatar = '';
        $this->_historylead = '';
        $this->_historycontent = '';
    }

    public function __destruct()
    {
        unset($this->_teamID, $this->_tagId, $this->_avatar, $this->_award, $this->_historycontent, $this->_historylead);
    }

    /**
     * Initialize data
     * @param array $entity
     * @return Fpt_Business_Team_Metadata_Team
     * @author QuangTM
     */
    public function init(array $entity)
    {
        $this->_teamID = isset($entity['team_id']) ? intval($entity['team_id']) : 0;
        $this->_tagId = isset($entity['tag_id']) ? intval($entity['tag_id']) : 0;

        // History
        $this->_award = isset($entity['award']) ? $entity['award'] : NULL;
        $this->_avatar = isset($entity['avatar']) ? $entity['avatar'] : NULL;
        $this->_historylead = isset($entity['lead']) ? $entity['lead'] : NULL;
        $this->_historycontent = isset($entity['content']) ? $entity['content'] : NULL;
        return $this;
    }

    /**
     * Get formated data team
     * @return array
     * @author TuanDH
     */
    public function getFormatedTeamHistory()
    {
        return array(
            'ID'      => $this->_teamID,
            'TagId'   => $this->_tagId,
            'Avatar'  => $this->_avatar,
            'Award'   => $this->_award,
            'Lead'    => $this->_historylead,
            'Content' => $this->_historycontent,
        );
    }

}