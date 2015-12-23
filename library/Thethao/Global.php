<?php

/**
 * @author      :   Lamtx
 * @name        :   Thethao_Global
 * @copyright   :   Fpt Online
 * @todo        :   Global for Application
 */
class Thethao_Global
{

    /**
     * store config ini
     * @var array
     */
    private static $_configs = array();

    /**
     * store nosql instance
     * @var array
     */
    private static $_nosql = array();

    /**
     * store cache instance
     * @var array
     */
    private static $_cache = array();

    /**
     * store db instance
     * @var array
     */
    private static $_db = array();

    /**
     * store job instance
     * @var array
     */
    private static $_jobClient = array();

    /**
     * Search of object
     * @var object
     */
    private static $_searchObject = null;

    /**
     * Search of object
     * @var object
     */
    private static $_searchVideo = null;

    /**
     * Start Session
     * @return integer
     */
    public function session()
    {
        Zend_Session::start();
        $intUserId = (int) Fpt_Sso::checkLoginClient();
        $sesionThethao = new Zend_Session_Namespace('thethao');
        $sesionThethao->uid = $intUserId;
        return $intUserId;
    }

    /**
     * call fucn close all resource: db, memcache, redis
     * @author LamTX
     */
    public static function closeResource()
    {
        self::closeDb();
        self::closeRedis();
        self::closeCache();
        self::closeGearmanClient();
    }

    /**
     * close redis storage
     * @author LamTX
     */
    public static function closeRedis()
    {
        //check not emtpy
        if (!empty(self::$_nosql))
        {
            //loop redis ins
            foreach (self::$_nosql as $farm => $redis)
            {
                //close redis
                $redis->getStorage()->close();
            }
        }
        //reset var
        self::$_nosql = array();
    }

    /**
     * close gearman storage
     * @author LamTX
     */
    public static function closeGearmanClient()
    {
        //reset var
        self::$_jobClient = array();
    }

    /**
     * close Db
     * @author LamTX
     */
    public static function closeDb()
    {
        //check empty
        if (!empty(self::$_db))
        {
            //loop db ins to close
            foreach (self::$_db as $dbName => $db)
            {
                //check connected
                if ($db->isConnected())
                {
                    $db->closeConnection();
                }
            }
        }
        self::$_db = array();
    }

    /**
     * Close memcache
     * @author LamTX
     */
    public static function closeCache()
    {
        //reset param
        self::$_cache = array();
    }

    /**
     * Get DB connection
     * @param string $strDbname
     * @param string $strType master|slave
     * @param string $zone
     * @return Zend_Db_Adapter_Abstract
     * @author LamTX
     */
    public static function getDB($strDbname, $strType = 'slave', $zone = ZONE_ENV)
    {
        //check from job?
        if (SERVER_MASTER == 'thethao')
        {
            //if call from job -> always read from master
            $strType = 'master';
        }
        //set key by $strType and $strDbname
        $dbName = $strType . $strDbname . $zone;
        //check exists Db instance and Db instanceof Zend_Db_Adapter_Abstract
        if (!array_key_exists($dbName, self::$_db) || !(($dbAdapter = self::$_db["$dbName"]) instanceof Zend_Db_Adapter_Abstract))
        {
            //get config database
            $arrConf = self::getApplicationIni('database');
            //set Zone
            $arrConf = $arrConf[$zone];
            //check $strDbname && $strType isset config
            if (null != ($arrConf = $arrConf["$strDbname"]["$strType"]))
            {
                //get server shadding
                $arrConf = $arrConf[0];
                //construct Zend Db
                $dbAdapter = Zend_Db::factory($arrConf['adapter'], $arrConf['params']);
                //set instance to store db instance
                self::$_db["$dbName"] = $dbAdapter;
            }
            else
            {
                //Exception $strDbname and $strType
                throw new Exception("Db name $strDbname and type $strType not exists in config database.");
            }
        }
        return $dbAdapter;
    }

