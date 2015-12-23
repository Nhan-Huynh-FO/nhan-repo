<?php

/**
 * @author      :   HungNT1
 * @name        :   IndexController
 * @copyright   :   Fpt Online
 * @todo        :   Index Controller
 */
class IndexController extends Zend_Controller_Action
{

    /**
     * @todo - VnE Thethao home page
     * @author HungNT1
     */
    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        // Get block cate
        $intCateId = $this->_request->getParam('cateid', SITE_ID);

        //get page
        $intPage = $this->_request->getParam('page', 1);

        //get Instance New
        $objNews = Thethao_Model_News::getInstance();

        //get Instance News
        $objArticle = $this->view->objArticle;

        //init params to get list article with rule 2, list on folder Thethao
        $arrParamNews = array(
            'category_id'   => $intCateId,
            'limit'         => LIMIT_LIST,
            'offset'        => (($intPage - 1) * LIMIT_LIST),
            'article_type'  => NULL
        );

        //get list article with rule 2 list on Thethao
        $arrListData = $objNews->getListArticleIdsByRule2($arrParamNews);

        //check if page > max page then return to max page
        if ($arrListData['total'] > 0)
        {
            $maxPage = ceil($arrListData['total'] / LIMIT_LIST);
            if ($intPage > $maxPage)
            {
                $this->_redirect($this->configView['url'] . '/page/' . $maxPage . '.html');
            }
        }

        //set data exclude
        $arrArticleExclude  = array();
        $arrHotNews = array();
        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        if (1 == $intPage && !empty($arrListData['data']))
        {
            //check bai thuong mai, chi so sanh o trang 1 ma thoi
            $zoneID = Thethao_Global::getZoneByCateId(SITE_ID);
            if ($zoneID != 0)
            {
                $modelBlock = Fpt_Data_News_Block::getInstance();
                $modelBlock->setZoneId($zoneID);
                $arrListData['data'] = $modelBlock->getNewsByZone($zoneID, $arrListData['data']);
            }
            //set data exclude
            $arrArticleExclude  = $arrListData['data'];

            // set exclude article id from arrData
            $objBlock->setExclude($arrListData['data']);
            $arrHotNews = array_slice($arrListData['data'], 0, LIMIT_TOP);
            $arrListData['data'] = array_diff($arrListData['data'], $arrHotNews);
            $objArticle->setIdBasic($arrHotNews);
        }
        // set id from $arrListPaging to get article
        $objArticle->setIdBasic($arrListData['data']);

        //InitParam Paging
        $arrParamPaging = array(
            'total' => $arrListData['total'],
            'page' => $intPage,
            'url' => $this->view->configView['url'] . '/page',
            'perpage' => LIMIT_LIST,
            'classPagination' => 'pagination_news right',
            'extEnd' => '.html',
            'separate' => '/'
        );

        //Set param
        $this->_request->setParam('block_cate', $intCateId);

        //Assign to view
        $this->view->assign(array(
            'intCategoryId'     => SITE_ID,
            'arrHotNews'        => $arrHotNews,
            'arrParamPaging'    => $arrParamPaging,
            'arrData'           => $arrListData['data'],
            'arrDiffNews'       => "",
            'offsetMore'        => LIMIT_LIST,
            'arrArticleExclude' => $arrArticleExclude,
            'paramsBlock' => Zend_Json::encode(array(
                'pagecode' => 'default_index_index_' . SITE_ID,
                'exclude' => implode(',', $objBlock->getExclude())
            )),
            'ogType'            => 'website',
            'ogTitle'           => 'Thể thao - Tin thể thao 24h, lịch thi đấu, kết quả, video clip - VnExpress',
            'ogUrl'             => $this->view->configView['url'],
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => 'Tin nhanh video clip hình ảnh các môn thể thao: bóng đá quyền anh đua xe bóng chuyền cầu lông… đang diễn ra ở VN & thế giới.'
        ));

        // Set metadata tags
        $this->view->headTitle()->prepend('Thể thao - Tin thể thao 24h, lịch thi đấu, kết quả, video clip - VnExpress');
        $this->view->headMeta()->setName('description', 'Tin nhanh video clip hình ảnh các môn thể thao: bóng đá quyền anh đua xe bóng chuyền cầu lông… đang diễn ra ở VN & thế giới.');
        $this->view->headMeta()->setName('keywords', 'Thể thao,tin tức,bóng đá');

        if (DEVICE_ENV != 1)
        {
            //ad js
            $this->view->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/utils/calendar/calendar.js');
        }
        else
        {
            //Set no render view
            $this->_helper->viewRenderer->setNoRender(true);
            echo $this->view->render('mobile/index.phtml');
        }
    }

    /**
     * Get data action
     */
    public function getdataAction()
    {
        //set header
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');

        //set cache
        $this->_request->setParam('cache_file', 1);

        //Set no render
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        //Get param
        $arrParam   = $this->_request->getParams();

        //XSS
        $arrParam['block']  = trim(strip_tags($arrParam['block']));
        $arrParam['data']   = trim(strip_tags($arrParam['data']));

        //Set response
        $response   = array('error'=>1, 'html'=>'');

        //check block to get view
        switch ( $arrParam['block'] )
        {
            case 'video':
                if ( !empty($arrParam['data']) )
                {
                    //set data to view
                    $dataView   = array(
                        'data'  => explode(',', $arrParam['data']),
                        'info'  => array(
                            'share_url'  => 'http://video.vnexpress.net/the-thao/',
                            'name'  => 'Video'
                        )
                    );

                    //set id basic
                    $this->view->objArticle->setIdBasic($dataView['data']);

                    //assign to view
                    $this->view->assign(array(
                        'layout'    => array('title'=>'Video Nhạc'),
                        'data'      => $dataView
                    ));

                    //set response
                    $response['error']  = 0;
                    $response['html']   = $this->view->render('/block/left_3photo_carousel.phtml');
                }//end if
                break;
        }//end switch case

        //end code data
        $jsonData = Zend_Json::encode($response);

        # JSON if no callback
        if (!isset($arrParam['callback']))
        {
            echo($jsonData);exit;
        }
        //init model new
        $modelNew = Thethao_Model_News::getInstance();
        # JSONP if valid callback
        if ($modelNew->isValidCallback($arrParam['callback']))
        {
            echo("{$arrParam['callback']}($jsonData)");exit;
        }
    }
    public function blockAction()
    {
        $this->_helper->layout->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

        $this->_request->setParam('cache_file', 1);
        $arrParams = $this->_request->getParams();

        $arrParams['pagecode'] = trim(strip_tags($arrParams['pagecode']));
        $arrParams['exclude'] = trim(strip_tags($arrParams['exclude']));
        $exclude = explode(',', $arrParams['exclude']);

        $objBlock = Thethao_Plugin_Block::getInstance();
        $objBlock->setExclude($exclude);

        $html = array();
        if (isset($arrParams['part']) && !empty($arrParams['part']))
        {
            if (!empty($arrParams['pagecode']))
            {
                foreach ($arrParams['part'] as $position)
                {
                    $html[$position] = $this->view->GetBlock($position, $arrParams['pagecode']);
                }
            }
        }
        echo Zend_Json::encode($html);
    }
}