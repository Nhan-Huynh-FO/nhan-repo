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
        $this->_request->setParam('block_cate', CATE_SEA_GAMES_ANH);
        //Get album
        $newsModel = Thethao_Model_News::getInstance();
        $intLimit = LIMIT_LIST_PHOTO + 3;
        //format param
        $arrParam = array(
            'category_id' => CATE_SEA_GAMES_ANH,
            'limit' => $intLimit,
            'offset' => 0
        );
        //get all article by rule 2
        $arrAlbum = $newsModel->getListArticleIdsByRule2($arrParam);
        //set id article
        $this->view->objArticle->setIdBasic($arrAlbum['data']);

        $intTotalAlbum = $arrAlbum['total'];
        //InitParam Paging
        $arrParamPaging = array(
            'total' => $intTotalAlbum,
            'page' => 1,
            'perpage' => LIMIT_LIST_PHOTO,
            'classPagination' => 'pagination_news right',
        );
        //gan meta data
        $titleMeta = 'Hình ảnh SEA Games 28 - VnExpress';
        $keywords = 'SEA Games 28, tin tuc , bong da';
        $description = 'SEA Games 28 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu SEA Games 28, tường thuật bình luận bóng đá SEA Games 28';
        // Assign to view
        $this->view->assign(array(
            'arrData' => $arrAlbum['data'],
            'intOffset' => $intLimit,
            'intTotalAlbum' => $intTotalAlbum,
            'arrParamPaging' => $arrParamPaging,
            'breakCumbOther' => array(),
            'intCategoryId' => CATE_SEA_GAMES_ANH,
            'ogType' => 'website',
            'ogTitle' => $titleMeta,
            'ogUrl' => $this->view->configView['url'] . '/photo',
            'ogImage' => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => 'SEA Games 28 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu SEA Games 28, tường thuật bình luận bóng đá SEA Games 28'
        ));
        /* END */

        //init cate for set menu
        //set cate active
        $this->_request->setParam('cateActiveId', CATE_SEA_GAMES_ANH);
        //set cate id to show active in menu
        $this->_request->setParam('cateid', SEA_GAMES);
        $this->view->headTitle($titleMeta);
        $this->view->headMeta()->setName('description', $description);
        $this->view->headMeta()->setName('keywords', $keywords);
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
            'category_id' => CATE_SEA_GAMES_ANH,
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
