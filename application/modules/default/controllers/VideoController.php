<?php

/**
 * @author   : PhongTX
 * @name : VideoController
 * @copyright   : FPT Online
 * @todo    : Video Controller
 */
class VideoController extends Zend_Controller_Action
{

    /**
     * @author   : PhongTX
     * @name : videoAction
     * @copyright   : FPT Online
     * @todo    : Video Action
     */
    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);

        //set cache
        $this->_request->setParam('cache_file', 1);
        //Get article id
        $intVideoId = $this->_request->getParam('id', 0);
        //init path url vne
        $pathUrl = $this->view->configView['url_vne'];

        //check video to return vne
        if ($intVideoId)
        {
            $pathUrl .= $this->_request->getPathInfo();
        }
        else
        {
            $pathUrl .= '/video/the-thao';
        }
        //redirect to vne
        $this->_redirect($pathUrl, array('code' => 301));

    }

    /**
     * @author   : PhongTX
     * @name : vneInfoAction get data video for xml
     * @copyright   : FPT Online
     * @todo    : vneInfo Action
     * @return   : XML
     */
    function vneInfoAction()
    {
        $this->_helper->layout()->disableLayout();
        $intVideoID = (int) $this->getRequest()->getParam('id', 0);
        $typeHTML   = $this->getRequest()->getParam('typeHTML', '');
        $sizeIMG    = $this->getRequest()->getParam('sizeIMG', 'size4');
        //get type video
        $typeVideo   = $this->getRequest()->getParam('type', 1);

        //Get exclude
        $strExclude = $this->getRequest()->getParam('exclude', '');

        if ($intVideoID > 0 && $intVideoID < 5000000)
        {
            //init instance model news article fpt
            if ($typeVideo != 2)
            {
                $this->view->objArticle->setIdBasic($intVideoID);
                $arrArticle = $this->view->objArticle->getArticleFull($intVideoID);
                $arrMedia = array();
                if (!empty($arrArticle))
                {
                    // Reference article
                    $arrParams =  array('article_id' => $intVideoID, 'limit' => 1, 'offset' => 0);
                    $arrMedia   = $this->view->objArticle->getObjectReferenceByArticleId($arrParams);

                    $arrMedia = $arrMedia['data'];
                    $duration = 0;
                    if (!empty($arrMedia))
                    {
                        $arrMedia = current($arrMedia);
                            if($arrMetaData = Zend_Json::decode($arrMedia['meta_data']) )
                            {
                                $duration = (int)$arrMetaData['duration'];
                            }
                    }
                    $arrMedia['duration'] = $duration;
                }
            }
            else {

                $arrDetail = $this->view->objArticle->getDetailVideo(array($intVideoID));
                $arrArticle = array(
                    'thumbnail_url' => $arrDetail[$intVideoID]['thumbnail_url'],
                    'title'     => $arrDetail[$intVideoID]['caption'],
                    'share_url' => $arrDetail[$intVideoID]['share_url'],
                    'cate'      => $arrDetail[$intVideoID]['category_id']

                );
                $arrMedia['url'] = $arrDetail[$intVideoID]['url'];
            }
            if (!empty ($arrArticle['cate']))
            {
                $arrCateID = array_keys($arrArticle['cate']);
            }
        }

        //Check HTML5
        if ( $typeHTML == 'html5' )
        {
            $this->_helper->viewRenderer->setNoRender();
            $callback   = $this->getRequest()->getParam('callback', 'defaultCallback');
            if ( isset($arrMedia) && !empty($arrMedia) )
            {
                $response   = array(
                    'error'         => 0,
                    'media_url'     => $arrMedia['url'],
                    'thumbnail_url' => $this->view->Imageurl($arrArticle['thumbnail_url'], $sizeIMG)
                );
            }
            else
            {
                $response   = array(
                    'error'         => 1,
                    'media_url'     => '',
                    'thumbnail_url' => ''
                );
            }//end if

            //Echo response
            echo $callback.'('.Zend_Json::encode($response).')';
            exit;
        }//end if

        //Check lagiga & seri A
        $isCopyright = 'false';
        if(is_array($arrCateID) && (in_array(1002598, $arrCateID) || in_array(1002599, $arrCateID) ) ){
            $isCopyright = 'true';
        }
        $this->view->assign(array(
                'arrMedia'   => $arrMedia,
                'arrArticle'   => $arrArticle,
                'videoID'    => $intVideoID,
                'isCopyright'    => $isCopyright,
                'strExclude' => $strExclude,
        ));
    }

    /**
     * Get suggest video
     */
    function vneMoreAction()
    {
        $this->_helper->layout()->disableLayout();
        //Get video id
        $intVideoID = (int) $this->getRequest()->getParam('id', 0);
        $exclude = $this->getRequest()->getParam('exclude', '');
        //dataExclude bao gom ID dang xem va 2 video lien quan
        $dataExclude    = (isset($exclude) && !empty($exclude))?explode(',', $exclude):array();
        $countExclude   = count($dataExclude);
        $arrArticle = array();
        if($intVideoID > 0)
        {
            //Get model isntance
            $newsModel  = Thethao_Model_News::getInstance();
            $modelTag = new Fpt_Data_News_Tag();

            $arrVideoInfo = $this->view->objArticle->getArticleFull($intVideoID);
            $cateID         = ( $intVideoID > 0 && !empty($arrVideoInfo)) ? $arrVideoInfo["category_id"] : CATE_ID_VIDEO;
            //get video lien quan
            if (!empty($arrVideoInfo['list_tag_id']))
            {
                $arrParams = array(
                    'tag_id' => $arrVideoInfo['list_tag_id'],
                    'article_type' => VIDEO,
                    'limit'        => LIMIT_VIDEO_MORE + $countExclude,
                    'offset'       => 0
                );
                //get list article by tag
                $arrArticle = $modelTag->getArticleUnionTag($arrParams);

            }
            //excluse video dang xem va 2 video lien quan
            $arrArticle = array_diff($arrArticle,$dataExclude);
            //toltal media
            $totalMedia = count($arrArticle);
            //if total media < 9 then get new video
            if ($totalMedia < LIMIT_VIDEO_MORE)
            {
                $limit_more  = (LIMIT_VIDEO_MORE - $totalMedia) + $countExclude; //( $limitTopHot là total top & hot video)
                $arrParams = array(
                    'category_id' => $cateID,
                    'limit' => $limit_more,
                    'offset' => 0,
                    'article_type' => VIDEO
                );
                //get list articleid new
                $arrNewVideo = $newsModel->getListArticleIdsByRule2($arrParams);
                if (!empty($arrNewVideo["data"]))
                {
                    //merge media & new video
                    $arrArticle = array_merge($arrArticle, $arrNewVideo["data"]);
                }
            }
            //lay dung so luong hien thi
            $arrArticle = array_slice($arrArticle, 0, LIMIT_VIDEO_MORE);
            if(is_array($arrArticle) && count($arrArticle)>0)
            {
                $this->view->objArticle->setIdBasic($arrArticle);
                //get Instance Block
                $objBlock = Thethao_Plugin_Block::getInstance();
                //set excluses
                $objBlock->setExclude($arrArticle);

                $arrArticleResult = array();
                foreach($arrArticle as $intArticleID)
                {
                    $arrParams = array(
                                    'article_id' => $intArticleID,
                                    'limit' => 1,
                                    'offset' => 0
                    );
                    $arrMedia = $this->view->objArticle->getObjectReferenceByArticleId($arrParams);
                    $duration = 0;
                    if(!empty($arrMedia)){
                        $arrMedia = current($arrMedia);
                        if($arrMetaData = Zend_Json::decode($arrMedia['meta_data']) ){
                            $duration = (int)$arrMetaData['duration'];
                        }
                    }
                    $arrArticleDetail = $this->view->objArticle->getArticleBasic($intArticleID);
                    $arrArticleDetail['duration'] = $duration;
                    $arrArticleResult[] = $arrArticleDetail;
                }
            }
            $this->view->arrData = $arrArticleResult;
        }
    }
}