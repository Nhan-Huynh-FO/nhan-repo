<?php

/**
 * @author  : HungNT1
 * @name    : WidgetController
 * @copyright   : FPT Online
 * @todo    : WidgetController
 */
class WidgetController extends Zend_Controller_Action
{

    /**
     * Widget hot news
     */
    public function indexAction()
    {
        //disable layout
        $this->_helper->layout->disableLayout();
        //set cache
        $this->_request->setParam('cache_file', 1);
        //Get article id
        $arrParam = $this->_request->getParams();

        switch ($arrParam['type'])
        {
            case 2:
                $intLimit = 4;
                break;
            case 1:
            default :
                $intLimit = 3;
                break;
        }

        //init parram
        $arrParams = array(
            'category_id' => SITE_ID,
            'article_type' => 0,
            'limit' => $intLimit,
            'offset' => 0,
        );
        //get Instance News
        $modelNews       = Thethao_Model_News::getInstance();
        //get data with rule 1
        $arrData = $modelNews->getListArticleIdsByRule1($arrParams);

        //set id article
        $this->view->objArticle->setIdBasic($arrData['data']);
        //check type
        $intType = 1;
        if (isset($arrParam['type']))
        {
            $intType = $arrParam['type'] > 2 ? 1 : $arrParam['type'];
        }

        $this->view->assign(array(
            'arrData'   => $arrData['data'],
            'type'      => $intType,
        ));
    }
    /**
     * Widget category
     */
    public function categoryAction()
    {
        //disable layout
        $this->_helper->layout->disableLayout();
        //set cache
        $this->_request->setParam('cache_file', 1);
        //Get article id
        $arrParam = $this->_request->getParams();

        //get Instance News
        $modelNews       = Thethao_Model_News::getInstance();

        switch ($arrParam['type'])
        {
            case 2:
                $intLimit = 4;
                break;
            case 1:
            default :
                $intLimit = 3;
                break;
        }

        //init category id
        $intCategoryId = isset($arrParam['id']) ? $arrParam['id'] : SITE_ID;

        //get info cache
        $objCategory = Thethao_Model_Category::getInstance();
        $arrCate = $objCategory->getInfoCateByBlock(array('category_id' => $intCategoryId));

        $intCategoryId = empty($arrCate['cate']['name']) ? SITE_ID : $intCategoryId;

        //init parram
        $arrParams = array(
            'category_id' => $intCategoryId,
            'article_type' => 0,
            'limit' => $intLimit,
            'offset' => 0,
        );

        //get data with rule 1
        $arrData = $modelNews->getListArticleIdsByRule1($arrParams);
        //set id article
        $this->view->objArticle->setIdBasic($arrData['data']);

        //check type
        $intType = 1;
        if (isset($arrParam['type']))
        {
            $intType = $arrParam['type'] > 2 ? 1 : $arrParam['type'];
        }

        $this->view->assign(array(
            'arrData'   => $arrData['data'],
            'type'      => $intType,
            'cateInfo'  => $arrCate,
        ));
    }

    public function matchapiAction()
    {
        //set header
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');

        //set cache
        $this->_request->setParam('cache_file', 1);

        //Disable layout
        $this->_helper->layout->disableLayout(true);
        //Set no render view
        $this->_helper->viewRenderer->setNoRender(true);

        //get param limit
        $intLimit = $this->_request->getParam('limit', 2);
        //get param league, defaul 18-worldcup
        $intLeague = $this->_request->getParam('league', 18);

        //init response
        $respose = array('data' => '', 'error' => 0);

        $matchModel = Thethao_Model_Match::getInstance();
        $modelObject = Fpt_Data_Materials_Object::getInstance();
        $beginHappenDateTime = (time() - (100*60)); //lay truoc do 100p
        $endHappenDateTime = 1405382400; // ngay 15-7-2014

        // Get list match id by league id with time happened from current -> 15/7
        $arrMatchIDs = $matchModel->getMatchIDsByLeague($intLeague, $beginHappenDateTime, $endHappenDateTime, false);

        // Count future
        $count = count($arrMatchIDs);

        if ($count > $intLimit)
        {
            $arrMatchIDs = array_slice($arrMatchIDs, 0, $intLimit, true);
        }

        if (!empty($arrMatchIDs))
        {
            $modelObject->getMatch()->setId(array_keys($arrMatchIDs));
            $respose['data'] = $arrMatchIDs;
        }

        $jsonData = Zend_Json::encode($respose);

        # JSON if no callback
        if (!isset($_GET['callback']))
        {
            echo($jsonData);exit;
        }
        //init model new
        $modelNew = Thethao_Model_News::getInstance();
        # JSONP if valid callback
        if ($modelNew->isValidCallback($_GET['callback']))
        {
            echo("{$_GET['callback']}($jsonData)");exit;
        }
    }

    /**
     * get team id and player id of team
     */
    public function teamapiAction()
    {
        //set header
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');

        //set cache
        $this->_request->setParam('cache_file', 1);

        //Disable layout
        $this->_helper->layout->disableLayout(true);
        //Set no render view
        $this->_helper->viewRenderer->setNoRender(true);

        //init response
        $respose = array('data' => '', 'error' => 1);

        $modelObject = Fpt_Data_Materials_Object::getInstance();
        //get models instance
        $modelFootball = Thethao_Model_Football::getInstance();
        //get table with seasonId and leagueId
        $tableRanking = $modelFootball->getListTableRanking(153, 18);
		$arrTeamPlayer = array();
        if (!empty($tableRanking))
        {
            //set id object
            $modelObject->getTeam()->setId(array_keys($tableRanking));
            $playerModel = Thethao_Model_Player::getInstance();
            foreach ($tableRanking as $teamId => $table)
            {
                // Get Players List of Match
                $arrPlayers = $playerModel->getListPlayersByTeam($teamId);
                $arrTeamPlayer[$teamId] = $arrPlayers;
            }
			$respose['error'] = 0;
        }
        $respose['data'] = $arrTeamPlayer;
        $jsonData = Zend_Json::encode($respose);

        # JSON if no callback
        if (!isset($_GET['callback']))
        {
            echo($jsonData);exit;
        }
        //init model new
        $modelNew = Thethao_Model_News::getInstance();
        # JSONP if valid callback
        if ($modelNew->isValidCallback($_GET['callback']))
        {
            echo("{$_GET['callback']}($jsonData)");exit;
        }
    }

    /**
     * Get table worldcup
     */
    public function tableapiAction()
    {
        //set header
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        //set cache
        $this->_request->setParam('cache_file', 1);
        //Disable layout
        $this->_helper->layout->disableLayout(true);
        //Set no render view
        $this->_helper->viewRenderer->setNoRender(true);

        //init response
        $respose = array('data' => '', 'error' => 1);

        $modelObject = Fpt_Data_Materials_Object::getInstance();
        // Get models instance
        $modelFootball = Thethao_Model_Football::getInstance();
        //get table with seasonId and leagueId
        $tableRanking = $modelFootball->getListTableRanking(153, 18);

        if (!empty($tableRanking))
        {
            //set id object
            $modelObject->getTeam()->setId(array_keys($tableRanking));
            $respose['error'] = 0;
        }
        $respose['data'] = $tableRanking;
        $jsonData = Zend_Json::encode($respose);
        # JSON if no callback
        if (!isset($_GET['callback']))
        {
            echo($jsonData);exit;
        }
        //init model new
        $modelNew = Thethao_Model_News::getInstance();
        # JSONP if valid callback
        if ($modelNew->isValidCallback($_GET['callback']))
        {
            echo("{$_GET['callback']}($jsonData)");exit;
        }
    }
}