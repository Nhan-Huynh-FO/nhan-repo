<?php

/**
 * @author      :   ThuyNT
 * @name        :   ScorerController
 * @copyright   :   Fpt Online
 * @todo        :   Scorer Controller
 */
class ScorerController extends Zend_Controller_Action
{

    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);        
        $strCateCode = $this->_request->getParam('controller');       
        //get instance Cate
        $objCate = Thethao_Model_Category::getInstance();
        //get page of cate
        $intPage = $this->_request->getParam('page', 1);
        // Get models instance
        $modelPlayer = new Thethao_Model_Player();
        $arrTopPlayer = $modelPlayer->getListTopPlayers(SEASON_ID);
		
        $countTotal = count($arrTopPlayer);		
        $limit = LIMIT_TOP_SCORER;
        $offset = ($intPage - 1) * LIMIT_TOP_SCORER;
        if(!empty($arrTopPlayer))
        {
            $arrTopPlayer = array_slice($arrTopPlayer, $offset, $limit);
            foreach($arrTopPlayer as $data)
            {
                $arrPlayerId[] = $data['PlayerID'];
                $arrTeamId[]   = $data['TeamID'];
            }
            $this->view->objObject->getPlayer()->setId($arrPlayerId);
            $this->view->objObject->getTeam()->setId($arrTeamId);
        }		
        //InitParam Paging
        $arrParamPaging = array(
            'total' => $countTotal,
            'page' => $intPage,
            'url' => $this->view->configView['url'] . '/vua-pha-luoi/page',
            'perpage' => LIMIT_TOP_SCORER,
            'classPagination' => 'pagination_news',
            'extEnd' => '.html',
            'separate' => '/'
        );
        $this->view->assign(array(
            'arrTopPlayer' => $arrTopPlayer,
            'strCateCode' => $strCateCode,
			'arrParamPaging' => $arrParamPaging,
			'countTotal'	=> $countTotal
        ));
        //gan meta data
        $titleMeta = 'Vua phá lưới World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = '';
		//set cate active
        $this->_request->setParam('cateActiveId', WORLD_CUP);
        $this->_request->setParam('block_cate', WORLD_CUP);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        //Add script
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
		if (DEVICE_ENV == 1)
        {
            //Set no render view
            $this->_helper->viewRenderer->setNoRender(true);
            echo $this->view->render('/scorer/m_index.phtml');
        }
    }
	
	public function moreAction()
    {
        // set cache
        $this->_request->setParam('cache_file', 1);
        //Disable layout
        $this->_helper->layout->disableLayout(true);
        //Set no render view
        $this->_helper->viewRenderer->setNoRender(true);
		
        //get instance Cate
        $objCate = Thethao_Model_Category::getInstance();
		
        $offset = $this->_request->getParam('offset');
        $limit = $this->_request->getParam('limit');
		
        // Get models instance
        $modelPlayer = new Thethao_Model_Player();
        $arrTopPlayer = $modelPlayer->getListTopPlayers(SEASON_ID);
        $countTotal = count($arrTopPlayer);		
        if(!empty($arrTopPlayer))
        {
            $arrTopPlayer = array_slice($arrTopPlayer, $offset, $limit);
            foreach($arrTopPlayer as $data)
            {
                $arrPlayerId[] = $data['PlayerID'];
                $arrTeamId[]   = $data['TeamID'];
            }
            $this->view->objObject->getPlayer()->setId($arrPlayerId);
            $this->view->objObject->getTeam()->setId($arrTeamId);
        }
        $this->view->assign(array(
            'arrTopPlayer' => $arrTopPlayer,
            'strCateCode' => $strCateCode,
			'stt'	=> $offset + 1
        ));
		
		$arrResult = array(
            'error' => 0,
            'offset' => $offset + $limit,
            'total' => $countTotal,
            'html' => ''
        );
        if (empty($arrTopPlayer))
        {
            $arrResult['error'] = 1;
            $arrResult['message'] = 'Dữ liệu đã được tải hết';
        }
        else
        {
            //set obj get Article
            $arrResult['html'] = $this->view->render('/scorer/box/m_more.phtml');
        }
        echo Zend_Json::encode($arrResult);
        exit;
    }
}
