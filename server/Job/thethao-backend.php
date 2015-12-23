<?php

//set display error
ini_set("display_errors", 1);
error_reporting(E_ALL);

//difened application path
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
// Define application environment
defined('ZONE_ENV') || define('ZONE_ENV', (getenv('ZONE_ENV') ? getenv('ZONE_ENV') : 'hn'));
defined('DEVICE_ENV') || define('DEVICE_ENV', 4);
define('SERVER_MASTER', 'thethao');
define('FROM_JOB', 1);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    realpath(APPLICATION_PATH . '/../library'),
    realpath(APPLICATION_PATH . '/../server')
)));

//include defined configs
require_once APPLICATION_PATH . '/configs/defined.php';
require_once APPLICATION_PATH . '/../server/Job/Application.php';
//include file Autoload for Zend
require_once 'Zend/Loader/Autoloader.php';
//include file Autoload for Fpt framwork
require_once 'Fpt/Autoloader.php';

//getInstance Autoload
$Autoloader = Zend_Loader_Autoloader::getInstance();
//set default Autoload to Fpt framework
$Autoloader->setDefaultAutoloader(array(new Fpt_Autoloader(), 'loadClass'));
//regis name space for giaitri
$Autoloader->registerNamespace('Thethao_')->registerNamespace('Fpt_')->registerNamespace('Job_');
//get application conf
$arrConfig = Thethao_Global::getConfig('job');
$application = new Application($arrConfig);
Zend_Controller_Front::getInstance()->setParam('bootstrap', $application);
unset($application);
//get job hn
$arrJobConf = $arrConfig['job']['hn']['sport'];
//get job hcm
$arrHCMJobConf = $arrConfig['job']['hcm']['sport'];
//merge config server
$arrJobConf['servers'] = array_merge($arrJobConf['servers'], $arrHCMJobConf['servers']);

$objWorker = new Fpt_Job_Worker_Adapter_Gearman($arrJobConf);
//Map function register den function thuc hien worker
//framework add, edit, delete article and update category
$objWorker->addFunction($arrConfig['job']['task']['sport']['add_article'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['edit_article'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['delete_article'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['set_hot_article'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['edit_cate'], 'function_reduce');

$objWorker->addFunction($arrConfig['job']['task']['sport']['update_hot_by_cate'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['clear_cache_match'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['clear_cache_ranking'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['clear_cache_top_player'], 'function_reduce');

$objWorker->addFunction($arrConfig['job']['task']['sport']['update_top_view'], 'function_reduce');
//Job Block update
$objWorker->addFunction($arrConfig['job']['task']['sport']['thethao_updateblock'], 'function_reduce');

//Thuc hien loop de run worker
try
{
    //ini_set('memory_limit', '1G');
    $return = $objWorker->run();
}
catch (Exception $ex)
{
    $result = array('success' => 0, $ex->getMessage());
}

function function_reduce($job)
{

    $time_start = microtime(true);
    global $objWorker;
    $arrData = $objWorker->getNotifyData($job);
    echo "\n ************" . date('Y/m/d H:i:s') . "**************\n";
    var_dump($arrData);
    //Get class
    $className = $arrData['class'];
    //Get function
    $function = $arrData['function'];
    if (!($className && $function))
    {
        echo 'not class and function';
        return true;
    }
    //check params
    $args = $arrData['args'];
    try
    {
        Fpt_Data_Model::_destruct(); //close resource cache central
        $result = call_user_func_array(array($className, $function), array($args));
        if ($result === NULL)
        {
            $result = array('success' => 0, 'return null / not class or function');
        }
        else
        {
            $returnCode = ($result != false) ? 1 : 0;
            $result = array('success' => $returnCode, $result);
        }

        //Closed all resource
        Thethao_Global::closeResource();
        Fpt_Data_Model::_destruct(); //close resource cache central

        $logfile = realpath(dirname(__FILE__)) . '/logs/backend-' . $function . '-' . date("Y-m-d") . '.log';
        $logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Stream($logfile);
        $logger->addWriter($writer);
        $logdata = "\n";
        $logdata .= "Result execute :" . Zend_Json::encode($result);
        $logdata .= "\n";
        $logdata .= "Execute function: '" . $function . "' at class : '" . $className . "' with params :" . Zend_Json::encode($args) . "\n";
        $logger->log($logdata, Zend_Log::INFO);
    }
    catch (Exception $ex)
    {
        $result = array('success' => 0, $ex->getMessage());
    }
    $end = microtime(true) - $time_start;
    echo "\n Time:" . $end . ' - ' . date("Y/m/d H:i:s");
    echo "\n Memory:" . round(memory_get_usage(true) / 1048576, 2) . " megabytes";
    echo "\n Result:";
    var_dump($result);
}

?>