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
//regis name space for thethao
$Autoloader->registerNamespace('Thethao_')->registerNamespace('Fpt_')->registerNamespace('Job_');
//get application conf
$arrConfig = Thethao_Global::getConfig('application');
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

//job send mail in detail
$objWorker->addFunction($arrConfig['job']['task']['sport']['send_mail'], 'function_reduce');
//job predict in match detail
$objWorker->addFunction($arrConfig['job']['task']['sport']['match_predict'], 'function_reduce');
//job clear apc from framework
$objWorker->addFunction($arrConfig['job']['task']['sport']['clearapcfile'], 'function_reduce');

//loop de run worker
$objWorker->run();

function function_reduce($job)
{
    global $objWorker;
    $arrData = $objWorker->getNotifyData($job);

    //Get class
    $className = $arrData['class'];
    //Get function
    $function = $arrData['function'];
    if (!($className && $function))
    {
        var_dump($className, $function);
        echo 'not class and function 1';
        return true;
    }
    //check params
    $args = $arrData['args'];

    try
    {
        Thethao_Global::closeResource(); //close resource cache central

        $result = call_user_func_array(array($className, $function), array($args));
        if ($result === NULL)
        {
            var_dump($className, $function);
            echo 'not class, function or return null ';
        }

        Thethao_Global::closeResource();

        $logfile = realpath(dirname(__FILE__)) . '/logs/fontend-' . $function . '-' . date("Y-m-d") . '.log';
        $logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Stream($logfile);
        $logger->addWriter($writer);
        $logdata = "\n";
        $logdata .= "Result execute :" . Zend_Json::encode($result);
        $logdata .= "\n";
        $logdata .= "Execute function: '" . $function . "' at class : '" . $className . "' with params :" . Zend_Json::encode($args) . "\n";
        echo "====>Result execute :" . Zend_Json::encode($result);
        $logger->log($logdata, Zend_Log::INFO);
    }
    catch (Exception $ex)
    {
        print_r($ex);
        return false;
    }
    return true;
}

?>