    /**
     * Get Cache Instance
     * @param string $strFarmName
     * @param int $strShading
     * @param string $zone
     * @return Fpt_Cache_Adapter_Abstract
     * @author LamTX
     */
    public static function getCache($strFarmName = 'all', $strShading = NULL, $zone = ZONE_ENV)
    {
        $strInstance = $strFarmName . $zone;
        //check shadding
        if ($strShading != NULL)
        {
            //get config cache form Application Ini
            $arrConf = self::getApplicationIni('cache');
            //set Zone
            $arrConf = $arrConf[$zone];
            //check $strFarmName exists
            if (!array_key_exists($strFarmName, $arrConf))
            {
                //Exception $strFarmName not in config cache
                throw new Exception("Farm $strFarmName not exists in config cache.");
            }
            //get adapter cache
            $adapter = $arrConf["$strFarmName"]['adapter'];
            //get servers cache
            $arrConf = $arrConf["$strFarmName"]['servers'];
            //get Id shadding
            $intShadingId = crc32($strShading) % count($arrConf);
            //gan Farm with shadding
            $strFarmName .= $intShadingId;
            //check instance strFarmName is Fpt_Cache_Adapter_Abstract
            if (!array_key_exists($strInstance, self::$_cache) || !(($cacheAdapter = self::$_cache["$strInstance"]) instanceof Fpt_Cache_Adapter_Abstract))
            {
                //check $strFarmName exists
                if (!array_key_exists($intShadingId, $arrConf))
                {
                    //Exception $strFarmName not in config cache
                    throw new Exception("Shading Id $intShadingId not exists in config cache.");
                }
                //get servers cache
                $arrConf = $arrConf["$intShadingId"];
                //load instance Fpt_Cache
                $cacheAdapter = Fpt_Cache::factory($adapter, $arrConf);
                //set instance
                self::$_cache["$strInstance"] = $cacheAdapter;
            }
        }
        else
        {
            //check instance strFarmName is Fpt_Cache_Adapter_Abstract
            if (!array_key_exists($strInstance, self::$_cache) || !(($cacheAdapter = self::$_cache["$strInstance"]) instanceof Fpt_Cache_Adapter_Abstract))
            {
                //get config cache form Application Ini
                $arrConf = self::getApplicationIni('cache');
                //set Zone
                $arrConf = $arrConf[$zone];
                //check $strFarmName exists
                if (!array_key_exists($strFarmName, $arrConf))
                {
                    //Exception $strFarmName not in config cache
                    throw new Exception("Farm $strFarmName not exists in config cache.");
                }
                //get adapter cache
                $adapter = $arrConf["$strFarmName"]['adapter'];
                //get servers cache
                $arrConf = $arrConf["$strFarmName"]['servers'];
                //load instance Fpt_Cache
                $cacheAdapter = Fpt_Cache::factory($adapter, $arrConf);
                //set instance
                self::$_cache["$strInstance"] = $cacheAdapter;
            }
        }
        return $cacheAdapter;
    }

    /**
     * Get DB nosql connection
     * @param string $strFarmName
     * @param int $shardingDB
     * @param string $zone
     * @return object
     * @author LamTX
     */
    public static function getRedis($strFarmName = 'article', $shardingDB = 0, $zone = ZONE_ENV)
    {
        $slotRedis = 0;
        $strInstance = $zone . $strFarmName . $shardingDB;

        try
        {
            if (!array_key_exists($strInstance, self::$_nosql) || !(self::$_nosql[$strInstance] instanceof Fpt_Nosql_Adapter_Abstract))
            {
                if (FROM_JOB)
                {
                    $arrConf = self::getConfig('job');
                    $arrConf = $arrConf['nosql'];
                }
                else
                {
                    $arrConf = self::getApplicationIni('nosql');
                }
                if (!isset($arrConf[$zone][$strFarmName][$shardingDB]))
                {
                    throw new Exception("Zone: $zone and strFarmName: $strFarmName not exists in config Nosql.");
                }

                //set Zone
                $arrConf = $arrConf[$zone][$strFarmName][$shardingDB];
                //set adapter
                $adapter = $arrConf['adapter'];
                //set servers
                $arrServer = $arrConf['servers'];
                //get total servers
                $intServerNum = count($arrServer);

                //get config environment
                $intId = FROM_JOB == 1 ? 0 : ( getenv('REDIS_ENV') != '' ? (int) getenv('REDIS_ENV') : array_rand($arrServer));

                //check isset config
                $intId = isset($arrServer[$intId]) ? $intId : array_rand($arrServer);

                while ($intServerNum > 0)
                {
                    $objRedis = Fpt_Nosql::factory($adapter, $arrServer[$intId]);

                    if ($objRedis->getStorage())
                    {
                        //select DB Storage
                        $objRedis->selectDb($slotRedis);
                        //set instance
                        self::$_nosql[$strInstance] = $objRedis;
                        break;
                    }
                    else
                    {
                        $intServerNum--;
                        unset($arrServer[$intId]);
                        $intId = array_rand($arrServer);
                    }
                }
                if (!array_key_exists($strInstance, self::$_nosql) || !(self::$_nosql[$strInstance] instanceof Fpt_Nosql_Adapter_Abstract))
                {
                    return new Thethao_Stdclass();
                }
            }
            return self::$_nosql[$strInstance]->getStorage();
        }
        catch (Exception $ex)
        {
            //log error
            Fpt_Data_Model::sendLog($ex);
            return new Thethao_Stdclass();
        }
        return new Thethao_Stdclass();
    }

