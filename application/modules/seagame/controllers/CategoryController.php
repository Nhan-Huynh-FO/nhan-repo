<?php

/**
 * @author      :   HungNT1
 * @name        :   CategoryController
 * @copyright   :   Fpt Online
 * @todo        :   Category Controller
 */
class CategoryController extends Zend_Controller_Action
{

    /**
     * cate folder page
     * @author HungNT1
     */
    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        $strCateCode = $this->_request->getParam('cate_code');
        //set render view if cate code is lich-thi-dau
        if ($strCateCode == 'lich-thi-dau')
        {
            return $this->_forward('lich-thi-dau');
        }
        //get instance Cate
        $objCate = Thethao_Model_Category::getInstance();
        //get cate id by cate code
        $intCateId = $objCate->getIdByCode($strCateCode, SEA_GAMES);

        //get page of cate
        $intPage = $this->_request->getParam('page', 1);

        //check cate id is not exists
        if ($intCateId < 1)
        {
            $this->_redirect("/");
        }

        // Get Category detail
        $cateDetail = $objCate->getDetailByCateId($intCateId);

        //str link
        $strLink = $this->_request->getPathInfo();
        if (strpos(rtrim($strLink, '/'), $cateDetail['link']) === FALSE)
        {
            //$this->_redirect($this->view->configView['url'] . $cateDetail['link'], array('code' => 301));
        }

        if ($intPage > 1)
        {
            $limit = LIMIT_LIST_CATE;
            $offset = ($intPage - 1)*LIMIT_LIST_CATE + LIMIT_TOP_NEWS_CATE;
        }else {
            $limit = LIMIT_LIST_CATE + LIMIT_TOP_NEWS_CATE;
            $offset = 0;
        }

        //init param to get list data with rule 2
        $arrParamNews = array(
            'category_id' => $intCateId,
            'article_type' => NULL,
            'limit' => $limit,
            'offset' => $offset,
        );

        //init model news
        $objNews = Thethao_Model_News::getInstance();

        //get list article with rule 2 list on Thethao
        $arrListData = $objNews->getListArticleIdsByRule2($arrParamNews);

        // set id from $arrListPaging to get article
        $this->view->objArticle->setIdBasic($arrListData['data']);

        $arrArticleExclude = array();
        $arrHotNews = $arrListExclude = array();
        if (1 == $intPage && !empty($arrListData['data']))
        {
            $arrArticleExclude = $arrListData['data'];
            //get Instance Block
            $objBlock = Thethao_Plugin_Block::getInstance();
            // set exclude article id from arrData
            $objBlock->setExclude($arrArticleExclude);
            $arrHotNews = array_slice($arrListData['data'], 0, LIMIT_TOP_NEWS_CATE);
            $arrListData['data'] = array_diff($arrListData['data'], $arrHotNews);
            //get Exclude
            $arrListExclude = $objBlock->getExclude();
        }

        //get video for box right
        $arrParamVideo = array(
            'category_id' => CATE_SEA_GAMES_VIDEO,
            'limit' => LIMIT_RELATED_VIDEO + LIMIT_LIST_CATE,
            'offset' => 0,
            'article_type' => TYPE_VIDEO
        );
        //get list article with rule 1 list on folder video
        $arrListVideo = $objNews->getListArticleIdsByRule1($arrParamVideo);
        if (1 == $intPage && !empty($arrListVideo['data']))
        {
            $arrListVideo['data'] = array_diff($arrListVideo['data'], $arrListExclude);
        }
        if (count($arrListVideo['data']) > LIMIT_RELATED_VIDEO)
        {
            $arrListVideo['data'] = array_slice($arrListVideo['data'], 0, LIMIT_RELATED_VIDEO);
        }
        // set id from $arrListPaging to get article
        $this->view->objArticle->setIdBasic($arrListVideo['data']);

        //check if page > max page then return to max page
        if ($arrListData['total'] > 0)
        {
            $maxPage = ceil($arrListData['total'] / LIMIT_LIST_CATE);
            if ($intPage > $maxPage)
            {
                //$this->_redirect($this->configView['url'] . $cateDetail['link'] . '/page/' . $maxPage . '.html');
            }
        }

        //InitParam Paging
        $arrParamPaging = array(
            'total' => $arrListData['total'] - LIMIT_TOP_NEWS_CATE,
            'page' => $intPage,
            'url' => $this->view->configView['url'] . $cateDetail['link'] . '/page',
            'perpage' => LIMIT_LIST_CATE,
            'classPagination' => 'pagination_news center',
            'extEnd' => '.html',
            'separate' => '/'
        );

        //Set param
        $this->_request->setParam('block_cate', $intCateId);

