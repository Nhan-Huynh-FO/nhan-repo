<?php
error_reporting(E_ALL);
//difened application path
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
// Define application environment
defined('ZONE_ENV')
        || define('ZONE_ENV', (getenv('ZONE_ENV') ? getenv('ZONE_ENV') : 'hn'));
define('SERVER_MASTER', 'thethao');
define('FROM_JOB', 1);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            get_include_path(),
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/../server')
        ))); //get_include_path(),
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
$arrConfig = Thethao_Global::getConfig('job');
$application = new Application($arrConfig);
Zend_Controller_Front::getInstance()->setParam('bootstrap', $application);
unset($application);
$arrJobConf = $arrConfig['job'][ZONE_ENV]['sport'];

$objWorker = new Fpt_Job_Worker_Adapter_Gearman($arrJobConf);

//Add function to worker
$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_cache_match'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_cache_relatedmatch'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_bxh_match'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_match_betrate'], 'function_reduce');

$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_player_champion'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_player_cache'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_team_cache'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['delete_match'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_olympicranking_cache'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_tennis_ranking_cache'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_tennis_schedule_cache'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['background_crawler'], 'function_reduce');

$objWorker->addFunction($arrConfig['job']['task']['sport']['rewrite_keybox'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['thethao_updatequestion'], 'function_reduce');
$objWorker->addFunction($arrConfig['job']['task']['sport']['thethao_updateaward'], 'function_reduce');

//Thuc hien loop de run worker
$objWorker->run();

function function_reduce($job) {
    ini_set('memory_limit','1G');
    ini_set('display_errors',1);
    global $objWorker;
    $arrData = $objWorker->getNotifyData($job);
    //Get class
    $className = $arrData['class'];
    //Get function
    $function = $arrData['function'];

    if (!($className && $function)) {
        echo 'not class or function 1"' . $className .'", "' .$function . '"';
        return true;
    }
    //check params
    $args = $arrData['args'];
    try {
        Fpt_Data_Model::_destruct();//close resource cache central
        $result = call_user_func_array(array($className, $function), array($args));
        if ($result === NULL) {
            echo 'not class or function return null"' . $className .'", "' .$function . '"';
        }
        var_dump('===Result===',Zend_Json::encode($result),'______________________________________');

        Thethao_Global::closeResource();
        Fpt_Data_Model::_destruct();//close resource cache central
		/*
		$logfile = realpath(dirname(__FILE__)) . '/logs/crawler-' . $function . '-' . date("Y-m-d") . '.log';
        $logger  = new Zend_Log();
        $writer  = new Zend_Log_Writer_Stream($logfile);
        $logger->addWriter($writer);
        $logdata = "\n";
        $logdata .= "Result execute :" . Zend_Json::encode($result);
        $logdata .= "\n";
        $logdata .= "Execute function: '" . $function . "' at class : '" . $className . "' with params :" . Zend_Json::encode($args) . "\n";
        $logger->log($logdata, Zend_Log::INFO);*/
    }
    catch (Exception $ex)
    {
        print_r($ex);
        return false;
    }
    return true;
}
?>