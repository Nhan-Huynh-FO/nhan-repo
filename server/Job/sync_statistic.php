<?php
/**
 * Usage: precache/ del cache for list statistic
 * List statistic: top view/comment + tv schedule (sntv+todaytv)
 */
//difened application path
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));
// Define application environment
defined('APPLICATION_ENV')
        || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
// Define application environment
defined('ZONE_ENV')
        || define('ZONE_ENV', (getenv('ZONE_ENV') ? getenv('ZONE_ENV') : 'hcm'));
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

error_reporting(E_ALL);
//getInstance Autoload
$Autoloader = Zend_Loader_Autoloader::getInstance();

//set default Autoload to Fpt framework
$Autoloader->setDefaultAutoloader(array(new Fpt_Autoloader(), 'loadClass'));
//regis name space for thethao
$Autoloader->registerNamespace('Thethao_')->registerNamespace('Fpt_')->registerNamespace('Job_');
//get application conf
$arrConfig   = Thethao_Global::getConfig('job');

//new application conf
$application = new Application($arrConfig);
//set to bootstrap params
Zend_Controller_Front::getInstance()->setParam('bootstrap', $application);
//unset applicaion
unset($application);

//Try execute
$result = array();
try
{
    //get model news
    $modelNews = Thethao_Model_News::getInstance();
    //get model category
    $modelCategory = Thethao_Model_Category::getInstance();
    //get full array cate
    $arrCate = $modelCategory->getFullCategoryByParentId();
    //only get id
    $arrCate = array_keys($arrCate);
    //add site_id
    $arrCate[] = SITE_ID;
    //clear cache
    $result['interaction']    = $modelNews->clearCacheTop('all', $arrCate);

    //get top view 24h all Site
    $result['top_view_all_site']    = $modelNews->getTopView24hAllSite();

	//Closed all resource
    Thethao_Global::closeResource();
    Fpt_Data_Model::_destruct();

}
catch (Exception $ex)
{
    //send log
    Thethao_Global::sendLog($ex);

    //Closed all resource
    Thethao_Global::closeResource();
    Fpt_Data_Model::_destruct();

    echo $ex->getMessage();
}