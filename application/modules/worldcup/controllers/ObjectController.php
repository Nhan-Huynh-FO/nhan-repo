<?php

/**
 * @author      :   ThuyNT
 * @name        :   ObjectController
 * @copyright   :   Fpt Online
 * @todo        :   Object Controller
 */
class ObjectController extends Zend_Controller_Action
{

    public function indexAction()
    {

    }

    /**
     * Get Player Info
     * @param player id, seoname
     * @author dungdv8
     */
    public function cauThuAction()
    {
        //get player id
        $player_id = $this->_request->getParam('id', NULL);
        $arrParam = $this->_request->getParams();
        $player_name = $arrParam[3];

        //get model player
        $playerModel = Thethao_Model_Player::getInstance();
        // detail infomation player
        $modelObject = $this->view->objObject->getPlayer();

        $modelObject->setId($player_id);
        $arrPlayer = $modelObject->getDetailObjectBasic($player_id);

        $seoName = Fpt_Filter::setSeoLink($arrPlayer['name']);

        //get information of player
        // If seo name not match with title in url
        if ($player_name != $seoName)
        {
            $this->_redirect($this->view->ShareurlWorldcup($arrPlayer['share_url']));
        }
        //danh gia cau thu
        $assessment = $playerModel->getDetailPlayerRatingTerm($player_id);

        $modelNews = $this->view->objArticle;

        //init param get news of playser
        $arrParam = array(
            'object_id' => $player_id,
            'object_type' => OBJECT_TYPE_PLAYER,
            'limit' => LIMIT_RELATED_PLAYER,
            'offset' => 0
        );

        $objectNews = $modelNews->getObjectNews($arrParam);

        unset($arrParam);

        //set id
        $modelNews->setIdBasic($objectNews['data']);
        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        //set exclude article id from objectNews
        $objBlock->setExclude($objectNews['data']);

        // Award local
        $localAward = $playerModel->getDetailPlayerByID($player_id, 1);

        // Group award local
        $local = $playerModel->groupPlayerAward($localAward);
        // Award international
        $interAward = $playerModel->getDetailPlayerByID($player_id, 2);

        // Group award international
        $inter = $playerModel->groupPlayerAward($interAward);
        //set award's names
        $awardType = array(1 => 'Vô địch', 2 => 'Á quân', 3 => 'Hạng 3');
        // information transfer player
        $infotr = $playerModel->getInformationTransfer($player_id);
        //assign to view
        $this->view->assign(array(
            'arrPlayer' => $arrPlayer,
            'assessment' => $assessment,
            'newsPlayer' => $objectNews['data'],
            'locals' => $local,
            'inter' => $inter,
            'awardType' => $awardType,
            'arrInfotr' => $infotr,
            'objArticle' => $modelNews,
            'seoName' => $seoName,
            'intCategoryId' => WORLD_CUP,
            'strCateCode'       => $this->_request->getParam('action')
        ));
        //gan meta data
        $titleMeta = 'Thông tin cầu thủ ' . $arrPlayer['name'] . ' World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = '';
        // Init Menu
        $this->_request->setParam('cateid', WORLD_CUP);
        $this->view->headMeta()->setName('object_id', $player_id);
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
    }

    /**
     * Get Team Info
     * @param team id, seoname
     * @author HungNT3
     */
    public function doiBongAction()
    {
        //Set param
        $this->_request->setParam('block_cate', SITE_ID);
        // Get param teamId
        $teamID = $this->_request->getParam('id', null);
        $arrParam = $this->_request->getParams();


        $newsModel = $this->view->objArticle;
        $objTeam = $this->view->objObject->getTeam();

        // Get team detail
        $objTeam->setId($teamID);
        $teamDetail = $objTeam->getDetailObjectBasic($teamID);

        //str link
        $strLink = $this->_request->getPathInfo();

        //check redirect link
        if (strpos($teamDetail['share_url'], $strLink) === FALSE && APPLICATION_ENV != 'development')
        {
            $this->_redirect($this->view->ShareurlWorldcup($teamDetail['share_url']), array('code' => 301));
        }

        //get model football
        $modelTeam = Thethao_Model_Team::getInstance();

        // Get team extend content
        $extend = $modelTeam->getDetailTeamExtendByIDs(array($teamID));

        //init param get news of playser
        $arrParam = array(
            'object_id' => $teamDetail['id'],
            'object_type' => OBJECT_TYPE_TEAM,
            'limit' => LIMIT_RELATED_PLAYER,
            'offset' => 0
        );
        //get article id
        $objectNews = $newsModel->getObjectNews($arrParam);

        if (!empty($objectNews['data']))
        {
            //set id
            $newsModel->setIdBasic($objectNews['data']);
        }
        unset($arrParam);

        $arrFixtureId = array();
        // Get list match id by team with time happened ago 90days
        // Get fixture about team
        $currentTime = time();
        $endTime = 1405382400; // ngay 15-7-2014
		$pastTime = 1398902400; //ngay 1-5-2014

        // Get list match id by team with time happened from current -> 15/7
        $arrFixtureId = $objTeam->getMatchIDsByTeam($teamID, $endTime, $pastTime, false);
        $arrFixtureId = array_unique($arrFixtureId);

        $countItem = count($arrFixtureId);
        $currentItem = 0;
        if (!empty($arrFixtureId))
        {
            $objMatch = $this->view->objObject->getMatch();
            // ksort($arrFixtureId);
            $objMatch->setId(array_keys($arrFixtureId));

            foreach ($arrFixtureId as $happenTime)
            {
                if ($currentTime > $happenTime)
                {
                    $currentItem++;
                }
                else {
                    break;
                }
            }
        }
        $currentItem--;
        if ($currentItem > 0)
        {
            $arrCurrentItem = array_slice($arrFixtureId, $currentItem, $countItem, true);
            $arrPastItem = array_slice($arrFixtureId, 0, $currentItem, true);
            $arrFixtureId = $arrCurrentItem + $arrPastItem;
            asort($arrFixtureId);
        }

        // Assign to view
        $this->view->assign(array(
            'teamId' => $teamID,
            'teamDetail' => $teamDetail,
            'arrNewsId' => $objectNews['data'],
            'countItem' => $countItem,
            'arrFixture' => $arrFixtureId,
            'extend' => $extend[$teamDetail['id']],
            'intCategoryId' => WORLD_CUP,
            'strCateCode'       => $this->_request->getParam('action')
        ));
        if (DEVICE_ENV == 1)
        {
            //Append css
            $this->view->headLink()->appendStylesheet($this->view->configView['css'] . '/m_lichthidau.css');
        }
        //gan meta data
        $titleMeta = 'Thông tin đội bóng ' . $teamDetail['name'] . ' World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = '';
        // Init Menu
        $this->_request->setParam('cateid', WORLD_CUP);
        $this->view->headMeta()->setName('object_id', $teamID);
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
    }

