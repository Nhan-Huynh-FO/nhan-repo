<?php

/**
 * @author      :   PhongTX
 * @name        :   MatchController
 * @copyright   :   Fpt Online
 * @todo        :   Match Controller
 */
class MatchController extends Zend_Controller_Action
{

    /**
     *
     * @var int
     */
    private $_matchID = 0;

    /**
     *
     * @var array
     */
    private $_matchDetail = NULL;

    /**
     *
     * @var string
     */
    private $_metaTitle = '';

    /**
     * @todo - Worldcup Match
     * @author PhongTX
     */
    public function indexAction()
    {
        $this->_redirect("/");
    }

    private function _getInfoMatch()
    {
        $params = $this->_request->getParams();
        $this->view->isMatch = true;
        $this->_matchID = intval($params['id'], 0);
        $matchSeoName = $params[3] ? $params[3] : NULL;
        // Models instance
        $objMatch = $this->view->objObject->getMatch();
        $objTeam = $this->view->objObject->getTeam();
        $objMatch->setId($this->_matchID);
        // Get match detail
        $this->_matchDetail = $objMatch->getDetailObjectBasic($this->_matchID);

        if (empty($this->_matchDetail))
        {
            $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV], array('code' => 301));
        }
        // Set id and type
        $this->_matchDetail['article_id'] = $this->_matchID;
        $this->_matchDetail['article_type'] = OBJECT_TYPE_MATCH;

        $objTeam->setId(array($this->_matchDetail['team1'], $this->_matchDetail['team2']));

        //get object match
        $matchModel = Thethao_Model_Match::getInstance();

        //get match extend
        $extend = $matchModel->getDetailMatchExtend(array($this->_matchID));

        $this->_matchDetail['scoreCard'] = isset($extend[$this->_matchID]['scoreCard']) ? $extend[$this->_matchID]['scoreCard'] : array();

        $this->_matchDetail['formGuide'] = isset($extend[$this->_matchID]['formGuide']) ? $extend[$this->_matchID]['formGuide'] : array();

        //get detail team
        $team1 = $objTeam->getDetailObjectBasic($this->_matchDetail['team1']);
        $team2 = $objTeam->getDetailObjectBasic($this->_matchDetail['team2']);

        $title = Fpt_Filter::setSeoLink('tran-' . $team1['name'] . ' vs ' . $team2['name']);
        $viewApp = $this->_request->getParam('view', '');
        if ($title != $matchSeoName)
        {
            $this->_redirect($this->_matchDetail['share_url'] . ($viewApp == 'app' ? '?view=app':''), array('code' => 301));
        }

        // Get tuong-thuat data
        $arrData = $matchModel->getTuongThuat($this->_matchID);
        $arrData['relate_id'] = intval($arrData['relate_id']);

        $this->_matchDetail['isReport'] = 0;
        $this->_matchDetail['dataReport'] = '';
        if ($arrData['relate_id'] > 0) {
            //get page 2 report
            $arrDataExtend = $this->view->objArticle->getDetailPageExtend($arrData['relate_id'], 2);
            $this->_matchDetail['isReport'] = empty($arrDataExtend['content']) ? 0 : 1;
            $this->_matchDetail['dataReport'] = $arrDataExtend['content'];
        }

        if (!$this->_matchDetail['isReport'])
        {
            $this->_matchDetail['share_url'] = str_replace('/report/', '/tuong-thuat/', $this->_matchDetail['share_url']);
        }

        $this->_matchDetail['share_url'] = str_replace('/thong-ke/', '/tuong-thuat/', $this->_matchDetail['share_url']);

        /* End dien bien tran dau */
        $this->view->assign(array(
            'matchDetail' => $this->_matchDetail,
            'team1' => $team1,
            'team2' => $team2,
            'actionName' => $this->_request->getActionName(),
            'seoTitle' => $title,
        ));
        if (DEVICE_ENV == 1)
        {
            //Append css
            $this->view->headLink()->appendStylesheet($this->view->configView['css'] . '/m_trandau.css');
        }
        //gan meta data
        $this->_metaTitle = $team1['name'] . ' vs ' . $team2['name'] . ' World Cup 2014 - VnExpress';
    }

    /**
     * @todo - Worldcup Match
     * @author PhongTX
     */
    public function thongKeAction()
    {
        //Set param
        $this->_request->setParam('block_cate', SITE_ID);
        //get common info of match
        $this->_getInfoMatch();

        if (!empty($this->_matchDetail))
        {
            //get object match
            $playerModel = Thethao_Model_Player::getInstance();

            /* Begin - Doi hinh */
            // Get players attend match of 2 team
            // Team 1
            $playersTeam1 = $playerModel->getPlayersTeamMatch($this->_matchID, $this->_matchDetail['team1']);
            // Team 2
            $playersTeam2 = $playerModel->getPlayersTeamMatch($this->_matchID, $this->_matchDetail['team2']);
            /* End - Doi hinh */
        }
        // Assign to view
        $this->view->assign(array(
            'playersTeam1' => $playersTeam1,
            'playersTeam2' => $playersTeam2,
            'intCategoryId' => WORLD_CUP,
            'strCateCode'       => $this->_request->getParam('action')
        ));
        //gan meta data
        $titleMeta = 'Thống kê trận đấu ' . $this->_metaTitle;
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = '';
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        // Add helper
        $this->view->addHelperPath(APPLICATION_PATH . '/modules/worldcup/views/scripts/match/helper');
        //Append css
        $this->view->headLink()->appendStylesheet($this->view->configView['css'] . '/lightbox/lightbox.css');
        //add js
        $this->view->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/require.min.js');

        //Add js
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
    }

    /**
     * @todo - Worldcup Match
     * @author PhongTX
     */
    public function tuongThuatAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        //Set param
        $this->_request->setParam('block_cate', SITE_ID);
        //Get param
        $viewApp = $this->_request->getParam('view', '');
        //get common info of match
        $this->_getInfoMatch();
        // Get models' instance
        $modelMatch = Thethao_Model_Match::getInstance();

        $modelArticle = $this->view->objArticle;
        // Get tuong-thuat data
        $arrData = $modelMatch->getTuongThuat($this->_matchID);
        $arrData['relate_id'] = intval($arrData['relate_id']);

        $arrBlockDataId = array();
        $timeUpdate = time();
        if ($arrData['relate_id'] > 0)
        {
            $modelArticle->setIdBasic($arrData['relate_id']);
            $tuongThuat = $modelArticle->getArticleFull($arrData['relate_id']);
            if (intval($tuongThuat['publish_time']) <= time())//is schedule article
            {
                $tuongThuat['relate_id'] = $arrData['relate_id'];
            }

            //get all block of article
            //init model live data
            $objLive = Fpt_Data_News_Live::getInstance();

            //get list block id of article
            //init param to get block
            $arrParamBlock = array(
                'article_id' => $tuongThuat['article_id'],
                'order' => 0,
                'from_time' => NULL,
            );

            $arrBlockData = $objLive->getListBlockByArticleId($arrParamBlock);
            $arrBlockDataId = $arrBlockData['data'];
            $timeUpdate = $arrBlockData['time'];

            //set block id if not null
            if (!empty($arrBlockDataId))
            {
                $objLive->setBlockId($arrBlockDataId);
            }
        }
        else
        {
            $tuongThuat['relate'] = $arrData['relate'];
        }

        $intCateIdPolyAds = isset($tuongThuat['category_id']) ? $tuongThuat['category_id'] : WORLD_CUP;
		$articleTrackingId = isset($tuongThuat['article_id']) ? $tuongThuat['article_id'] : WORLD_CUP;

        // Assign to view
        $this->view->assign(array(
            'tuongThuat' => !is_array($tuongThuat) ? array('relate' => 'Không có thông tin tường thuật trận đấu') : $tuongThuat,
            'intCategoryId' => WORLD_CUP,
            'objLive' => $objLive,
            'arrBlockDataId' => $arrBlockDataId,
            'timeUpdate' => $timeUpdate,
            'strCateCode'       => $this->_request->getParam('action'),
            'intCateIdPolyAds' => $intCateIdPolyAds,
            'articleTrackingId' => $articleTrackingId,
			'ogType' => 'website',
            'ogTitle' => $tuongThuat['title_format'] . ' - VnExpress World Cup',
            'ogUrl' => $this->view->ShareurlWorldcup($this->_matchDetail['share_url']),
            'ogImage' => !empty($tuongThuat['thumbnail_url']) ? $this->view->ImageurlArticle($tuongThuat, 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => trim(strip_tags(html_entity_decode($tuongThuat['lead'], ENT_COMPAT, 'UTF-8')))
        ));
        //gan meta data
        $titleMeta = 'Tường thuật trận đấu ' . $this->_metaTitle;
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = '';
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        $this->view->headMeta()->setName('object_id', $this->_matchID);

        //add js
        $this->view->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/require.min.js');

        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);

        //check view app
        if (!empty($viewApp) && $viewApp == 'app')
        {
            //Disable layout
            $this->_helper->layout->disableLayout(true);
            echo $this->view->render('match/applive.phtml');
            exit;
        }
    }

    /**
     * @author HungNT
     * @return type
     */
    public function predictAction()
    {
        //get common info of match
        $this->_getInfoMatch();
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        // Get user login
        $session = new Zend_Session_Namespace(SESSION_NAMESPACE);

        $arrParams = $this->_request->getParams();
        $intMatchID = (int) ($arrParams['matchid']) ? $arrParams['matchid'] : 0;
        $intGoalTeam1 = (int) ($arrParams['goalteam1']) ? $arrParams['goalteam1'] : 0;
        $intGoalTeam2 = (int) ($arrParams['goalteam2']) ? $arrParams['goalteam2'] : 0;

        //get site id
        $client_ip = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        // Prepare for reading cache
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache = vsprintf(CACHING_PREFIX . 'client_ip:%s:%s', array($intMatchID, $client_ip));
        //get memcache
        $predict = $memcacheInstance->read($keyCache);
        if ($predict === false)
        {
            //Check matchID
            if (!$intMatchID)
            {
                echo -1;
                return;
            }
            //Check input data
            if ($intGoalTeam1 < 0 || $intGoalTeam2 < 0)
            {
                echo -2;
                return;
            }
            $params = array(
                'match_id' => $intMatchID,
                'mobile' => NULL,
                'userid' => (int) $session->userid,
                'goal_team1' => (int) $arrParams['goalteam1'],
                'goal_team2' => (int) $arrParams['goalteam2'],
                'numpeople' => NULL,
                'codepre' => NULL,
                'type' => 1
            );

            //Insert predict
            $matchModel = Thethao_Model_Match::getInstance();
            $arrReponse = $matchModel->pushJobInsertMatchPredict($params);

            // Write to cache
            if ($arrReponse)
            {
                $memcacheInstance->write($keyCache, $arrReponse, 60);
                Thethao_Global::writeMemcache($keyCache, $arrReponse, 'all', NULL, 60);
            }

            echo Zend_Json::encode($arrReponse);
        }
        else
        {
            echo -1;
            return;
        }
    }

    /**
     * function get soccer of footballmatch
     *
     */
    public function soccerAction()
    {
        header('Expires: 60000'); // cache expires 60s
        //Disable layout
        $this->_helper->layout->disableLayout(true);

        //get id of match
        $matchId = (int)$this->_request->getParam('id');

        //init response
        $respose = array();

        if ($matchId > 0)
        {
            //get common info of match
            $this->_getInfoMatch();

            switch ($this->_matchDetail['status'])
            {
                case 1:
                    //tran dau ket thuc
                    $respose['match']['time']  = 'Trước trận đấu';
                    break;
                case 2:
                    //tran dau ket thuc
                    $respose['match']['time']  = 'Kết thúc';
                    break;
                case 4:
                    $respose['match']['time'] = 'Hết hiệp 1';
                    break;
                case 6:
                    $respose['match']['time'] = 'Hết hiệp 2';
                    break;
                case 8:
                    $respose['match']['time'] = 'Hết hiệp phụ thứ nhất';
                    break;
                case 10:
                    $respose['match']['time'] = 'Hết hiệp phụ thứ hai';
                    break;
                case 11:
                    $respose['match']['time'] = 'Penalty';
                    break;
                default:
                    //tran dau dang dien ra
                    $respose['match']['time']  = $this->_matchDetail['matchTime']. "'";
                    break;
            }
            $score = $this->_matchDetail['goal1'] . ' - ' . $this->_matchDetail['goal2'];
            if($this->_matchDetail['penalty1'] > 0 || $this->_matchDetail['penalty2'] > 0)
            {
                $score .='<p class="txt_11">penalty</p>';
                $score .='<p style="color:red" class="txt_11">('. $this->_matchDetail['penalty1'] . ' - ' . $this->_matchDetail['penalty2'].')</p>';
            }

            //init match score
            $respose['match']['score'] = $score;
            //init score card
            $respose['match']['card']  = $this->view->render('match/soccer.phtml');

        }
        else {
            $respose['error'] = array('errorKey' => 'content-not-found');
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
     * @author HungNT
     * @return report Match
     */
    public function reportAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        //Set param
        $this->_request->setParam('block_cate', SITE_ID);
        //get param view app
        $viewApp = $this->_request->getParam('view', '');
        //get common info of match
        $this->_getInfoMatch();

        $strLinkTuongThuat = $this->view->ShareurlWorldcup($this->_matchDetail['share_url']);
        if (!$this->_matchDetail['isReport'])
        {
            $this->_matchDetail['share_url'] = str_replace('/report/', '/tuong-thuat/', $strLinkTuongThuat);
            $this->_redirect($this->_matchDetail['share_url'] . ($viewApp == 'app' ? '?view=app':''), array('code' => 301));
        }
        // Get models' instance
        $modelMatch = Thethao_Model_Match::getInstance();

        $modelArticle = $this->view->objArticle;
        // Get tuong-thuat data
        $arrData = $modelMatch->getTuongThuat($this->_matchID);
        $arrData['relate_id'] = intval($arrData['relate_id']);

        //str link
        $strLink = $this->_request->getPathInfo();

        $strLinkTuongThuat = $this->view->ShareurlWorldcup($this->_matchDetail['share_url']);
        $this->_matchDetail['share_url'] = str_replace('/tuong-thuat/', '/report/', $this->_matchDetail['share_url']);
        $this->_matchDetail['share_url'] = $this->view->ShareurlWorldcup($this->_matchDetail['share_url']);
        //check link
        if (strpos($this->_matchDetail['share_url'], $strLink) === FALSE && APPLICATION_ENV != 'development')
        {
            $this->_redirect($this->_matchDetail['share_url'], array('code' => 301));
        }
        if ($arrData['relate_id'] > 0)
        {
            $modelArticle->setIdBasic($arrData['relate_id']);
            //get detail article
            $tuongThuat = $modelArticle->getArticleFull($arrData['relate_id']);

            if (intval($tuongThuat['publish_time']) <= time())//is schedule article
            {
                $tuongThuat['relate_id'] = $arrData['relate_id'];
            }

        }
        else
        {
            $tuongThuat['relate'] = $arrData['relate'];
        }

        $intCateIdPolyAds = isset($tuongThuat['category_id']) ? $tuongThuat['category_id'] : WORLD_CUP;
		$articleTrackingId = isset($tuongThuat['article_id']) ? $tuongThuat['article_id'] : WORLD_CUP;

        // Assign to view
        $this->view->assign(array(
            'tuongThuat' => $tuongThuat,
            'intCategoryId' => WORLD_CUP,
            'strCateCode'       => $this->_request->getParam('action'),
            'intCateIdPolyAds' => $intCateIdPolyAds,
            'articleTrackingId' => $articleTrackingId,
            'strLinkTuongThuat' => $strLinkTuongThuat,
			'ogType' => 'website',
            'ogTitle' => $tuongThuat['title_format'] . ' - VnExpress World Cup',
            'ogUrl' => $this->_matchDetail['share_url'],
            'ogImage' => !empty($tuongThuat['thumbnail_url']) ? $this->view->ImageurlArticle($tuongThuat, 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => trim(strip_tags(html_entity_decode($tuongThuat['lead'], ENT_COMPAT, 'UTF-8')))
        ));
        //gan meta data
        $titleMeta = 'Tổng thuật trận đấu ' . $this->_metaTitle;
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = '';
        //add js
        $this->view->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/require.min.js');

        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        $this->view->headMeta()->setName('object_id', $this->_matchID);
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
        //check view app
        if (!empty($viewApp) && $viewApp == 'app')
        {
            //Disable layout
            $this->_helper->layout->disableLayout(true);
            echo $this->view->render('match/appreport.phtml');
            exit;
        }
    }
}

?>