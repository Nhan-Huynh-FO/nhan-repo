<?php
/**
 * @author   : HungNT - 25/09/2012
 * @name : BookController
 * @copyright   : FPT Online
 * @todo    : Book Controller
 */
class LichThiDauController extends Zend_Controller_Action
{

    /**
     * @author  : HungNT - 27/12/2013
     * @name    : indexAction
     * @copyright   : FPT Online
     * @todo    : index Action
     */
    public function indexAction()
    {
        //set cache file
        $this->_request->setParam('cache_file', 1);

        //set cate to params
        $this->_request->setParam('cateid', CATE_ID_FIXTURE);
        //Set category id for block_cate user in block
        $this->_request->setParam('block_cate', CATE_ID_FIXTURE);

        // Get default params
        $group    = NULL;
        $leagueId = NULL;
        $unixTime =  strtotime(date('Y/m/d'));

        // Set gmt vn + 7
        $gmt      = 7;
        // Get models' instance
        $footballModel  = Thethao_Model_Football::getInstance();
        $matchModel     = Thethao_Model_Match::getInstance();
        $tennisModel = Thethao_Model_Tennis::getInstance();
        // Get Object Match
        $objMatch = $this->view->objObject->getMatch();

        // Get tennis matches' id by time
        $arrTennisMatches     = $tennisModel->getTennisSchedule($unixTime, $unixTime);
        // Format fixture for tennis
        $tennisFixture = array();
        $arrPlayerTennis = array();
        foreach ($arrTennisMatches as $key => $value)
        {
            $tennisFixture[$value['league_name']][$key] = $value;
            array_push($arrPlayerTennis, $value['player1a'], $value['player1b'], $value['player2a'], $value['player2b']);
        }

        //set id tennis
        $this->view->objObject->getTennis()->setId($arrPlayerTennis);

        // Get list league in QT and VN
        $arrListLeagueQT = $footballModel->getListLeagueByGroup(GROUP_FOOTBAL_QT);
        $arrListLeagueTN = $footballModel->getListLeagueByGroup(GROUP_FOOTBAL_VN);

        // Get list matches' id by happen time
        $arrMatchIDs = $matchModel->getMatchIDsHappenTime($unixTime, $leagueId, $gmt);

        // Set list matches' id
        $objMatch->setId($arrMatchIDs);

        $fixture = array();
        foreach ($arrMatchIDs as $id)
        {
            $match = $objMatch->getDetailObjectBasic($id);
            if (!isset($fixture[$match['group']]))
            {
                $fixture[$match['group']] = array(
                    $match['league_id'] => array(
                        'name'       => $match['league_name'],
                        'season_name' => $match['name_season'],
                        'league_id'   => $match['league_id'],
                        'season_id'   => $match['season_id'],
                        'arrMatches' => array($match)
                    )
                );
            }
            else
            {
                if (isset($fixture[$match['group']][$match['league_id']]))
                {
                    array_push($fixture[$match['group']][$match['league_id']]['arrMatches'], $match);
                }
                else
                {
                    $fixture[$match['group']][$match['league_id']] = array(
                        'name'       => $match['league_name'],
                        'season_name' => $match['name_season'],
                        'league_id'   => $match['league_id'],
                        'season_id'   => $match['season_id'],
                        'arrMatches' => array($match)
                    );
                }
            }
        }
        // Assign to view
        $this->view->assign(array(
            'group'           => $group,
            'currTime'        => time() - 25200, //7 * 3600 => Convert to GMT +0
            'leagueId'        => intval($leagueId),
            'prevUnixTime'    => $unixTime - 86400,
            'currUnixTime'    => $unixTime,
            'nextUnixTime'    => $unixTime + 86400,
            'fixture'         => $fixture,
            'arrListLeagueQT' => $arrListLeagueQT,
            'arrListLeagueTN' => $arrListLeagueTN,
            'gmt'             => $gmt,
            'tennisFixture'   => $tennisFixture,
            'intCategoryId'     => CATE_ID_FIXTURE,
            'intArticleId'      => CATE_ID_FIXTURE,
            'ogType'            => 'website',
            'ogTitle'           => 'Lịch thi đấu bóng đá trong nước và quốc tế - VnExpress',
            'ogUrl'             => $this->view->configView['url'] . '/lich-thi-dau/',
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => 'Lịch thi đấu bóng đá Anh,Việt Nam,Cúp C1,Tây ban Nha,Bóng đá Ý 2012 cập nhật liên tục.'
        ));

        //Prepend CSS
        $this->view->headLink()->prependStylesheet($this->view->configView['css'] . '/jquery-ui-1.8.18.custom.css');

        // Set title
        $this->view->headTitle('Lịch thi đấu bóng đá trong nước và quốc tế - VnExpress');

        // Set metadata tags
        $this->view->headMeta()
                ->appendName('description', 'Lịch thi đấu bóng đá Anh,Việt Nam,Cúp C1,Tây ban Nha,Bóng đá Ý 2012 cập nhật liên tục.')
                ->appendName('keywords', 'Lich thi dau,bong da,2012');
    }

