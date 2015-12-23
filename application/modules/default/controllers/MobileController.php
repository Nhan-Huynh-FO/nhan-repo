<?php
/**
 * Mobile controler
 */
class MobileController extends Zend_Controller_Action
{

    function init()
    {
        // set cache
        $this->_request->setParam('cache_file', 1);
        //Disable layout
        $this->_helper->layout->disableLayout(true);
        //Set no render view
        $this->_helper->viewRenderer->setNoRender(true);
    }

    function listhomeAction()
    {
        // get news model
        $modelNews = Thethao_Model_News::getInstance();
        // get params
        $intCateId = $this->_request->getParam('category_id');
        $offset = $this->_request->getParam('offset');
        $limit = $this->_request->getParam('limit');
        $exclude = $this->_request->getParam('exclude', '');
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
        $arrDiffNews = array();
        // init params array
        $arrParams = array(
            'category_id' => $intCateId,
            'article_type' => NULL,
            'limit' => $limit + $totalExclude,
            'offset' => $offset,
        );
        // get news by rule 2: set folder X + liston into folder X
        // => order : publish date DESC, priority DESC, publish time DESC
        $arrData = $modelNews->getListArticleIdsByRule2($arrParams);
		if (!empty($arrData['data']))
		{
			$arrDiffNews = array_diff($arrExclude, $arrData['data']);
			$arrData['data'] = array_diff($arrData['data'], $arrExclude);
            $arrData['data'] = array_slice($arrData['data'], 0, $limit);
		}

        $arrResult = array(
            'error' => 0,
            'offset' => $offset + count($arrDiffNews) + $limit,
            'total' => count($arrData['data']),
			'exclude' => implode(',', $arrDiffNews),
            'html' => ''
        );
        if (empty($arrData['data']))
        {
            $arrResult['error'] = 1;
            $arrResult['message'] = 'Dữ liệu đã được tải hết';
        }
        else
        {
            $this->view->assign(array(
                'arrData'       => $arrData['data'],
                'intCategoryId' => $intCateId,
            ));
            //set obj get Article
            $this->view->objArticle->setIdBasic($arrData['data']);
            $arrResult['html'] = $this->view->render('mobile/listcategory.phtml');
        }
        echo Zend_Json::encode($arrResult);
    }

    function listcategoryAction()
    {
        // get news model
        $modelNews = Thethao_Model_News::getInstance();
        // get params
        $intCateId = $this->_request->getParam('category_id');
        $offset = $this->_request->getParam('offset');
        $limit = $this->_request->getParam('limit');
        $exclude = $this->_request->getParam('exclude', '');
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
        // init params array
        $arrParams = array(
            'category_id'   => $intCateId,
            'article_type'  => NULL,
            'offset'    => $offset,
            'limit'     => $limit + $totalExclude,
        );
        // get news by rule 2: set folder X + liston into folder X
        // => order : publish date DESC, priority DESC, publish time DESC
        $arrData = $modelNews->getListArticleIdsByRule2($arrParams);
        $arrResult = array(
            'error' => 0,
            'offset' => $offset + $limit,
            'total' => count($arrData['data']),
            'exclude' => '',
            'html' => ''
        );
        if (empty($arrData['data']))
        {
            $arrResult['error'] = 1;
            $arrResult['message'] = 'Dữ liệu đã được tải hết';
        }
        else
        {
            $arrData['data'] = array_slice(array_diff($arrData['data'], $arrExclude), 0, $limit);
            //set data exclude return
            $arrResult['exclude'] = implode(',', array_slice($arrData['data'], -5, 5));
            $this->view->assign(array(
                'arrData' => $arrData['data'],
                'intCategoryId' => $intCateId,
            ));
            //set obj get Article
            $this->view->objArticle->setIdBasic($arrData['data']);
            $arrResult['html'] = $this->view->render('mobile/listcategory.phtml');
        }
        echo Zend_Json::encode($arrResult);
    }

