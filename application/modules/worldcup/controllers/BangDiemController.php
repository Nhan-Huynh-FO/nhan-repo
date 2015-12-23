<?php

/**
 * @author      :   PhongTX
 * @name        :   BangDiemController
 * @copyright   :   Fpt Online
 * @todo        :   BangDiem Controller
 */
class BangDiemController extends Zend_Controller_Action
{

    /**
     * @todo - Worldcup bang diem page
     * @author PhongTX
     */
    public function indexAction()
    {
        $leagueId = LEAGUE_ID;
        $seasonId = SEASON_ID;

        //Set category id for block_cate user in block
        $this->_request->setParam('block_cate', WORLD_CUP);
        // Get models instance
        $modelFootball = Thethao_Model_Football::getInstance();

        // Get detail table ranking by league and season
        $tableRanking = $modelFootball->getListTableRanking($seasonId, $leagueId);
        if (!empty($tableRanking))
        {
            //set id object
            $this->view->objObject->getTeam()->setId(array_keys($tableRanking));
            $temp = current($tableRanking);
            $maxUpdateTime = $temp['UpdateTime'];
        }
        else
        {
            $maxUpdateTime = time();
        }

        // Get league detail
        $leagueDetail = $modelFootball->getListLeagueByIDs(array($leagueId));

        // Get season by league
        $arrSeasonByLeague = $modelFootball->getListSeasonByLeagueID($leagueId);
        $directUrL = $this->view->configView['url'] . '/bang-diem';
        //gan meta data
        $titleMeta = 'Bảng điểm World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = 'Fifa World Cup 2014 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu WC 2014 tại Brazil, tường thuật bình luận bóng đá World Cup 2014';
        // Assign to view
        $this->view->assign(array(
            'tableRanking' => $tableRanking,
            'leagueDetail' => $leagueDetail[0],
            'leagueID' => $leagueId,
            'seasonID' => $seasonId,
            'maxUpdateTime' => $maxUpdateTime,
            'ogType' => 'website',
            'ogTitle' => $titleMeta,
            'ogUrl' => $directUrL,
            'ogImage' => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => $description,
            'strCateCode'       => $this->_request->getParam('controller')
        ));

        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);

        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
    }

}
