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
        $newsModel  = Thethao_Model_News::getInstance();
        $intLimit   = LIMIT_PHOTO + 3;
        //format param
        $arrParam  = array(
            'category_id' => CATE_ID_PHOTO,
            'limit'       => $intLimit,
            'offset'      => 0
        );
        //get all article by rule 2
        $arrAlbum     = $newsModel->getListArticleIdsByRule2($arrParam);

        if (!empty($arrAlbum['data']))
        {
            //set id
            $this->view->objArticle->setIdBasic($arrAlbum['data']);
        }

        $arrTop3  = array_splice($arrAlbum['data'], 0, 3);
        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        //set exclude article id from arrHot
        $objBlock->setExclude($arrTop3);

        //set cateid
        $this->_request->setParam('cateid', CATE_ID_PHOTO);

        $intTotalAlbum = $arrAlbum['total'];
        //InitParam Paging
        $arrParamPaging = array(
            'total'           => $intTotalAlbum,
            'page'            => 1,
            'perpage'         => LIMIT_PHOTO,
            'classPagination' => 'pagination_news right',
        );

        // Assign to view
        $this->view->assign(array(
            'arrTop'        => $arrTop3,
            'arrData'       => $arrAlbum['data'],
            'intOffset'     => $intLimit,
            'intTotalAlbum' => $intTotalAlbum,
            'arrParamPaging'=> $arrParamPaging,
            'breakCumbOther'=> array(),
            'intCategoryId' => CATE_ID_PHOTO,
            'paramsBlock' => Zend_Json::encode(array(
                'pagecode' => 'default_photo_index_' . CATE_ID_PHOTO,
                'exclude' => implode(',', $objBlock->getExclude())
            )),
            'ogType'            => 'website',
            'ogTitle'           => 'Lưu trữ hình ảnh cầu thủ, trận đấu các giải bóng đá trong nước và thế giới - ảnh thể thao – Golf thể thao',
            'ogUrl'             => $this->view->configView['url'],
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => 'Tin nhanh video clip hình ảnh các môn thể thao: bóng đá quyền anh đua xe bóng chuyền cầu lông… đang diễn ra ở VN & thế giới.'
        ));

        /* END */

        $this->view->headTitle('Lưu trữ hình ảnh cầu thủ, trận đấu các giải bóng đá trong nước và thế giới - ảnh thể thao – Golf thể thao');
        $this->view->headMeta()->setName('description', 'Lưu trữ hình ảnh của các cầu thủ, pha ghi bàn, bàn thắng đẹp, sự kiện các trận đấu của các giải bóng đá trong nước và quốc tế.');
        $this->view->headMeta()->setName('keywords', 'Hinh anh,bong da,the thao');
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
            'error'    => 0,
            'msg'      => 'Success',
            'html'     => NULL,
        );
        //get page of cate
        $intPage   = $this->_request->getParam('page', 1);
        //Get album
        $newsModel = Thethao_Model_News::getInstance();

        //trang 1 - offset 0 - get 21
        //trang 2 - offset 21 - get 18
        //trang 3 - offset 39 - get 18
        //trang 4 - offset 57 - get 18
        //format param
        $arrParam = array(
            'category_id' => CATE_ID_PHOTO,
            'limit'       => 24,
            'offset'      => ($intPage - 2) * 24 + 27
        );

        //get all article by rule 2
        $arrAlbum = $newsModel->getListArticleIdsByRule2($arrParam);

        if (!empty($arrAlbum['data']))
        {
            //set id
            $this->view->objArticle->setIdBasic($arrAlbum['data']);
        }
        //InitParam Paging
        $arrParamPaging = array(
            'total'           => $arrAlbum['total'],
            'page'            => $intPage,
            'perpage'         => 27,
            'classPagination' => 'pagination_news right',
        );

        // Assign to view
        $this->view->assign(array(
            'arrData'         => $arrAlbum['data'],
            'arrParamPaging'  => $arrParamPaging,
        ));
        //Render view
        $response['html'] = $this->view->render('photo/ajax_paging.phtml');
        //Return Json
        echo Zend_Json::encode($response);
    }
}