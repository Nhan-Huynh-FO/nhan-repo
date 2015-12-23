<?php
//register_shutdown_function('__shutdown');
//$time_start = microtime(true);
//check http_apc precache
$http_apc = isset($_SERVER['HTTP_APC']) && (int) $_SERVER['HTTP_APC'] == 1;

//check http apc to set device env
if ($http_apc)
{
    $deviceEnvDT = intval($_SERVER['HTTP_DEVICE']);
}
else
{
    //Check cookie to include detect device
    $deviceEnvDT = isset($_COOKIE['device_env']) ? intval($_COOKIE['device_env']) : 0;
}
if ( !in_array($deviceEnvDT, array(1, 2, 4, 5)) )
{
    //MT DEV
    require_once realpath('/home/hungnt1/phpgiaitri/Fpt/Data/Mobile/Detect.php');
    //MT QC
    //require_once realpath('/build/phpgiaitri/lib/php/framework/Fpt/Data/Mobile/Detect.php');
    //MT PRODUCTION
    //require_once realpath('/build/phpV3/lib/php/framework/Fpt/Data/Mobile/Detect.php');
}
else
{
    //define switch desktop
    define('SWITCH_DESKTOP', $deviceEnvDT == 5 ? TRUE : FALSE);
    define('DEVICE_ENV', $deviceEnvDT == 5 ? 4 : $deviceEnvDT);
}//end if

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
define('SERVER_MASTER', NULL);
define('FROM_JOB', 0);

apc_clear_cache();//Xoa cache file
apc_clear_cache('user');//Xoa cache file

//set variable
$server_request_uri = $_SERVER['REQUEST_URI'];
$skip_apc_cache = 0;

//check to define key cache apc
if ($server_request_uri == '/')
{
    if ($http_apc)
    {
        $skip_apc_cache = 1;
    }
    define('KEY_CACHE_PAGE', 'CACHE_HOME_SEAGAME_' . (SWITCH_DESKTOP ? 5 : DEVICE_ENV) . '_' . md5($server_request_uri));
}
else
{
    define('KEY_CACHE_PAGE', 'CACHE_HOME_SEAGAME_' . (SWITCH_DESKTOP ? 5 : DEVICE_ENV) . '_' . md5($server_request_uri));
}
$data = apc_fetch(KEY_CACHE_PAGE);

if (!empty($data) && !$skip_apc_cache)
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
    '/home/tiendq3/phpV3',
    APPLICATION_PATH,
    realpath(APPLICATION_PATH . '/../library'),
)));
//print_r(get_include_path());die;
//include defined configs
require_once APPLICATION_PATH . '/configs/defined-seagame.php';
//include file Autoload for Zend
require_once 'Zend/Loader/Autoloader.php';
//include file Autoload for Fpt framwork
require_once 'Fpt/Autoloader.php';
//getInstance Autoload
$Autoloader = Zend_Loader_Autoloader::getInstance();
//set default Autoload to Fpt framework
$Autoloader->setDefaultAutoloader(array(new Fpt_Autoloader(), 'loadClass'));
//regis name space for thethao
$Autoloader->registerNamespace('Thethao_');
//get config from application
$config = Thethao_Global::getConfig('application', 'seagame');

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

function __shutdown()
{
    global $time, $memory;
    $endTime = microtime(true);
    $endMemory = memory_get_usage();
    $error = error_get_last();
    print_r($error);
    echo 'Time: [' . ($endTime - $time) . '] - Memory: [' . number_format(($endMemory - $memory) / 1024) . 'Kb]';
}
?>