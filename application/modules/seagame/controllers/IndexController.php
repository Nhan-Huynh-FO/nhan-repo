<?php

/**
 * @author      :   PhongTX
 * @name        :   IndexController
 * @copyright   :   Fpt Online
 * @todo        :   Index Controller
 */
class IndexController extends Zend_Controller_Action
{

    /**
     * @todo - Worldcup home page
     * @author PhongTX
     */
    public function indexAction()
    {

        //set cache
        $this->_request->setParam('cache_file', 1);
        // Get block cate
        $intCateId = $this->_request->getParam('cateid', SEA_GAMES);

        //get Instance New
        $objNews = Thethao_Model_News::getInstance();
        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        //get Instance News
        $objArticle = $this->view->objArticle;
        $arrHotNews = $arrListExclude = array();

        //get 1 tin build top for seagame
        $arrParams = array(
                'area' => array(
                    array(
                        'category_id' => SITE_ID,
                        'showed_area' => 'seagames'
                    )
                ),
                'offset' => 0,
                'limit' => 1
            );
        //get Box Hot News
        $arrHotNews = $objArticle->getTopHotArticleByArr($arrParams);
        //khoi tao arrHotExclude
        $arrHotExclude = $arrListExclude =  array();
        if(!empty($arrHotNews))
        {
            //each data $arrHotNews
            foreach ($arrHotNews[0] as $key=>$rows)
            {
                //check $arrHotNews is article
                if ($rows['article_id'] > 0)
                {
                    //set article Hot to Exclude
                    $arrHotExclude[] = $rows['article_id'];
                }
            }
        }

        //get 5 tin with rule 3 of cate seagames
        $paramTopNews = array(
            'category_id' => SEA_GAMES,
            'offset' => 0,
            'limit' => LIMIT_TOP_NEWS + 1
        );
        $arrDataRule3 = $objNews->getListArticleIdsByRule3($paramTopNews);
        //check and remove hot news in top news
        $arrTopNews = array_slice(array_diff($arrDataRule3['data'], $arrHotExclude), 0, LIMIT_TOP_NEWS);

        $arrDataExclude = array_merge($arrHotExclude, $arrTopNews);
        //set exclude
        $objBlock->setExclude($arrDataExclude);
        //set obj get Article
        $objArticle->setIdBasic($arrDataExclude);

        //get video with rule 1
        $arrParamVideo = array(
            'category_id'   => CATE_SEA_GAMES_VIDEO,
            'article_type'  => TYPE_VIDEO,
            'limit'         => LIMIT_VIDEO + LIMIT_TOP_NEWS + 1,
            'offset'        => 0,
            'withcore'      => FALSE
        );
        //category_id, article_type, limit, offset, withcore
        //get list video with rule 1
        $arrTmp = $objNews->getListArticleIdsByRule1($arrParamVideo);
        $arrListVideo = array_slice(array_diff($arrTmp['data'],$arrDataExclude), 0, LIMIT_VIDEO);
        // set id from $arrListPaging to get article
        $objArticle->setIdBasic($arrListVideo);

        //get photo seagame with rule 1
        $arrParamPhoto = array(
            'category_id'   => CATE_SEA_GAMES_ANH,
            'limit'         => LIMIT_PHOTO + LIMIT_TOP_NEWS + 1,
            'offset'        => 0,
            'article_type'  => TYPE_PHOTO
        );
        //get list video with rule 1
        $arrTmp = $objNews->getListArticleIdsByRule1($arrParamPhoto);
        $arrListPhoto = array_slice(array_diff($arrTmp['data'],$arrDataExclude), 0, LIMIT_PHOTO);
        // set id from $arrListPaging to get article
        $objArticle->setIdBasic($arrListPhoto);

        //init list cate to get rule 1
        $arrListCate = array(CATE_SEA_GAMES_BONGDA => 3, CATE_SEA_GAMES_CACMONKHAC => 3, CATE_SEA_GAMES_BENLE => 4, CATE_SEA_GAMES_BINHLUAN => 4);
        $arrParamOther = array(
            'category_id'   => 0,
            'limit'         => LIMIT_TOP_NEWS + 1,
            'offset'        => 0,
        );
        $arrListOther = array();
        foreach ($arrListCate as $cateId => $limit)
        {
            //init param to get data
            $arrParamOther['category_id'] = $cateId;
            $arrParamOther['limit'] = $arrParamOther['limit'] + $limit;
            $arrTmp = $objNews->getListArticleIdsByRule1($arrParamOther);
            $arrListOther[$cateId] = array_slice(array_diff($arrTmp['data'],$arrDataExclude), 0, $limit);
            // set id from $arrListPaging to get article
            $objArticle->setIdBasic($arrListOther[$cateId]);
        }

        //set block cate and cate active
        $this->_request->setParam('block_cate', $intCateId);
        //init cate for set menu
        //set cate active
        $this->_request->setParam('cateActiveId', $intCateId);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', SEA_GAMES);

        //Assign to view
        $this->view->assign(array(
            'intCategoryId'     => SEA_GAMES,
            'arrHotNews'        => $arrHotNews[0],
            'arrTopNews'        => $arrTopNews,
            'arrListVideo'      => $arrListVideo,
            'arrListPhoto'      => $arrListPhoto,
            'arrOtherNews'      => $arrListOther,
            'offsetMore'        => LIMIT_LIST,
            'arrListExclude'    => $arrDataExclude,
            'isHome'            => 1,
            'ogType'            => 'website',
            'ogTitle'           => 'SEA Games 28, tin tức, video, lịch thi đấu SEA Games - VnExpress',
            'ogUrl'             => $this->view->configView['url'],
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => 'SEA Games 28 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu SEA Games 28 tại Singapore, tường thuật bình luận bóng đá SEA Games 28.'
        ));
        
        // Set metadata tags
        $this->view->headTitle()->prepend('SEA Games 28, tin tức, video, lịch thi đấu SEA Games - VnExpress');
        $this->view->headMeta()->setName('description', 'SEA Games 28 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu SEA Games 28 tại Singapore, tường thuật bình luận bóng đá SEA Games 28.')
                               ->setName('keywords', 'SEA Games 28, tin tuc , bong da');
    }

}

?>