    public function chitietAction()
    {
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $response = array(
            'error' => 0,
            'msg'   => 'Success',
            'html'  => NULL,
        );

        $date     = $this->_request->getParam('ngay', NULL);
        $leagueId = intval($this->_request->getParam('lid', 0));
        $group    = $this->_request->getParam('g', NULL);
        $gmt      = 7;

        //1 - mobile , 0 - web, tablet
        $device = DEVICE_ENV & 1;

        // Determine group
        if ($group != 'quoc-te' && $group != 'trong-nuoc')
            $group = NULL;

        if (!$leagueId)
            $leagueId = NULL;

        // Determine date to get fixture
        if (empty($date))
            $unixTime = strtotime(date('Y/m/d'));
        else
            $unixTime = strtotime($date);

        // Get models' instance
        $footballModel  = Thethao_Model_Football::getInstance();
        $matchModel     = Thethao_Model_Match::getInstance();
        $tennisModel = Thethao_Model_Tennis::getInstance();
        $objMatch = $this->view->objObject->getMatch();


        // Get list league
        // Quoc te
        $arrListLeagueQT = $arrListLeagueTN = NULL;

        if ($group == NULL || $group == 'quoc-te')
            $arrListLeagueQT = $footballModel->getListLeagueByGroup(GROUP_FOOTBAL_QT);
        if ($group == NULL || $group == 'trong-nuoc')
            $arrListLeagueTN = $footballModel->getListLeagueByGroup(GROUP_FOOTBAL_VN);

        // Get list matches' id by happen time
        $arrMatchIDs = $matchModel->getMatchIDsHappenTime($unixTime, $leagueId, $gmt);
        // Set list matches' id
        $objMatch->setId($arrMatchIDs);

        $fixture = array();
        foreach ($arrMatchIDs as $id)
        {
            $match = $objMatch->getDetailObjectBasic($id);
            if (!isset($fixture[$match['group']]))
            {
                $fixture[$match['group']] = array(
                    $match['league_id'] => array(
                        'name'       => $match['league_name'],
                        'season_name' => $match['name_season'],
                        'league_id'   => $match['league_id'],
                        'season_id'   => $match['season_id'],
                        'arrMatches' => array($match)
                    )
                );
            }
            else
            {
                if (isset($fixture[$match['group']][$match['league_id']]))
                {
                    array_push($fixture[$match['group']][$match['league_id']]['arrMatches'], $match);
                }
                else
                {
                    $fixture[$match['group']][$match['league_id']] = array(
                        'name'       => $match['league_name'],
                        'season_name' => $match['name_season'],
                        'league_id'   => $match['league_id'],
                        'season_id'   => $match['season_id'],
                        'arrMatches' => array($match)
                    );
                }
            }
        }
        $tennisFixture = array();
        if (!$device)
        {
            // Get tennis matches' id by time
            $arrTennisMatches     = $tennisModel->getTennisSchedule($unixTime, $unixTime);

            // Format fixture for tennis
            $arrPlayerTennis = array();
            if (!empty($arrTennisMatches))
            {
                foreach ($arrTennisMatches as $key => $value)
                {
                    $tennisFixture[$value['league_name']][$key] = $value;
                    array_push($arrPlayerTennis, $value['player1a'], $value['player1b'], $value['player2a'], $value['player2b']);
                }

                $this->view->objObject->getTennis()->setId($arrPlayerTennis);
            }
        }
        // Assign to view
        $this->view->assign(array(
            'group'           => $group,
            'currTime'        => time() - 25200,
            'leagueId'        => intval($leagueId),
            'prevUnixTime'    => $unixTime - 86400,
            'currUnixTime'    => $unixTime,
            'nextUnixTime'    => $unixTime + 86400,
            'fixture'         => $fixture,
            'arrListLeagueQT' => $arrListLeagueQT,
            'arrListLeagueTN' => $arrListLeagueTN,
            'gmt'             => $gmt,
            'tennisFixture'   => $tennisFixture
        ));

        $response['html'] = $this->view->render('lich-thi-dau/chitiet_'.$device.'.phtml');
        echo Zend_Json::encode($response);
        exit;
    }

