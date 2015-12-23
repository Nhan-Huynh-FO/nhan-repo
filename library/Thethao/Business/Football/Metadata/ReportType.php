<?php

/**
 * @copyright   FOSP
 * @version     20120407
 * @todo        Format data Report type
 * @name        Fpt_Business_ReportType_Metadata_ReportType
 * @author      QuangTM
 */
class Thethao_Business_Football_Metadata_ReportType
{

    /**
     * Report type id
     * @var int
     */
    private $_id;

    /**
     * Report type name
     * @var string
     */
    private $_name;

    public function __construct()
    {
        $this->_id = 0;
        $this->_name = '';
    }

    public function __destruct()
    {
        unset($this->_id, $this->_name);
    }

    /**
     * Initialize data
     * @param array $entity
     * @return Fpt_Business_ReportType_Metadata_ReportType
     */
    public function init(array $entity)
    {
        $this->_id = intval($entity['report_type_id']);
        $this->_name = !isset($entity['name']) ? '' : $entity['name'];
        return $this;
    }

    /**
     * Get formated data
     * @return array
     */
    public function getFormatedData()
    {
        return array(
            'ID'   => $this->_id,
            'Name' => $this->_name,
        );
    }

}