    public function loadBoxFixtureAction()
    {
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        // Get params
        $intScore = $this->_request->getParam('score', NULL);
        $strType = $this->_request->getParam('type', NULL);
        $teamId = $this->_request->getParam('teamId', NULL);
        // Default response
        $response = array(
            'error' => 0,
            'msg' => 'Success',
            'html' => NULL,
        );

        if ($intScore)
        {
            $past = false;
            $future = false;
            $sort = false;
            $objTeam = $this->view->objObject->getTeam();
            $objMatch = $this->view->objObject->getMatch();
            if ($strType == 'next')
            {
                $past = true;
                if (DEVICE_ENV == 1)
                {
                    $intScore++;
                }
                $maxHappenDateTime = 1405382400; // ngay 15-7-2014;
                $minHappenDateTime = $intScore;
            }
            else
            {
                $sort = true;
                $future = true;
                if (DEVICE_ENV == 1)
                {
                    $intScore--;
                }
                $maxHappenDateTime = $intScore;
                $minHappenDateTime = 1398902400; //ngay 1-5-2014;
            }

            // Get fixture
            $arrFixtureId = $objTeam->getMatchIDsByTeam($teamId, $maxHappenDateTime, $minHappenDateTime, $sort);
            if (count($arrFixtureId) > LIMIT_MATCH_IN_TEAM)
            {
                $past = true;
                $future = true;
                $arrFixture = array_slice($arrFixtureId, 0, LIMIT_MATCH_IN_TEAM, true);
            }
            else
            {
                if ($strType == 'next')
                {
                    $future = false;
                }
                else
                {
                    $past = false;
                }
                $arrFixture = $arrFixtureId;
            }

            asort($arrFixture);
            $objMatch->setId(array_keys($arrFixture));
             // Set Id
            $objTeam->setId(array($teamId));
            // Assign to view
            $this->view->assign(array(
                'arrFixture' => $arrFixture,
                'teamId' => $teamId,
                'currentTime' => time() - 25200,
                'futureFixture' => $future,
                'pastFixture' => $past,
                'pastScoreFixture' => current($arrFixture),
                'futureScoreFixture' => end($arrFixture),
            ));

            // Render html
            $response['html'] = $this->view->render('object/box_fixture.phtml');
        }
        else
        {
            $response['error'] = 1;
            $response['msg'] = 'Invalid parameters';
        }

        echo Zend_Json::encode($response);

        exit;
    }

    public function loadListFixtureAction()
    {
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        // Get params
        $params = $this->_request->getParams();
        $teamID = $params['id'] ? $params['id'] : 0;
        // Get models' Oject instance
        $modelObject = $this->view->objObject;
        // Default response
        $response = array(
            'error' => 0,
            'msg' => 'Success',
            'html' => NULL
        );

        if ($teamID)
        {
            $objTeam = $modelObject->getTeam();
            $maxHappenDateTime = 1405382400; // ngay 15-7-2014;;
            $minHappenDateTime = 1398902400; //ngay 1-5-2014
            // Get match id by team
            $arrFixture = $objTeam->getMatchIDsByTeam($teamID, $maxHappenDateTime, $minHappenDateTime, $sort);
            if (is_array($arrFixture) && !empty($arrFixture))
            {
                arsort($arrFixture);
                // Set Id
                $objTeam->setId(array($teamID));
                $modelObject->getMatch()->setId(array_keys($arrFixture));

                // Assign to view
                $this->view->assign(array(
                    'arrFixture' => $arrFixture
                ));
                // Render HTML
                if(DEVICE_ENV == 1)
                {
                    $response['html'] = $this->view->render('mobile/list_fixture.phtml');
                }
                else
                {
                    $response['html'] = $this->view->render('object/list_fixture.phtml');
                }
            }
            else
            {
                $response['error'] = 2;
                $response['msg'] = 'Not found match for team';
            }
        }
        else
        {
            $response['error'] = 1;
            $response['msg'] = 'Invalid parameters';
        }


        // Output
        echo Zend_Json::encode($response);

        exit;
    }

}
