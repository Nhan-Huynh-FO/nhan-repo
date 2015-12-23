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
        //get instance Cate
        $objCate = Thethao_Model_Category::getInstance();
        //get cate id by cate code
        $intCateId = $objCate->getIdByCode($this->_request->getParam('cate_code', SITE_ID));

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
            $this->_redirect($this->view->configView['url'] . $cateDetail['link'], array('code' => 301));
        }

        //init param to get list data with rule 2
        $arrParamNews = array(
            'category_id'   => $intCateId,
            'article_type'  => NULL,
            'limit'         => LIMIT_LIST,
            'offset'        => ($intPage - 1) * LIMIT_LIST,
        );

        //init model news
        $objNews = Thethao_Model_News::getInstance();

        //get list article with rule 2 list on Thethao
        $arrListData = $objNews->getListArticleIdsByRule2($arrParamNews);

        $arrArticleExclude = array();
        $arrHotNews = array();
        //get Instance Block
        $objBlock = Thethao_Plugin_Block::getInstance();
        if (1 == $intPage && !empty($arrListData['data']))
        {
            //check bai thuong mai, chi so sanh o trang 1 ma thoi
            $zoneID = Thethao_Global::getZoneByCateId($intCateId);
            if ($zoneID != 0)
            {
                $modelBlock = Fpt_Data_News_Block::getInstance();
                $modelBlock->setZoneId($zoneID);
                $arrListData['data'] = $modelBlock->getNewsByZone($zoneID, $arrListData['data']);
            }
            $arrArticleExclude = $arrListData['data'];
            // set exclude article id from arrData
            $objBlock->setExclude($arrListData['data']);

            $arrHotNews = array_slice($arrListData['data'], 0, LIMIT_TOP);
            $arrListData['data'] = array_diff($arrListData['data'], $arrHotNews);
            // set id from $arrListPaging to get article
            $this->view->objArticle->setIdBasic($arrHotNews);

        }

        // set id from $arrListPaging to get article
        $this->view->objArticle->setIdBasic($arrListData['data']);

        //check if page > max page then return to max page
        if ($arrListData['total'] > 0)
        {
            $maxPage = ceil($arrListData['total'] / LIMIT_LIST);
            if ($intPage > $maxPage)
            {
                $this->_redirect($this->configView['url'] . $cateDetail['link'] . '/page/' . $maxPage . '.html');
            }
        }

        //InitParam Paging
        $arrParamPaging = array(
            'total' => $arrListData['total'],
            'page' => $intPage,
            'url' => $this->view->configView['url'] . $cateDetail['link'] . '/page',
            'perpage' => LIMIT_LIST,
            'classPagination' => 'pagination_news right',
            'extEnd' => '.html',
            'separate' => '/'
        );

        //Set param
        $this->_request->setParam('block_cate', $intCateId);

        //Param calendar
        $parserParent           = SITE_ID;
        $parserChild            = $intCateId;
        $parserChildRecursive   = 0;
        if ( !empty($cateDetail['full_parent']) && DEVICE_ENV != 1 )
        {
            $nParser	= count($cateDetail['full_parent']);
            switch ( $nParser )
            {
                case 1:
                    $parserChild            = $cateDetail['full_parent'][0];
                    $parserChildRecursive   = $intCateId;
                    break;
                case 2:
                default:
                    $parserChild            = $cateDetail['full_parent'][0];
                    $parserChildRecursive	= $cateDetail['full_parent'][1];
                    break;
            }//end switch
        }//end if

        //gan meta data
        $titleMenta = $keywords = $cateDetail['catename'] . ' - VnExpress Thể Thao';
        $description = $cateDetail['catename'] . ' - VnExpress Thể Thao';
        switch ($intCateId) {
            case CATE_ID_BONGDA:
                $titleMenta = 'Lưu trữ tin tức bóng đá thể thao – Bóng đá thể thao';
                $description = 'Lưu trữ tin tức hình ảnh các ngôi sao thể thao,bóng đá,hậu trường thể thao';
                $keywords = 'Bong da,the thao,tin tuc bong đá, bóng đá 24h';
                break;
            case CATE_ID_BONGDATRONGNUOC:
                $titleMenta = 'Tin tức, bình luận, Tin tức thể thao trong nước mới nhất, V-League, VFF – Bóng đá trong nước';
                $description = 'Thông tin mới nhất về THỂ THAO TRONG NƯỚC, Liên đoàn Bóng Đá Việt Nam, các giải đấu, lịch thi đấu V League, VFF, ĐTQG, Cúp Quốc Gia, Giải Hạng Nhất, U19, U23';
                $keywords = 'Tin tức, bong da, bóng đá việt nam,V-League, VFF bóng đá trong nước';
                break;
            case CATE_ID_NGOAIHANGANH:
                $titleMenta = 'Tin tức, bình luận, video, lịch thi đấu bóng đá giải - Ngoại hạng Anh';
                $description = 'Ngoại hạng Anh - Tin tức giải Premier League , Video bàn thắng, bảng xếp hạng, lịch thi đấu Ngoại hạng Anh, liên tục trên thể thao vnexpress';
                $keywords = 'Giải ngoại hạng Anh , ngoai hang anh , bong da ngoai hang anh , bong da anh , tin bong da , bong da quoc te , bao bong da';
                break;
            case CATE_ID_BEHINDS_SCENES:
                $titleMenta = 'Lưu trữ tin tức hậu trường thể thao';
                $description = 'Lưu trữ tin tức hình ảnh các ngôi sao thể thao,bóng đá,hậu trường thể thao';
                $keywords = 'Hau truong,the thao,bong da';
                break;
            case CATE_ID_CACMONKHAC:
                $titleMenta = 'Tin nhanh,hình ảnh,video kết quả thi đấu các môn thể thao khác';
                $description = 'Tin tức,hình ảnh,video các môn thể thao khác';
                $keywords = 'Bong da,the thao,tin tuc';
                break;
            case CATE_ID_TENNIS:
                $titleMenta = 'Tin tức quần vợt, tennis, lịch thi đấu, video trực tuyến - Tennis';
                $description = 'Cập nhật nhanh tin tức Quần vợt mới nhất hôm nay. Xem kết quả, lịch thi đấu, video clip thể thao Tennis trực tuyến Online tại Việt Nam & Thế giới.';
                $keywords = 'tennis 24h, quan vot, tennis truc tuyen , tennis online, tin tuc tennis, lich thi dau tennis';
                break;
            case CATE_ID_RACING:
                $titleMenta = 'Đua xe - Thể thao VnExpress';
                $description = 'Cập nhật nhanh tin tức, video các giải đua xe trong và ngoài nước.';
                $keywords = 'Đua xe, tay dua, cong thuc 1 , du axe F1';
                break;
            case CATE_ID_AFFCUP:
                $titleMenta = 'Aff Cup 2012 - Tin tức, hình ảnh ,video mới nhất về AFF Cup 2012 - VnExpress';
                $description = 'Cập nhật liên tục tin tức mới nhất về giải bóng đá Đông Nam Á - AFF cup 2012 tổ chức tại Malaysia và Thái Lan';
                $keywords = 'aff cup 2012,bong da,the thao';
                break;
			case CATE_ID_CHESS:
                $titleMenta = 'Cờ Vua: Lịch thi đấu, giải thi đấu cờ vua - VnExpress Thể Thao';
                $description = 'Tin tức nhanh về thông tin các giải thi đấu cờ vua Quốc tế, giải thi đấu tại Việt Nam cùng các cờ thủ xuất sắc nhất';
                $keywords = 'Tin tức nhanh về thông tin các giải thi đấu cờ vua Quốc tế, giải thi đấu tại Việt Nam cùng các cờ thủ xuất sắc nhất';
                break;
            case CATE_ID_GOLF:
                $titleMenta = 'Tin Golf Việt Nam: Giải đấu và lịch thi đấu môn đánh Golf – Golf thể thao';
                $description = 'Tin tức, video đánh giá bình luận về Golf, nhận xét các giải đấu trong nước, quốc tế cùng hướng dẫn các kỹ năng chơi Golf dành cho giới kinh doanh, Golf thủ Việt Nam..';
                $keywords = 'Golf thủ, tay golf,tin tức golf, giải golf, Presidents Cup, FLC Golf Championship 2015';
                break;
            default:
                $titleMenta = 'Tin tức, bình luận, video, lịch thi đấu bóng đá giải - ' . $cateDetail['catename'];
                $description = 'Tin nhanh,tin tức với hình ảnh,Video clip,bình luận về các giải bóng đá: ' . $cateDetail['catename'];
                $keywords = 'Tin tức, bong da, the gioi';
        }

        //Assign to view
        $this->view->assign(array(
            'intCategoryId'     => $intCateId,
            'arrHotNews'        => $arrHotNews,
            'arrParamPaging'    => $arrParamPaging,
            'arrData'           => $arrListData['data'],
            'parserParent'      => $parserParent,
            'parserChild'       => $parserChild,
            'parserChildRecursive'  => $parserChildRecursive,
            'arrArticleExclude' => $arrArticleExclude,
            'paramsBlock' => Zend_Json::encode(array(
                'pagecode' => 'default_category_index_' . $intCateId,
                'exclude' => implode(',', $objBlock->getExclude())
            )),
            'ogType'            => 'website',
            'ogTitle'           => $titleMenta,
            'ogUrl'             => $this->view->configView['url'] . $cateDetail['link'],
            'ogImage'           => 'http://m.f9.img.vnexpress.net/2014/01/10/fa68f81afe10b727852361b1894ccc74.jpg',
            'ogDescription'     => $description
        ));

        //init cate for set menu
        $idCateMenu = $cateDetail['parent_id'] == SITE_ID ? $intCateId : $cateDetail['parent_id'];
        //set cate active
        $this->_request->setParam('cateActiveId', $intCateId);
        //set cateid to show active in menu
        $this->_request->setParam('cateid', $idCateMenu);

        // Set metadata tags
        $this->view->headTitle()->prepend($titleMenta);
        $this->view->headMeta()->setName('description', $description)
                ->setName('keywords', $keywords);

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
     * Topic action
     */
    public function topicAction()
    {
        //Get string link
        $strLink = $this->view->configView['url_vne'] . $this->_request->getPathInfo();
        //Redirect sang VNE
        $this->_redirect($strLink, array('code'=>301));
    }

    /**
     * Tag action
     */
    public function tagAction()
    {
        //Get string link
        $strLink = $this->view->configView['url_vne'] . $this->_request->getPathInfo();
        //Redirect sang VNE
        $this->_redirect($strLink, array('code'=>301));
    }

}