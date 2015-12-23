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
        //get instance Cate
        $objCate = Thethao_Model_Category::getInstance();
        //get cate id by cate code
        $intCateId = $objCate->getIdByCode($strCateCode, WORLD_CUP);

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

        //init param to get list data with rule 2
        $arrParamNews = array(
            'category_id' => $intCateId,
            'article_type' => NULL,
            'limit' => LIMIT_LIST_CATE,
            'offset' => ($intPage - 1) * LIMIT_LIST_CATE,
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

            $arrHotNews = array_slice($arrListData['data'], 0, LIMIT_TOP);
            $arrListData['data'] = array_diff($arrListData['data'], $arrHotNews);
            //get Exclude
            $arrListExclude = $objBlock->getExclude();
        }
        //get video
        $arrParamVideo = array(
            'category_id' => CATE_ID_VIDEO,
            'limit' => LIMIT_VIDEO + LIMIT_LIST_CATE,
            'offset' => 0,
            'article_type' => TYPE_VIDEO
        );
        //get list article with rule 2 list on Thethao
        $arrListVideo = $objNews->getListArticleIdsByRule1($arrParamVideo);
        if (1 == $intPage && !empty($arrListVideo['data']))
        {
            $arrListVideo['data'] = array_diff($arrListVideo['data'], $arrListExclude);
        }
        if (count($arrListVideo['data']) > LIMIT_VIDEO)
        {
            $arrListVideo['data'] = array_slice($arrListVideo['data'], 0, LIMIT_VIDEO);
        }
        // set id from $arrListPaging to get article
        $this->view->objArticle->setIdBasic($arrListVideo['data']);
        //get video
        $arrParamPhoto = array(
            'category_id' => CATE_ID_PHOTO,
            'limit' => LIMIT_PHOTO + LIMIT_LIST_CATE,
            'offset' => 0,
            'article_type' => TYPE_PHOTO
        );
        //get list article with rule 2 list on Thethao
        $arrListPhoto = $objNews->getListArticleIdsByRule1($arrParamPhoto);
        if (1 == $intPage && !empty($arrListPhoto['data']))
        {
            $arrListPhoto['data'] = array_diff($arrListPhoto['data'], $arrListExclude);
        }
        if (count($arrListPhoto['data']) > LIMIT_PHOTO)
        {
            $arrListPhoto['data'] = array_slice($arrListPhoto['data'], 0, LIMIT_PHOTO);
        }
        // set id from $arrListPaging to get article
        $this->view->objArticle->setIdBasic($arrListPhoto['data']);
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
            'total' => $arrListData['total'],
            'page' => $intPage,
            'url' => $this->view->configView['url'] . $cateDetail['link'] . '/page',
            'perpage' => LIMIT_LIST_CATE,
            'classPagination' => 'pagination_news',
            'extEnd' => '.html',
            'separate' => '/'
        );

        //Set param
        $this->_request->setParam('block_cate', $intCateId);

        //gan meta data
        $titleMeta = $cateDetail['catename'] . ' World Cup 2014 - VnExpress';
        $keywords = 'World cup 2014, tin tuc , bong da';
        $description = 'Fifa World Cup 2014 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu WC 2014 tại Brazil, tường thuật bình luận bóng đá World Cup 2014';
        //Assign to view
        $this->view->assign(array(
            'intCategoryId' => $intCateId,
            'arrHotNews' => $arrHotNews,
            'arrParamPaging' => $arrParamPaging,
            'arrData' => $arrListData['data'],
            'arrListVideo' => $arrListVideo['data'],
            'arrListPhoto' => $arrListPhoto['data'],
            'arrArticleExclude' => $arrArticleExclude,
            'arrListExclude' => $arrListExclude,
            'ogType' => 'website',
            'ogTitle' => $titleMenta,
            'ogUrl' => $this->view->configView['url'] . $cateDetail['link'],
            'ogImage' => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => $description
        ));

        if (DEVICE_ENV != 1)
        {
            //ad js
            $this->view->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/utils/calendar/calendar.js');
        }
        else
        {
            //Set no render view
            $this->_helper->viewRenderer->setNoRender(true);
            echo $this->view->render('mobile/category.phtml');
        }
        //init cate for set menu
        //set cate active
        $this->_request->setParam('cateActiveId', $intCateId);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);

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

}