    function othernewsAction()
    {
        //get all param
        $arrParam = $this->_request->getParams();
        //New object
        $modelNews = Thethao_Model_News::getInstance();
        // get offset
        $offset = (int)$this->_request->getParam('offset');
        $limit = (int)$this->_request->getParam('limit');
		$exclude = $this->_request->getParam('exclude');
		if ($exclude)
		{
			$offset = $offset + 1;
			$limit = $limit + 1;
		}
        //Init param get article contextual
        $arrParams = array(
            'article_id' => (int)$arrParam['article_id'],
            'limit' => $limit,
            'offset' => $offset,
        );
        $arrOtherNews = $this->view->objArticle->getRecommendationByArticleId($arrParams);
        if (empty($arrOtherNews['data']))
        {
            //Init array params other news
            $arrOtherNewsParams = array(
                'category_id'   => (int)$arrParam['category_id'],
                'article_type'  => NULL,
                'limit'     => $limit,
                'offset'    => $offset,
            );
            //Get other news (Get news by rule 2: folder và liston lên folder của tin đang xem)
            $arrOtherNews = $modelNews->getListArticleIdsByRule2($arrOtherNewsParams);
        }
        //var_dump('<pre>', $arrOtherNews);die;
        //check data empty
        if (is_array($arrOtherNews) && !empty($arrOtherNews['data']))
        {
            $intKeyExists = array_search($arrParam['article_id'], $arrOtherNews['data']);
            if ($intKeyExists !== false)
            {
                unset($arrOtherNews['data'][$intKeyExists]);
            }
            $arrOtherNews['data'] = array_slice($arrOtherNews['data'], 0, LIMIT_OTHER_NEWS);
        }//end if

        $this->view->assign(array(
            'arrOtherNews' => $arrOtherNews['data']
        ));
        $arrResult = array(
            'error' => 0,
            'offset' => $offset + LIMIT_OTHER_NEWS,
            'total' => count($arrOtherNews['data']),
			'exclude' => $intKeyExists !== false ? 1 : 0,
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
            $arrResult['html'] = $this->view->render('mobile/othernews.phtml');
        }
        echo Zend_Json::encode($arrResult);
    }

    /**
     * @author   : PhongTX
     * @name : slideshowAction
     * @copyright   : FPT Online
     * @todo    : Slideshow Action
     */
    public function slideshowAction()
    {
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        // Set not render view
        $this->_helper->viewRenderer->setNoRender(true);
        //Get article id
        $id = max(0, $this->_request->getParam('id', 0));
        $arrResult = array('data' => '');
        if ($id > 0)
        {
            //Get instance
            $instance = $this->view->objArticle;
            //Set id
            $instance->setIdBasic($id);
            //Get full detail article
            $arrDetail = $instance->getArticleBasic($id);
            //Set params
            $arrParams = array(
                'article_id' => $id,
                'limit' => LIMIT_SLIDESHOW_PHOTO,
                'offset' => 0
            );
            //Get list object reference
            $arrReference = $instance->getObjectReferenceByArticleId($arrParams);
            //Assign to views
            $this->view->assign(array(
                'arrDetail' => $arrDetail,
                'arrData' => $arrReference['data']
            ));
            $arrResult['data'] = $this->view->render('mobile/slideshow.phtml');
        }
        echo Zend_Json::encode($arrResult);
    }

    /**
     * @author   : AnPCD
     * otherphotoAction
     */
    public function otherphotoAction()
    {
        //get limit & offset
        $offset = (int)$this->_request->getParam('offset');
        $limit = (int)$this->_request->getParam('limit');

        //Get album
        $newsModel = Thethao_Model_News::getInstance();

        //format param
        $arrParam = array(
            'category_id' => CATE_ID_PHOTO,
            'limit'       => $limit,
            'offset'      => $offset,
        );

        //get all article by rule 2
        $arrAlbum = $newsModel->getListArticleIdsByRule2($arrParam);

        // Assign to view
        $this->view->assign(array(
            'arrData'         => $arrAlbum['data']
        ));

        $response = array(
            'error' => 0,
            'offset' => $offset + LIMIT_PHOTO_MORE,
            'total' => count($arrAlbum['data']),
            'html' => '',
        );

        if (!empty($arrAlbum['data']))
        {
            //set id
            $this->view->objArticle->setIdBasic($arrAlbum['data']);
            //Render view
            $response['html'] = $this->view->render('mobile/otherphoto.phtml');
        }
        else
        {
            $response['error'] = 1;
            $response['msg']   = 'Dữ liệu đã được tải hết';
        }
        //Return Json
        echo Zend_Json::encode($response);
    }
}