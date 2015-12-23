<?php

/**
 * @author      :   ThuyNT
 * @name        :   WinnersController
 * @copyright   :   Fpt Online
 * @todo        :   Winners Controller
 */
class WinnersController extends Zend_Controller_Action
{

    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        $strCateCode = $this->_request->getParam('controller');
        //get instance Cate
        $objCate = Thethao_Model_Category::getInstance();
        // Get models instance
        $modelBlock = new Thethao_Model_Block;
        $arrParams = array(
            'key_id' => 'trungthuong_worldcup2014'
        );
        $arrListWinner = $modelBlock->getKeyBox($arrParams);
        $this->view->assign(array(
            'arrListWinner' => $arrListWinner,
            'strCateCode' => $strCateCode
        ));
        //gan meta data
        $titleMeta = 'Danh sách trúng thưởng World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = '';
        //Set param
        $this->_request->setParam('block_cate', WORLD_CUP);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        //Add script
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
    }
}
