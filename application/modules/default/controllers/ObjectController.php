<?php

/**
 * @author      :   AnPCD
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
     * Get fixture by match id
     * @param array $arrMatchIDs
     * @return array
     * @author QuangTM
     */
    private function __getFixtureByMatchIDs(array $arrMatchIDs)
    {
        // Get models' instance
        $objMatch = $this->view->objObject->getMatch();
        $objTeam = $this->view->objObject->getTeam();
        // Get match detail by curr segment
        $objMatch->setId($arrMatchIDs);
        $arrMatchDetails = array();
        foreach ($arrMatchIDs as $id)
        {
            //get list match
            $match = $objMatch->getDetailObjectBasic($id);
            if (!empty($match))
            {
                $objTeam->setId(array($match['team1'], $match['team2']));
            }
            $arrMatchDetails[] = $match;
        }
        return $arrMatchDetails;
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
        $strLink = $this->view->configView['url'] . $this->_request->getPathInfo();

        //check redirect link
        if ($strLink != $teamDetail['share_url'] && APPLICATION_ENV != 'development')
        {
            $this->_redirect($teamDetail['share_url'], array('code' => 301));
        }

        //get model football
        $modelTeam     = Thethao_Model_Team::getInstance();

        // Get team extend content
        $extend = $modelTeam->getDetailTeamExtendByIDs(array($teamID));

        //init param get news of playser
        $arrParam = array(
            'object_id' => $teamDetail['id'],
            'object_type' => OBJECT_TYPE_TEAM,
            'limit' => 4,
            'offset' => 0
        );
        //get article id
        $objectNews = $newsModel->getObjectNews($arrParam);

        if (!empty ($objectNews['data']))
        {
            //set id
            $newsModel->setIdBasic($objectNews['data']);
        }
        unset($arrParam);

        $arrFixtureId = array();
        // Get list match id by team with time happened ago 90days
        // Get fixture about team
        $currentTime = time();
        $pastToCurrentMatchIDs   = $objTeam->getMatchIDsByTeam($teamID, $currentTime, ($currentTime - 7776000), FALSE);
        // Get list match id by team with time happened from current + 90days
        $currentToFutureMatchIDs = $objTeam->getMatchIDsByTeam($teamID, ($currentTime + 7776000), $currentTime, FALSE);

        // Count future
        $count = count($currentToFutureMatchIDs);
        $future = false;
        $past   = false;

        if (LIMIT_MATCH_IN_TEAM < $count)
        {
            $future = true;
            $past   = true;
            $offset = $count - LIMIT_MATCH_IN_TEAM;
            $arrFixtureId = array_slice($currentToFutureMatchIDs, $offset, LIMIT_MATCH_IN_TEAM, true);
        }
        else {
            $arrTmp = $pastToCurrentMatchIDs + $currentToFutureMatchIDs;
            if (count($arrTmp) > LIMIT_MATCH_IN_TEAM)
            {
                $past   = true;
            }
            arsort($arrTmp);
            $arrFixtureId = array_slice($arrTmp, 0, LIMIT_MATCH_IN_TEAM, true);
        }
        if (!empty ($arrFixtureId))
        {
            ksort($arrFixtureId);
            $objMatch = $this->view->objObject->getMatch();
            $objMatch->setId(array_keys($arrFixtureId));
        }

        /*         * ********************* END BUSINESS HERE**************************** */
        // Assign to view
        $this->view->assign(array(
            'teamId'     => $teamID,
            'teamDetail' => $teamDetail,
            'arrNewsId' => $objectNews['data'],
            'currentTime' => $currentTime,
            'arrFixture' => $arrFixtureId,
            'extend'    => $extend[$teamDetail['id']],
            'future'    => $future,
            'past'      => $past,
            'pastScore' => current($arrFixtureId),
            'futureScore' => end($arrFixtureId),
            'intCategoryId'     => CATE_ID_BONGDA,
        ));

        // Init Menu
        $this->_request->setParam('cateid', CATE_ID_BONGDA);
        $this->view->headMeta()->setName('object_id', $teamID);
        //Add script
        $this->view->headScript()->appendFile($this->view->configView['js'] . '/team.js', 'text/javascript');
        // Set meta data
        $this->view->headTitle("Thông tin đội bóng {$teamDetail['name']}");
        $this->view->headMeta()->setName('keywords', "Thông tin, chuyển nhượng, lịch sử, kết quả, lịch thi đấu, đội bóng, {$teamDetail['name']}");
        $this->view->headMeta()->setName('description', "Thông tin, chuyển nhượng, lịch sử, kết quả, lịch thi đấu, đội bóng, {$teamDetail['name']}");
    }

    public function loadBoxFixtureAction()
    {
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        // Get params
        $intScore   = $this->_request->getParam('score', NULL);
        $strType    = $this->_request->getParam('type', NULL);
        $teamId     = $this->_request->getParam('teamId', NULL);
        // Default response
        $response = array(
            'error' => 0,
            'msg'   => 'Success',
            'html'  => NULL,
        );

        if ($intScore)
        {
            $past   = false;
            $future = false;
            $sort = false;
            $objTeam = $this->view->objObject->getTeam();
            if ($strType == 'next')
            {
                $past = true;
                $maxHappenDateTime = NULL;
                $minHappenDateTime = $intScore + 3600;
            }
            else
            {
                $sort   = true;
                $future = true;
                $maxHappenDateTime = $intScore - 3600;
                $minHappenDateTime = NULL;
            }

            // Get fixture
            $arrFixtureId   = $objTeam->getMatchIDsByTeam($teamId, $maxHappenDateTime, $minHappenDateTime, $sort);

            if (count($arrFixtureId) > LIMIT_MATCH_IN_TEAM)
            {
                $past   = true;
                $future = true;
                $arrFixture = array_slice($arrFixtureId, 0, LIMIT_MATCH_IN_TEAM, true);
            }
            else {
                if ($strType == 'next')
                {
                    $future     = false;
                }
                else
                {
                    $past       = false;
                }
                $arrFixture = $arrFixtureId;
            }
            if (!empty ($arrFixture))
            {
                asort($arrFixture);
                $objMatch = $this->view->objObject->getMatch();
                $objMatch->setId(array_keys($arrFixture));
            }
            // Set Id
            $objTeam->setId(array($teamId));

            // Assign to view
            $this->view->assign(array(
                'arrFixture'    => $arrFixture,
                'teamId'        => $teamId,
                'currentTime'   => time() - 25200,
                'future'    => $future,
                'past'      => $past,
                'pastScore' => current($arrFixture),
                'futureScore' => end($arrFixture),
            ));

            // Render html
            $response['html'] = $this->view->render('object/box_fixture.phtml');
        }
        else
        {
            $response['error'] = 1;
            $response['msg']   = 'Invalid parameters';
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
        $ajax   = $params['ajax'] ? $params['ajax'] : 0;
        // Get models' instance
        $footballModel  = Thethao_Model_Football::getInstance();
        $matchModel     = Thethao_Model_Match::getInstance();
        $teamModel      = Thethao_Model_Team::getInstance();
        // Get models' Oject instance
        $modelObject = $this->view->objObject;
        if($ajax =='fixture')
        {
            // Default response
            $response = array(
                'error' => 0,
                'msg'   => 'Success',
                'html'  => NULL,
                'ext'   => NULL,
            );

            if ($teamID)
            {
                $modelTeam = $modelObject->getTeam();
                $modelTeam->setId($teamID);

                // Get match id by team
                $arrMatchIDs = $modelTeam->getMatchIDsByTeam($teamID);
                if (is_array($arrMatchIDs) && count($arrMatchIDs))
                {
                    // Get list league and season team attended
                    $arrLeagueSeason = $teamModel->getListLeagueAndSeasonTeamAttend($teamID);
                    $arrLeagueIDs    = array_keys($arrLeagueSeason);

                    // Get league detail
                    $arrLeagueDetails = $footballModel->getListLeagueByIDs($arrLeagueIDs);

                    // Assign to view
                    $this->view->assign(array(
                        'arrLeagueDetails' => $arrLeagueDetails,
                    ));

                    // Render HTML
                    $response['html'] = $this->view->render('object/fixture.phtml');

                    // Extend param
                    $response['ext'] = $arrLeagueSeason;
                }
                else
                {
                    $response['error'] = 2;
                    $response['msg']   = 'Not found match for team';
                }
            }
            else
            {
                $response['error'] = 1;
                $response['msg']   = 'Invalid parameters';
            }
        }
        if($ajax =='load-list-fixture')
        {
            $leagueID = intval($this->_request->getParam('leagueid', 0));
            $seasonID = intval($this->_request->getParam('seasonid', 0));

            // Default response
            $response = array(
                'error' => 0,
                'msg'   => 'Success',
                'html'  => NULL,
            );

            if ($teamID)
            {
                $modelMatch = $modelObject->getMatch();
                $modelTeam = $modelObject->getTeam();
                $modelTeam->setId($teamID);
                // Get all league and season
                if (!$leagueID && !$seasonID)
                {
                    // Get matchID
                    $arrMatchIDs = array_keys($modelTeam->getMatchIDsByTeam($teamID));
                }
                else
                {
                    // Get match id by team, season, league
                    $arrMatchIDs = $matchModel->getMatchIDsByTeamLeagueSeason($teamID, $leagueID, $seasonID);
                }
                //set id
                $modelMatch->setId($arrMatchIDs);

                if (is_array($arrMatchIDs) && count($arrMatchIDs))
                {
                    // Split array into many chunks
                    $arrChunks = array_chunk($arrMatchIDs, 10);

                    // Set array match ids again
                    $arrMatchIDs = $arrChunks[0];

                    // Assign to view
                    $this->view->assign(array(
                        'arrMatchIDs'   => $arrMatchIDs
                    ));

                    // Render HTML
                    if (DEVICE_ENV == 1) {
                        $response['html']   = $this->view->render('object/list_fixture_1.phtml');
                    } else {
                        $response['html']   = $this->view->render('object/list_fixture.phtml');
                    }
                    $response['extend'] = $arrChunks;
                }
                else
                {
                    $response['error'] = 2;
                    $response['msg']   = 'Not found match for team';
                }
            }
            else
            {
                $response['error'] = 1;
                $response['msg']   = 'Invalid parameters';
            }
        }
        if($ajax =='load-list-season')
        {
            // Get params
            $arrSeasonIDs = $this->_request->getParam('arrSeasonIDs', 0);

            // Default response
            $response = array(
                'error' => 0,
                'msg'   => 'Success',
                'data'  => NULL,
            );

            if (is_array($arrSeasonIDs) && count($arrSeasonIDs))
            {
                // Get list season
                $arrSeaonDetails = $footballModel->getListSeasonByIDs($arrSeasonIDs);

                foreach ($arrSeaonDetails as $season)
                {
                    $response['data'][] = array(
                        'id' => $season['SeasonID'],
                        'name' => $season['NameSeason']
                    );

                }
            }
            else
            {
                $response['error'] = 1;
                $response['msg']   = 'Invalid parameters';
            }
        }
        if($ajax =='expand-fixture-list')
        {
            // Default response
            $response = array(
                'error' => 0,
                'msg'   => 'Success',
                'html'  => NULL,
            );

            if ($this->_request->isXmlHttpRequest())
            {
                // Get params
                $arrMatchIDs = $this->_request->getParam('arrMatchIDs', NULL);

                // Verify param request
                if (is_array($arrMatchIDs) && count($arrMatchIDs))
                {
                    $modelMatch = $modelObject->getMatch();
                    //set id
                    $modelMatch->setId($arrMatchIDs);
                    // Assign to view
                    $this->view->assign(array(
                        'arrMatchIDs'   => $arrMatchIDs
                    ));

                    // Render HTML
                    // Render HTML
                    if (DEVICE_ENV == 1) {
                        $response['html']   = $this->view->render('object/list_fixture_1.phtml');
                    } else {
                        $response['html']   = $this->view->render('object/list_fixture.phtml');
                    }
                }
                else
                {
                    $response['error'] = 2;
                    $response['msg']   = 'Invalid parameters';
                }
            }
            else
            {
                $response['error'] = 1;
                $response['msg']   = 'Invalid method';
            }

        }

        // Output
        echo Zend_Json::encode($response);

        exit;
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
        $playerModel    = Thethao_Model_Player::getInstance();
        // detail infomation player
        $modelObject = $this->view->objObject->getPlayer();

        $modelObject->setId($player_id);
        $arrPlayer = $modelObject->getDetailObjectBasic($player_id);

        $seoName = Fpt_Filter::setSeoLink($arrPlayer['name']);

        //get information of player
        // If seo name not match with title in url
        if ($player_name != $seoName)
        {
            $this->_redirect($arrPlayer['share_url']);
        }
        //danh gia cau thu
        $assessment = $playerModel->getDetailPlayerRatingTerm($player_id);

        $modelNews = $this->view->objArticle;

        //init param get news of playser
        $arrParam = array(
            'object_id' => $player_id,
            'object_type' => OBJECT_TYPE_PLAYER,
            'limit' => LIMIT_TOP_VIEW,
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
            'intCategoryId'     => CATE_ID_BONGDA,
        ));
        // Init Menu
        $this->_request->setParam('cateid', CATE_ID_BONGDA);
        $this->view->headMeta()->setName('object_id', $player_id);
        //Head title
        $this->view->headTitle('Thông tin cầu thủ ' . $arrPlayer['name'] . ' - Vnexpress.');
        //Head meta data
        $this->view->headMeta()->appendName('description', META_DESC_DOI_BONG_CAU_THU);
        $this->view->headMeta()->appendName('keywords', META_KEYWORDS_DOI_BONG_CAU_THU);

    }

    public function tinTucCauThuAction()
    {
        try
        {
            //Set param
            $this->_request->setParam('block_cate', SITE_ID);
            $params = $this->_request->getParams();
            //get player id
            $playerID = intval($this->_request->getParam('id', NULL));
            $intPage  = intval($this->_request->getParam('page', 1));
            $title    = $params[2] ? $params[2] : '';

            $canContinue = FALSE;
            //Set start offset
            $this->view->limit = $perpage     = LIMIT_NEW_MORE;
            $intStart    = ($intPage - 1) * $perpage;

            if ($playerID > 0)
            {
                $objPlayer = $this->view->objObject->getPlayer();
                $objPlayer->setId($playerID);
                $playerDetail = $objPlayer->getDetailObjectBasic($playerID);
                if (is_array($playerDetail) && !empty($playerDetail))
                {
                    $seoName = Fpt_Filter::setSeoLink($playerDetail['name']);
                    if ($title === $seoName)
                    {
                        $this->view->playerName = $playerDetail['name'];
                        $modelArticle = $this->view->objArticle;
                        //Get List ID ArticleID
                        $arrParam = array(
                            'object_id'     => $playerID,
                            'object_type'   => OBJECT_TYPE_PLAYER,
                            'limit'         => $perpage,
                            'offset'        => $intStart
                        );
                        $arrListIDNews = $modelArticle->getObjectNews($arrParam);
                        $intTotal = isset($arrListIDNews['total']) ? intval($arrListIDNews['total']) : 0;
                        //Check Data
                        if (!empty($arrListIDNews['data']))
                        {
                            $modelArticle->setIdBasic($arrListIDNews['data']);
                            //get Instance Block
                            $objBlock = Thethao_Plugin_Block::getInstance();
                            //set exclude article id from arrListIDNews
                            $objBlock->setExclude($arrListIDNews['data']);
                        }

                        //InitParam Paging
                        $strUri         = '/tin-tuc-cau-thu/' . $seoName . '-' . $playerID;
                        $arrParamPaging = array(
                            'total'        => $intTotal,
                            'page'         => $intPage,
                            'uri'          => $strUri,
                            'showItem'     => 3,
                            'perpage'      => $perpage,
                            'idPagination' => 'pagination',
                            'extEnd'       => '.html',
                            'separate'     => '-'
                        );

                        $this->view->headTitle("Tin tức cầu thủ - " . $playerDetail['name']);

                        //Head meta data
                        $this->view->headMeta()->appendName('description', "Tin tức cầu thủ - " . $playerDetail['name']);

                        /* Assign to view */
                        $this->view->assign(array(
                            'arrDataNews' => $arrListIDNews['data'],
                            'total' => $intTotal,
                            'arrParamPaging'    => $arrParamPaging,
                            'intCategoryId'     => CATE_ID_BONGDA,
                        ));

                        $canContinue = true;
                    }
                    else
                    {
                        //$this->_redirect('/tin-tuc-cau-thu/' . $seoName . '-' . $playerID . '-' . $intPage . '.html');
                    }
                }
            }

            if ($canContinue != true)
            {
                $this->_redirect('/error');
                exit;
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }

    }

    public function tayVotAction()
    {
        //Set param
        $this->_request->setParam('block_cate', SITE_ID);
        //get params
        $params = $this->_request->getParams();
        $player_id = $params['id'] ? $params['id'] : NULL;
        $player_name = $params[3] ? $params[3] : NULL;
        $gender = $this->_request->getParam('gender', 1);
        $year = $this->_request->getParam('year', date("Y"));

        //get Tennis's instance
        $tennisModel = Thethao_Model_Tennis::getInstance();
        //init player detail full
        $arrPlayer = array();

        $objTennis = $this->view->objObject->getTennis();
        $objTennis->setId($player_id);

        //get detail player tennis
        $arrPlayer['info'] = $objTennis->getDetailObjectBasic($player_id);

        //get static player tennist
        $statistic = $tennisModel->getTennisPlayerDetail(array($player_id));

        $arrPlayer['statistic'] = $statistic[$player_id];

        $seoName = Fpt_Filter::setSeoLink($arrPlayer['info']['name']);

        if ($player_name != $seoName)
        {
            $this->_redirect($arrPlayer['info']['share_url']);
        }

        //get News's instance
        $modelNews = $this->view->objArticle;
        //init param get news of playser
        $arrParam = array(
            'object_id' => $player_id,
            'object_type' => OBJECT_TYPE_TENNIS,
            'limit'     => 4,
            'offset'    => 0
        );
        $moreID = $modelNews->getObjectNews($arrParam);
        $modelNews->setIdBasic($moreID['data']);

        $articleIdPlayer = $moreID['data'];
        $matchesPlayerAttend = $tennisModel->getMatchesPlayerAttend($player_id, strtotime('-4 months'), time());
        $arrMatchesSingle = array();
        $arrMatchesDouble = array();
        foreach ($matchesPlayerAttend as $key => $value)
        {
            //single
            if ($value['match_type'] == 1)
            {
                $objTennis ->setId(array($value['player1a'],$value['player2a']));
                $value['player1a_info'] = $objTennis->getDetailObjectBasic($value['player1a']);
                $value['player2a_info'] = $objTennis->getDetailObjectBasic($value['player2a']);
                array_push($arrMatchesSingle, $value);
            }
            //double
            if ($value['match_type'] == 2)
            {
                $objTennis ->setId(array($value['player1a'],$value['player2a'],$value['player1b'],$value['player2b']));
                $value['player1a_info'] = $objTennis->getDetailObjectBasic($value['player1a']);
                $value['player1b_info'] = $objTennis->getDetailObjectBasic($value['player1b']);
                $value['player2a_info'] = $objTennis->getDetailObjectBasic($value['player2a']);
                $value['player2b_info'] = $objTennis->getDetailObjectBasic($value['player2b']);
                if ($player_id == $value['player1a'])
                {
                    $arrMatchesDouble[$value['player1b_info']['name']][$key] = $value;
                }
                if ($player_id == $value['player1b'])
                {
                    $arrMatchesDouble[$value['player1a_info']['name']][$key] = $value;
                }
                if ($player_id == $value['player2a'])
                {
                    $arrMatchesDouble[$value['player2b_info']['name']][$key] = $value;
                }
                if ($player_id == $value['player2b'])
                {
                    $arrMatchesDouble[$value['player2a_info']['name']][$key] = $value;
                }
            }
        }
        //set breakcumb
        $breakCumbOther = array(
            'link'  => $arrPlayer['info']['share_url'],
            'name'  => 'Tay vợt',
        );

        // Assign to view
        $this->view->assign(array(
            'arrPlayer' => $arrPlayer,
            'totalNews' => $moreID['total'],
            'newsPlayer' => $articleIdPlayer,
            'gender' => $gender,
            'year' => $year,
            'seoName' => $seoName,
            'categoryID' => CATE_ID_TENNIS,
            'player_id' => $player_id,
            'arrMatchesSingle' => $arrMatchesSingle,
            'arrMatchesDouble' => $arrMatchesDouble,
            'breakCumbOther' => $breakCumbOther,
            'intCategoryId'     => CATE_ID_TENNIS,
        ));

        // Init menu
        $this->_request->setParam('cateid', CATE_ID_TENNIS);
        $this->view->headMeta()->setName('object_id', $player_id);
        //Head title
        $this->view->headTitle('Thông tin tay vợt ' . $arrPlayer['info']['name'] . ' - Vnexpress');
        //Set image_src headLink
        $image_src = array(
            'rel' => 'image_src',
            'href' => !empty($arrPlayer['info']['thumbnail']) ? $this->view->Imageurl($arrPlayer['info']['thumbnail'], 'size1') : ''
        );
        $this->view->headLink($image_src);
    }

    public function tinTucTayVotAction()
    {
        try
        {
            //Set param
            $this->_request->setParam('block_cate', SITE_ID);
            //get params
            $params = $this->_request->getParams();
            $player_id = $params['id'] ? $params['id'] : NULL;
            $intPage = $params['page'] ? $params['page'] : 1;
            $title = $params[2] ? $params[2] : '';
            $canContinue = FALSE;
            //Set start offset
            $perpage = LIMIT_NEW_MORE;
            $intStart = ($intPage - 1) * $perpage;
            if ($player_id >= 0)
            {
                //get Object's instance
                $tennisObj = $this->view->objObject->getTennis();
                $tennisObj->setId($player_id);

                //get detail player tennis
                $playerDetail = $tennisObj->getDetailObjectBasic($player_id);

                if (is_array($playerDetail) && !empty($playerDetail))
                {
                    $seoName = Fpt_Filter::setSeoLink($playerDetail['name']);
                    if ($title === $seoName)
                    {
                        //get News's instance
                        $newsModel = $this->view->objArticle;
                        //init param get news of playser
                        $arrParam = array(
                            'object_id' => $player_id,
                            'object_type' => OBJECT_TYPE_TENNIS,
                            'limit' => $perpage,
                            'offset' => $intStart
                        );
                        $arrListIDNews = $newsModel->getObjectNews($arrParam);
                        //Get List ID ArticleID
                        $intTotal = isset($arrListIDNews['total']) ? intval($arrListIDNews['total']) : 0;
                        //Check Data
                        if (!empty($arrListIDNews['data']))
                        {
                            $newsModel->setIdBasic($arrListIDNews['data']);
                            //get Instance Block
                            $objBlock = Thethao_Plugin_Block::getInstance();
                            //set exclude article id from $arrListIDNews
                            $objBlock->setExclude($arrListIDNews['data']);
                        }

                        //InitParam Paging
                        $strUri = '/tin-tuc-tay-vot/' . $seoName . '-' . $player_id;
                        $arrParamPaging = array(
                            'total' => $intTotal,
                            'page' => $intPage,
                            'uri' => $strUri,
                            'showItem' => 3,
                            'perpage' => $perpage,
                            'idPagination' => 'pagination',
                            'extEnd' => '.html',
                            'separate' => '-'
                        );

                        //Head title
                        $this->view->headTitle("Tin tức tay vợt - " . $playerDetail['name_format']);
                        //Head meta data
                        $this->view->headMeta()->appendName('description', "Tin tức tay vợt - " . $playerDetail['name_format']);
                        /* Assign to view */
                        $this->view->assign(array(
                            'limit' => LIMIT_NEW_MORE,
                            'arrDataNews' => $arrListIDNews['data'],
                            'arrParamPaging' => $arrParamPaging,
                            'intCategoryId' => SITE_ID,
                            'intArticleId'  => SITE_ID,
                        ));
                        $canContinue = true;
                    }
                    else
                    {
                        $this->_redirect('/tin-tuc-tay-vot/' . $seoName . '-' . $player_id . '-' . $intPage . '.html');
                    }
                }
            }

            if ($canContinue != true)
            {
                $this->_redirect('/error');
                exit;
            }
        }
        catch (Exception $ex)
        {
            Thethao_Global::sendlog($ex);
        }
    }

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

    private function _getInfoMatch()
    {
        $params = $this->_request->getParams();
        $this->view->isMatch = true;
        $this->_matchID = intval($params['id'], 0);
        $matchSeoName = $params[3] ? $params[3] : NULL;
        // Models instance
        $modelMatch = $this->view->objObject->getMatch();
        $modelTeam = $this->view->objObject->getTeam();
        $modelMatch->setId($this->_matchID);
        // Get match detail
        $this->_matchDetail = $modelMatch->getDetailObjectBasic($this->_matchID);
        if (empty($this->_matchDetail))
        {
            $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV], array('code'=>301));
        }
        // Set id and type
        $this->_matchDetail['article_id']   = $this->_matchID;
        $this->_matchDetail['article_type'] = OBJECT_TYPE_MATCH;

        $modelTeam->setId(array($this->_matchDetail['team1'], $this->_matchDetail['team2']));

        //get detail team
        $team1 = $modelTeam->getDetailObjectBasic($this->_matchDetail['team1']);
        $team2 = $modelTeam->getDetailObjectBasic($this->_matchDetail['team2']);

        $title = Fpt_Filter::setSeoLink('tran-' . $team1['name'] . ' vs ' . $team2['name']);
        $viewApp = $this->_request->getParam('view', '');
        if ($title != $matchSeoName)
        {
            $this->_redirect($this->_matchDetail['share_url'] . ($viewApp == 'app' ? '?view=app':''));
        }
        //get Instance Block
        $this->_objBlock = Thethao_Plugin_Block::getInstance();
        // Assign to view
        $currTimeGmt = time() - 25200;
        // Int the end to show box danh gia (happen + 45phut)
        $theEnd = $currTimeGmt < $this->_matchDetail['datetime_happen'] + 2700 ? FALSE : TRUE;

        $this->view->assign(array(
            'matchDetail' => $this->_matchDetail,
            'team1' => $team1,
            'team2' => $team2,
            'actionName' => $this->_request->getActionName(),
            'seoTitle' => $title,
            'currTime' => $currTimeGmt,
            'theEnd' => $theEnd,
            'shareUrl' => $this->_matchDetail['share_url']
        ));

        // Set title
        $this->view->headTitle(' - Trận ' . $team1['name'] . ' vs ' . $team2['name']);

        // Set metadata
        $this->view->headMeta()->setName('keywords', 'Trận đấu, ảnh và video, điểm tin, thống kê và đội hình, tường thuật, đánh giá, ' . $team1['name'] . ', ' . $team2['name']);
    }

    public function thongKeAction()
    {
        //Set param
        $this->_request->setParam('block_cate', SITE_ID);
        //get common info of match
        $this->_getInfoMatch();
        // Get models
        $matchModel     = Thethao_Model_Match::getInstance();
        $playerModel    = Thethao_Model_Player::getInstance();

        // Get report type
        $arrReportTypes = $matchModel->getAllReportType();

        // Get match statistic detail
        // Team 1
        $statisticTeam1 = $matchModel->getMatchStatistic($this->_matchDetail['team1'], $this->_matchID);

        // Team 2
        $statisticTeam2 = $matchModel->getMatchStatistic($this->_matchDetail['team2'], $this->_matchID);

        $statistic = array();
        if (count($statisticTeam1) && count($statisticTeam2))
        {
            foreach ($arrReportTypes as $reportType)
            {
                $statistic[$reportType['ID']] = array(
                    'Name' => $reportType['Name'],
                    'Team1Value' => isset($statisticTeam1[$reportType['ID']]) ? $statisticTeam1[$reportType['ID']]['Values'] : 0,
                    'Team2Value' => isset($statisticTeam2[$reportType['ID']]) ? $statisticTeam2[$reportType['ID']]['Values'] : 0,
                );
            }
        }

        // Get players attend match of 2 team
        // Team 1
        $playersTeam1 = $playerModel->getPlayersTeamMatch($this->_matchID, $this->_matchDetail['team1']);

        // Team 2
        $playersTeam2 = $playerModel->getPlayersTeamMatch($this->_matchID, $this->_matchDetail['team2']);

        // Assign to view
        $this->view->assign(array(
            'statistic' => $statistic,
            'playersTeam1' => $playersTeam1,
            'playersTeam2' => $playersTeam2,
            'intCategoryId' => CATE_ID_BONGDA,
        ));

        // Init Menu
        $this->_request->setParam('cateid', CATE_ID_BONGDA);
        $this->view->headMeta()->setName('object_id', $this->_matchID);
        // Add helper
        $this->view->addHelperPath(APPLICATION_PATH . '/modules/default/views/scripts/object/helper');

        // Prepend title
        $this->view->headTitle()->prepend('Thống kê');

        // Append title
        $this->view->headTitle()->append(' - VnExpress');
    }

    public function diemTinAction()
    {
        //Set param
        $this->_request->setParam('block_cate', SITE_ID);
        //get common info of match
        $this->_getInfoMatch();
        // Detect Total Predict match
        $intTotalPredict = 0;

        // Get models' instance
        $modelMatch     = Thethao_Model_Match::getInstance();

        $newsModel = $this->view->objArticle;
        // Get diem-tin data
        $diemTin = $modelMatch->getDiemTin($this->_matchID);

        $sopCastLink = array();
        $articleDiemTin = array();
        if ($diemTin != NULL)
        {
            //set id
            $newsModel->setIdBasic($diemTin['ArticleID']);
            //set exclude article id from ArticleID
            $this->_objBlock->setExclude(array($diemTin['ArticleID']));

            // Get article detail
            $articleDiemTin = $newsModel->getArticleBasic($diemTin['ArticleID']);

            // Format sopcast
            if (!empty($diemTin['LinkSopCast']))
            {
                $arrSopCastLinks = explode("\r\n", $diemTin['LinkSopCast']);
                foreach ($arrSopCastLinks as $sopCast)
                {
                    $temp = explode('-', $sopCast);
                    $sopCastLink[trim($temp[0])] = trim($temp[1]);
                }
            }
        }
        // Process related news => process by team id
        $arrArticle1 = $arrArticle2 = array();
        $arrParamsArticle1 = array(
            'object_id'     => $this->_matchDetail['team1'],
            'object_type'   => OBJECT_TYPE_TEAM,
            'article_type'  => NULL,
            'withcore'      => TRUE,
            'limit'         => 4,
            'offset'        => 0
        );
        $arrArticle1 = $newsModel->getObjectNews($arrParamsArticle1);

        $arrParamsArticle2     = array(
            'object_id'     => $this->_matchDetail['team2'],
            'object_type'   => OBJECT_TYPE_TEAM,
            'article_type'  => NULL,
            'withcore'      => TRUE,
            'limit'         => 4,
            'offset'        => 0
        );
        $arrArticle2 = $newsModel->getObjectNews($arrParamsArticle2);
        $hasRelatedNews = FALSE;
        $arrArticleDetails = array();
        if ($arrArticle1['total'] > 0 || $arrArticle2['total'] > 0)
        {
            $arrArticleIDs = ($arrArticle1['data'] + $arrArticle2['data']);

            if (1 < count($arrArticleIDs))
            {
                arsort($arrArticleIDs);
                $arrArticleIDs = array_slice(array_keys($arrArticleIDs), 0, 4);
                $arrArticleDetails = $arrArticleIDs;
                $newsModel->setIdBasic($arrArticleIDs);
                $hasRelatedNews = TRUE;
            }
        }
        // Get max and min time to get history match
        $historyMatch2Teams = array();
        $historyMatchTeam1  = array();
        $historyMatchTeam2  = array();
        /*
        $maxTime = min(strtotime(date('Y/m/d', $this->_matchDetail['datetime_happen'])) - 1, time() - 25200);
        $minTime = $maxTime - 157680000;

        // Get history match by each team
        $historyMatchTeam1 = array_keys($modelObject->getTeam()->getMatchIDsByTeam($this->_matchDetail['team1'], $maxTime, $minTime));
        $historyMatchTeam2 = array_keys($modelObject->getTeam()->getMatchIDsByTeam($this->_matchDetail['team2'], $maxTime, $minTime));
        $modelMatch->setId($historyMatchTeam1);
        $modelMatch->setId($historyMatchTeam2);
        // Count number of history match by each team
        $numberHistoryMatchTeam1 = count($historyMatchTeam1);
        $numberHistoryMatchTeam2 = count($historyMatchTeam2);
        $historyMatch2Teams = array();
        if ($numberHistoryMatchTeam1 && $numberHistoryMatchTeam2)
        {
            // Get history competition of 2 team
            $historyMatch2Teams = array_intersect($historyMatchTeam1, $historyMatchTeam2);

            // Only the 5 matches played in history.
            if (count($historyMatch2Teams))
            {
                $historyMatch2Teams = array_slice($historyMatch2Teams, 0, 5);
            }

            // Only the 5 matches played in history
            $historyMatchTeam1 = array_slice($historyMatchTeam1, 0, 5);
            $historyMatchTeam2 = array_slice($historyMatchTeam2, 0, 5);
        }
        */
        // Get report predict match
        $hasPredict = FALSE;
        $arrTopPredict = array();
        $arrReportPredict = $modelMatch->getReportMatchPredict($this->_matchID);

        if (is_array($arrReportPredict) && count($arrReportPredict))
        {
            $hasPredict = TRUE;
            //Get top predict match
            $arrTopPredict = $modelMatch->getTopMatchPredict($this->_matchID);
            $arrTopPredict = array_slice($arrTopPredict, 0, 3);

            $intTotalPredict = intval($arrReportPredict[0] + $arrReportPredict[1] + $arrReportPredict[2]);
            if ($arrReportPredict)
            {
                $percentTeam1 = intval(($arrReportPredict[0] / $intTotalPredict) * 100);
                $percentTeam2 = intval(($arrReportPredict[1] / $intTotalPredict) * 100);
                $percentDrawn = intval(($arrReportPredict[2] / $intTotalPredict) * 100);
            }
        }
        // Get list match betrate
        $arrMatchBetrate = $modelMatch->getMatchBetrate(array($this->_matchID));
        // Assign to view
        $this->view->assign(array(
            'hasRelatedNews'     => $hasRelatedNews,
            'articleDiemTin'     => $articleDiemTin,
            'sopCastLink'        => $sopCastLink,
            'arrArticleDetails'  => $arrArticleDetails,
            'historyCompetition' => $historyMatch2Teams,
            'historyTeam1'       => $historyMatchTeam1,
            'historyTeam2'       => $historyMatchTeam2,
            'arrReportPredict'   => $arrReportPredict,
            'arrTopPredict'      => $arrTopPredict,
            'intTotalPredict'    => $intTotalPredict,
            'hasPredict'         => $hasPredict,
            'percentTeam1'       => $percentTeam1,
            'percentTeam2'       => $percentTeam2,
            'percentDrawn'       => $percentDrawn,
            'arrMatchBetrate'    => $arrMatchBetrate,
            'intCategoryId'      => CATE_ID_BONGDA,
        ));
        // Init Menu
        $this->_request->setParam('cateid', CATE_ID_BONGDA);
        $this->view->headMeta()->setName('object_id', $this->_matchID);
        // Prepend title
        $this->view->headTitle()->prepend('Thông tin trước trận');
        $this->view->headMeta()->setName('description', html_entity_decode(!empty($arrArticleDetails[0]['lead']) ? $arrArticleDetails[0]['lead'] : 'Thông tin trước trận - Trận ' . $this->team1['name'] . ' vs ' . $this->team2['name'], ENT_QUOTES, 'UTF-8'));

        // Add helper path
        $this->view->addHelperPath(APPLICATION_PATH . '/modules/default/views/scripts/object/helper');
        if ($intTotalPredict > 0)
        {
            $this->view->headScript()->appendFile($this->view->configView['js'] . '/highcharts/highcharts.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->configView['js'] . '/highcharts/modules/exporting.js', 'text/javascript');
            $this->view->headScript()->appendFile($this->view->configView['js'] . '/report.match.js', 'text/javascript');
        }
        // Append title
        $this->view->headTitle()->append(' - VnExpress');
    }

    public function tuongThuatAction() // tuongthuat
    {
        //set cache
        $this->_request->setParam('cache_file', 1);

        //Set param
        $this->_request->setParam('block_cate', SITE_ID);
        $viewApp = $this->_request->getParam('view', '');

        //get common info of match
        $this->_getInfoMatch();
        // Get models' instance
        $modelMatch     = Thethao_Model_Match::getInstance();

        $modelArticle = $this->view->objArticle;
        // Get tuong-thuat data
        $arrData = $modelMatch->getTuongThuat($this->_matchID);
        $arrData['relate_id'] = intval($arrData['relate_id']);
        $arrBlockDataId = array();
        $timeUpdate     = time();
        if ($arrData['relate_id'] > 0)
        {
            $modelArticle->setIdBasic($arrData['relate_id']);
            $tuongThuat = $modelArticle->getArticleFull($arrData['relate_id']);
            if (intval($tuongThuat['publish_time']) <= time())//is schedule article
			{
				$tuongThuat['relate_id'] = $arrData['relate_id'];
			}
            $tuongThuat['relate_id'] = $arrData['relate_id'];

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
            $timeUpdate     = $arrBlockData['time'];

            //set block id if not null
            if (!empty ($arrBlockDataId))
            {
                $objLive->setBlockId($arrBlockDataId);
            }
        }
        else
        {
            $tuongThuat['relate'] = $arrData['relate'];
        }

        $intCateIdPolyAds = isset($tuongThuat['category_id']) ? $tuongThuat['category_id'] : CATE_ID_TUONGTHUAT;
		$articleTrackingId = isset($tuongThuat['article_id']) ? $tuongThuat['article_id'] : CATE_ID_TUONGTHUAT;

        // Assign to view
        $this->view->assign(array(
            'tuongThuat'        => !is_array($tuongThuat) ? array('relate' => 'Không có thông tin tường thuật trận đấu') : $tuongThuat,
            'intCategoryId'     => $intCateIdPolyAds,
            'objLive'           => $objLive,
            'arrBlockDataId'    => $arrBlockDataId,
            'timeUpdate'        => $timeUpdate,
            'intArticleId'         => $articleTrackingId,
			'ogType' => 'website',
            'ogTitle' => $tuongThuat['title_format'] . ' - VnExpress Thể Thao',
            'ogUrl' => $tuongThuat['share_url'],
            'ogImage' => !empty($tuongThuat['thumbnail_url']) ? $this->view->ImageurlArticle($tuongThuat, 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => trim(strip_tags(html_entity_decode($tuongThuat['lead'], ENT_COMPAT, 'UTF-8')))
        ));

        // Init menu
        $this->_request->setParam('cateid', CATE_ID_TUONGTHUAT);
        $this->view->headMeta()->setName('object_id', $this->_matchID);
        // Prepend title
        $this->view->headTitle()->prepend('Tường thuật');
        $this->view->headMeta()->setName('object_id', $this->_matchID);
        // Set interval refresh page
        $currentTime = time() - 25200;
        if ($currentTime >= $this->_matchDetail['datetime_happen'] && $currentTime <= $this->_matchDetail['datetime_happen'] + 10800 && $this->_matchDetail['status'] == 1)
            $this->view->headMeta()->setHttpEquiv('REFRESH', 180);
        // Append title
        $this->view->headTitle()->append(' - VnExpress');

        //check view app
        if (!empty($viewApp) && $viewApp == 'app')
        {
            //Disable layout
            $this->_helper->layout->disableLayout(true);
            echo $this->view->render('object/applive.phtml');
            exit;
        }
    }

    public function anhVideoAction()
    {
        //get common info of match
        $this->_getInfoMatch();
        // Get models' instance
        $newsModel = $this->view->objArticle;
        // Get datetime to get data
        // $dateTimeToGetData = $this->_matchDetail['datetime_happen'] + 172800;

        // **************************** Process video => process by tag
        // Params
        $arrParamsVideo1 = array(
            'object_id'    => $this->_matchDetail['team1'],
            'object_type'  => OBJECT_TYPE_TEAM,
            'article_type' => VIDEO,
            'withcore'     => TRUE,
            'limit'        => 3,
            'offset'       => 0
            );
        $video1 = $newsModel->getObjectNews($arrParamsVideo1);
        $arrParamsVideo2     = array(
            'object_id'    => $this->_matchDetail['team2'],
            'object_type'  => OBJECT_TYPE_TEAM,
            'article_type' => VIDEO,
            'withcore'     => TRUE,
            'limit'        => 3,
            'offset'       => 0
        );
        $video2 = $newsModel->getObjectNews($arrParamsVideo2);

        $hasVideo = FALSE;
        $arrVideos = array();
        if ($video1['total'] > 0 || $video2['total'] > 0)
        {
            $arrArticleVideoIDs = $video1['data'] + $video2['data'];

            if (count($arrArticleVideoIDs))
            {
                arsort($arrArticleVideoIDs);
                $arrArticleVideoIDs = array_slice(array_keys($arrArticleVideoIDs), 0,3);
                $arrVideos = $arrArticleVideoIDs;
                $hasVideo = TRUE;
            }
        }
        // **************************** Process Ảnh => process by tag
        $arrParamsAlbum1 = array(
            'object_id'    => $this->_matchDetail['team1'],
            'object_type'  => OBJECT_TYPE_TEAM,
            'article_type' => ALBUM,
            'withcore'     => TRUE,
            'limit'        => 9,
            'offset'       => 0
        );
        $album1 = $newsModel->getObjectNews($arrParamsAlbum1);
        $arrParamsAlbum2     = array(
            'object_id'    => $this->_matchDetail['team2'],
            'object_type'  => OBJECT_TYPE_TEAM,
            'article_type' => ALBUM,
            'withcore'     => TRUE,
            'limit'        => 9,
            'offset'       => 0
        );
        $album2 = $newsModel->getObjectNews($arrParamsAlbum2);

        $hasAlbum = FALSE;
        $arrAlbums = array();

        if ($album1['total'] && $album2['total'])
        {
            $arrArticleAlbumIDs = ($album1['data'] + $album2['data']);
            if (count($arrArticleAlbumIDs))
            {
                arsort($arrArticleAlbumIDs);
                $arrArticleAlbumIDs = array_slice(array_keys($arrArticleAlbumIDs), 0, 9);
                $arrAlbums = $arrArticleAlbumIDs;
                $hasAlbum = TRUE;
            }
        }
        //Set ID
        $newsModel->setIdBasic(array_merge($arrVideos,$arrAlbums));
        //Set param
        $this->_request->setParam('block_cate', SITE_ID);
        // Assign to view
        $this->view->assign(array(
            'hasVideo'  => $hasVideo,
            'arrVideos' => array_values($arrVideos),
            'hasAlbum'  => $hasAlbum,
            'arrAlbums' => array_values($arrAlbums),
            'intCategoryId'      => CATE_ID_BONGDA,
        ));

        $this->_request->setParam('cateid', CATE_ID_BONGDA);
        $this->view->headMeta()->setName('object_id', $this->_matchID);
        // Prepend title
        $this->view->headTitle()->prepend('Ảnh và Video');
        // Append title
        $this->view->headTitle()->append(' - VnExpress');
    }


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
        $client_ip  = (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
        // Prepare for reading cache
        $memcacheInstance = Thethao_Global::getCache();
        $keyCache         = vsprintf(CACHING_PREFIX . 'client_ip:%s:%s', array($intMatchID, $client_ip));

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
        else {
            echo -1;
            return;
        }
    }

    /**
      /**
     * Get list player by team id & lates match id
     * @param id, name
     * @author ThuyNT
     */
    public function loadListPlayerAction()
    {
        // Get params
        $teamID = $this->_request->getParam('teamID', 0);
        // Default response
        $response = array(
            'error' => 0,
            'msg' => 'Success',
            'data' => NULL,
        );
        if ($teamID > 0)
        {
            $playerModel = Thethao_Model_Player::getInstance();
            // Get Players List of Match
            $arrPlayers = $playerModel->getListPlayersByTeam($teamID);
            if (!empty($arrPlayers))
            {
                $objPlayer = $this->view->objObject->getPlayer();
                $objPlayer->setId($arrPlayers);
                foreach ($arrPlayers as $id)
                {
                    $playerDetail = $objPlayer->getDetailObjectBasic($id);
                    $response['data'][] = array(
                        'id' => $playerDetail['id'],
                        'name' => $playerDetail['name']
                    );
                }
            }
            else
            {
                $response['error'] = 1;
                $response['msg'] = 'Invalid parameters';
            }

        }
        // Output
        echo Zend_Json::encode($response);
        exit;
    }

}