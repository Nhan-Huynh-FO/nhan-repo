<?php

/**
 * @author      :   Lamtx
 * @name        :   Thethao_Business_Block_Adapter_Mysql
 * @copyright   :   Fpt Online
 * @todo        :   Block business
 * @return      :   Thethao_Business_Block_Adapter_Mysql
 */
class Thethao_Business_Block_Adapter_Mysql
{

    /**
     *
     * @param type $page
     * @param type $intStatus
     * @param type $device
     * @return type
     */
    public function getBlockByPage($page, $device = 4, $intStatus = 1)
    {
        //default reference
        $arrBlock = array();
        $inDev = $device;

        try
        {
            // Get DB Object
            $db = Thethao_Global::getDB('block', 'slave');
            // Prepare SQL
            $stmt = $db->prepare('CALL sp_block_getWidgetByPageCode_v2(:p_pagecode, :p_status, :p_deviceenv);');
            // Bind params
            $stmt->bindParam('p_pagecode', $page, PDO::PARAM_STR);
            $stmt->bindParam('p_status', $intStatus, PDO::PARAM_INT);
            $stmt->bindParam('p_deviceenv', $inDev, PDO::PARAM_INT);
            // Execute
            $stmt->execute();
            //fetch result
            while ($row = $stmt->fetch())
            {
                $arrData = array();
                $arrArea = array(0 => 'top', 1 => 'top_news_right', 2 => 'left', 3 => 'right', 4 => 'footer');
                if (!isset($arrArea[$row['area']]))
                    continue;
                $area = $arrArea[$row['area']];
                $config = $intStatus == 1 ? json_decode($row['config'], true) : json_decode($row['config_new'], true);
                if ($row['type'] == 1)
                {
                    $arrData['type'] = 'dynamic';
                    $arrData['class'] = $config['class'];
                    $arrData['function'] = $config['function'];
                    $arrData['param'] = $config['config'];
                }
                else
                {
                    $arrData['type'] = 'static';
                }
                $arrData['view'] = $config['view'];
                $arrData['layout'] = isset($config['layout']) ? $config['layout'] : array();
                $arrBlock[$area][] = $arrData;
            }
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $arrBlock;
    }

    /**
     * get data from table keybox with key id
     * @param int $intKeyId
     * @return array
     */
    public function getKeyBox($arrKey = NULL)
    {
        $arrReturn = array();
        try
        {
            //get db slave
            $sport_m = Thethao_Global::getDB('sport', 'slave');
            // Get user activeation key
            $objStmt = $sport_m->prepare('CALL sp_getKeyBox(:p_keyid)');
            // Bind param
            $objStmt->bindParam('p_keyid', $arrKey, PDO::PARAM_STR);
            // Fetch result
            $objStmt->execute();
            //fetch result
            while ($row = $objStmt->fetch())
            {
                $arrReturn[$row['key_id']] = $row['values'];
            }

            // Close cursor
            $objStmt->closeCursor();
            // Release
            unset($objStmt);
        }
        catch (Zend_Db_Exception $ex)
        {
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }
}