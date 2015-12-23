<?php

/**
 * @author      :   ThuyNT
 * @name        :   BoxLichThiDauController
 * @copyright   :   Fpt Online
 * @todo        :   LichThiDau Controller
 */
class BoxLichThiDauController extends Zend_Controller_Action
{

    /**
     * @todo - Worldcup BoxLichThiDau
     * @author ThuyNT
     */
    public function indexAction()
    {
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);
        // Get params
        $intScore   = (int)$this->_request->getParam('score', NULL);
        $strType    = $this->_request->getParam('type', NULL);
        $dataEx     = $this->_request->getParam('exclude', NULL);

        $objMatch = $this->view->objObject->getMatch();
        $matchModel = Thethao_Model_Match::getInstance();
        // Default response
        $response = array(
            'error' => 0,
            'msg'   => 'Success',
            'html'  => NULL,
            'type'  => $strType
        );
        $past   = false;
        $future = false;
        $sort = false;

        if ($intScore)
        {
            $strType = strip_tags($strType);
			$strType = ($strType != 'next' && $strType != 'prev') ? 'next':$strType;
            if ($strType == 'next')
            {
                $past = true;
                $endHappenDateTime = 1405382400; // ngay 15-7-2014
                $beginHappenDateTime = $intScore;
            }
            else
            {
                $future = true;
                $endHappenDateTime = $intScore;
               // $beginHappenDateTime = 1401667200; //ngay 2-6-2014
                $beginHappenDateTime = NULL;
            }
            // Get fixture
            $arrMatchIDs   = $matchModel->getMatchIDsByLeague(LEAGUE_ID, $beginHappenDateTime, $endHappenDateTime, $sort);
            //print_r($arrMatchIDs); die;
            if(!empty($arrMatchIDs))
            {
                if(!empty($dataEx))
                {
                    $arrMatchIDs = array_diff_key($arrMatchIDs, $dataEx);
                }

                if ($strType == 'prev')
                {
                    arsort($arrMatchIDs); //sap xep tu lon den nho
                }
                if (count($arrMatchIDs) > LIMIT_FIXTURE)
                {
                    $past   = true;
                    $future = true;
                    $arrMatchIDs = array_slice($arrMatchIDs, 0, LIMIT_FIXTURE, true);
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
                }
                asort($arrMatchIDs);

            }
        }
        else {
            /*******BOX LICH THI DAU*********** */
            $arrMatchIDs = array();
            $sort = false;
            // Get list match id by league with time happened ago 90days
            $beginHappenDateTime = time() - 18*60 *60;
            $pastToCurrentMatchIDs = array();
            $endHappenDateTime = 1405382400; // ngay 15-7-2014
            $pastToCurrentMatchIDs = $matchModel->getMatchIDsByLeague(LEAGUE_ID, ($beginHappenDateTime - 1296000), $beginHappenDateTime,$sort);
            //Get list match id by team with time happened from current -> 15/7
            $currentToFutureMatchIDs = $matchModel->getMatchIDsByLeague(LEAGUE_ID, $beginHappenDateTime, $endHappenDateTime, $sort);
            // Count future
            $countFuture = count($currentToFutureMatchIDs);
            $countPast = count($pastToCurrentMatchIDs);
            $future = false;
            $past   = false;
            if($countFuture > LIMIT_FIXTURE)
            {
                $future = true;
                $offset = 0;
                $arrMatchIDs = array_slice($currentToFutureMatchIDs, $offset, LIMIT_FIXTURE, true);
            }
            else
            {
                $arrTmp = $pastToCurrentMatchIDs + $currentToFutureMatchIDs;
                $future = false;
                arsort($arrTmp); //sort by happen date time desc
                $arrMatchIDs = array_slice($arrTmp, 0, LIMIT_FIXTURE, true);
                asort($arrMatchIDs);
            }
            if($countPast > 0)
            {
                $past   = true;
            }
        }
        if (!empty ($arrMatchIDs))
        {
            $objMatch->setId(array_keys($arrMatchIDs));
        }
        /*******END BOX LICH THI DAU*********** */
        // Assign to view
        $this->view->assign(array(
            'arrMatchIDs'       => $arrMatchIDs,
            'future'            => $future,
            'past'              => $past,
            'pastScore'         => current($arrMatchIDs),
            'futureScore'       => end($arrMatchIDs)
        ));
        // Render html
        $response['html'] = $this->view->render('common/box_lich_thi_dau.phtml');
        echo Zend_Json::encode($response);
        exit;
    }

}

?>