    /**
     * @todo make cache key
     * @param string $cacheKey
     * @param array $args
     * @return string
     * @author LamTX
     */
    public static function makeCacheKey($cacheKey, $args = NULL)
    {
        //get key config
        $_Config = self::getConfig('caching');

        //valid key
        if (isset($_Config[$cacheKey]))
        {
            //check param
            if (NULL === $args)
            {
                return $_Config[$cacheKey];
            }
            return vsprintf($_Config[$cacheKey], $args);
        }
        return false;
    }

    /**
     * Get application application configuration
     * @param string $config_key
     * @return object
     * @author LamTX
     */
    public static function getApplicationIni($config_key = null)
    {
        //get config from Options of  Zend_Controller_Front
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        //check section
        if (NULL !== $config_key)
        {
            //check key in config
            if (array_key_exists($config_key, $config))
            {
                //return data config
                return $config["$config_key"];
            }
        }
        //return data
        return $config;
    }

    /**
     * get config to file ini
     * @param string $filename
     * @return array
     * @author LamTX
     */
    public static function getConfig($filename, $extra = null)
    {
        //check instance by filename
        if (!isset(self::$_configs[$filename]))
        {
            if ($extra)
            {
                //set file
                $file = APPLICATION_PATH . '/' . 'configs' . '/' . $filename . '-' . APPLICATION_ENV . '-'. $extra .'.ini';
            }
            else
            {
                //set file
                $file = APPLICATION_PATH . '/' . 'configs' . '/' . $filename . '-' . APPLICATION_ENV . '.ini';
            }
            //check extension apc
            if (extension_loaded('apc'))
            {
                //get cache apc and config by array
                if ((($config = apc_fetch($file)) === false) || !is_array($config))
                {
                    //load file config by Zend_Config_Ini
                    $config = new Zend_Config_Ini($file);
                    //conver to array
                    $config = $config->toArray();
                    //set to cache apc
                    apc_store($file, $config, 0);
                }
            }
            else
            {
                //load file config by Zend_Config_Ini
                $config = new Zend_Config_Ini($file);
                //conver to array
                $config = $config->toArray();
            }
            //file is application then none instance
            if ($filename == 'application')
            {
                //retrun config
                return $config;
            }
            //set config to instance
            self::$_configs[$filename] = $config;
        }
        //retrun config
        return self::$_configs[$filename];
    }

