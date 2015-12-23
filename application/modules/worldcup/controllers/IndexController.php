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
        $intCateId = $this->_request->getParam('cateid', WORLD_CUP);

        //get page
        $intPage = $this->_request->getParam('page', 1);

        //get Instance New
        $objNews = Thethao_Model_News::getInstance();
        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        //get Instance News
        $objArticle = $this->view->objArticle;
        $arrHotNews = $arrListExclude = array();
        if (1 == $intPage)
        {
            // Params get hot news
            $arrParams = array(
                    'area' => array(
                        array(
                            'category_id' => SITE_ID,
                            'showed_area' => 'worldcup'
                        )
                    ),
                    'offset' => 0,
                    'limit' => 1
                );
            // Get Box Hot News
            $arrHotNews    = $objArticle->getTopHotArticleByArr($arrParams);
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
            //init params to get list article with rule 2, list on folder Thethao
            $arrParamNews = array(
                'category_id'   => $intCateId,
                'limit'         => LIMIT_LIST,
                'offset'        => 0,
                'article_type'  => NULL
            );
            //get list article with rule 2 list on Thethao
            $arrListData = $objNews->getListArticleIdsByRule2($arrParamNews);
            if(!empty($arrHotExclude))
            {
                //set obj get Article
                $objArticle->setIdBasic($arrHotExclude);
                $arrListData['data'] = array_diff($arrListData['data'], $arrHotExclude);
            }
            $arrExclude = array_merge($arrHotExclude, $arrListData['data']);
            $objBlock->setExclude($arrExclude);
            //get Exclude
            $arrListExclude = $objBlock->getExclude();

        }
		else
		{
			//init params to get list article with rule 2, list on folder Thethao
			$arrParamNews = array(
				'category_id'   => $intCateId,
				'limit'         => LIMIT_LIST,
				'offset'        => (($intPage - 1) * LIMIT_LIST),
				'article_type'  => NULL
			);
			//get list article with rule 2 list on Thethao
			$arrListData = $objNews->getListArticleIdsByRule2($arrParamNews);
		}
        //set obj get Article
        $objArticle->setIdBasic($arrListData['data']);
        //get video
        $arrParamVideo = array(
            'category_id'   => CATE_ID_VIDEO,
            'limit'         => LIMIT_VIDEO + LIMIT_LIST,
            'offset'        => 0,
            'article_type'  => TYPE_VIDEO
        );
        //get list video with rule 1
        $arrListVideo = $objNews->getListArticleIdsByRule1($arrParamVideo);
        if(1 == $intPage && !empty($arrListVideo['data']))
        {
            $arrListVideo['data'] = array_diff($arrListVideo['data'],$arrListExclude);
        }
        if(count($arrListVideo['data']) > LIMIT_VIDEO)
        {
            $arrListVideo['data'] = array_slice($arrListVideo['data'],0,LIMIT_VIDEO);
        }
        // set id from $arrListPaging to get article
        $objArticle->setIdBasic($arrListVideo['data']);
        //get video
        $arrParamPhoto = array(
            'category_id'   => CATE_ID_PHOTO,
            'limit'         => LIMIT_PHOTO + LIMIT_LIST,
            'offset'        => 0,
            'article_type'  => TYPE_PHOTO
        );
        //get list video with rule 1
        $arrListPhoto = $objNews->getListArticleIdsByRule1($arrParamPhoto);
        if(1 == $intPage && !empty($arrListPhoto['data']))
        {
            $arrListPhoto['data'] = array_diff($arrListPhoto['data'],$arrListExclude);
        }
        if(count($arrListPhoto['data']) > LIMIT_PHOTO)
        {
            $arrListPhoto['data'] = array_slice($arrListPhoto['data'],0,LIMIT_PHOTO);
        }
        // set id from $arrListPaging to get article
        $objArticle->setIdBasic($arrListPhoto['data']);
        //check if page > max page then return to max page
        if ($arrListData['total'] > 0)
        {
            $maxPage = ceil($arrListData['total'] / LIMIT_LIST);
            if ($intPage > $maxPage)
            {
                $this->_redirect($this->configView['url'] . '/page/' . $maxPage . '.html');
            }
        }
        //InitParam Paging
        $arrParamPaging = array(
            'total' => $arrListData['total'],
            'page' => $intPage,
            'url' => $this->view->configView['url'] . '/page',
            'perpage' => LIMIT_LIST,
            'classPagination' => 'pagination_news',
            'extEnd' => '.html',
            'separate' => '/'
        );

        //Set param
        $this->_request->setParam('block_cate', $intCateId);

        //init cate for set menu
        //set cate active
        $this->_request->setParam('cateActiveId', $intCateId);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);

        //Assign to view
        $this->view->assign(array(
            'intCategoryId'     => WORLD_CUP,
            'arrHotNews'       => $arrHotNews[0],
            'arrParamPaging'    => $arrParamPaging,
            'arrData'           => $arrListData['data'],
            'arrListVideo'      => $arrListVideo['data'],
            'arrListPhoto'      => $arrListPhoto['data'],
            'offsetMore'        => LIMIT_LIST,
            'arrListExclude'    => $arrListExclude,
            'isHome'            => 1,
            'ogType'            => 'website',
            'ogTitle'           => 'World Cup 2014 Brazil, tin tức, video, lịch thi đấu World Cup - VnExpress',
            'ogUrl'             => $this->view->configView['url'],
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => 'Fifa World Cup 2014 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu WC 2014 tại Brazil, tường thuật bình luận bóng đá World Cup 2014'
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
            echo $this->view->render('mobile/index.phtml');
        }
        // Set metadata tags
        $this->view->headTitle()->prepend('World Cup 2014 Brazil, tin tức, video, lịch thi đấu World Cup - VnExpress');
        $this->view->headMeta()->setName('description', 'Fifa World Cup 2014 - tin nhanh , hình ảnh , video clip bóng đá, lịch thi đấu WC 2014 tại Brazil, tường thuật bình luận bóng đá World Cup 2014.')
                               ->setName('keywords', 'World Cup 2014, tin tuc , bong da');
    }

}

?>