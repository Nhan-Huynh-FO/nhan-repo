<?php

/**
 * @author      :   PhongTX
 * @name        :   VideoController
 * @copyright   :   Fpt Online
 * @todo        :   Video Controller
 */
class VideoController extends Zend_Controller_Action
{

    /**
     * @todo - Worldcup video page
     * @author PhongTX
     */
    public function indexAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
        //Set category id for block_cate user in block
        $this->_request->setParam('block_cate', CATE_ID_VIDEO);
        //get instance Cate
        $objCate = Thethao_Model_Category::getInstance();
        //get page
        $intPage = max(1, $this->_request->getParam('p', 1));
        //Get article id
        $intVideoId = $this->_request->getParam('id', 0);
        //New object
        $objNews = new Thethao_Model_News();
        //Get instance
        $objArticle = $this->view->objArticle;
        if (DEVICE_ENV != 1)
        {
            $limit = LIMIT_LIST_VIDEO + 5;
        }
        else
        {
            $limit = LIMIT_LIST_VIDEO + 1;
        }
        $totalNew = 1;
        //trang chu video
        if ($intVideoId <= 0)
        {
            //Id tracking
            $articleTrackingId = 0;

            $intPage = 1;
            //Get cate id
            $intCateId = CATE_ID_VIDEO; //Cate video
            //Init params
            $arrParams = array(
                'category_id' => $intCateId,
                'limit' => $limit, //Limit 12 video / 1 page + 4 tin video lien quan
                'offset' => ($intPage - 1) * LIMIT_LIST_VIDEO
            );
            //get all article by rule 2
            $arrListVideo = $objNews->getListArticleIdsByRule2($arrParams);
            //get Video new
            $intVideoId = array_shift($arrListVideo['data']);
            //Get detail video
            $arrVideoInfo = $objArticle->getArticleFull($intVideoId);

            //Set category related
            $intCateIdRelated = $arrVideoInfo['category_id'];

            //Id tracking
            $articleTrackingId = $intVideoId;

            //meta title + image + description
            $strMetaTitle = html_entity_decode($arrVideoInfo['title'], ENT_COMPAT, 'UTF-8') . 'World Cup 2014 - VnExpress';
            $strMetaImage = !empty($arrVideoInfo['thumbnail_url']) ? $this->view->Imageurl($arrVideoInfo['thumbnail_url'], 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg';
            $strMetaDescript = trim(strip_tags(html_entity_decode($arrVideoInfo['lead'], ENT_COMPAT, 'UTF-8'))) . ' - VnExpress';
        }
        else
        {
            //Id tracking
            $articleTrackingId = $intVideoId;

            //Get detail video
            $arrVideoInfo = $objArticle->getArticleFull($intVideoId);

            //check data not empty
            if (empty($arrVideoInfo))
            {
                $this->_redirect("/video");
            }

            //check publish
            if (intval($arrVideoInfo['publish_time']) > time())//is schedule article
            {
                $sig = md5($id . $arrVideoInfo['creation_time']); //gen sig

                if ($sig != $this->_request->getParam('sig'))//secr not match
                {
                    $this->_redirect($this->view->configView['linkerror'][DEVICE_ENV], array('code' => 301));
                }
            }

            //check pvtt
            if ($arrVideoInfo['privacy'] & 64)
            {
                $this->_redirect($this->view->configView['url_vne'] . "/phong-van-truc-tuyen/" . Fpt_Filter::setSeoLink($arrVideoInfo['title_format']) . '-' . $arrVideoInfo['article_id'] . '.html');
            }

            //str link
            $strLink = $this->_request->getPathInfo();
            //check link
            //check redirect link
            if (strpos($arrVideoInfo['share_url'], $strLink) === FALSE && APPLICATION_ENV != 'development')
            {
                $this->_redirect($this->view->ShareurlWorldcup($arrVideoInfo['share_url']), array('code' => 301));
            }
            //Init params
            $arrParams = array(
                'category_id' => CATE_ID_VIDEO,
                'limit' => $limit, //Limit 12 video / 1 page + 4 tin video lien quan
                'offset' => (($intPage - 1) * LIMIT_LIST_VIDEO)
            );
            //get all article by rule 2
            $arrListVideo = $objNews->getListArticleIdsByRule2($arrParams);
            if (!empty($arrListVideo['data']))
            {
                $arrListVideo['data'] = array_diff($arrListVideo['data'], array($intVideoId));
            }
            //meta title + image + description
            $strMetaTitle = html_entity_decode($arrVideoInfo['title'], ENT_COMPAT, 'UTF-8') . ' - VnExpress';
            $strMetaImage = !empty($arrVideoInfo['thumbnail_url']) ? $this->view->Imageurl($arrVideoInfo['thumbnail_url'], 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg';
            $strMetaDescript = trim(strip_tags(html_entity_decode($arrVideoInfo['lead'], ENT_COMPAT, 'UTF-8'))) . ' - VnExpress';
        }
        if (DEVICE_ENV != 1)
        {
            /* Get video lien quan */
            //video exclude
            //$videoExclude = array_unique(array_merge((array) $arrListVideo['data'], array($intVideoId)));
            $videoExclude = array($intVideoId);
            $mVideoExclude = count($videoExclude);
            //khoi tao so luong video lien quan
            $mRelateVideo = 0;

            //khoi tao data arrVideoRelate
            $arrVideoRelate = array();

            //get video lien quan -> by tag
            if (!empty($arrVideoInfo['list_tag_id']))
            {
                //New object
                $obj = new Fpt_Data_News_Tag();
                $arrParams = array(
                    'tag_id' => $arrVideoInfo['list_tag_id'],
                    'site_id' => SITE_ID,
                    'article_type' => TYPE_VIDEO,
                    'limit' => LIMIT_RELATED_VIDEO + $mVideoExclude,
                    'offset' => 0
                );
                //get list video lien quan
                $arrVideoResult = $obj->getArticleUnionTag($arrParams);
                //neu co video lien quan
                if (!empty($arrVideoResult))
                {
                    //loai tru tin dang xem
                    $arrVideoRelate = array_diff($arrVideoResult, $videoExclude);
                    if (count($arrVideoRelate) > LIMIT_RELATED_VIDEO)
                    {
                        $arrVideoRelate = array_slice($arrVideoRelate, 0, LIMIT_RELATED_VIDEO);
                    }
                    $videoExclude = array_unique(array_merge($videoExclude, $arrVideoRelate));
                    //tinh tong so video lien quan + exclude
                    $mRelateVideo = count($arrVideoRelate);
                    $mVideoExclude = count($videoExclude);
                }
                unset($arrVideoResult);
            }
            //tong so video lien quan nho hon so luong can lay
            //Nếu không đủ 2 video thì lấy thêm video cùng chuyên mục
            //Loại trừ Video đang xem
            //Sắp xếp Video mới lên trên
            if ($mRelateVideo < LIMIT_RELATED_VIDEO)
            {
                //Init params
                $arrParamsVideoRule2 = array(
                    'category_id' => CATE_ID_VIDEO,
                    'limit' => (LIMIT_RELATED_VIDEO - $mRelateVideo) + 1,
                    'offset' => 0
                );
                //Get news by rule 2 : set folder  X  +  ListOn vao folder  X  => order by publish_time
                $arrListVideoLasest = $objNews->getListArticleIdsByRule2($arrParamsVideoRule2);
                //Loai tru video dang xem
                $arrListVideoLasest = array_diff($arrListVideoLasest['data'], $videoExclude);
                //merge list video folder vao video lien de lay them cac video moi nhat
                // cung folder dang xem
                $arrVideoRelate = array_merge($arrVideoRelate, $arrListVideoLasest);
                //lay dung so luong hien thi
                $arrVideoRelate = array_slice(array_unique($arrVideoRelate), 0, LIMIT_RELATED_VIDEO);
            }
            //set vao article detail
            $objArticle->setIdBasic($arrVideoRelate);
            //Loai tru 4 tin lien quan
            $videoExclude = array_merge($arrVideoRelate, $videoExclude);
            $arrEx = array();
            $arrListVideoTemp = array_slice($arrListVideo['data'], 0, LIMIT_LIST_VIDEO);
            foreach ($videoExclude as $idMege)
            {
                if (in_array($idMege, $arrListVideoTemp))
                {
                    $totalNew++;
                }
                if (in_array($idMege, $arrListVideo['data']))
                {
                    array_push($arrEx, $idMege);
                }
            }
            $arrListVideo['data'] = array_slice(array_values(array_diff($arrListVideo['data'], $arrEx)), 0, LIMIT_LIST_VIDEO);
            //Set lai total
            $arrListVideo['total'] = $arrListVideo['total'] - $totalNew;
        }
        else
        {
            $totalNew = 1;
        }
        //Get total page
        $totalPage = $arrListVideo['total'] ? intval(ceil($arrListVideo['total'] / LIMIT_LIST_VIDEO)) : 0;
        //set vao article detail
        $objArticle->setIdBasic($arrListVideo['data']);
        ////////////////////get detail reference video //////////////////
        $list_object = $arrVideoInfo['list_object'];
        if (!empty($list_object))
        {
            foreach ($list_object as $object)
            {
                $object_type = $object['object_type'];
                $object_id = $object['object_id'];
            }
        }
        if ($object_type == 202)
        {
            // lấy detail video từ library
            $arrDataRV = $objArticle->getDetailVideo(array($object_id));
            if (!empty($arrDataRV))
            {
                $arrDataRV = array(
                    'url' => $arrDataRV[$object_id]['url'],
                    'object_id' => $object_id,
                    'object_type' => $object_type,
                    'have_license' => $arrDataRV[$object_id]['have_license']
                );
            }
        }
        else
        {
            $arrParamRV = array(
                'article_id' => $intVideoId,
                'limit' => 1,
                'offset' => 0
            );
            $arrDataRV = $objArticle->getObjectReferenceByArticleId($arrParamRV);
            if (isset($arrDataRV['data']) && !empty($arrDataRV['data']))
            {
                $arrDataRV = current($arrDataRV['data']);
                //$arrDataRV['have_license'] = 0;
            }
            else
            {
                $arrDataRV = array();
            }
        }
        ////////////////////end get detail reference video /////////////
        //InitParam Paging
        $arrParamPaging = array(
            'total' => $arrListVideo['total'],
            'page' => $intPage,
            'perpage' => LIMIT_LIST_VIDEO,
            'classPagination' => 'pagination_news right',
        );
        $share_url = str_replace('http://thethao.vnexpress.net/', 'http://worldcup.vnexpress.net/', $arrVideoInfo['share_url']);
        //init cate for set menu
        //set cate active
        $this->_request->setParam('cateActiveId', CATE_ID_VIDEO);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
        //Assign to views
        $this->view->assign(array(
            'totalNew' => $totalNew,
            'arrEx' => $videoExclude,
            'data' => $arrVideoInfo,
            'arrMenu' => $objCate->getMenu(CATE_ID_VIDEO),
            'arrData' => $arrListVideo,
            'intTotalVideo' => $arrListVideo['total'],
            'intOffset' => $limit,
            'totalPage' => $totalPage,
            'currPage' => $intPage,
            'arrParamPaging' => $arrParamPaging,
            'cateId' => CATE_ID_VIDEO,
            'intCateIdPolyAds' => CATE_ID_VIDEO,
            'arrVideoResult' => $arrVideoRelate,
            'articleTrackingId' => $articleTrackingId,
            'notRefreshPage' => 1,
            'strMetaTitle' => htmlspecialchars($strMetaTitle),
            'strMetaImage' => $strMetaImage,
            'strMetaDescript' => htmlspecialchars($strMetaDescript),
            'isArticleHotVnE' => $arrVideoInfo['privacy'] & 512,
            'arrDataRV' => $arrDataRV,
			'ogType' => 'website',
            'ogTitle' => $strMetaTitle,
            'ogUrl' => $share_url,
            'ogImage' => !empty($arrVideoInfo['thumbnail_url']) ? $this->view->Imageurl($arrVideoInfo['thumbnail_url'], 'size2') : 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription' => $strMetaDescript
        ));
        //Append css
        $this->view->headLink()->appendStylesheet($this->view->configView['css'] . '/lightbox/video_feedback.css')
                ->appendStylesheet($this->view->configView['css'] . '/lightbox/lightbox.css');
        //Add js
        $this->view->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/flash.detect.js')
                ->headScript()->appendFile($this->view->configView['vnecdn']['js'] . '/jquery.jcache.js')
                ->headScript()->appendFile($this->view->configView['js'] . '/video.js')
                ->headScript()->appendFile($this->view->configView['eclick']['ads']['js']);
        $keywords = 'World cup 2014, tin tuc , bong da';
        $this->view->headTitle($strMetaTitle);
        $this->view->headMeta()->setName('description', $strMetaDescript);
        $this->view->headMeta()->setName('keywords', $keywords);
        //init cate for set menu
        //set cate active
        $this->_request->setParam('cateActiveId', CATE_ID_VIDEO);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', WORLD_CUP);
    }

    /**
     * @author   : PhongTX
     * @name : vneInfoAction get data video for xml
     * @copyright   : FPT Online
     * @todo    : vneInfo Action
     * @return   : XML
     */
    public function vneInfoAction()
    {

        //Disable layout
        $this->_helper->layout()->disableLayout();
        //Get video id
        $intVideoId = (int) $this->getRequest()->getParam('id', 0);
        //Get type HTML
        $typeHTML = $this->getRequest()->getParam('typeHTML', '');
        //Get type Video
        $typeVideo = $this->getRequest()->getParam('type', 1);
        //Get from page
        $getFrom = $this->getRequest()->getParam('gf', 0);
        $getFrom = $getFrom == 1 ? 1 : 0;
        //Get exclude
        $strExclude = $this->getRequest()->getParam('exclude', '');

        //Valid data
        if ($intVideoId > 0)
        {
            // Get instance
            $obj = $this->view->objArticle;
            //Default $arrData
            $arrData = array();
            $arrVideoInfo = $obj->getArticleFull($intVideoId);
            //Valid data
            if (!empty($arrVideoInfo))
            {
                $list_object = $arrVideoInfo['list_object'];
                if (!empty($list_object))
                {
                    foreach ($list_object as $object)
                    {
                        $object_type = $object['object_type'];
                        $object_id = $object['object_id'];
                    }
                }
                if ($object_type == 202)
                {
                    // lấy detail video từ library
                    $arrDataRV = $obj->getDetailVideo(array($object_id));
                    if (!empty($arrDataRV))
                    {
                        $arrDataRV = array(
                            'url' => $arrDataRV[$object_id]['url'],
                            'object_id' => $object_id,
                            'have_license' => $arrDataRV[$object_id]['have_license']
                        );
                    }
                }
                else
                {
                    //Set params
                    $arrParams = array(
                        'article_id' => $intVideoId,
                        'limit' => 1,
                        'offset' => 0
                    );
                    // Reference article
                    $arrData = $obj->getObjectReferenceByArticleId($arrParams);
                    if (!empty($arrData['data'][0]))
                    {
                        $arrDataRV = array(
                            'url' => $arrData['data'][0]['url'],
                            'have_license' => 0
                        );
                    }
                }
                $duration = 0;
                //Valid data
                if (!empty($arrData['data']))
                {
                    $arrData = current($arrData['data']);
                    if ($arrMetaData = Zend_Json::decode($arrData['meta_data']))
                    {
                        $duration = (int) $arrMetaData['duration'];
                    }
                }
                $arrData['duration'] = $duration;
            }//end if            
        }//end if
        //Assign to views
        $this->view->assign(array(
            'arrData' => $arrData,
            'arrDataRV' => $arrDataRV,
            'data' => $arrVideoInfo,
            'intId' => $intVideoId,
            'getFrom' => $getFrom,
            'strExclude' => $strExclude,
            'typeVideo' => $typeVideo,
            'showRefresh' => 1
        ));
        //Check HTML5
        if ($typeHTML == 'html5')
        {
            $this->_helper->viewRenderer->setNoRender();
            $callback = $this->getRequest()->getParam('callback', 'defaultCallback');
            if (isset($arrData) && !empty($arrData))
            {
                $response = array(
                    'error' => 0,
                    'media_url' => $arrDataRV['url'],
                    'thumbnail_url' => $this->view->ImageurlArticle($arrVideoInfo, 'size11')
                );
            }
            else
            {
                $response = array(
                    'error' => 1,
                    'media_url' => '',
                    'thumbnail_url' => ''
                );
            }//end if
            //Echo response
            echo $callback . '(' . Zend_Json::encode($response) . ')';
            exit;
        }//end if
    }

    /**
     * Get suggest video
     */
    public function vneMoreAction()
    {
        //disable layout
        $this->_helper->layout()->disableLayout();
        //get params
        $params = $this->_request->getParams();
        //Get instance
        $objArticle = $this->view->objArticle;
        //New object
        $objNews = new Thethao_Model_News();
        //dataExclude bao gom ID dang xem, tags, video cung chuyen muc
        $dataExclude = (isset($params['exclude']) && !empty($params['exclude'])) ? explode(',', $params['exclude']) : array();
        $countExclude = count($dataExclude);
        //get detail video
        $id = $params['cate_code'];
        $arrVideoInfo = $objArticle->getArticleFull($id);
        //khoi tao so luong $count
        $count = 0;
        //khoi tao data $arrVideoSuggest
        $arrVideoSuggest = array();
        //get data suggest
        //get video lien quan
        if (!empty($arrVideoInfo['list_tag']))
        {
            //New object
            $obj = new Fpt_Data_News_Tag();
            $arrParams = array(
                'tag_id' => array(),
                'article_type' => TYPE_VIDEO,
                'limit' => LIMIT_SUGGEST_VIDEO + $countExclude,
                'offset' => 0
            );
            foreach ($arrVideoInfo['list_tag'] as $tag)
            {
                $arrParams['tag_id'][] = $tag['tag_id'];
            }
            //get list video lien quan
            $arrVideoResult = $obj->getArticleByArrTag($arrParams);
            foreach ($arrVideoResult as $value)
            {
                $arrVideoSuggest = array_unique(array_merge($arrVideoSuggest, $value['data']));
            }
            //neu co video lien quan
            if (!empty($arrVideoResult))
            {
                //loai tru tin dang xem
                $arrVideoSuggest = array_diff($arrVideoSuggest, $dataExclude);
                //tinh tong so video lien quan
                $count = count($arrVideoSuggest);
            }
            unset($arrVideoResult);
        }
        //tong so nho hon so luong can lay
        //Nếu không đủ 9 video thì lấy thêm video cùng chuyên mục
        //Loại trừ Video đang xem
        //Sắp xếp Video mới lên trên
        if ($count < LIMIT_SUGGEST_VIDEO)
        {
            //get instance Cate
            $objCate = Thethao_Model_Category::getInstance();
            //Get cate id
            $intCateId = $arrVideoInfo['category_id'];
            $arrCateDetail = $objCate->getMenu(BLOCK_CATE_VIDEO);
            if (!isset($arrCateDetail['child'][$intCateId]))
            {
                $intCateId = BLOCK_CATE_VIDEO;
            }

            //Init params
            $arrParamsVideoRule3 = array(
                'category_id' => $intCateId,
                'limit' => LIMIT_SUGGEST_VIDEO + $countExclude,
                'offset' => 0
            );
            //Get news by rule 3 : folder X + subfolder X  => order by publish_time
            $arrListVideoLasest = $objNews->getListArticleIdsByRule3($arrParamsVideoRule3);
            //Loai tru video dang xem
            $arrListVideoLasest = array_diff($arrListVideoLasest['data'], $dataExclude);
            //merge list video folder vao video lien de lay them cac video moi nhat
            // cung folder dang xem
            $arrVideoSuggest = array_merge($arrVideoSuggest, $arrListVideoLasest);
            //lay dung so luong hien thi
            $arrVideoSuggest = array_slice(array_unique($arrVideoSuggest), 0, LIMIT_SUGGEST_VIDEO);
        }
        else
        {
            //lay dung so luong hien thi
            $arrVideoSuggest = array_slice($arrVideoSuggest, 0, LIMIT_SUGGEST_VIDEO);
        }

        //set vao article detail
        $objArticle->setIdBasic($arrVideoSuggest);
        //var_dump($arrVideoSuggest);die;
        //assign to view
        $this->view->assign(array(
            'arrData' => $arrVideoSuggest
        ));
    }

    /**
     * @author   : PhongTX
     * @name : ajaxlistvideoAction
     * @copyright   : FPT Online
     * @todo    : ajaxlistvideo Action
     * @return   : Json
     */
    public function ajaxlistvideoAction()
    {
        //set cache
        $this->_request->setParam('cache_file', 1);
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
        // Get cate id
        $intCateId = intval($this->_request->getParam('cateId', 0));
        //Get curr page
        $currPage = intval($this->_request->getParam('currPage', 1));
        //Get str Ex
        $strEx = $this->_request->getParam('strEx', '');
        $arrEx = explode(',', $strEx);
        // Get total new
        $intTotalNew = intval($this->_request->getParam('totalNew', 0));
        // if hasn't cate => load by id
        if ($intCateId)
        {
            //New object
            $objNews = new Thethao_Model_News();
            //Init params
            $arrParams = array(
                'category_id' => $intCateId,
                'limit' => LIMIT_LIST_VIDEO + count($arrEx), //Limit 12 video / 1 page
                'offset' => ($currPage - 1) * (LIMIT_LIST_VIDEO + $intTotalNew)
            );
            //get all article by rule 2
            $arrData = $objNews->getListArticleIdsByRule2($arrParams);
            $arrListVideoTemp = array_slice($arrData['data'], 0, LIMIT_LIST_VIDEO);
            $arrPush = array();
            $i = 0;
            foreach ($arrEx as $idEx)
            {
                if (in_array($idEx, $arrListVideoTemp))
                {
                    $i++;
                }
                if (in_array($idEx, $arrData['data']))
                {
                    array_push($arrPush, $idEx);
                }
            }
            $arrData['data'] = array_slice(array_values(array_diff($arrData['data'], $arrPush)), 0, LIMIT_LIST_VIDEO);
            //Set lai total
            $arrData['total'] = $arrData['total'] - $intTotalNew - $i;

            //Valid data
            if (!empty($arrData['data']))
            {
                //set obj get Article
                $this->view->objArticle->setIdBasic($arrData['data']);
            }
            //Get total page
            $totalPage = $arrData['total'] ? intval(ceil($arrData['total'] / LIMIT_LIST_VIDEO)) : 0;
            //InitParam Paging
            $arrParamPaging = array(
                'total' => $arrData['total'],
                'page' => $currPage,
                'perpage' => LIMIT_LIST_VIDEO,
                'classPagination' => 'pagination_news right',
            );
            // Assign to view
            $this->view->assign(array(
                'arrData' => $arrData,
                'arrParamPaging' => $arrParamPaging,
                'totalPage' => $totalPage,
                'currPage' => $currPage,
                'cateId' => $intCateId
            ));
            //Render view
            $response['html'] = $this->view->render('video/box/list_video.phtml');
        }
        //Return Json
        echo Zend_Json::encode($response);
    }

    /**
     * @author      : AnPCD
     * @name        : feedbackAction
     * @copyright   : FPT Online
     * @todo        : Ajax insert Feedback Video (Phản hồi của user về Video)
     */
    public function feedbackAction()
    {
        // Disable render layout
        $this->_helper->layout->disableLayout(true);
        // Set not render view
        $this->_helper->viewRenderer->setNoRender(true);
        //Init ajax return value
        $response = array(
            'isSuccess' => 0,
            'isCaptchaError' => 0
        );

        //Get Params
        $arrParams = $this->_request->getParams();
        //Get type
        $arrParams['type'] = array_sum($arrParams['type']);
        //Init flag for Check Valid captcha
        $flagCheckCaptcha = FALSE;
        //Trim captcha id
        $captchaID = trim($arrParams['captchaID']);
        //Valid captcha id
        if ($captchaID)
        {
            // session namespace zend
            $captchaSession = new Zend_Session_Namespace('Zend_Form_Captcha_' . $captchaID);
            //Get session word captcha
            $captchaSession->word = $_SESSION['word'];
            $captchaIterator = $captchaSession->getIterator();
            //Get captcha word
            $captchaWord = $captchaIterator['word'];
            //Get & trim input captcha
            $input_word = trim($arrParams['txtCode']);
            //Valid input word captcha & word captcha session
            if ($input_word == $captchaWord)
            {
                $flagCheckCaptcha = TRUE;
            }
            else
            {
                //Return response Captcha Error
                $response['isCaptchaError'] = 1;
            }
        }
        //Valid check captcha
        if ($flagCheckCaptcha)
        {
            //push job call userFeedbackVideo
            $jobParams = array(
                'class' => 'Job_Vnexpress_Async',
                'function' => 'userFeedbackVideo',
                'args' => array('params' => array(
                        'email' => NULL,
                        'fullname' => NULL,
                        'videoid' => (int) $arrParams['videoid'],
                        'type' => (int) $arrParams['type'],
                        'content' => mb_substr(trim(strip_tags($arrParams['content'])), 0, 1000, 'UTF-8'),
                        'siteid' => SITE_ID,
                        'Ip' => $_SERVER['REMOTE_ADDR']
                    )
                )
            );


            //get application config
            $config = Vnexpress_Global::getApplicationIni();
            //To array
            $jobConfiguration = $config['job']['task']['vnexpress']['feedback_video'];
            //get job post article instance
            $jobClient = Vnexpress_Global::getJobClientInstance('vnexpress');
            //Register job
            $result = $jobClient->doBackgroundTask($jobConfiguration, $jobParams);
            //Valid result
            if ($result)
            {
                //Return success
                $response['isSuccess'] = 1;
                //refesh array params
                $arrParams = null;
            }
        }
        echo Zend_Json::encode($response);
        return;
    }

}

?>