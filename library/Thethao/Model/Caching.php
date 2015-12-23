<?php

/**
 * @todo model caching
 * @return Thethao_Model_Caching
 */
class Thethao_Model_Caching extends Thethao_Model
{

    protected static $_instance = null;

    /**
     * Constructor of class
     * we don't permit an explicit call of the constructor! (like $v = new Singleton())
     */
    public function __construct()
    {
        
    }

    /**
     * Get singleton instance
     * @return Thethao_Model_Caching
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

    /**
     * call job clear cache file
     * @param array $articleDetail
     * @param string $action
     * @return boolean|array
     */
    public function clearCacheFile($action = 'file')
    {
        try
        {
            //set secret key
            $secret_key = 'e146b700f216df93a2dd5ca6e26c7c2c';

            //get app config
            $config = Thethao_Global::getApplicationIni();
            $arrServer = $config['ipserver']['thethao'];
            $host = $config['view']['host'];
            $headers = array("Host: " . $host);

            //init curl
            $arrCurl = array();
            //$arrServer = array('180.148.142.92', '180.148.142.171');
            foreach ($arrServer as $i => $server)
            {
                $arrCurl[$i] = curl_init();
                curl_setopt($arrCurl[$i], CURLOPT_HTTPHEADER, $headers);
                curl_setopt($arrCurl[$i], CURLOPT_TIMEOUT, 5);
                curl_setopt($arrCurl[$i], CURLOPT_RETURNTRANSFER, true);
                curl_setopt($arrCurl[$i], CURLOPT_URL, "http://$server/clear_file.php?action=$action&sck=$secret_key");
            }
            //create the multiple cURL handle
            $mh = curl_multi_init();
            foreach($arrCurl as $ch)
            {
                curl_multi_add_handle($mh,$ch);
            }
            //execute the handles
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            } while($running > 0);
            // get content and remove handles to debug if fall
            /*foreach($arrCurl as $id => $c) {
                $result[$id] = curl_multi_getcontent($c);
            }*/
            foreach($arrCurl as $ch)
            {
                curl_multi_remove_handle($mh, $ch);

            }
            //close the handles
            curl_multi_close($mh);
            //var_dump( $result);
            return true;
        }
        catch (Exception $ex)
        {
            //log error
            Thethao_Global::sendLog($ex);
        }
        return false;
    }

}
