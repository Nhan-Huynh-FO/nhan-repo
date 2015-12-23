<?php

/**
 * @copyright   FOSP
 * @version     20120509
 * @todo        Format data match_relate
 * @name        Thethao_Business_Football_Metadata_MatchRelate
 * @author      QuangTM
 */
class Thethao_Business_Football_Metadata_MatchRelate
{

    /**
     *
     * @var int
     */
    private $_matchID;

    /**
     *
     * @var int
     */
    private $_articleID;

    /**
     *
     * @var string
     */
    private $_articleContent;

    /**
     *
     * @var string
     */
    private $_relate;
    /**
     * relate id = article_id
     * @var int
     */
    private $_relateID;


    /**
     *
     * @var string
     */
    private $_linkSopcast;

    /**
     *
     * @var bool
     */
    private $_hasData;

    public function __construct()
    {
        $this->_matchID = 0;
        $this->_articleID = 0;
        $this->_articleContent = '';
        $this->_relate = '';
        $this->_relateID = 0;
        $this->_linkSopcast = '';
        $this->_hasData = FALSE;
    }

    /**
     * Initialize data
     * @param array|bool $entity
     * @return Fpt_Business_MatchRelate_Metadata_MatchRelate
     * @author QuangTM
     */
    public function init($entity)
    {
        if ($entity === FALSE)
            return $this;
        $this->_matchID = intval($entity['match_id']);
        $this->_articleID = isset($entity['article_id']) ? intval($entity['article_id']) : 0;
        $this->_articleContent = isset($entity['article_content']) ? $entity['article_content'] : NULL;
        $this->_relate = isset($entity['relate']) ? $entity['relate'] : '';
        $this->_relateID = isset($entity['relate_id']) ? $entity['relate_id'] : '';
        $this->_linkSopcast = isset($entity['link_sopcast']) ? $entity['link_sopcast'] : '';
        $this->_hasData = TRUE;
        return $this;
    }

    /**
     * Get formated data for diem tin
     * @return array
     * @author QuangTM
     */
    public function getFormatedDataForDiemTin()
    {
        return $this->_hasData ? array(
            'ArticleID'      => $this->_articleID,
            'ArticleContent' => $this->_articleContent,
            'LinkSopCast'    => $this->_linkSopcast,
            ) : FALSE;
    }

    /**
     * Get Formated data for tuong thuat
     * @return array
     * @author QuangTM
     */
    public function getFormatedDataForTuongThuat()
    {
        return $this->_hasData ? array('relate' => $this->_relate, 'relate_id' => $this->_relateID) : FALSE;
    }

    public function __destruct()
    {
        unset($this->_articleID, $this->_linkSopcast, $this->_matchID, $this->_relate, $this->_hasData, $this->_articleContent);
    }

    protected function __clone()
    {
        ;
    }

}