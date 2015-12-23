<?php

/**
 * @todo block adapter redis
 * @return Thethao_Business_Block_Adapter_Redis
 * @author LamTX
 */
class Thethao_Business_Block_Adapter_Redis extends Thethao_Business_Block_Adapter_Abstract
{

    /**
     * @var system_model Singleton instance
     */
    protected static $_instance;
    protected $_key_block_detail;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     * @author LamTX
     */
    public function __construct()
    {
        //get key config
        $key = Thethao_Global::getConfig('caching');
        //init keys redis related to this class
        $this->_key_block_detail = $key['block_detail'];
    }

    /**
     * Get singletom instance
     * @return News
     * @author LamTX
     */
    public final static function getInstance()
    {
        //Check instance
        if (is_null(self::$_instance))
        {
            self::$_instance = new self();
        }

        //Return instance
        return self::$_instance;
    }

    public function setBlockByPage($strPageCode, $device, $arrLayout)
    {
        $arrReturn = false;
        try
        {
            $redisInstance = Thethao_Global::getRedis('article');
            //get key cache
            $keyCache = vsprintf($this->_key_block_detail, array($strPageCode, $device));

            $layout = empty($arrLayout) ? '-1' : json_encode($arrLayout);

            //get data
            $arrReturn = $redisInstance->set($keyCache, $layout);

        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $arrReturn;
    }

    /**
     *
     * @param type $strPageCode
     * @return type
     */
    public function getBlockByPage($strPageCode, $intDevice)
    {
        try
        {
            $redisInstance = Thethao_Global::getRedis('article');
            //get key cache
            $keyCache = vsprintf($this->_key_block_detail, array($strPageCode, $intDevice));

            $layout = $redisInstance->get($keyCache);
            //get data
            return $layout != '-1' ? json_decode($layout, 1): $layout;

        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }

    }

    /**
     * @author   : PhongTX - 29/01/2013
     * call job update caching FE
     * @param : string $strPageCode
     * @name : delBlockByPage
     * @copyright   : FPT Online
     * @todo    : delBlockByPage
     */
    public function delBlockByPage($strPageCode, $device)
    {
        try
        {
            $redisInstance = Thethao_Global::getRedis('article');
            //get key cache mobile
            $keyCache = vsprintf($this->_key_block_detail, array($strPageCode, $device));
            return $redisInstance->delete($keyCache);
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
    }
}