    public function fixtureajaxAction()
    {
        $this->_request->setParam('cache_file', 1);
        // Disable render view
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        // Get params
        $strSeasonID = $this->_request->getParam('sid', '1,2,3,4');
        $strLeagueID = $this->_request->getParam('lid', '1,2,3,4');
        $gmt         = 0;
        $arrSeasonID = explode(',', $strSeasonID);
        $arrLeagueID = explode(',', $strLeagueID);

        // Determine unix time today (ignore hour, minute, second)
        $unixTimeToday  = intval(strtotime(date('Y/m/d')));
        // Determine day of week
        $dayOfWeek      = date('N');
        // Determine unix time of monday
        $unixTimeModay  = $unixTimeToday - ($dayOfWeek - 1) * 86400;
        // Determine unix time of sunday
        $unixTimeSunday = $unixTimeToday + ( 7 - $dayOfWeek + 1) * 86400 - 1;

        // Get model match
        $footballModel  = Thethao_Model_Football::getInstance();
        $matchModel     = Thethao_Model_Match::getInstance();
        // Get league name
        $arrLeagues = $footballModel->getListLeagueByIDs($arrLeagueID);

        $numLeagues = count($arrLeagueID);
        $arrData    = array();
        for ($i = 0; $i < $numLeagues; $i++)
        {
            array_push($arrData, $matchModel->getMatchInWeek($arrSeasonID[$i], $arrLeagueID[$i], $unixTimeModay, $unixTimeSunday));
        }

        // Assign to view
        $this->view->assign(array(
            'arrData'     => $arrData,
            'arrLeagues'  => $arrLeagues,
            'strSeasonID' => $strSeasonID,
            'strLeagueID' => $strLeagueID,
            'gmt'         => $gmt,
        ));

        echo Zend_Json::encode(array('html'=> $this->view->render('lich-thi-dau/fixtureajax.phtml' )));
    }

    public function getFixtureAction()
    {
        // Disable render view
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        // Get param
        $strLeagueID = $this->_request->getParam('lid', NULL);
        $strSeasonID = $this->_request->getParam('sid', NULL);
        $gmt         = intval($this->_request->getParam('gmt', 0));

        // Default response
        $response = array(
            'error' => 0,
            'html'  => NULL,
            'msg'   => 'Success',
        );

        if (!empty($strLeagueID) && !empty($strSeasonID))
        {
            $arrSeasonID = explode(',', $strSeasonID);
            $arrLeagueID = explode(',', $strLeagueID);

            // Determine unix time today (ignore hour, minute, second)
            $unixTimeToday  = intval(strtotime(date('Y/m/d')));
            // Determine day of week
            $dayOfWeek      = date('N');
            // Determine unix time of monday
            $unixTimeModay  = $unixTimeToday - ($dayOfWeek - 1) * 86400;
            // Determine unix time of sunday
            $unixTimeSunday = $unixTimeToday + ( 7 - $dayOfWeek + 1) * 86400 - 1;

            // Get model match
            $footballModel  = Thethao_Model_Football::getInstance();
            $matchModel     = Thethao_Model_Match::getInstance();
            // Get league name
            $arrLeagues = $footballModel->getListLeagueByIDs($arrLeagueID);

            $numLeagues = count($arrLeagueID);
            $arrData    = array();
            for ($i = 0; $i < $numLeagues; $i++)
            {
                array_push($arrData, $matchModel->getMatchInWeek($arrSeasonID[$i], $arrLeagueID[$i], $unixTimeModay, $unixTimeSunday));
            }

            // Assign to view
            $this->view->assign(array(
                'arrData'     => $arrData,
                'arrLeagues'  => $arrLeagues,
                'strSeasonID' => $strSeasonID,
                'strLeagueID' => $strLeagueID,
                'gmt'         => $gmt,
            ));

            $response['html'] = $this->view->render('lich-thi-dau/list_fixtures.phtml');
        }
        else
        {
            $response['error'] = 1;
            $response['msg']   = 'Invalid parameters';
        }

        // Output response
        echo Zend_Json::encode($response);

        exit;
    }

    public function getFixtureAffAction()
    {
        // Disable render view
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        // Get param of Aff Cup 2014
        $intLeagueID = 14;
        $intSeasonID = 154;

		$unixTimeStart 	= $this->_request->getParam('startDate', NULL);
		$unixTimeEnd 	= $this->_request->getParam('endDate', NULL);
		$type = $this->_request->getParam('type', NULL);

        // Default response
        $response = array(
            'error' => 0,
            'html'  => NULL,
            'msg'   => 'Success',
        );
		$unixTimeEnd = $unixTimeEnd + 23*60 * 60;
		//echo date('d/m/Y H:i', $unixTimeEnd);
		// Get model match
		$matchModel     = Thethao_Model_Match::getInstance();

		// Geta match of AffCup
		$arrData    = $matchModel->getMatchInWeek($intSeasonID, $intLeagueID, $unixTimeStart, $unixTimeEnd);
		//echo '<pre>';
		//print_r($arrData);die;
		if ($type == 'prev')
		{
			krsort($arrData);
		}

		//print_r($arrData);die;
		// Assign to view
		$this->view->assign(array(
			'arrData'   => $arrData,
			'type'		=> $type
		));

		$response['html'] = $this->view->render('lich-thi-dau/list_fixtures_aff.phtml');

        // Output response
        echo Zend_Json::encode($response);

        exit;
    }
}