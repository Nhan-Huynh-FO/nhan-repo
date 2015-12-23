<?php

/**
 * @author      :   ThuyNT
 * @name        :   DudoanController
 * @copyright   :   Fpt Online
 * @todo        :   Dudoan Controller
 */
class DuDoanController extends Zend_Controller_Action
{

    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        $strCateCode = $this->_request->getParam('controller');
        //get instance Cate
        $objCate = Thethao_Model_Category::getInstance();
        // Get models instance
        $modelFootball = Thethao_Model_Football::getInstance();
        // Get detail table ranking by league and season
        $arrMatch = $modelFootball->getListTableRanking(SEASON_ID, LEAGUE_ID);
        if (!empty($arrMatch))
        {
            //set id object
            $this->view->objObject->getTeam()->setId(array_keys($arrMatch));
        }
        //gan meta data
        $titleMeta = 'Dự đoán World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = 'Fifa World Cup 2014 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu WC 2014 tại Brazil, tường thuật bình luận bóng đá World Cup 2014';
        $this->view->assign(array(
            'arrMatch' => $arrMatch,
            'strCateCode' => $strCateCode,
            'ogType'            => 'website',
            'ogTitle'           => $titleMeta,
            'ogUrl'             => $this->view->configView['url'],
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => $description
        ));
        //Set param
        $this->_request->setParam('block_cate', WORLD_CUP);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        //Add script
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
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
