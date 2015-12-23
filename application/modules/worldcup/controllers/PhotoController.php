<?php

/**
 * @name        :   PhotoController
 * @copyright   :   Fpt Online
 * @todo        :   Photo Controller
 */
class PhotoController extends Zend_Controller_Action
{

    /**
     * Default action
     */
    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        //Set category id for block_cate user in block
        $this->_request->setParam('block_cate', CATE_ID_PHOTO);
        //Get album
        $newsModel = Thethao_Model_News::getInstance();
        $intLimit = LIMIT_LIST_PHOTO + 1;
        //format param
        $arrParam = array(
            'category_id' => CATE_ID_PHOTO,
            'limit' => $intLimit,
            'offset' => 0
        );
        //get all article by rule 2
        $arrAlbum = $newsModel->getListArticleIdsByRule2($arrParam);
        $arrListExclude = array();
        if (!empty($arrAlbum['data']))
        {
            //set id
            $this->view->objArticle->setIdBasic($arrAlbum['data']);
            //get Instance Block
            $objBlock = Thethao_Plugin_Block::getInstance();
            // set exclude article id from arrData
            $objBlock->setExclude($arrAlbum['data']);
            //get Exclude
            $arrListExclude = $objBlock->getExclude();
        }
        $arrTop = array_shift($arrAlbum['data']);
        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        //set exclude article id from arrHot
        $objBlock->setExclude($arrTop);
        //set cateid
        $this->_request->setParam('cateid', CATE_ID_PHOTO);
        $arrAlbum['total'] = $arrAlbum['total'] - 1;
        $intTotalAlbum = $arrAlbum['total'];
        //InitParam Paging
        $arrParamPaging = array(
            'total' => $intTotalAlbum,
            'page' => 1,
            'perpage' => LIMIT_LIST_PHOTO,
            'classPagination' => 'pagination_news right',
        );
        //gan meta data
        $titleMeta = 'Hình ảnh World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = 'Fifa World Cup 2014 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu WC 2014 tại Brazil, tường thuật bình luận bóng đá World Cup 2014';
        // Assign to view
        $this->view->assign(array(
            'arrTop' => $arrTop,
            'arrData' => $arrAlbum['data'],
            'intOffset' => $intLimit,
            'intTotalAlbum' => $intTotalAlbum,
            'arrParamPaging' => $arrParamPaging,
            'breakCumbOther' => array(),
            'intCategoryId' => CATE_ID_PHOTO,
            'arrListExclude' => $arrListExclude,
            'ogType'            => 'website',
            'ogTitle'           => $titleMeta,
            'ogUrl'             => $this->view->configView['url'] . '/photo',
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => 'Fifa World Cup 2014 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu WC 2014 tại Brazil, tường thuật bình luận bóng đá World Cup 2014'
        ));
        /* END */

        //init cate for set menu
        //set cate active
        $this->_request->setParam('cateActiveId', CATE_ID_PHOTO);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
        //$this->view->headScript()->appendFile($this->view->config['js'] . '/wordcup.js');
    }

    /**
     * ajaxpaging action
     */
    public function ajaxpagingAction()
    {
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        // Set not render view
        $this->_helper->viewRenderer->setNoRender(true);
        // Default response
        $response = array(
            'error' => 0,
            'msg' => 'Success',
            'html' => NULL,
        );
        //get page of cate
        $intPage = $this->_request->getParam('page', 1);
        //Get album
        $newsModel = Thethao_Model_News::getInstance();

        //trang 1 - offset 0 - get 21
        //trang 2 - offset 21 - get 20
        //trang 3 - offset 41 - get 20
        //format param
        $arrParam = array(
            'category_id' => CATE_ID_PHOTO,
            'limit' => LIMIT_LIST_PHOTO,
            'offset' => ($intPage - 2) * LIMIT_LIST_PHOTO + (LIMIT_LIST_PHOTO + 1)
        );

        //get all article by rule 2
        $arrAlbum = $newsModel->getListArticleIdsByRule2($arrParam);

        if (!empty($arrAlbum['data']))
        {
            //set id
            $this->view->objArticle->setIdBasic($arrAlbum['data']);
        }
        $arrAlbum['total'] = $arrAlbum['total'] - 1;
        //InitParam Paging
        $arrParamPaging = array(
            'total' => $arrAlbum['total'],
            'page' => $intPage,
            'perpage' => LIMIT_LIST_PHOTO,
            'classPagination' => 'pagination_news right',
        );

        // Assign to view
        //print_r($arrAlbum['data']); die;
        $this->view->assign(array(
            'arrData' => $arrAlbum['data'],
            'arrParamPaging' => $arrParamPaging,
        ));
        //Render view
        $response['html'] = $this->view->render('photo/ajax_paging.phtml');
        //Return Json
        echo Zend_Json::encode($response);
    }

}