        //gan meta data
        $titleMeta = $cateDetail['catename'] . ' SEA Games 28 - VnExpress';
        $keywords = 'SEA Games 28, tin tuc , bong da';
        $description = 'SEA Games 28 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu SEA Games 28, tường thuật bình luận bóng đá SEA Games 28';
        //Assign to view
        $this->view->assign(array(
            'intCategoryId' => $intCateId,
            'arrHotNews' => $arrHotNews,
            'arrParamPaging' => $arrParamPaging,
            'arrData' => $arrListData['data'],
            'arrListVideo' => $arrListVideo['data'],
            'arrArticleExclude' => $arrArticleExclude,
            'arrListExclude' => $arrListExclude,
            'ogType' => 'website',
            'ogTitle' => $titleMeta,
            'ogUrl' => $this->view->configView['url'] . $cateDetail['link'],
            'ogImage' => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => $description
        ));

        //init cate for set menu
        //set cate active
        $this->_request->setParam('cateActiveId', $intCateId);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', SEA_GAMES);

        // Set metadata tags
        $this->view->headTitle()->prepend($titleMeta);
        $this->view->headMeta()->setName('description', $description)
                ->setName('keywords', $keywords);
    }

    /**
     * Topic action
     */
    public function topicAction()
    {
        //Get string link
        $strLink = $this->view->configView['url_vne'] . $this->_request->getPathInfo();
        //Redirect sang VNE
        $this->_redirect($strLink, array('code' => 301));
    }

    /**
     * Tag action
     */
    public function tagAction()
    {
        //Get string link
        $strLink = $this->view->configView['url_vne'] . $this->_request->getPathInfo();
        //Redirect sang VNE
        $this->_redirect($strLink, array('code' => 301));
    }

    function othernewsAction()
    {
        //Disable layout
        $this->_helper->layout->disableLayout(true);
        // Set not render view
        $this->_helper->viewRenderer->setNoRender(true);

        //New object
        $modelNews = Thethao_Model_News::getInstance();
        $intCategoryId = (int) $this->_request->getParam('category_id', 0);
        $intLimit = (int) $this->_request->getParam('limit');
        $intOffset = (int) $this->_request->getParam('offset');
        $arrExclude = $this->_request->getParam('exclude');
        if (empty($exclude))
        {
            $arrExclude = array();
            $totalExclude = 0;
        }
        else
        {
            $arrExclude = explode(',', $exclude);
            $totalExclude = count($arrExclude);
        }
        $intOffset = $intOffset - $totalExclude;

        //Init array params other news
        $arrOtherNewsParams = array(
            'category_id' => $intCategoryId,
            'article_type' => NULL,
            'limit' => $intLimit + $totalExclude,
            'offset' => $intOffset,
        );
        //Get other news (Get news by rule 2: folder và liston lên folder của tin đang xem)
        $arrOtherNews = $modelNews->getListArticleIdsByRule2($arrOtherNewsParams);

        //var_dump('<pre>', $arrExclude, $arrOtherNews);die;
        //check data empty
        if (!empty($arrOtherNews['data']))
        {
            $arrOtherNews['data'] = array_slice(array_diff($arrOtherNews['data'], $arrExclude), 0, LIMIT_MORE_NEWS);
        }//end if

        $this->view->assign(array(
            'arrOtherNews' => $arrOtherNews['data']
        ));
        $arrResult = array(
            'error' => 0,
            'offset' => $intOffset + LIMIT_MORE_NEWS,
            'total' => count($arrOtherNews['data']),
            'exclude' => $arrExclude,
            'html' => ''
        );
        if (empty($arrOtherNews['data']))
        {
            $arrResult['error'] = 1;
            $arrResult['message'] = 'Dữ liệu đã được tải hết';
        }
        else
        {
            //set obj get Article
            $this->view->objArticle->setIdBasic($arrOtherNews['data']);
            $arrResult['html'] = $this->view->render('category/othernews.phtml');
        }
        echo Zend_Json::encode($arrResult);
    }

    public function lichThiDauAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        $strCateCode = 'lich-thi-dau';
        //get cate id by cate code
        $intCateId = SEA_GAMES;

        //Set param
        $this->_request->setParam('block_cate', $intCateId);
        //set cate active
        $this->_request->setParam('cateActiveId', $intCateId);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', SEA_GAMES);

        //gan meta data
        $titleMeta = 'Lịch thi đấu SEA Games 28 - VnExpress SEA Games';
        $keywords = 'SEA Games 28, tin tuc , bong da';
        $description = 'SEA Games 28 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu SEA Games 28, tường thuật bình luận bóng đá SEA Games 28';
        //Assign to view
        $this->view->assign(array(
            'intCategoryId' => $intCateId,
            'strCateCode' => $strCateCode,
            'ogType' => 'website',
            'ogTitle' => $titleMeta,
            'ogUrl' => $this->view->configView['url'] . '/lich-thi-dau/',
            'ogImage' => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => $description
        ));

        // Set metadata tags
        $this->view->headTitle()->prepend($titleMeta);
        $this->view->headMeta()->setName('description', $description)
                ->setName('keywords', $keywords);
    }

}
