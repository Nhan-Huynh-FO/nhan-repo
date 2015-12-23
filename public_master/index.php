<?php
//set variable
$server_request_uri = $_SERVER['REQUEST_URI'];

//check method khong phai la GET
if ($_SERVER['REQUEST_METHOD'] != 'GET')
{
	//set list link accept post
	$arrLinkAcceptPost = array(
		'/detail/ajaxsendmail',        
	);
	
    $check_post = false;
    foreach ($arrLinkAcceptPost as $link_accept)
    {
        if (strpos($server_request_uri, $link_accept) === 0)
        {
            $check_post = true;
            break;
        }
    }
    if (!$check_post)
    {
        header('HTTP/1.0 404 Not Found');
        exit();
    }
}

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
    //MT PRODUCTION
    require_once realpath('/build/phpV3/lib/php/framework/Fpt/Data/Mobile/Detect.php');
}
else
{
    //define switch desktop
    define('SWITCH_DESKTOP', $deviceEnvDT == 5 ? TRUE : FALSE);
    define('DEVICE_ENV', $deviceEnvDT == 5 ? 4 : $deviceEnvDT);
}//end if

// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
define('FROM_JOB', 1);
//set variable
$skip_apc_cache = 0;

//check to define key cache apc
if ($server_request_uri == '/' || $server_request_uri == '/index.php')
{
    if ($http_apc)
    {
        $skip_apc_cache = 1;
    }    
	define('KEY_CACHE_PAGE', 'CACHE_HOME_THETHAO_V3_' . (SWITCH_DESKTOP ? 5 : DEVICE_ENV) . '_' . md5($_SERVER['SERVER_NAME'].'/'));
}
else
{
    define('KEY_CACHE_PAGE', 'CACHE_PAGE_THETHAO_V3_' . (SWITCH_DESKTOP ? 5 : DEVICE_ENV) . '_' . md5($server_request_uri));
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
    get_include_path(),
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