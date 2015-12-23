<?php

/**
 * @author      :   PhuongTN
 * @name        :   server-log-error.php
 * @version     :   2012
 * @copyright   :   FPT Online
 * @todo        :   Run job server php
 */
//Load staring
echo "Staring at " . date('d/m/Y H:i:s', time()) . "\n";

//Define data
define('DOCUMENT_ROOT', realpath(dirname(__FILE__)));
define('APPLICATION_PATH', realpath(DOCUMENT_ROOT . '/../../application'));
define('LIBS_PATH', realpath(DOCUMENT_ROOT . '/../../library'));
define('SERVER_PATH', realpath(DOCUMENT_ROOT . '/../../server/Job/models'));
define('ROOT_PATH', realpath(DOCUMENT_ROOT . '/../../'));
//Setup Include Paths
set_include_path(implode(PATH_SEPARATOR, array(
            LIBS_PATH,
            SERVER_PATH,
            APPLICATION_PATH.'/modules/default/models',
            get_include_path()
        )));



//Load Autoloader
require_once 'Zend/Loader/Autoloader.php';

Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

//Check console params options
$opts = new Zend_Console_Getopt(
                array(
                    'v-i' => 'verbose option'
                )
);

//Get info console
$verbose = $opts->getOption('v');

//load application env by unix var
define('APP_ENV', isset($_SERVER['APPLICATION_ENV']) ? $_SERVER['APPLICATION_ENV'] : 'development');
define('ZONE_ENV', isset($_SERVER['ZONE_ENV']) ? $_SERVER['ZONE_ENV'] : 'hn');
$_SERVER['SERVER_MASTER'] = 'thethao';

//Print information envirement
if ($verbose) {
    echo "DOCUMENT_ROOT : " . DOCUMENT_ROOT . "\n";
    echo "APPLICATION_PATH : " . APPLICATION_PATH . "\n";
    echo "LIBS_PATH : " . LIBS_PATH . "\n";
    echo "ENVIRONMENT : " . APP_ENV . "\n";
}

//Get Configuration
$configuration = Thethao_Global::getApplicationIni();
//Load defined configuration
require_once APPLICATION_PATH . '/configs/defined.php';
//Set worker
$jobConfiguration = $configuration['job'][ZONE_ENV]['sport'];

$worker = Fpt_JobWorker::getInstance($jobConfiguration);


//Add function to worker
$worker->addFunction($jobConfiguration[$jobConfiguration['adapter']]['function']['write_cache'], 'reduceFn');
$worker->addFunction($jobConfiguration[$jobConfiguration['adapter']]['function']['init_data_team_match'], 'reduceFn');
$worker->addFunction($jobConfiguration[$jobConfiguration['adapter']]['function']['init_data_team_match_by_teamid'], 'reduceFn');
$worker->addFunction($jobConfiguration[$jobConfiguration['adapter']]['function']['init_data_league_season_by_teamid'], 'reduceFn');


//Run worker
$return = $worker->run();

//Print result
if ($verbose) {
    echo "Result : $return\n";
}

//Function worker
function reduceFn($job) {
    global $worker, $verbose;
    //Get parameters
    $params = $worker->getNotifyData($job);
    var_dump($params);
    //Init logger
    if ($verbose) {
        $logfile = DOCUMENT_ROOT . '/logs/caching-' . $params['function'] . '-' . date("Y-m-d") . '.log';
        $logger = new Zend_Log();
        $writer = new Zend_Log_Writer_Stream($logfile);
        $logger->addWriter($writer);
    }
    //Try execute
    $result = 0;
    try {
        //Get class
        $className = $params['class'];

        //Check class
        if (empty($className)) {
            return false;
        }

        //Get function
        $function = $params['function'];

        //Check function
        if (empty($function)) {
            return false;
        }

        //check params
        $args = $params['args'];


        //Starting execute script
        if (empty($args)) {
            $result = call_user_func_array(array($className, $function));
        } else {
            $result = call_user_func_array(array($className, $function), array($args));
        }

        //Try close
        Thethao_Global::closeDb();
        Thethao_Global::closeCache();
        Thethao_Global::closeRedis();
        Thethao_Global::closeGearmanClient();
        //Debug

        if ($verbose) {
            $logdata = "\n";
            $logdata .= "Result execute :" . Zend_Json::encode($result);
            $logdata .= "\n";
            $logdata .= "Execute function: '" . $function . "' at class : '" . $className . "' with params :" . Zend_Json::encode($args) . "\n";
            $logger->log($logdata, Zend_Log::INFO);

        }
    } catch (Exception $ex) {
        $logdata = "Error :" . $ex->getMessage();
        $logger->log($logdata, Zend_Log::ERR);
        echo $logdata;
    }

    //Return
    return $result;
}

