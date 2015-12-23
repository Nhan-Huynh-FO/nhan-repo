<?php

/**
 * @author      :   PhongTX
 * @name        :   LichThiDauController
 * @copyright   :   Fpt Online
 * @todo        :   LichThiDau Controller
 */
class LichThiDauController extends Zend_Controller_Action
{

    /**
     * @todo - Worldcup LichThiDau
     * @author PhongTX
     */
    public function indexAction()
    {
        $arrTemp = array();
        //Default array graoup name
        $arrGroupName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        //Default array date & array detail match
        $arrDate = $arrDetailMatch = array('vong_bang' => array(), 'vong_16' => array(), 'tu_ket' => array(), 'ban_ket' => array(), 'tranh_giai_ba' => array(), 'chung_ket' => array());
        //Init date
        $beginHappenDateTime = strtotime('2014/06/13');
        $endHappenDateTime = strtotime('2014/07/15');
        //Get instance
        $matchModel = Thethao_Model_Match::getInstance();
        //Get list match id
        $arrListMatchIds = $matchModel->getMatchIDsByLeague(LEAGUE_ID, $beginHappenDateTime, $endHappenDateTime, false);
        if (!empty($arrListMatchIds))
        {
            // Models instance
            $objMatch = $this->view->objObject->getMatch();
            //Set id
            $objMatch->setId(array_keys($arrListMatchIds));
            //get match extend
            $extend = $matchModel->getDetailMatchExtend(array_keys($arrListMatchIds));
            foreach (array_keys($arrListMatchIds) as $intMatchId)
            {
                /* End dien bien tran dau */
                $arrTemp = $objMatch->getDetailObjectBasic($intMatchId);
                $arrTemp['scoreCard'] = isset($extend[$intMatchId]['scoreCard']) ? $extend[$intMatchId]['scoreCard'] : array();
                $arrTemp['formGuide'] = isset($extend[$intMatchId]['formGuide']) ? $extend[$intMatchId]['formGuide'] : array();

                if (in_array($arrTemp['round'], $arrGroupName))
                {
                    $arrDetailMatch['vong_bang'][] = $arrTemp;
                }
                else
                {
                    switch ($arrTemp['round'])
                    {
                        case 2:
                            $arrDetailMatch['vong_16'][] = $arrTemp;
                            break;
                        case 3:
                            $arrDetailMatch['tu_ket'][] = $arrTemp;
                            break;
                        case 4:
                            $arrDetailMatch['ban_ket'][] = $arrTemp;
                            break;
                        case 5:
                            $arrDetailMatch['tranh_giai_ba'][] = $arrTemp;
                            break;
                        case 6:
                            $arrDetailMatch['chung_ket'][] = $arrTemp;
                            break;
                    }
                }
            }
        }
        //Define array date
        $arrDate['vong_bang'] = array(13 => 2, 14 => 3, 15 => 4, 16 => 3, 17 => 3, 18 => 3, 19 => 3, 20 => 3, 21 => 3, 22 => 3, 23 => 4, 24 => 4, 25 => 4, 26 => 4, 27 => 2);
        $arrDate['vong_16'] = array(28 => 1, 29 => 2, 30 => 2, 1 => 2, 2 => 1);
        $arrDate['tu_ket'] = array(4 => 1, 5 => 2, 6 => 1);
        $arrDate['ban_ket'] = array(9 => 1, 10 => 1);
        $arrDate['tranh_giai_ba'] = array(13 => 1);
        $arrDate['chung_ket'] = array(14 => 1);
        //Get currTimeGmt
        $currTimeGmt = time() - 110 * 60;
        //Get date now
        $dateNow = date('dmY',time());
        $this->view->assign(array(
            'dateNow' => $dateNow,
            'arrDetailMatch' => $arrDetailMatch,
            'arrDate' => $arrDate,
            'currTime' => $currTimeGmt,
            'strCateCode' => $this->_request->getParam('controller'),
            'ogType'            => 'website',
            'ogTitle'           => 'Lịch thi đấu World Cup 2014 - VnExpress',
            'ogUrl'             => $this->view->configView['url'].'/lich-thi-dau',
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => 'Fifa World Cup 2014 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu WC 2014 tại Brazil, tường thuật bình luận bóng đá World Cup 2014'
        ));
        //gan meta data
        $titleMeta = 'Lịch thi đấu World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = 'Fifa World Cup 2014 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu WC 2014 tại Brazil, tường thuật bình luận bóng đá World Cup 2014';
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        if (DEVICE_ENV == 1)
        {
            //Append css
            $this->view->headLink()->appendStylesheet($this->view->configView['css'] . '/m_lichthidau.css');
        }
        else
        {
            $this->view->headScript()->appendFile($this->view->configView['js'] . '/lichthidau.js');
        }
    }

}

?>