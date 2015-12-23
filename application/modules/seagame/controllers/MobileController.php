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
}
