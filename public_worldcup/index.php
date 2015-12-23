<?php    
//$time_start = microtime(true);
//Check cookie to include detect device
$deviceEnvDT = isset($_COOKIE['device_env']) ? (int)$_COOKIE['device_env'] : 0;
if ( !in_array($deviceEnvDT, array(1, 2, 4, 5)) )
{
    //MT DEV
    require_once realpath('/home/phongtx/framework/trunk/phpgiaitri/Fpt/Data/Mobile/Detect.php');
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
if (APPLICATION_ENV != 'production')
{
    apc_clear_cache(); //Xoa cache file
    apc_clear_cache('user'); //Xoa cache file
}

define('KEY_CACHE_PAGE', 'CACHE_PAGE_THETHAO_V3_' . (SWITCH_DESKTOP?5:DEVICE_ENV) . '_' . md5($_SERVER['REQUEST_URI']));
$data = apc_fetch(KEY_CACHE_PAGE);

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

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

//difened application path
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('ZONE_ENV') || define('ZONE_ENV', (getenv('ZONE_ENV') ? getenv('ZONE_ENV') : 'hn'));
// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    //get_include_path(),
    //'/home/phuongtn/framework',
    //'/home/phongtx/framework/trunk/phpgiaitri',
    '/home/thuynt2/phpgiaitri',
    APPLICATION_PATH,
    realpath(APPLICATION_PATH . '/../library'),
)));
//print_r(get_include_path());die;
//include defined configs
require_once APPLICATION_PATH . '/configs/defined-worldcup.php';
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
$config = Thethao_Global::getConfig('application', true);
//get Application Zend
$application = new Zend_Application(
        APPLICATION_ENV, $config
);
//Zend application execute
$application->bootstrap()->run();
//$end = microtime(true) - $time_start;
//echo "\n Time:" . $end.' - '.date("Y/m/d H:i:s");
//echo "\n Memory:" . round(memory_get_usage(true) / 1048576, 2) . " megabytes";
?>