    /**
     * Delete memcache multi zone func
     * @param array $arrKeyMemcache
     * @param string $strFarmName
     * @param string $strShading
     * @return boolean|array
     * @author PhuongTN
     */
    public static function deleteMemcache($arrKeyMemcache, $strFarmName = 'all', $strShading = NULL)
    {
        try
        {
            //get application config
            $config = Thethao_Global::getApplicationIni();
            //get all zoneNames
            $zoneNames = array_keys($config['cache']);

            if (!empty($zoneNames))
            {
                //loop zone
                foreach ($zoneNames as $zoneName)
                {
                    // Get instance memcache
                    $memcacheInstance = Thethao_Global::getCache($strFarmName, $strShading, $zoneName);

                    foreach ($arrKeyMemcache as $keyCache)
                    {
                        //Delete key cache
                        $delRS[$zoneName][$keyCache] = $memcacheInstance->delete($keyCache);
                        if ($delRS[$zoneName][$keyCache] == false)
                        {
                            //try again
                            $delRS[$zoneName][$keyCache]['2'] = $memcacheInstance->delete($keyCache);
                        }
                    }
                }
            }

            return $delRS;
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return false;
    }

    /**
     * func failure_callback of memcachedv1
     * @param string $host
     * @param int $port
     */
    public static function memcacheLog($host, $port)
    {
        //format arrray logs
        $arrLog = array('app_id' => SITE_ID,
            'action' => 1, //memcache action
            'error_code' => 10000, //
            'error_msg' => 'connect memcache fail on:' . $host . ':' . $port . ':ip:' . $_SERVER['SERVER_ADDR'],
            'error_time' => time());
        debug_print_backtrace();
        //push error log
        Fpt_Log_Error::pushlog($arrLog);
        //flush all instance to retry
        //self::$_cache = array();
    }

    /**
     * this func is use for master server: write hcm only
     * @param string $keyMemcache
     * @param array $arrData
     * @param string $strFarmName
     * @return boolean|array
     * @author PhuongTN
     */
    public static function writeMemcache($keyMemcache, $arrData, $strFarmName = 'all', $strShading = NULL, $intTime = 0)
    {
        try
        {
            $writeRS = false;

            //get memcache ins
            $objCache = Thethao_Global::getCache($strFarmName, $strShading, 'hcm');

            //write key cache
            $writeRS = $objCache->write($keyMemcache, $arrData, $intTime);
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return $writeRS;
    }

    /**
     * Get job client
     * @param string $strFarmName
     * @param string $zoneName
     * @return Fpt_Job_Client
     * @author LamTX
     */
    public static function getJobClientInstance($strFarmName = 'sport', $zoneName = ZONE_ENV)
    {
        try
        {
            //Get caching instance
            if (!isset(self::$_jobClient[$strFarmName]))
            {
                //get array conf
                $arrConf = Thethao_Global::getApplicationIni();
                //get job client instance
                self::$_jobClient[$strFarmName] = Fpt_Job_Client::factory($arrConf['job'][$zoneName][$strFarmName]['adapter'], $arrConf['job'][$zoneName][$strFarmName]);
                unset($arrConf);
            }
            //Return jobclient
            return self::$_jobClient[$strFarmName];
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
            return false;
        }
    }

    /**
     * write log file to send mail
     * @param Exception $ex
     * @param int $level
     * @return null
     * @author LamTX
     */
    public static function sendLog($ex, $message = '', $trace = '', $errorCode = 0, $intAction = 0)
    {
        try
        {
            if (APPLICATION_ENV == 'development')
            {
                var_dump('<pre><br><br><h2>' . $ex->getMessage() . '<br></h2><br>' . $ex->getTraceAsString() . '<br>');
                return;
            }

            Fpt_Data_Model::sendLog($ex);
        }
        catch (Exception $ex)
        {
            return false;
        }
    }

    /**
     * Get search instance
     * @author HungNT1
     * @return <Fpt_Search>
     */
    public static function getSearch()
    {
        //Get search instance
        if (self::$_searchObject == null)
        {
            $configs = self::getApplicationIni('search');
            //Get search instance
            self::$_searchObject = Fpt_Search::factory('solr', $configs['object']['solr']);
        }

        //Return caching
        return self::$_searchObject;
    }

    /**
     * VN To ASCII
     * @author NhuantTP
     * @param <string> $str
     * @param <boolean> $toLower
     * @return <string> $str
     */
    public static function vnToAscii($str, $toLower = false)
    {
        //Check to lower
        if ($toLower)
        {
            $str = mb_strtolower($str, 'UTF-8');
        }
        else
        {
            $str = preg_replace('/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/', 'A', $str);
            $str = preg_replace('/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/', 'E', $str);
            $str = preg_replace('/(Ì|Í|Ị|Ỉ|Ĩ)/', 'I', $str);
            $str = preg_replace('/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/', 'O', $str);
            $str = preg_replace('/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/', 'U', $str);
            $str = preg_replace('/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/', 'Y', $str);
            $str = preg_replace('/(Đ)/', 'D', $str);
        }//end if
        //Replace
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);

        //Return
        return trim($str);
    }

    /**
     * get redis paging end
     * @param int $offset start: index if get range, scorestart if get score
     * @param int $limit end: index if get range, scoreend if get score
     * @param boolean $byScore
     * @param boolean $revert
     * @return type end
     */
    public static function getRedisPaging($offset = 0, $limit = 10, $byScore = false, $revert = false)
    {
        if ($byScore === true)
        {
            //when get by score:param limit mean: end score
            $end = $limit;

            if ($limit == 0)
            {
                //get till the end of this list
                $end = '+inf';
            }
            if ($offset == 0)
            {
                //get till the start of this list
                $offset = '-inf';
            }

            return array('startScore' => $offset, 'endScore' => $end);
        }
        if ($limit == 0)
        {
            $end = '-1';
        }
        else if ($limit == 1)
        {
            $end = ($offset == 0) ? 0 : $offset + $limit - 1;
        }
        else
        {
            $end = ($limit > 1) ? ($offset + $limit - 1) : ($offset + $limit);
        }
        return $end;
    }

    public static function getZoneByCateId($cateid)
    {
        //get app conf
        $config = self::getApplicationIni();
        $config = $config['zone'];

        return $config[$cateid];
    }
}
