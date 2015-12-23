<?php

/**
 * @copyright       : FOSP
 * @version         : 20120322
 * @todo            : Table ranking controller
 * @name            : RankingController
 * @author          : QuangTM
 */
class FootballController extends Zend_Controller_Action
{

    /**
     * Action show table ranking detail by season and league
     * @author QuangTM
     */
    public function bangXepHangAction()
    {
        $leagueId        = intval($this->_request->getParam('lid', 1));
        $seasonId        = intval($this->_request->getParam('sid', 0));

        //Set category id for block_cate user in block
        $this->_request->setParam('block_cate', 'league_' . $leagueId);
        // Get models instance
        $modelFootball     = Thethao_Model_Football::getInstance();
        if (!$seasonId)
        {
            // Get season by league
            $arrSeasonByLeague = $modelFootball->getListSeasonByLeagueID($leagueId);
            $currentYear       = date('Y');
            foreach ($arrSeasonByLeague as $value)
            {

                if (substr($value['NameSeason'], 0, 4) == $currentYear)
                {
                    $currentSeasonByLeague = $value;
                }
            }
            if (isset($currentSeasonByLeague))
            {
                $seasonByLeague = $currentSeasonByLeague;
            }
            else
            {
                $currentYear = $currentYear - 1;
                foreach ($arrSeasonByLeague as $value)
                {

                    if (substr($value['NameSeason'], 0, 4) == $currentYear)
                    {
                        $currentSeasonByLeague = $value;
                    }
                }
                if (isset($currentSeasonByLeague))
                {
                    $seasonByLeague = $currentSeasonByLeague;
                }
                else
                {
                    $seasonByLeague = $arrSeasonByLeague[0];
                }
            }
            // Get league detail
            $leagueDetail   = $modelFootball->getListLeagueByIDs(array($leagueId));

            // Get link to redirect
            $directUrL = $this->view->configView['url'] . '/bang-xep-hang/' . Fpt_Filter::setSeoLink($leagueDetail[0]['Name']) . '-' . ($seasonByLeague['NameSeason']) . '-' . $leagueId . '-' . $seasonByLeague['SeasonID'] . '.html';

            // Redirect
            $this->_redirect($directUrL);
            exit;
        }

        // Get detail table ranking by league and season
        $tableRanking = $modelFootball->getListTableRanking($seasonId, $leagueId);

        if (!empty ($tableRanking))
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

        // Get all league
        $allLeague = $modelFootball->getLeagueAll();

        // Get league to json
        $arrLeagues = array();
        foreach ($allLeague as $league)
        {
            $arrLeagues[$league['LeagueID']] = Fpt_Filter::setSeoLink($league['Name']);
        }
        // Get league detail
        $leagueDetail                    = $modelFootball->getListLeagueByIDs(array($leagueId));

        // Get season by league
        $arrSeasonByLeague = $modelFootball->getListSeasonByLeagueID($leagueId);

        $arrSeasons = array();
        //Sort descending list season
        if (is_array($arrSeasonByLeague) && count($arrSeasonByLeague))
        {
            foreach ($arrSeasonByLeague as $value)
            {
                $arrSeasons[$value['SeasonID']] = $value['NameSeason'];
            }
        }
        arsort($arrSeasons);

        // Get season detail
        $seasonDetail = $modelFootball->getListSeasonByIDs(array($seasonId));

        switch ($leagueId)
        {
            case 1:
                $cateActiveId = CATE_ID_NGOAIHANGANH;
                break;
            case 2:
                $cateActiveId = CATE_ID_LALIGA;
                break;
            case 3:
                $cateActiveId = CATE_ID_SERIA;
                break;
            case 6:
            case 7:
                $cateActiveId = CATE_ID_BONGDATRONGNUOC;
                break;
            default :
                $cateActiveId = CATE_ID_CACGIAIKHAC;
                break;
        }

        $directUrL = $this->view->configView['url'] . '/bang-xep-hang/' . Fpt_Filter::setSeoLink($leagueDetail[0]['Name']) . '-' . ($seasonDetail[0]['NameSeason']) . '-' . $leagueId . '-' . $seasonDetail[0]['SeasonID'] . '.html';

        $title       = 'Bảng xếp hạng ' . $leagueDetail[0]['Name'];
        $description = 'Bảng xếp hạng ' . $leagueDetail[0]['Name'] . ' - Tin nhanh mới nhất, lịch thi đấu, video, hình ảnh, kết quả bảng xếp hạng các giải Bóng đá.';
        $keywords    = 'bang xep hang ngoai hang Anh, bang xep hang seri a, bang xep hang bungdesliga, bang xep hang la liga';

        // Assign to view
        $this->view->assign(array(
            'tableRanking'      => $tableRanking,
            'allLeague'         => $allLeague,
            'arrLeagueJson'     => Zend_Json::encode($arrLeagues),
            'leagueDetail'      => $leagueDetail[0],
            'arrSeasonByLeague' => $arrSeasons,
            'arrSeasonJson'     => Zend_Json::encode($arrSeasons),
            'seasonDetail'      => $seasonDetail[0],
            'leagueID'          => $leagueId,
            'seasonID'          => $seasonId,
            'maxUpdateTime'     => $maxUpdateTime,
            'intCategoryId'     => $cateActiveId,
            'ogType'            => 'website',
            'ogTitle'           => $title,
            'ogUrl'             => $directUrL,
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => $description
        ));

        // Init menu
        $this->_request->setParam('cateid', CATE_ID_BONGDA);
        $this->_request->setParam('cateActiveId', $cateActiveId);


        $this->view->headTitle($title . ' - VnExpress');
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
        //Append JS
        $this->view->headScript()->appendFile($this->view->configView['js'] . '/table_ranking.js');

    }
}