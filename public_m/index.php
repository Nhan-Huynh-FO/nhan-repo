<?php

//Check cookie to include detect device
$deviceEnvDT = isset($_COOKIE['device_env']) ? intval($_COOKIE['device_env']) : 0;
if (!in_array($deviceEnvDT, array(1, 2, 3, 4, 5)))
{
    require_once realpath('/home/tiendq3/phpgiaitri/Fpt/Data/Mobile/Detect.php');
}
else
{
    //define switch desktop
    define('SWITCH_DESKTOP', $deviceEnvDT == 5 ? true : false);
    //define device env
    define('DEVICE_ENV', $deviceEnvDT == 5 ? 4 : $deviceEnvDT);
}//end if
//check to redirect feather phone - m240
if (DEVICE_ENV != 3)
{
    $urlWeb = 'http://tiendq3.v3.thethao.vnexpressdev.net' . $_SERVER['REQUEST_URI'];
    header("Location: {$urlWeb}");
    exit;
}//end if
// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));


apc_clear_cache();//Xoa cache file
apc_clear_cache('user');//Xoa cache file

define('TIME_CACHE', 900);
$key = 'CACHE_PAGE_THETHAO_V3_' . DEVICE_ENV . '_'  . md5($_SERVER['REQUEST_URI']);
$data = apc_fetch($key);

if (!empty($data))
{
    $headers = array('Since' => '', 'Match' => '');
    $temp = 'HTTP_' . strtoupper(str_replace('-', '_', 'If-Modified-Since'));
    if (!empty($_SERVER[$temp]))
    {
        $headers['Since'] = $_SERVER[$temp];
    }
    $temp = 'HTTP_' . strtoupper(str_replace('-', '_', 'If-None-Match'));
    if (!empty($_SERVER[$temp]))
    {
        $headers['Match'] = $_SERVER[$temp];
    }
    $PageWasUpdated = !($headers['Since'] != '' && (strtotime($headers['Since']) == $data['time']));
    $DoIDsMatch = $headers['Match'] != '' && (strpos($headers['Match'], $data['etag']) !== false);

    if (!$PageWasUpdated && $DoIDsMatch)
    {
        header('ETag: ' . $data['etag'], true, 304);
    }
    else
    {
        header('Cache-Control: max-age=180');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $data['time']) . ' GMT');
        header('ETag: ' . $data['etag']);
        echo $data['content'];
    }
    exit;
}

//difened application path
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('ZONE_ENV')
        || define('ZONE_ENV', (getenv('ZONE_ENV') ? getenv('ZONE_ENV') : 'hn'));
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    //get_include_path(),
    //'/home/phuongtn/framework',
    '/home/hungnt1/phpgiaitri',
    APPLICATION_PATH,
    realpath(APPLICATION_PATH . '/../library'),
)));
//print_r(get_include_path());die;
//include defined configs
require_once APPLICATION_PATH . '/configs/defined.php';
//include file Autoload for Zend
require_once 'Zend/Loader/Autoloader.php';
//include file Autoload for Fpt framwork
require_once 'Fpt/Autoloader.php';
//getInstance Autoload
$Autoloader = Zend_Loader_Autoloader::getInstance();
//set default Autoload to Fpt framework
$Autoloader->setDefaultAutoloader(array(new Fpt_Autoloader(), 'loadClass'));
//regis name space for giaitri
$Autoloader->registerNamespace('Thethao_');
//get config from application
$config = Thethao_Global::getConfig('application');
//get Application Zend
$application = new Zend_Application(
                APPLICATION_ENV,
                $config
);
//Zend application execute
$application->bootstrap()->run();
//$end = microtime(true) - $time_start;
//echo "\n Time:" . $end.' - '.date("Y/m/d H:i:s");
//echo "\n Memory:" . round(memory_get_usage(true) / 1048576, 2) . " megabytes";
?>